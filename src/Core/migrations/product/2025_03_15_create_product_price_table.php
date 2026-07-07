<?php

declare(strict_types=1);



class CreateProductPriceTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the weight_type table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS product_price (
                product_price_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                product_id INT UNSIGNED NOT NULL,
                customer_segment_id INT UNSIGNED NOT NULL,
                price decimal(15,8) NOT NULL DEFAULT '0.00000000',
                PRIMARY KEY (product_price_id),
                KEY product_id (product_id),
                KEY customer_segment_id (customer_segment_id),
                CONSTRAINT `fk_product_price_product` FOREIGN KEY (`product_id`) 
                    REFERENCES `product`(`product_id`) ON DELETE CASCADE,
                CONSTRAINT `fk_product_price_customer_segment` FOREIGN KEY (`customer_segment_id`) 
                    REFERENCES `customer_segment`(`customer_segment_id`) ON DELETE CASCADE
            ) ENGINE=InnoDb AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_price' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_price': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the weight_type table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_price;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_price' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_price': " . $e->getMessage() . "\n";
        }
    }
}
