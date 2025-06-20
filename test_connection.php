<?php
require 'config.php';  // Includes the connection

try {
    $stmt = $pdo->query("SELECT DATABASE()");
    echo "Success! Connected to: " . $stmt->fetchColumn();
} catch(PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>