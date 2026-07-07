<?php

declare(strict_types=1);

class CreateRegionTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the region table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `region` (
                `region_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `country_id` INT UNSIGNED NOT NULL,
                `name` varchar(128) NOT NULL,
                `code` varchar(32) NOT NULL,
                `status` tinyint NOT NULL DEFAULT '1',
                PRIMARY KEY (`region_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'region' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'region': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the region table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `region`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'region' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'region': " . $e->getMessage() . "\n";
        }
    }
} 