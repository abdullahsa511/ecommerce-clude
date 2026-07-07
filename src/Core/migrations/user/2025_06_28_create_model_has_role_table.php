<?php

declare(strict_types=1);

class CreateModelHasRoleTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the model_has_role table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `model_has_role` (
                `model_id` INT UNSIGNED NOT NULL,
                `model_type` VARCHAR(255) NOT NULL,
                `role_id` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`model_id`, `model_type`, `role_id`),
                KEY `model_has_role_model_id_model_type_index` (`model_id`, `model_type`),
                KEY `model_has_role_role_id_index` (`role_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'model_has_role' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'model_has_role': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the model_has_role table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `model_has_role`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'model_has_role' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'model_has_role': " . $e->getMessage() . "\n";
        }
    }
} 