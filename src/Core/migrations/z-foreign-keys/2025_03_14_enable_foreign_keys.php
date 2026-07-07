<?php

declare(strict_types=1);

class EnableForeignKeys
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function up(): void
    {
        // Enable foreign key checks
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS=1");
        echo "Foreign key checks enabled.\n";
    }

    public function down(): void
    {
        // Disable foreign key checks
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        echo "Foreign key checks disabled.\n";
    }
} 