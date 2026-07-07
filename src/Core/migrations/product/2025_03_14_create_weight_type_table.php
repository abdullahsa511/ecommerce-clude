<?php

declare(strict_types=1);



class CreateWeightTypeTable
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
            CREATE TABLE IF NOT EXISTS weight_type (
                weight_type_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                code varchar(191) NULL DEFAULT NULL,
                weight_type varchar(191) Nullable DEFAULT NULL,
                value decimal(15,8) NOT NULL DEFAULT '0.00000000',
                deleted_at TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (weight_type_id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'weight_type' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'weight_type': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the weight_type table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS weight_type;";

        try {
            $this->pdo->exec($query);
            echo "Table 'weight_type' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'weight_type': " . $e->getMessage() . "\n";
        }
    }
}
