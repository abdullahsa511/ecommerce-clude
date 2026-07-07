<?php

declare(strict_types=1);

class CreateTimezoneTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the timezone table.
     */
    public function up(): void
    {
        $query = "CREATE TABLE IF NOT EXISTS `timezone` (
            `timezone_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `country_code` char(3) NOT NULL,
            `timezone` varchar(125) NOT NULL DEFAULT '',
            `gmt_offset` float(10,2) DEFAULT NULL,
            `dst_offset` float(10,2) DEFAULT NULL,
            `raw_offset` float(10,2) DEFAULT NULL,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at` datetime DEFAULT NULL,
            PRIMARY KEY (`timezone_id`),
            UNIQUE KEY `country_code_timezone` (`country_code`,`timezone`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;";

        try {
            $this->pdo->exec($query);
            echo "Table 'timezone' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'timezone': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the timezone table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `timezone`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'timezone' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'timezone': " . $e->getMessage() . "\n";
        }
    }
} 