<?php

declare(strict_types=1);

class AddForeignKeysToPinboardItem
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
            "ALTER TABLE `pinboard_items` 
             ADD CONSTRAINT `fk_pinboard_job_id` 
             FOREIGN KEY (`job_id`) REFERENCES `job` (`job_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_pinboard_product_id` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_pinboard_project_id` 
             FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_pinboard_media_id` 
             FOREIGN KEY (`media_id`) REFERENCES `media` (`media_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_pinboard_comment_id` 
             FOREIGN KEY (`comment_id`) REFERENCES `comment` (`comment_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE"
        ];

        try {
            foreach ($queries as $query) {
                $this->pdo->exec($query);
            }
            echo "Foreign key constraints dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping foreign key constraints: " . $e->getMessage() . "\n";
        }
    }
}