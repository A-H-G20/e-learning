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
    die("Database connection failed: " . $e->getMessage());
}

// Check if instructor logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch users where role = user, deaf, blind
$fetch_students_stmt = $pdo->prepare("
    SELECT id AS user_id, name, email, role, phone_number
    FROM users
    WHERE role IN ('user', 'deaf', 'blind')
    ORDER BY name ASC
");
$fetch_students_stmt->execute();
$students = $fetch_students_stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Students</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../AdminHtml/Css/admin.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Righteous&family=Secular+One&display=swap');
  </style>
</head>

<body>

<button class="mobile-menu-toggle">
    <i class="fas fa-bars"></i>
</button>

<div class="dashboard-container">
<nav class="sidebar">
    <div class="sidebar-header">
        <div class="logo">E-Learn</div>
    </div>
    <ul class="nav-menu">
    <li class="nav-item">
    <a href="index.php" class="nav-link">
        <i class="fas fa-home"></i> Dashboard
    </a>
</li>

<li class="nav-item">
    <a href="course.php" class="nav-link">
        <i class="fas fa-book-open"></i> Courses
    </a>
</li>

<li class="nav-item">
    <a href="admin_User.php" class="nav-link active">
        <i class="fas fa-user-graduate"></i> Students
    </a>
</li>

<li class="nav-item">
    <a href="admin.php" class="nav-link">
        <i class="fas fa-user-shield"></i> Admins
    </a>
</li>

<li class="nav-item">
    <a href="instructor.php" class="nav-link ">
        <i class="fas fa-chalkboard-teacher"></i> Instructors
    </a>
</li>

<li class="nav-item">
    <a href="enrollment.php" class="nav-link">
        <i class="fas fa-book-reader"></i> Course Enrollment
    </a>
</li>


<li class="nav-item">
    <a href="admin_settings.php" class="nav-link">
        <i class="fas fa-cogs"></i> Settings
    </a>
</li>

<li class="nav-item">
    <a href="../logout.php" class="nav-link">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</li>

    </ul>
</nav>

<main class="main-content">
<div class="student-management">
    <div class="management-header">
        <h2>Student Management</h2>
    </div>

    <table class="student-table" id="studentsTable">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
         
            <th>Phone Number</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php
    if ($students) {
        foreach ($students as $student) {
            echo "<tr>";
            echo "<td data-label='Name'>" . htmlspecialchars($student['name']) . "</td>";
            echo "<td data-label='Email'>" . htmlspecialchars($student['email']) . "</td>";
            echo "<td data-label='Role'>" . htmlspecialchars(ucfirst($student['role'])) . "</td>";
            
            echo "<td data-label='Phone'>" . htmlspecialchars($student['phone_number']) . "</td>";
            echo "<td data-label='Actions'>
                    <div class='student-actions'>
                        <button class='action-btn delete-btn' onclick='deleteStudent(" . htmlspecialchars($student['user_id']) . ")'>
                            <i class='fas fa-trash'></i>
                        </button>
                    </div>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No users found.</td></tr>";
    }
    ?>
    </tbody>
    </table>
</div>

</main>
</div>

<script>
function deleteStudent(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch('delete_student.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'user_id=' + encodeURIComponent(userId)
        })
        .then(response => response.text())
        .then(data => {
            alert(data.trim());
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
    }
}
</script>

</body>
</html>
