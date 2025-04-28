<?php
session_start();
include 'config.php'; // your DB connection

$userId = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId) {
    $fullName = trim($_POST['fullName']);
    $phone = trim($_POST['phone']);
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['password']; // New password (optional)

    // Fetch current hashed password
    $stmt = $conn->prepare("SELECT password, image FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user || !password_verify($currentPassword, $user['password'])) {
        echo "Incorrect current password.";
        exit;
    }

    // Handle image upload if a new one was uploaded
    $newImage = $user['image']; // default keep old image
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['avatar']['tmp_name'];
        $imageName = uniqid() . "_" . basename($_FILES['avatar']['name']);
        $destination = "uploads/" . $imageName;
        move_uploaded_file($imageTmpPath, $destination);
        $newImage = $destination;
    }

    // Prepare the update
    if (!empty($newPassword)) {
        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name = ?, phone_number = ?, image = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $fullName, $phone, $newImage, $newHashedPassword, $userId);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, phone_number = ?, image = ? WHERE id = ?");
        $stmt->bind_param("sssi", $fullName, $phone, $newImage, $userId);
    }

    if ($stmt->execute()) {
       header("Location: account_details.php"); // Redirect to profile page with success message
        exit;
    } else {
        echo "Error updating profile.";
    }
}
?>
