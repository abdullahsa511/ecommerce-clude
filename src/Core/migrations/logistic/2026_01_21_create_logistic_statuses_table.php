<?php

declare(strict_types=1);

class CreateLogisticStatusesTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the logistic_statuses table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `logistic_statuses` (
                `logistic_statuses_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `language_id` INT UNSIGNED NOT NULL DEFAULT 1,
                `name` VARCHAR(32) NOT NULL,
                `sort_order` INT NOT NULL DEFAULT 0,
                PRIMARY KEY (`logistic_statuses_id`, `language_id`),
                KEY `language_id` (`language_id`),
                UNIQUE KEY `logistic_statuses_name_language_id` (`name`, `language_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'logistic_statuses' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'logistic_statuses': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the logistic_statuses table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `logistic_statuses`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'logistic_statuses' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'logistic_statuses': " . $e->getMessage() . "\n";
        }
    }
} 