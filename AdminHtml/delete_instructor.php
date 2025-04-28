<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'e-learning';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_id'])) {
    $admin_id = intval($_POST['admin_id']);

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'instructor'");
    $stmt->execute([$admin_id]);

    echo "Instructor deleted successfully.";
} else {
    echo "Invalid request.";
}
?>
