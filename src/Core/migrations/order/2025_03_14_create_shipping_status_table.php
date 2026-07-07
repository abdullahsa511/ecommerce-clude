<?php

declare(strict_types=1);

class CreateShippingStatusTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the shipping_status table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `shipping_status` (
                `shipping_status_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `language_id` INT UNSIGNED NOT NULL,
                `name` varchar(32) NOT NULL,
                PRIMARY KEY (`shipping_status_id`,`language_id`),
                KEY `shipping_status_id` (`shipping_status_id`),
                KEY `language_id` (`language_id`),
                UNIQUE KEY `shipping_status_name_language_id` (`name`, `language_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'shipping_status' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'shipping_status': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the shipping_status table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `shipping_status`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'shipping_status' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'shipping_status': " . $e->getMessage() . "\n";
        }
    }
} 