<?php

declare(strict_types=1);

class CreateMessageTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the message table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `message` (
                `message_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `type` varchar(20) NOT NULL DEFAULT 'message',
                `data` text NOT NULL,
                `meta` text NOT NULL,
                `status` tinyint(6) NOT NULL DEFAULT '0', -- unread = 0, read = 1
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`message_id`),
                KEY `type_status_date` (`status`, `type`,`created_at`,`message_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'message' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'message': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the message table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `message`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'message' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'message': " . $e->getMessage() . "\n";
        }
    }
} 