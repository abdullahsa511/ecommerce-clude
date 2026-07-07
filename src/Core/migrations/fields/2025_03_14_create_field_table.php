<?php

declare(strict_types=1);

class CreateFieldTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the field table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `field` (
                `field_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `field_group_id` int NOT NULL,
                `type` varchar(32) NOT NULL,
                `value` text NOT NULL,
                `status` tinyint NOT NULL DEFAULT 0,
                `sort_order` int NOT NULL DEFAULT 0,
                PRIMARY KEY (`field_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'field' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'field': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the field table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `field`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'field' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'field': " . $e->getMessage() . "\n";
        }
    }
} 