<?php

declare(strict_types=1);

class CreateSubscriptionStatusTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the subscription_status table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `subscription_status` (
                `subscription_status_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `language_id` INT UNSIGNED NOT NULL,
                `name` varchar(32) NOT NULL,
                PRIMARY KEY (`subscription_status_id`,`language_id`),
                KEY `subscription_status_id` (`subscription_status_id`),
                KEY `language_id` (`language_id`),
                UNIQUE KEY `subscription_status_name_language_id` (`name`, `language_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'subscription_status' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'subscription_status': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the subscription_status table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `subscription_status`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'subscription_status' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'subscription_status': " . $e->getMessage() . "\n";
        }
    }
} 