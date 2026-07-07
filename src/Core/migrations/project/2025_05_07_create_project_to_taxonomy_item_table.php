<?php

declare(strict_types=1);



class CreateProjectToTaxonomyItemTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_to_taxonomy_item table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS project_to_taxonomy_item (
                project_id INT UNSIGNED NOT NULL,
                taxonomy_item_id INT UNSIGNED NOT NULL,
                PRIMARY KEY (project_id,taxonomy_item_id),
                KEY project_id (project_id),
                KEY taxonomy_item_id (taxonomy_item_id),
                CONSTRAINT `fk_project_to_taxonomy_item_project` FOREIGN KEY (`project_id`) 
                    REFERENCES `project`(`project_id`) ON DELETE CASCADE,
                CONSTRAINT `fk_project_to_taxonomy_item_taxonomy_item` FOREIGN KEY (`taxonomy_item_id`) 
                    REFERENCES `taxonomy_item`(`taxonomy_item_id`) ON DELETE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'project_to_taxonomy_item' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'project_to_taxonomy_item': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_to_taxonomy_item table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS project_to_taxonomy_item;";

        try {
            $this->pdo->exec($query);
            echo "Table 'project_to_taxonomy_item' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'project_to_taxonomy_item': " . $e->getMessage() . "\n";
        }
    }
}

