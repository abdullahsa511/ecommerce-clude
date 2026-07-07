<?php

declare(strict_types=1);



class CreateComponentTable
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
            CREATE TABLE IF NOT EXISTS component (
                component_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                name varchar(191) NOT NULL,
                model varchar(191) NULL,
                section_title varchar(191) NULL,
                section_subtitle varchar(500) NULL,
                section_link varchar(191) NULL,
                title varchar(191) NULL,
                subtitle varchar(191) NULL,
                description varchar(500) NULL,
                image JSON,
                mobile_banner JSON,
                images JSON,
                links JSON,
                buttons JSON,
                template varchar(191) NULL,
                active BOOLEAN NOT NULL DEFAULT TRUE,
                banner_way_points JSON NULL DEFAULT NULL,
                UNIQUE KEY `name` (`name`),
                UNIQUE KEY `component_id_model` (`component_id`, `model`),
                PRIMARY KEY (component_id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'component' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'component': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the site table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS component;";

        try {
            $this->pdo->exec($query);
            echo "Table 'component' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'component': " . $e->getMessage() . "\n";
        }
    }
}
