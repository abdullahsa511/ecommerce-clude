<?php

declare(strict_types=1);

class CreateReturnTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the return table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `return` (
                `return_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `order_id` INT UNSIGNED NOT NULL,
                `customer_order_id` varchar(64) NOT NULL DEFAULT '0',
                `product_id` INT UNSIGNED NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                `first_name` varchar(32) NOT NULL,
                `last_name` varchar(32) NOT NULL,
                `email` varchar(96) NOT NULL,
                `phone_number` varchar(32) NOT NULL,
                `product` varchar(191) NOT NULL,
                `model` varchar(64) NOT NULL,
                `quantity` int(4) NOT NULL,
                `opened` tinyint NOT NULL,
                `return_reason_id` INT UNSIGNED NOT NULL DEFAULT 0,
                `return_resolution_id` INT UNSIGNED NOT NULL DEFAULT 0,
                `return_status_id` INT UNSIGNED NOT NULL DEFAULT 0,
                `note` text NOT NULL,
                `date_ordered` date NOT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`return_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'return' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'return': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the return table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `return`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'return' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'return': " . $e->getMessage() . "\n";
        }
    }
} 