<?php

declare(strict_types=1);

class CreateUserAddressTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the user_address table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `user_address` (
                `user_address_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` INT UNSIGNED NOT NULL,
                `first_name` varchar(32) NOT NULL,
                `last_name` varchar(32) NULL,
                `company` varchar(60) NOT NULL,
                `address_1` varchar(128) NOT NULL,
                `address_2` varchar(128) NOT NULL,
                `country_id` INT UNSIGNED NOT NULL DEFAULT '0',
                `region_id` INT UNSIGNED NOT NULL DEFAULT '0',
                `city` varchar(128) NOT NULL,
                `post_code` varchar(10) NOT NULL,
                `default_address` tinyint unsigned NOT NULL DEFAULT 0,
                `fields` text,
                `is_billing` tinyint unsigned NOT NULL DEFAULT 0,
                `is_shipping` tinyint unsigned NOT NULL DEFAULT 0,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `deleted_at` datetime DEFAULT NULL,
                PRIMARY KEY (`user_address_id`),
                KEY `user_id` (`user_id`),
                UNIQUE KEY `user_id_is_shipping_is_billing` (`user_id`, `is_billing`, `is_shipping`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'user_address' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'user_address': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the user_address table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `user_address`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'user_address' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'user_address': " . $e->getMessage() . "\n";
        }
    }
} 