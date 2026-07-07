<?php

declare(strict_types=1);



class CreateProductRelatedProjectTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_related_project table.
     */
    public function up(): void
    {
        $query = "CREATE TABLE IF NOT EXISTS product_related_project (
                project_id INT UNSIGNED NOT NULL,
                product_id INT UNSIGNED NOT NULL,
                sort_order int(3) NOT NULL DEFAULT '0',
                PRIMARY KEY (project_id,product_id),
                created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                KEY project_id (project_id),
                KEY product_id (product_id),
                UNIQUE KEY uk_project_id_product_id (project_id,product_id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;";
        try {
            $this->pdo->exec($query);
            echo "Table 'product_related_project' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_related_project': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_related_project table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_related_project;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_related_project' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_related_project': " . $e->getMessage() . "\n";
        }
    }
}

