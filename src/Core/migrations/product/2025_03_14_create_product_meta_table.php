<?php

declare(strict_types=1);


class CreateProductMetaTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_meta table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS product_meta (
                product_id INT unsigned NOT NULL DEFAULT '0',
                namespace varchar(32) NOT NULL,
                `key` varchar(191) NOT NULL,
                value longtext,
                PRIMARY KEY (product_id,namespace,`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_meta' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_meta': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_meta table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_meta;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_meta' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_meta': " . $e->getMessage() . "\n";
        }
    }
}