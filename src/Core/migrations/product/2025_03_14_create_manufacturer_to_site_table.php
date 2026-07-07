<?php

declare(strict_types=1);


class CreateManufacturerToSiteTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the manufacturer_to_site table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS manufacturer_to_site (
                manufacturer_id INT UNSIGNED NOT NULL,
                site_id tinyint(6) UNSIGNED NOT NULL DEFAULT '0',
                PRIMARY KEY (manufacturer_id,site_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'manufacturer_to_site' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'manufacturer_to_site': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the manufacturer_to_site table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS manufacturer_to_site;";

        try {
            $this->pdo->exec($query);
            echo "Table 'manufacturer_to_site' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'manufacturer_to_site': " . $e->getMessage() . "\n";
        }
    }
}

