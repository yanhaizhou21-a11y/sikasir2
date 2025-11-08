<?php
/**
 * Quick script to check if pdo_mysql extension is loaded
 * Access this file via: http://127.0.0.1:8000/check_extensions.php
 */

header('Content-Type: text/plain');

echo "PHP Version: " . PHP_VERSION . "\n";
echo "PHP Configuration File: " . php_ini_loaded_file() . "\n\n";

echo "Extensions Status:\n";
echo "==================\n";
echo "PDO: " . (extension_loaded('pdo') ? '✓ LOADED' : '✗ NOT LOADED') . "\n";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✓ LOADED' : '✗ NOT LOADED') . "\n";
echo "MySQLi: " . (extension_loaded('mysqli') ? '✓ LOADED' : '✗ NOT LOADED') . "\n\n";

if (extension_loaded('pdo_mysql')) {
    echo "✓ SUCCESS: pdo_mysql extension is working!\n";
    try {
        $pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
        echo "✓ SUCCESS: Can create PDO MySQL connection!\n";
    } catch (Exception $e) {
        echo "⚠ WARNING: PDO MySQL connection test failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "✗ ERROR: pdo_mysql extension is NOT loaded!\n";
    echo "\nPlease:\n";
    echo "1. Make sure php.ini has: extension=pdo_mysql\n";
    echo "2. Restart your web server or 'php artisan serve' process\n";
}
?>
