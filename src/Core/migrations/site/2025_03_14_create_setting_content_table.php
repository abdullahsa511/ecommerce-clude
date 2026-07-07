<?php

declare(strict_types=1);

use Dotenv\Dotenv;

class CreateSettingContentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the setting_content table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS setting_content (
                site_id tinyint(6) UNSIGNED NOT NULL DEFAULT 0,
                language_id INT unsigned NOT NULL DEFAULT '0',
                namespace varchar(128) NOT NULL,
                `key` varchar(128) NOT NULL,
                value text NOT NULL,
                PRIMARY KEY (site_id, language_id, namespace, `key`),
                KEY `site_id` (`site_id`),
                KEY `language_id` (`language_id`),
                KEY `namespace` (`namespace`),
                KEY `key` (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'setting_content' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'setting_content': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the setting_content table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS setting_content;";

        try {
            $this->pdo->exec($query);
            echo "Table 'setting_content' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'setting_content': " . $e->getMessage() . "\n";
        }
    }
}

