<?php
require 'config.php';
echo "<h2>Database Connection Test</h2>";
try {
    $db->query("SELECT 1");
    echo "<p style='color:green'>✓ Database connected successfully</p>";
    
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Tables found: " . implode(', ', $tables) . "</p>";
} catch(PDOException $e) {
    echo "<p style='color:red'>✗ Connection failed: " . $e->getMessage() . "</p>";
}