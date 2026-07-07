<?php

declare(strict_types=1);

class CreateOrderStatusTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the order_status table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `order_status` (
                `order_status_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `language_id` INT UNSIGNED NOT NULL,
                `name` varchar(32) NOT NULL,
                `sort_order` INT NOT NULL DEFAULT 0,
                PRIMARY KEY (`order_status_id`,`language_id`),
                KEY `order_status_id` (`order_status_id`),
                KEY `language_id` (`language_id`),
                UNIQUE KEY `order_status_name_language_id` (`name`, `language_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'order_status' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'order_status': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the order_status table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `order_status`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'order_status' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'order_status': " . $e->getMessage() . "\n";
        }
    }
} 