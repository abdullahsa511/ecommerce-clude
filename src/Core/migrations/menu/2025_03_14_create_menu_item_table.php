<?php

declare(strict_types=1);

class CreateMenuItemTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the menu_item table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `menu_item` (
                `menu_item_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `menu_id` INT UNSIGNED NOT NULL,
                `type` varchar(191) NOT NULL DEFAULT 'link',
                `url` varchar(191) NOT NULL DEFAULT '',
                `parent_id` INT UNSIGNED NOT NULL DEFAULT '0',
                `item_id` INT UNSIGNED DEFAULT NULL,
                `options` varchar(191) NOT NULL DEFAULT '',
                `sort_order` int(3) NOT NULL DEFAULT 0,
                `status` tinyint NOT NULL DEFAULT 0,
                PRIMARY KEY (`menu_item_id`),
                KEY `parent_id` (`parent_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'menu_item' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'menu_item': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the menu_item table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `menu_item`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'menu_item' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'menu_item': " . $e->getMessage() . "\n";
        }
    }
} 