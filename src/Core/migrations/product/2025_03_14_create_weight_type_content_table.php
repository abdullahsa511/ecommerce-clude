<?php

declare(strict_types=1);



class CreateWeightTypeContentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the weight_type_content table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS weight_type_content (
                weight_type_id INT UNSIGNED NOT NULL,
                language_id INT UNSIGNED NOT NULL,
                name varchar(32) NOT NULL,
                unit varchar(4) NOT NULL,
                PRIMARY KEY (weight_type_id,language_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'weight_type_content' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'weight_type_content': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the weight_type_content table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS weight_type_content;";

        try {
            $this->pdo->exec($query);
            echo "Table 'weight_type_content' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'weight_type_content': " . $e->getMessage() . "\n";
        }
    }
}