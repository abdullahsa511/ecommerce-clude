<?php

declare(strict_types=1);

class CreateCustomerSegmentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the digital_asset_log table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `customer_segment` (
                `customer_segment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `customer_segment_name` varchar(255) NOT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`customer_segment_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'customer_segment' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'customer_segment': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the digital_asset_log table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `customer_segment`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'customer_segment' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'customer_segment': " . $e->getMessage() . "\n";
        }
    }
} 