<?php

declare(strict_types=1);



class CreateVendorTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the vendor table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS vendor (
                vendor_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `vendor_code` VARCHAR(191) NULL DEFAULT NULL,
                admin_id INT unsigned NOT NULL DEFAULT '0',
                name varchar(191) NOT NULL DEFAULT '',
                slug varchar(191) NOT NULL DEFAULT '',
                image json NULL DEFAULT NULL,
                sort_order int(3) NOT NULL DEFAULT 0,
                created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                deleted_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (vendor_id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'vendor' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'vendor': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the vendor table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS vendor;";

        try {
            $this->pdo->exec($query);
            echo "Table 'vendor' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'vendor': " . $e->getMessage() . "\n";
        }
    }
}

