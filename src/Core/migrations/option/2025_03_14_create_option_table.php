<?php

declare(strict_types=1);

class CreateOptionTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the option table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `option` (
                `option_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `option_code` varchar(191) DEFULT NULL,
                `type_id` int(10) UNSIGNED NOT NULL,
                `type` varchar(64) NOT NULL,
                `sort_order` int(3) NOT NULL DEFAULT 0,
                `deleted_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`option_id`, `type`),
                INDEX `option_id` (`option_id`),
                INDEX `type` (`type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'option' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'option': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the option table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `option`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'option' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'option': " . $e->getMessage() . "\n";
        }
    }
} 