<?php

declare(strict_types=1);

class CreateSubscriptionLogTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the subscription_log table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `subscription_log` (
                `subscription_log_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `subscription_id` int(10) UNSIGNED NOT NULL,
                `subscription_status_id` int(10) UNSIGNED NOT NULL,
                `notify` tinyint NOT NULL DEFAULT 0,
                `note` text NOT NULL,
                `created_at` datetime NOT NULL,
                PRIMARY KEY (`subscription_log_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'subscription_log' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'subscription_log': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the subscription_log table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `subscription_log`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'subscription_log' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'subscription_log': " . $e->getMessage() . "\n";
        }
    }
} 