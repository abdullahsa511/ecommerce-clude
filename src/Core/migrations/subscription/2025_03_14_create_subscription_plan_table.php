<?php

declare(strict_types=1);

class CreateSubscriptionPlanTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the subscription_plan table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `subscription_plan` (
                `subscription_plan_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `period` enum('day','week','month','year') NOT NULL,
                `length` int(10) UNSIGNED NOT NULL,
                `cycle` int(10) UNSIGNED NOT NULL,
                `trial_period` enum('day','week','month','year') NOT NULL,
                `trial_length` int(10) UNSIGNED NOT NULL,
                `trial_cycle` int(10) UNSIGNED NOT NULL,
                `trial_status` tinyint(4) NOT NULL,
                `status` tinyint NOT NULL,
                `sort_order` int(3) NOT NULL DEFAULT 0,
                PRIMARY KEY (`subscription_plan_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'subscription_plan' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'subscription_plan': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the subscription_plan table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `subscription_plan`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'subscription_plan' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'subscription_plan': " . $e->getMessage() . "\n";
        }
    }
} 