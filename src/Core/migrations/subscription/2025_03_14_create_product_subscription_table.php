<?php

declare(strict_types=1);

class CreateProductSubscriptionTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_subscription table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `product_subscription` (
                `product_id` int(10) UNSIGNED NOT NULL,
                `subscription_plan_id` INT UNSIGNED NOT NULL,
                `user_group_id` int(10) UNSIGNED NOT NULL,
                `price` decimal(10,4) NOT NULL,
                `trial_price` decimal(10,4) NOT NULL,
                KEY (`product_id`,`subscription_plan_id`,`user_group_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_subscription' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_subscription': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_subscription table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `product_subscription`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_subscription' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_subscription': " . $e->getMessage() . "\n";
        }
    }
} 