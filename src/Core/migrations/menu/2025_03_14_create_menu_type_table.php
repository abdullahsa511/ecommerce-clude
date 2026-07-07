<?php

declare(strict_types=1);

class CreateMenuTypeTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the menu_type table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `menu_type` (
                `menu_type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `code` varchar(64) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`menu_type_id`),
                UNIQUE KEY `code` (`code`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'menu_type' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'menu_type': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the menu_type table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `menu_type`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'menu_type' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'menu_type': " . $e->getMessage() . "\n";
        }
    }
} 