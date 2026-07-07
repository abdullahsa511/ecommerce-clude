<?php

declare(strict_types=1);



class CreateProductCertificateTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the site table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS product_certificate (
                product_certificate_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                product_id INT UNSIGNED NOT NULL,
                media_id INT UNSIGNED NULL,
                logo JSON NULL,
                certificate_file JSON NULL,
                certificate_provider varchar(191) NULL,
                certificate_type varchar(191) NULL,
                file_format varchar(191) NULL,
                title varchar(191) NULL,
                description varchar(500) NULL,
                sort_order INT NULL DEFAULT 0,
                created_at datetime NULL,
                updated_at datetime NULL,
                deleted_at datetime NULL,
                UNIQUE KEY uniq_title_certificate_provider (title, certificate_provider),
                FOREIGN KEY (product_id) REFERENCES product (product_id) ON DELETE CASCADE ON UPDATE CASCADE,
                INDEX idx_sort_order (sort_order),
                PRIMARY KEY (product_certificate_id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_certificate' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_certificate': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the site table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_certificate;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_certificate' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_certificate': " . $e->getMessage() . "\n";
        }
    }
}
