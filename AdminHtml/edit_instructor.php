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

// Fetch admin


$admin_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);



// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone_number']);

    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone_number = ? WHERE id = ?");
    $stmt->execute([$name, $email, $phone, $admin_id]);

    header('Location: instructor.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Admin</title>
  <link rel="stylesheet" href="../AdminHtml/Css/admin.css">
</head>
<body>

<div class="form-container">
    <h2>Edit Admin</h2>
    <form method="POST">
        <div class="form-row">
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required>
        </div>
        <div class="form-row">
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
        </div>
        <div class="form-row">
            <label>Phone Number:</label>
            <input type="text" name="phone_number" value="<?= htmlspecialchars($admin['phone_number']) ?>" required>
        </div>
        <button type="submit" class="add-student-btn">Save Changes</button>
    </form>
</div>

</body>
</html>
<style>
:root {
    --primary: #ee6c4d;
    --primary-dark: #3d5a80;
    --secondary: #98c1d9;
    --surface: #ffffff;
    --text: #0f172a;
    --text-light: #64748b;
    --border: #e2e8f0;
    --shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
    --background: #f8fafc;
}

body {
    background: var(--background);
    font-family: 'Righteous', sans-serif;
    margin: 0;
    padding: 0;
    color: var(--text);
}

.form-container {
    width: 100%;
    max-width: 500px;
    background: var(--surface);
    padding: 2rem;
    margin: 4rem auto;
    border-radius: 1rem;
    box-shadow: var(--shadow);
}

.form-container h2 {
    text-align: center;
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary-dark);
    margin-bottom: 2rem;
}

form {
    width: 100%;
}

.form-row {
    margin-bottom: 1.5rem;
}

.form-row label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-light);
    font-size: 0.95rem;
    font-weight: 600;
}

.form-row input {
    width: 100%;
    padding: 0.9rem 1.2rem;
    border: 1px solid var(--border);
    border-radius: 0.75rem;
    background: var(--background);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-row input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(238, 108, 77, 0.2);
    background: white;
    outline: none;
}

.add-student-btn {
    width: 100%;
    background: var(--primary);
    color: white;
    padding: 1rem;
    font-size: 1.1rem;
    font-weight: bold;
    border: none;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: background 0.3s ease;
}

.add-student-btn:hover {
    background: var(--primary-dark);
}

@media (max-width: 600px) {
    .form-container {
        margin: 2rem 1rem;
        padding: 1.5rem;
    }
}
</style>
