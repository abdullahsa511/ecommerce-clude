<?php

declare(strict_types=1);

class CreateProjectStatusTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the order_status table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `project_status` (
                `project_status_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `language_id` INT UNSIGNED NOT NULL,
                `name` varchar(32) NOT NULL,
                PRIMARY KEY (`project_status_id`,`language_id`),
                KEY `project_status_id` (`project_status_id`),
                KEY `language_id` (`language_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'project_status' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'project_status': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the order_status table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `project_status`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'project_status' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'project_status': " . $e->getMessage() . "\n";
        }
    }
} 