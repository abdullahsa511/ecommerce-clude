<?php

declare(strict_types=1);

class CreateDigitalAssetLogTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the digital_asset_log table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `digital_asset_log` (
                `digital_asset_log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `digital_asset_id` INT UNSIGNED NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                `site_id` tinyint(6) NOT NULL,
                `ip` varchar(40) NOT NULL,
                `country` varchar(2),
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`digital_asset_log_id`),
                KEY (`user_id`),
                KEY (`digital_asset_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'digital_asset_log' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'digital_asset_log': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the digital_asset_log table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `digital_asset_log`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'digital_asset_log' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'digital_asset_log': " . $e->getMessage() . "\n";
        }
    }
} 