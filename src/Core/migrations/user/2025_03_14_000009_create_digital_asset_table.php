<?php

declare(strict_types=1);

class CreateDigitalAssetTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the digital_asset table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `digital_asset` (
                `digital_asset_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `digital_asset_code` varchar(191) DEFAULT NULL,
                `file` varchar(160) NOT NULL,
                `public` varchar(128) NOT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`digital_asset_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'digital_asset' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'digital_asset': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the digital_asset table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `digital_asset`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'digital_asset' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'digital_asset': " . $e->getMessage() . "\n";
        }
    }
} 