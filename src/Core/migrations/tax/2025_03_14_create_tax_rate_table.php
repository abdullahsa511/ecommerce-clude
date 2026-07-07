<?php

declare(strict_types=1);

class CreateTaxRateTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the tax_rate table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `tax_rate` (
                `tax_rate_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `region_group_id` INT UNSIGNED NOT NULL DEFAULT '0',
                `name` varchar(32) NOT NULL,
                `rate` decimal(15,4) NOT NULL DEFAULT '0.0000',
                `type` char(1) NOT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`tax_rate_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'tax_rate' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'tax_rate': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the tax_rate table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `tax_rate`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'tax_rate' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'tax_rate': " . $e->getMessage() . "\n";
        }
    }
} 