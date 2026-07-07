<?php

// Include the main autoload file which has all necessary definitions
require_once dirname(__DIR__) . '/autoload.php';

// Include utility functions
require_once dirname(__DIR__) . '/src/Core/System/utils/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default test database configuration if not set
if (!isset($_ENV['DB_HOST'])) $_ENV['DB_HOST'] = 'localhost';
if (!isset($_ENV['DB_NAME'])) $_ENV['DB_NAME'] = 'test_db';
if (!isset($_ENV['DB_USER'])) $_ENV['DB_USER'] = 'root';
if (!isset($_ENV['DB_PASSWORD'])) $_ENV['DB_PASSWORD'] = '';
if (!isset($_ENV['DB_CHARSET'])) $_ENV['DB_CHARSET'] = 'utf8mb4';

// Symfony Mailer: discard messages in tests unless overridden
if (!isset($_ENV['MAILER_DSN'])) {
    $_ENV['MAILER_DSN'] = 'null://null';
}