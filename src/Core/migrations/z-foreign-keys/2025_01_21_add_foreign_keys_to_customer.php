<?php

declare(strict_types=1);

class AddForeignKeysToOption
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
            // Customer foreign keys
            "ALTER TABLE `customer` 
             ADD CONSTRAINT `fk_customer_role` 
             FOREIGN KEY (`role_id`) REFERENCES `customer_role` (`role_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE
             ADD CONSTRAINT `fk_customer_user` 
             FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE",

            // Admin Failed Login foreign keys
            "ALTER TABLE `customer_failed_login` 
             ADD CONSTRAINT `fk_customer_failed_login_customer` 
             FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Admin Password Resets foreign keys
            "ALTER TABLE `customer_password_resets` 
             ADD CONSTRAINT `fk_customer_password_resets_customer` 
             FOREIGN KEY (`email`) REFERENCES `customer` (`email`) 
             ON DELETE CASCADE ON UPDATE CASCADE",
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