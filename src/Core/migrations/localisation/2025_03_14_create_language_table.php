<?php

declare(strict_types=1);

class CreateLanguageTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the language table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `language` (
                `language_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` varchar(64) NOT NULL,
                `code` varchar(12) NOT NULL,
                `locale` varchar(20) NOT NULL,
                `rtl` tinyint NOT NULL DEFAULT 0,
                `sort_order` int(3) NOT NULL DEFAULT 0,
                `status` tinyint NOT NULL DEFAULT 0,
                `default` tinyint NOT NULL DEFAULT 0,
                PRIMARY KEY (`language_id`),
                KEY `name` (`name`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'language' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'language': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the language table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `language`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'language' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'language': " . $e->getMessage() . "\n";
        }
    }
} 