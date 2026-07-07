<?php

declare(strict_types=1);

class CreateMenuTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the menu table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `menu` (
                `menu_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` varchar(191) NOT NULL DEFAULT '',
                `slug` varchar(191) NOT NULL DEFAULT '',
                PRIMARY KEY (`menu_id`),
                KEY `menu_id` (`menu_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'menu' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'menu': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the menu table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `menu`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'menu' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'menu': " . $e->getMessage() . "\n";
        }
    }
} 