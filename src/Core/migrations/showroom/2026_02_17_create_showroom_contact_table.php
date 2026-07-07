<?php

declare(strict_types=1);


class CreateShowroomContactTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the showroom_contact table.
     */
    public function up(): void
    {
        $query = "
           CREATE TABLE `showroom_contact` (
            `showroom_contact_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
            `showroom_id` int UNSIGNED NOT NULL,
            `name` varchar(191) DEFAULT NULL,
            `image` json DEFAULT NULL,
            `email` varchar(191) DEFAULT NULL,
            `phone` varchar(191) DEFAULT NULL,
            `designation` varchar(191) DEFAULT NULL,
            `message` text DEFAULT NULL,
            `sort_order` int UNSIGNED NOT NULL DEFAULT 0,
            `status` tinyint(1) NOT NULL DEFAULT 1,
            `sales_team_contact` tinyint(1) NOT NULL DEFAULT 0,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at` datetime DEFAULT NULL,
            PRIMARY KEY (`showroom_contact_id`),
            KEY `idx_showroom_id` (`showroom_id`),
            CONSTRAINT `fk_showroom_contact_showroom` FOREIGN KEY (`showroom_id`) REFERENCES `showrooms` (`showrooms_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'showroom_contact' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'showroom_contact': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the showroom_contact table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS showroom_contact;";

        try {
            $this->pdo->exec($query);
            echo "Table 'showroom_contact' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'showroom_contact': " . $e->getMessage() . "\n";
        }
    }
}
