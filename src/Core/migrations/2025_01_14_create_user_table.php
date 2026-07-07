<?php

declare(strict_types=1);

class CreateUserTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the user table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `user` (
                `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `uuid` binary(16) NOT NULL,
                `user_group_id` INT UNSIGNED NOT NULL DEFAULT 1,
                `site_id` INT UNSIGNED NOT NULL DEFAULT 1,
                `username` varchar(60) NOT NULL DEFAULT '',
                `first_name` varchar(32) NOT NULL DEFAULT '',
                `last_name` varchar(32) NOT NULL DEFAULT '',
                `password` varchar(191) NOT NULL DEFAULT '',
                `email` varchar(100) NOT NULL DEFAULT '',
                `otp_code` varchar(10) NULL DEFAULT '',
                `otp_created_at` datetime NULL DEFAULT '',
                `otp_expiry_time` datetime NULL DEFAULT '',
                `is_verified` tinyint NOT NULL DEFAULT 0,
                `is_admin` tinyint NOT NULL DEFAULT 0,
                `phone_number` varchar(32) NOT NULL DEFAULT '',
                `url` varchar(100) NULL DEFAULT null,
                `status` INT UNSIGNED NOT NULL DEFAULT '0',
                `display_name` varchar(250) NULL DEFAULT null,
                `avatar` varchar(250) NULL DEFAULT null,
                `bio` text,
                `designation` VARCHAR(50) DEFAULT NULL ,
                `token` varchar(32) NULL DEFAULT null,
                `subscribe` tinyint NOT NULL DEFAULT 0,
                `notify_orders` tinyint NOT NULL DEFAULT 0,
                `notify_quotes` tinyint NOT NULL DEFAULT 0,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `deleted_at` datetime DEFAULT NULL,
                PRIMARY KEY (`user_id`),
                KEY `username` (`username`),
                UNIQUE KEY `email` (`email`),
                UNIQUE KEY `uuid` (`uuid`),
                KEY `created_at` (`created_at`),
                KEY `is_verified` (`is_verified`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'user' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'user': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the user table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `user`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'user' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'user': " . $e->getMessage() . "\n";
        }
    }
} 