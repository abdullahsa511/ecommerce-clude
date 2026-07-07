<?php

declare(strict_types=1);



class CreateLengthTypeContentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the length_type_content table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS length_type_content (
                length_type_id INT UNSIGNED NOT NULL,
                language_id INT UNSIGNED NOT NULL,
                name varchar(32) NOT NULL,
                unit varchar(4) NOT NULL,
                PRIMARY KEY (length_type_id,language_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'length_type_content' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'length_type_content': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the length_type_content table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS length_type_content;";

        try {
            $this->pdo->exec($query);
            echo "Table 'length_type_content' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'length_type_content': " . $e->getMessage() . "\n";
        }
    }
}