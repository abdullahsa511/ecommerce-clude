<?php

declare(strict_types=1);

class CreateItemOptionTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_option table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `item_option` (
                `item_option_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `item_id` BIGINT UNSIGNED NULL,
                `product_id` INT UNSIGNED NOT NULL,
                `product_variant_id` INT UNSIGNED NOT NULL,
                `product_option_group_id` INT UNSIGNED NOT NULL,
                `product_option_id` INT UNSIGNED NOT NULL,
                `type_id` INT UNSIGNED NULL,
                `option_name` varchar(255) NOT NULL,
                `option_description` text NULL DEFAULT NULL,
                `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
                `sort_order` int(3) NOT NULL DEFAULT 0,
                `active_status` tinyint(1) NOT NULL DEFAULT 1,
                `value` json NULL DEFAULT NULL,
                `meta_description` text NULL DEFAULT NULL,
                `required` tinyint NOT NULL DEFAULT 0,
                `hex_color` varchar(50) DEFAULT NULL,
                `option_image` json DEFAULT NULL,
                PRIMARY KEY (`item_option_id`),
                UNIQUE KEY `uq_item_id_product_variant_id_product_option_group_id_name` (`product_id`,`item_id`, `product_variant_id`, `product_option_group_id`, `option_name`),
                KEY `product_id` (`product_id`),
                KEY `product_option_id` (`product_option_id`),
                KEY `type_id` (`type_id`),
                CONSTRAINT `fk_product_option_type` FOREIGN KEY (`type_id`) REFERENCES `type` (`type_id`) ON DELETE CASCADE,
                FOREIGN KEY (`product_variant_id`) REFERENCES `product_variant` (`product_variant_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (`product_option_group_id`) REFERENCES `product_option_group` (`product_option_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (`product_option_id`) REFERENCES `product_option` (`product_option_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        

        try {
            $this->pdo->exec($query);
            echo "Table 'item_option' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'item_option': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_option table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `item_option`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'item_option' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'item_option': " . $e->getMessage() . "\n";
        }
    }
} 