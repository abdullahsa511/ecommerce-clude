<?php

declare(strict_types=1);



class CreateDesignResourceDocumentTable
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
            CREATE TABLE IF NOT EXISTS design_resource_document (
                design_resource_document_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                design_resource_id INT UNSIGNED NOT NULL,
                media_id INT UNSIGNED NULL,
                name varchar(191) NULL,
                url varchar(500) NULL,
                description varchar(500) NULL,
                format varchar(191) NULL,
                `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (design_resource_document_id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'design_resource_document' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'design_resource_document': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the site table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS design_resource;";

        try {
            $this->pdo->exec($query);
            echo "Table 'design_resource' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'design_resource': " . $e->getMessage() . "\n";
        }
    }
}
