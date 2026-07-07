<?php

declare(strict_types=1);

class CreateFieldGroupTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the field_group table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `field_group` (
                `field_group_id` int NOT NULL AUTO_INCREMENT,
                `type` varchar(128) NOT NULL DEFAULT 'post',
                `status` tinyint NOT NULL,
                `sort_order` int NOT NULL DEFAULT 0,
                PRIMARY KEY (`field_group_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'field_group' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'field_group': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the field_group table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `field_group`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'field_group' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'field_group': " . $e->getMessage() . "\n";
        }
    }
}