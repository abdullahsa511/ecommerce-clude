<?php

declare(strict_types=1);

class CreateAttributeTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the attribute table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `attribute` (
                `attribute_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `attribute_code` varchar(191) DEFAULT NULL,
                `attribute_group_id` int(10) UNSIGNED NOT NULL,
                `sort_order` int(3) NOT NULL DEFAULT 0,
                `name` varchar(100) NULL,
                `description` text NULL,
                `metadata` varchar(100) NULL,
                `type` varchar(100) NULL,
                `value` text NULL,
                `image` varchar(255) NULL,
                PRIMARY KEY (`attribute_id`),
                UNIQUE KEY `uq_attribute_name_group_id` (`name`, `attribute_group_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'attribute' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'attribute': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the attribute table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `attribute`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'attribute' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'attribute': " . $e->getMessage() . "\n";
        }
    }
} 