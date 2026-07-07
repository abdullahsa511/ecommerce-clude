<?php

declare(strict_types=1);



class CreateDesignResourceTable
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
            CREATE TABLE IF NOT EXISTS design_resource (
                design_resource_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                media_id INT UNSIGNED NULL,
                hex_value varchar(20) null,
                img JSON NULL,
                img2 JSON NULL,
                title varchar(191) NULL,
                resource_type varchar(191) NULL,
                brand varchar(255) NULL,
                description varchar(500) NULL,
                type varchar(191) NULL,
                is_featured TINYINT(1) NULL DEFAULT 0,
                link_text varchar(191) NULL,
                grade varchar(191) NULL,
                slug varchar(191) NULL,
                sort_order INT NULL DEFAULT 0,
                created_at datetime NULL,
                updated_at datetime NULL,
                deleted_at datetime NULL,
                UNIQUE KEY uniq_title_resource_type (title, resource_type, brand),
                INDEX idx_sort_order (sort_order),
                PRIMARY KEY (design_resource_id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'design_resource' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'design_resource': " . $e->getMessage() . "\n";
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
