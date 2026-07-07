<?php

declare(strict_types=1);

class CreateFieldValueTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the field_value table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `field_value` (
                `field_value_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `field_id` int(10) UNSIGNED NOT NULL,
                `sort_order` int(3) NOT NULL DEFAULT 0,
                PRIMARY KEY (`field_value_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'field_value' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'field_value': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the field_value table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `field_value`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'field_value' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'field_value': " . $e->getMessage() . "\n";
        }
    }
} 