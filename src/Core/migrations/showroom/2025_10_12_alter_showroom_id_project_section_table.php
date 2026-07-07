<?php

declare(strict_types=1);

class AddShowroomIdToProjectSectionsTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to alter the project_sections table.
     */
    public function up(): void
    {
        $query = "
            ALTER TABLE `project_sections`
            ADD `showroom_id` INT UNSIGNED NULL DEFAULT NULL AFTER `project_id`;
        ";

        try {
            $this->pdo->exec($query);
            echo "Column 'showroom_id' added to 'project_sections' table successfully.\n";
        } catch (PDOException $e) {
            echo "Error adding column 'showroom_id': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by removing the showroom_id column.
     */
    public function down(): void
    {
        $query = "
            ALTER TABLE `project_sections`
            DROP COLUMN `showroom_id`;
        ";

        try {
            $this->pdo->exec($query);
            echo "Column 'showroom_id' removed from 'project_sections' table successfully.\n";
        } catch (PDOException $e) {
            echo "Error removing column 'showroom_id': " . $e->getMessage() . "\n";
        }
    }
}
