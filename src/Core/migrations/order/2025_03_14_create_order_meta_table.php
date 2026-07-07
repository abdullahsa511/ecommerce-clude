<?php

declare(strict_types=1);

class CreateOrderMetaTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the order_meta table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `order_meta` (
                `meta_id` INT unsigned NOT NULL AUTO_INCREMENT,
                `order_id` INT unsigned NOT NULL DEFAULT '0',
                `key` varchar(191) DEFAULT NULL,
                `value` longtext,
                PRIMARY KEY (`meta_id`),
                KEY `order_id` (`order_id`),
                KEY `key` (`key`(191))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'order_meta' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'order_meta': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the order_meta table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `order_meta`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'order_meta' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'order_meta': " . $e->getMessage() . "\n";
        }
    }
} 