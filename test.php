<?php
require 'config.php';
try {
    $stmt = $pdo->query("SELECT * FROM users");
    echo "Database connection successful! Found " . $stmt->rowCount() . " users.";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>