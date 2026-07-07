<?php

declare(strict_types=1);



class CreateProjectTable
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
            CREATE TABLE IF NOT EXISTS project (
                project_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                site_id tinyint(6) UNSIGNED NOT NULL DEFAULT 1,
                status_id tinyint(6) UNSIGNED NOT NULL DEFAULT 1,
                customer_id int(11) NULL,
                name varchar(191) NULL,
                slug varchar(191) NULL,
                description TEXT NULL,
                preview_text TEXT NULL,
                banner_way_points json NULL,
                location varchar(191) NULL,
                designer varchar(191) NULL,
                photographer varchar(191) NULL,
                status varchar(191) NULL,
                image json NULL,
                image_thumb json NULL,
                meta_title varchar(191) NULL,
                meta_description TEXT NULL,
                meta_keywords varchar(500) NULL,
                title varchar(191) NULL,
                label varchar(191) NULL,
                keyline_quote varchar(191) NULL,
                link_text varchar(191) NULL,
                is_featured tinyint(1) NOT NULL DEFAULT 0,
                keyline_quote varchar(255) NULL,
                main_title varchar(191) NULL,
                main_description_one TEXT NULL,
                main_description_two TEXT NULL,
                main_description_three TEXT NULL,
                main_description_four TEXT NULL,
                main_image_one json NULL,
                main_image_two json NULL,
                created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime NULL,
                banner_way_points json NULL DEFAULT NULL,
                credit_label VARCHAR(100) DEFAULT 'Designed by',
                PRIMARY KEY (project_id),
                KEY site_id (site_id),
                KEY customer_id (customer_id),
                KEY status (status),
                KEY created_at (created_at),
                KEY updated_at (updated_at),
                KEY deleted_at (deleted_at),
                KEY status_id (status_id),
                KEY is_featured (is_featured),
                KEY preview_text (preview_text)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'project' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'project': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the site table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS project;";

        try {
            $this->pdo->exec($query);
            echo "Table 'project' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'project': " . $e->getMessage() . "\n";
        }
    }
}
