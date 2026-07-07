<?php

declare(strict_types=1);


class CreateContactTimeSlotTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the contact_time_slot table.
     */
    public function up(): void
    {
        $query = "CREATE TABLE `contact_time_slot` (
                    `contact_time_slot_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                    `showroom_contact_id` int UNSIGNED NOT NULL,
                    `slot_time` varchar(191) NOT NULL,
                    `note` text DEFAULT NULL,
                    `status` tinyint(1) NOT NULL DEFAULT 1,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`contact_time_slot_id`),
                    KEY `idx_showroom_contact_id` (`showroom_contact_id`),
                    CONSTRAINT `fk_contact_time_slot_showroom_contact` FOREIGN KEY (`showroom_contact_id`) REFERENCES `showroom_contact` (`showroom_contact_id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        try {
            $this->pdo->exec($query);
            echo "Table 'contact_time_slot' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'contact_time_slot': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the contact_time_slot table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS contact_time_slot;";

        try {
            $this->pdo->exec($query);
            echo "Table 'contact_time_slot' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'contact_time_slot': " . $e->getMessage() . "\n";
        }
    }
}
