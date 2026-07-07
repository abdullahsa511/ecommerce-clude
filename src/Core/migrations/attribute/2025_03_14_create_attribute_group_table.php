<?php

declare(strict_types=1);

class CreateAttributeGroupTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the attribute_group table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `attribute_group` (
                `attribute_group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `code` varchar(191) NOT NULL,
                `sort_order` int(3) NOT NULL DEFAULT 0,
                `deleted_at` datetime DEFAULT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`attribute_group_id`),
                UNIQUE KEY `uq_attribute_group_code` (`code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'attribute_group' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'attribute_group': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the attribute_group table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `attribute_group`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'attribute_group' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'attribute_group': " . $e->getMessage() . "\n";
        }
    }
} 