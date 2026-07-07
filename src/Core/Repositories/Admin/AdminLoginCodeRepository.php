<?php

declare(strict_types=1);

namespace App\Core\Repositories\Admin;

use PDO;

class AdminLoginCodeRepository implements AdminLoginCodeRepositoryInterface
{
    public function __construct(private PDO $db)
    {
    }

    public function issueCode(int $userId, string $email, string $source, ?string $ip = null, ?string $userAgent = null): string
    {
        $plainCode = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $codeHash = hash('sha256', $plainCode);

        // Use DB time for expiry so exchange works across PHP services/containers with clock skew.
        $sql = 'INSERT INTO admin_login_code (code_hash, user_id, email, source, ip_address, user_agent, expires_at, consumed_at, created_at)
                VALUES (:code_hash, :user_id, :email, :source, :ip_address, :user_agent, DATE_ADD(NOW(), INTERVAL 5 MINUTE), NULL, NOW())';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':code_hash' => $codeHash,
            ':user_id' => $userId,
            ':email' => $email,
            ':source' => $source,
            ':ip_address' => substr((string) ($ip ?? ''), 0, 45),
            ':user_agent' => substr((string) ($userAgent ?? ''), 0, 255),
        ]);

        return $plainCode;
    }

    public function consumeCode(string $plainCode): ?array
    {
        $plainCode = $this->normalizePlainCode($plainCode);
        if ($plainCode === '') {
            return null;
        }

        $codeHash = hash('sha256', $plainCode);
        $now = date('Y-m-d H:i:s');

        $this->db->beginTransaction();
        try {
            // DB-side expiry (expires_at >= NOW()) avoids false "expired" when web vs API PHP clocks differ.
            $selectSql = 'SELECT id, user_id, email, source, expires_at, consumed_at
                          FROM admin_login_code
                          WHERE code_hash = :code_hash
                            AND consumed_at IS NULL
                            AND expires_at >= NOW()
                          LIMIT 1
                          FOR UPDATE';
            $selectStmt = $this->db->prepare($selectSql);
            $selectStmt->execute([':code_hash' => $codeHash]);
            $row = $selectStmt->fetch(PDO::FETCH_ASSOC);

            if (!is_array($row)) {
                $this->db->rollBack();

                return null;
            }

            $updateSql = 'UPDATE admin_login_code SET consumed_at = :consumed_at WHERE id = :id AND consumed_at IS NULL';
            $updateStmt = $this->db->prepare($updateSql);
            $updateStmt->execute([
                ':consumed_at' => $now,
                ':id' => (int) $row['id'],
            ]);

            $this->db->commit();

            return $row;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Strip whitespace / BOM / accidental encoding so the hash matches issueCode().
     */
    private function normalizePlainCode(string $plainCode): string
    {
        $plainCode = preg_replace('/^\xEF\xBB\xBF/', '', $plainCode) ?? $plainCode;
        $plainCode = str_replace(["\xc2\xa0", "\xe2\x80\xaf", "\xe2\x80\x8b"], '', $plainCode);
        $plainCode = trim($plainCode);

        if (
            (str_starts_with($plainCode, '"') && str_ends_with($plainCode, '"'))
            || (str_starts_with($plainCode, "'") && str_ends_with($plainCode, "'"))
        ) {
            $plainCode = trim($plainCode, "\"'");
        }

        if (preg_match('/%[0-9A-Fa-f]{2}/', $plainCode) === 1) {
            $decoded = rawurldecode($plainCode);
            if ($decoded !== '') {
                $plainCode = $decoded;
            }
        }

        return trim($plainCode);
    }
}

