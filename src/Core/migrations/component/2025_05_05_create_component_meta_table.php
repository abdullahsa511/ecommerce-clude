<?php

declare(strict_types=1);



class CreateComponentMetaTable
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
            CREATE TABLE IF NOT EXISTS component_meta (
                component_meta_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                component_id INT UNSIGNED NOT NULL,
                property varchar(191) NOT NULL,
                value varchar(191) NOT NULL,

                PRIMARY KEY (component_meta_id),

                CONSTRAINT `fk_component_meta_component` FOREIGN KEY (`component_id`) REFERENCES `component` (`component_id`) ON DELETE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'component_meta' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'component_meta': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the site table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS component_meta;";

        try {
            $this->pdo->exec($query);
            echo "Table 'component_meta' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'component_meta': " . $e->getMessage() . "\n";
        }
    }
}
