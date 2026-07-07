<?php

declare(strict_types=1);

class AddForeignKeysToDesignResource
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to add foreign key constraints
     */
    public function up(): void
    {        
        // Now add the foreign keys
        $this->addForeignKeys();
    }
    
    /**
     * Add foreign key constraints
     */
    private function addForeignKeys(): void
    {
        $queries = [
            // User Address foreign keys
            "ALTER TABLE `design_resource` 
             ADD CONSTRAINT `fk_design_resource_media_id` 
             FOREIGN KEY (`media_id`) REFERENCES `media` (`media_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE"
        ];

        try {
            foreach ($queries as $query) {
                $this->pdo->exec($query);
            }
            echo "Foreign key constraints added successfully.\n";
        } catch (PDOException $e) {
            echo "Error adding foreign key constraints: " . $e->getMessage() . "\n";
        }
    }
}