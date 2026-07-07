<?php

declare(strict_types=1);

class CreateSubscriptionPlanContentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the subscription_plan_content table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `subscription_plan_content` (
                `subscription_plan_id` int(10) UNSIGNED NOT NULL,
                `language_id` int(10) UNSIGNED NOT NULL,
                `name` varchar(255) NOT NULL,
                PRIMARY KEY (`subscription_plan_id`,`language_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'subscription_plan_content' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'subscription_plan_content': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the subscription_plan_content table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `subscription_plan_content`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'subscription_plan_content' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'subscription_plan_content': " . $e->getMessage() . "\n";
        }
    }
} 