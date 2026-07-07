<?php

declare(strict_types=1);

class CreateMenuItemMetaTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the menu_item_meta table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `menu_item_meta` (
                `menu_item_meta_id` INT unsigned NOT NULL AUTO_INCREMENT,
                `menu_item_id` INT unsigned NOT NULL DEFAULT '0',
                `key` varchar(191) DEFAULT NULL,
                `value` longtext,
                PRIMARY KEY (`menu_item_meta_id`),
                KEY `menu_item_id` (`menu_item_id`),
                KEY `key` (`key`(191))
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'menu_item_meta' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'menu_item_meta': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the menu_item_meta table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `menu_item_meta`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'menu_item_meta' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'menu_item_meta': " . $e->getMessage() . "\n";
        }
    }
} 