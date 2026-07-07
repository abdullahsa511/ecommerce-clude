<?php

declare(strict_types=1);

class CreateDigitalAssetContentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the digital_asset_content table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `digital_asset_content` (
                `digital_asset_id` INT UNSIGNED NOT NULL,
                `language_id` INT UNSIGNED NOT NULL,
                `name` varchar(64) NOT NULL,
                PRIMARY KEY (`digital_asset_id`,`language_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'digital_asset_content' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'digital_asset_content': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the digital_asset_content table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `digital_asset_content`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'digital_asset_content' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'digital_asset_content': " . $e->getMessage() . "\n";
        }
    }
} 