<?php

declare(strict_types=1);



class CreateSiteTable
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
            CREATE TABLE IF NOT EXISTS site (
                site_id tinyint NOT NULL AUTO_INCREMENT,
                `key` varchar(191) NOT NULL,
                name varchar(191) NOT NULL,
                host varchar(191) NOT NULL,
                theme varchar(191) NOT NULL,
                template varchar(191) NOT NULL DEFAULT '',
                description JSON,
                local_settings JSON,
                media_settings JSON,
                comments_settings JSON,
                orders_settings JSON,
                social_settings JSON,
                settings JSON,
                admin_email varchar(100) NULL,
                contact_email varchar(100) NULL,
                site_settings JSON NULL,
                seo_settings JSON NULL,
                other_settings JSON NULL,
                PRIMARY KEY (site_id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'site' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'site': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the site table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS site;";

        try {
            $this->pdo->exec($query);
            echo "Table 'site' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'site': " . $e->getMessage() . "\n";
        }
    }
}
