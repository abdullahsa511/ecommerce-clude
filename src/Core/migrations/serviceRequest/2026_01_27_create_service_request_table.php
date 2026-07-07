<?php

declare(strict_types=1);

class CreateServiceRequestTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the service_request table.
     */
    public function up(): void
    {
        $query = "CREATE TABLE service_request (
                service_request_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                uuid varchar(255) not null,
                company varchar(191) null default null,
                first_name varchar(191) null default null,
                last_name varchar(191) null default null,
                request_type varchar(191) null default null,
                catalogue_format varchar(191) null default null,
                form_type varchar(191) null default null,
                pinboard_id INT UNSIGNED DEFAULT NULL,
                customer_id INT UNSIGNED DEFAULT NULL,
                email VARCHAR(255) DEFAULT NULL,
                content TEXT DEFAULT NULL,
                phone_number varchar(191) null default null,
                `state` varchar(191) null default null,
                `project_details` varchar(191) null default null,
                mailing_address varchar(191) null default null,
                source_of_enquiry varchar(191) null default null,
                comment_attachment varchar(191) DEFAULT NULL COMMENT 'comment_photo.image',
                attachments JSON DEFAULT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                INDEX idx_pinboard_id (pinboard_id),
                INDEX idx_customer_id (customer_id),
                INDEX idx_email (email),
                INDEX idx_uuid (uuid)
            ) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;";

        try {
            $this->pdo->exec($query);
            echo "Table 'service_request' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'service_request': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the service_request table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `service_request`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'service_request' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'service_request': " . $e->getMessage() . "\n";
        }
    }
} 