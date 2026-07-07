<?php

declare(strict_types=1);

class CreateProductImageTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_image table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS product_image (
                product_image_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                product_id INT UNSIGNED NOT NULL,
                media_id INT UNSIGNED NULL,
                image JSON,
                image_link VARCHAR(255) NOT NULL,
                sort_order int(3) NOT NULL DEFAULT '0',
                status json NOT NULL,
                way_points json NULL,
                type VARCHAR(191) NULL DEFAULT NULL,
                PRIMARY KEY (product_image_id),
                KEY product_id (product_id),
                KEY media_id (media_id),
                CONSTRAINT `fk_product_image_media_id` FOREIGN KEY (`media_id`) 
                    REFERENCES `media`(`media_id`) ON DELETE CASCADE,
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_image' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_image': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_image table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_image;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_image' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_image': " . $e->getMessage() . "\n";
        }
    }
}