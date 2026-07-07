<?php

require_once __DIR__ . '/../../../autoload.php';

try {
    $pdo = new PDO(
        sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $_ENV['DB_HOST'],
            $_ENV['DB_NAME'],
            $_ENV['DB_CHARSET']
        ),
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    $pdo->query('SELECT 1');
    echo "Database connection successful.\n";
    exit(0);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}
