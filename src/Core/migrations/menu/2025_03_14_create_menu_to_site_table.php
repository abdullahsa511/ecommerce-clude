<?php

declare(strict_types=1);

class CreateMenuToSiteTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the menu_to_site table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `menu_to_site` (
                `menu_id` INT UNSIGNED NOT NULL,
                `site_id` tinyint(6) UNSIGNED NOT NULL,
                PRIMARY KEY (`menu_id`,`site_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'menu_to_site' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'menu_to_site': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the menu_to_site table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `menu_to_site`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'menu_to_site' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'menu_to_site': " . $e->getMessage() . "\n";
        }
    }
} 