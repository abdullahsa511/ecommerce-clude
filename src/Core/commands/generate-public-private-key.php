<?php

declare(strict_types=1);

require_once __DIR__ . '/../autoload.php';

function generateKeys(string $directory): void
{
    // Ensure the directory exists
//    if (!is_dir($directory)) {
//        mkdir($directory, 0755, true);
//    }
    $directory = ROOT_DIR . DIRECTORY_SEPARATOR;
    // Paths for the private and public keys
    $privateKeyPath = $directory . 'private.key';
    $publicKeyPath = $directory . 'public.key';

    // Generate the private key
    $generatePrivateKeyCommand = "openssl genrsa -out {$privateKeyPath} 2048";
    exec($generatePrivateKeyCommand, $outputPrivate, $returnPrivate);
    if ($returnPrivate !== 0) {
        throw new RuntimeException('Failed to generate private key: ' . implode("\n", $outputPrivate));
    }

    // Generate the public key from the private key
    $generatePublicKeyCommand = "openssl rsa -in {$privateKeyPath} -pubout -out {$publicKeyPath}";
    exec($generatePublicKeyCommand, $outputPublic, $returnPublic);
    if ($returnPublic !== 0) {
        throw new RuntimeException('Failed to generate public key: ' . implode("\n", $outputPublic));
    }

    echo "Keys generated successfully:\n";
    echo "Private Key: {$privateKeyPath}\n";
    echo "Public Key: {$publicKeyPath}\n";
}

// Example usage
try {
    generateKeys(__DIR__ . '/keys'); // Adjust the directory path as needed
} catch (RuntimeException $e) {
    echo 'Error: ' . $e->getMessage();
}
