<?php

declare(strict_types=1);


class CreateProductContentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_content table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS product_content (
                product_id INT UNSIGNED NOT NULL,
                language_id INT UNSIGNED NOT NULL,
                title varchar(191) NOT NULL DEFAULT '',
                name varchar(191) NOT NULL DEFAULT '',
                slug varchar(191) NOT NULL DEFAULT '',
                tag_line varchar(500) NOT NULL DEFAULT '',
                content text,
                tag text,
                meta_title varchar(191) NOT NULL DEFAULT '',
                meta_description varchar(191) NOT NULL DEFAULT '',
                meta_keywords varchar(191) NOT NULL DEFAULT '',
                rules TEXT DEFAULT NULL,
                icon varchar(191) NOT NULL DEFAULT '',
                PRIMARY KEY (product_id,language_id),
                KEY slug (slug),
                KEY title (title),
                KEY tag_line (tag_line),
                FULLTEXT search (name,content,title,tag_line)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_content' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_content': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_content table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_content;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_content' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_content': " . $e->getMessage() . "\n";
        }
    }
}