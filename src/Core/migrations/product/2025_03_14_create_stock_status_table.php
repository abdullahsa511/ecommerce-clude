<?php

declare(strict_types=1);



class CreateStockStatusTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the stock_status table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS stock_status (
                stock_status_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                language_id INT UNSIGNED NOT NULL,
                name varchar(32) NOT NULL,
                PRIMARY KEY (stock_status_id,language_id),
                KEY `stock_status_id` (`stock_status_id`),
                KEY `language_id` (`language_id`),
                UNIQUE KEY `stock_status_name_language_id` (`name`, `language_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'stock_status' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'stock_status': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the stock_status table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS stock_status;";

        try {
            $this->pdo->exec($query);
            echo "Table 'stock_status' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'stock_status': " . $e->getMessage() . "\n";
        }
    }
}

