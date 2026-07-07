<?php

declare(strict_types=1);

class CreateCountryTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the country table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `country` (
                `country_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` varchar(128) NOT NULL,
                `iso_code_2` varchar(2) NOT NULL,
                `iso_code_3` varchar(3) NOT NULL,
                `status` tinyint NOT NULL DEFAULT 0,
                PRIMARY KEY (`country_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'country' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'country': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the country table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `country`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'country' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'country': " . $e->getMessage() . "\n";
        }
    }
} 