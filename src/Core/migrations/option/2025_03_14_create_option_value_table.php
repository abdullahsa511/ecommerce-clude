<?php

declare(strict_types=1);

class CreateOptionValueTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the option_value table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `option_value` (
                `option_value_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `option_id` int(10) UNSIGNED NOT NULL,
                `image` varchar(255) NOT NULL,
                `sort_order` int(3) NOT NULL DEFAULT 0,
                PRIMARY KEY (`option_value_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'option_value' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'option_value': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the option_value table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `option_value`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'option_value' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'option_value': " . $e->getMessage() . "\n";
        }
    }
} 