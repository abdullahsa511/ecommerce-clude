<?php

declare(strict_types=1);

class CreateTaxRuleTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the tax_rule table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `tax_rule` (
                `tax_rule_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `tax_type_id` INT UNSIGNED NOT NULL,
                `tax_rate_id` INT UNSIGNED NOT NULL,
                `based` varchar(10) NOT NULL,
                `priority` int NOT NULL DEFAULT 1,
                PRIMARY KEY (`tax_rule_id`),
                KEY `tax_rule` (`tax_type_id`, `tax_rate_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'tax_rule' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'tax_rule': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the tax_rule table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `tax_rule`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'tax_rule' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'tax_rule': " . $e->getMessage() . "\n";
        }
    }
} 