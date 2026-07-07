<?php

declare(strict_types=1);

class CreateTaxTypeTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the tax_type table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `tax_type` (
                `tax_type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` varchar(32) NOT NULL,
                `content` varchar(191) NOT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`tax_type_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'tax_type' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'tax_type': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the tax_type table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `tax_type`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'tax_type' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'tax_type': " . $e->getMessage() . "\n";
        }
    }
} 