<?php

declare(strict_types=1);

class CreateLengthTypeTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the length_type table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS length_type (
            `language_id` int(10) unsigned NOT NULL,
            `name` varchar(30) NOT NULL,
            `unit` varchar(4) NOT NULL,
            `deleted_at` datetime NULL DEFAULT NULL CURRENT_TIMESTAMP,
            PRIMARY KEY (`length_type_id`,`language_id`),
            UNIQUE KEY `uq_length_type_content_language_id_name` (`language_id`,`name`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'length_type' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'length_type': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the length_type table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS length_type;";

        try {
            $this->pdo->exec($query);
            echo "Table 'length_type' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'length_type': " . $e->getMessage() . "\n";
        }
    }
}