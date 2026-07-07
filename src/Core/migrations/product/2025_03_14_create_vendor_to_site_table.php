<?php

declare(strict_types=1);



class CreateVendorToSiteTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the vendor_to_site table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS vendor_to_site (
                vendor_id INT UNSIGNED NOT NULL,
                site_id tinyint(6) UNSIGNED NOT NULL DEFAULT '0',
                PRIMARY KEY (vendor_id,site_id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'vendor_to_site' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'vendor_to_site': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the vendor_to_site table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS vendor_to_site;";

        try {
            $this->pdo->exec($query);
            echo "Table 'vendor_to_site' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'vendor_to_site': " . $e->getMessage() . "\n";
        }
    }
}

