<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=your_database", "root", "");
    echo "Connection successful!";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>