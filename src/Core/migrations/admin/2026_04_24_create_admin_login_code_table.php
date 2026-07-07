<?php

declare(strict_types=1);

class CreateAdminLoginCodeTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `admin_login_code` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `code_hash` CHAR(64) NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                `email` VARCHAR(255) NOT NULL,
                `source` VARCHAR(32) NOT NULL DEFAULT 'otp',
                `ip_address` VARCHAR(45) NOT NULL DEFAULT '',
                `user_agent` VARCHAR(255) NOT NULL DEFAULT '',
                `expires_at` DATETIME NOT NULL,
                `consumed_at` DATETIME NULL DEFAULT NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_admin_login_code_hash` (`code_hash`),
                KEY `idx_admin_login_code_user` (`user_id`),
                KEY `idx_admin_login_code_expires` (`expires_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        $this->pdo->exec($query);
    }

    public function down(): void
    {
        $this->pdo->exec("DROP TABLE IF EXISTS `admin_login_code`;");
    }
}

