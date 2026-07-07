<?php

declare(strict_types=1);



class CreateProductToDigitalAssetTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_to_digital_asset table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS product_to_digital_asset (
                product_id INT UNSIGNED NOT NULL,
                digital_asset_id INT UNSIGNED NOT NULL,
                PRIMARY KEY (product_id,digital_asset_id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_to_digital_asset' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_to_digital_asset': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_to_digital_asset table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_to_digital_asset;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_to_digital_asset' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_to_digital_asset': " . $e->getMessage() . "\n";
        }
    }
}

