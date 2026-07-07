<?php

declare(strict_types=1);

class CreateTaxRateToUserGroupTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the tax_rate_to_user_group table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `tax_rate_to_user_group` (
                `tax_rate_id` INT UNSIGNED NOT NULL,
                `user_group_id` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`tax_rate_id`,`user_group_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'tax_rate_to_user_group' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'tax_rate_to_user_group': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the tax_rate_to_user_group table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `tax_rate_to_user_group`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'tax_rate_to_user_group' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'tax_rate_to_user_group': " . $e->getMessage() . "\n";
        }
    }
} 