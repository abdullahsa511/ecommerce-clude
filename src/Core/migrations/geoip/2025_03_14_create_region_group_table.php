<?php

declare(strict_types=1);

class CreateRegionGroupTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the region_group table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `region_group` (
                `region_group_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` varchar(32) NOT NULL,
                `content` varchar(191) NOT NULL DEFAULT '',
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`region_group_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'region_group' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'region_group': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the region_group table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `region_group`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'region_group' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'region_group': " . $e->getMessage() . "\n";
        }
    }
} 