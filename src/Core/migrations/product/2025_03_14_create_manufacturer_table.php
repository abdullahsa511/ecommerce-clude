<?php

declare(strict_types=1);


class CreateManufacturerTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the manufacturer table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS manufacturer (
                manufacturer_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `manufacturer_code` VARCHAR(191) NULL DEFAULT NULL,
                name varchar(191) NOT NULL DEFAULT '',
                slug varchar(191) NOT NULL DEFAULT '',
                image varchar(191) DEFAULT NULL,
                sort_order int(3) NOT NULL DEFAULT 0,
                PRIMARY KEY (manufacturer_id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'manufacturer' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'manufacturer': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the manufacturer table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS manufacturer;";

        try {
            $this->pdo->exec($query);
            echo "Table 'manufacturer' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'manufacturer': " . $e->getMessage() . "\n";
        }
    }
}