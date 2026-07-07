<?php

declare(strict_types=1);


class CreateProductQuestionTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_question table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS product_question (
                product_question_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                product_id INT UNSIGNED NOT NULL,
                user_id INT UNSIGNED NOT NULL DEFAULT '0',
                author varchar(64) NOT NULL,
                content text NOT NULL,
                status tinyint UNSIGNED NOT NULL DEFAULT '0',
                parent_id INT UNSIGNED NOT NULL DEFAULT '0',
                created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (product_question_id),
                KEY product_id (product_id, status),
                KEY user_id (user_id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_question' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_question': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_question table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_question;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_question' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_question': " . $e->getMessage() . "\n";
        }
    }
}