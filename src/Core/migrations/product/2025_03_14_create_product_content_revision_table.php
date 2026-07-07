<?php

declare(strict_types=1);


class CreateProductContentRevisionTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_content_revision table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS product_content_revision (
                product_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                language_id INT UNSIGNED NOT NULL,
                content longtext,
                admin_id INT UNSIGNED NOT NULL,
                created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (product_id,language_id, created_at, admin_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_content_revision' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_content_revision': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_content_revision table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_content_revision;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_content_revision' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_content_revision': " . $e->getMessage() . "\n";
        }
    }
}