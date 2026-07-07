<?php

declare(strict_types=1);

class CreateOrderLogTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the order_log table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `order_log` (
                `order_log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `order_id` INT UNSIGNED NOT NULL,
                `order_status_id` INT UNSIGNED NOT NULL,
                `notify` tinyint NOT NULL DEFAULT '0',
                `public` tinyint NOT NULL DEFAULT '0',
                `note` text NOT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`order_log_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'order_log' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'order_log': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the order_log table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `order_log`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'order_log' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'order_log': " . $e->getMessage() . "\n";
        }
    }
} 