<?php

declare(strict_types=1);

class CreateVisitShowroomTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the visit_showroom table.
     */
    public function up(): void
    {
        $query = "CREATE TABLE `visit_showroom` (
                `visit_showroom_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `uuid` char(36) NOT NULL,
                `showroom_contact_id` INT UNSIGNED DEFAULT NULL,
                `pinboard_id` INT UNSIGNED DEFAULT NULL,
                `customer_id` INT UNSIGNED DEFAULT NULL,
                `showroom_id` INT UNSIGNED NOT NULL,
                `tour_type` VARCHAR(50) DEFAULT NULL,
                `date` DATE DEFAULT NULL,
                `meeting_time` VARCHAR(191) NULL DEFAULT NULL,
                `duration` VARCHAR(191) NULL DEFAULT NULL,
                `time_zone` VARCHAR(50) DEFAULT NULL,
                `enquiry_type` VARCHAR(100) DEFAULT NULL,
                `note` TEXT DEFAULT NULL,
                `label` VARCHAR(191) NULL DEFAULT NULL,
                `meeting_link` TEXT NULL DEFAULT NULL,
                `source` VARCHAR(255) NULL DEFAULT NULL,
                `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `cancelled_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`visit_showroom_id`),
                
                CONSTRAINT `fk_visit_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer`(`customer_id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `fk_visit_showroom` FOREIGN KEY (`showroom_id`) REFERENCES `showrooms`(`showrooms_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
                CONSTRAINT `fk_visit_showroom_contact` FOREIGN KEY (`showroom_contact_id`) REFERENCES `showroom_contact`(`showroom_contact_id`) ON DELETE RESTRICT ON UPDATE CASCADE,

                KEY `idx_visit_customer_id` (`customer_id`),
                KEY `idx_visit_showroom_id` (`showroom_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        try {
            $this->pdo->exec($query);
            echo "Table 'visit_showroom' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'visit_showroom': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the visit_showroom table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `visit_showroom`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'visit_showroom' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'visit_showroom': " . $e->getMessage() . "\n";
        }
    }
} 