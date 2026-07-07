<?php

declare(strict_types=1);



class CreateComponentItemTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the site table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS component_item (
                component_item_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                property_name varchar(191) NOT NULL,
                component_id INT UNSIGNED NOT NULL,
                model varchar(191) NULL,
                model_id INT UNSIGNED NULL,
                item_count INT UNSIGNED NOT NULL,
                is_recent tinyint NOT NULL DEFAULT 1,
                is_featured tinyint NOT NULL DEFAULT 1,
                fields JSON,
                related_models JSON,
                description text NULL,
                title varchar(191) NULL,
                subtitle varchar(191) NULL,
                link_text varchar(191) NULL,
                PRIMARY KEY (component_item_id),
                CONSTRAINT `fk_component_item_component` FOREIGN KEY (`component_id`) REFERENCES `component` (`component_id`) ON DELETE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'component_item' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'component_item': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the site table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS component_item;";

        try {
            $this->pdo->exec($query);
            echo "Table 'component_item' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'component_item': " . $e->getMessage() . "\n";
        }
    }
}
