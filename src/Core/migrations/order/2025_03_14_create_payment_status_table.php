<?php

declare(strict_types=1);

class CreatePaymentStatusTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the payment_status table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `payment_status` (
                `payment_status_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `language_id` INT UNSIGNED NOT NULL,
                `name` varchar(32) NOT NULL,
                PRIMARY KEY (`payment_status_id`,`language_id`),
                KEY `payment_status_id` (`payment_status_id`),
                KEY `language_id` (`language_id`),
                UNIQUE KEY `payment_status_name_language_id` (`name`, `language_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'payment_status' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'payment_status': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the payment_status table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `payment_status`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'payment_status' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'payment_status': " . $e->getMessage() . "\n";
        }
    }
} 