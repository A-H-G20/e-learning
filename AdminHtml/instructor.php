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

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch instructors
$fetch_instructors_stmt = $pdo->prepare("
    SELECT id AS instructor_id, name, email, role, phone_number
    FROM users
    WHERE role = 'instructor'
    ORDER BY name ASC
");
$fetch_instructors_stmt->execute();
$instructors = $fetch_instructors_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Instructors</title>
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
        <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></li>
        <li class="nav-item"><a href="course.php" class="nav-link"><i class="fas fa-book-open"></i> Courses</a></li>
        <li class="nav-item"><a href="Instructor_Student.php" class="nav-link"><i class="fas fa-users"></i> Students</a></li>
        <li class="nav-item"><a href="Instructor_Management.php" class="nav-link active"><i class="fas fa-chalkboard-teacher"></i> Instructors</a></li>
        <li class="nav-item"><a href="Instructor_Settings.php" class="nav-link"><i class="fas fa-cog"></i> Settings</a></li>
        <li class="nav-item"><a href="../logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>

<main class="main-content">
<div class="student-management">
    <div class="management-header">
        <h2>Instructor Management</h2>
        <button class="add-student-btn" onclick="location.href='add_instructor.php'">
            <i class="fas fa-plus"></i> Add New Instructor
        </button>
    </div>

    <table class="student-table" id="instructorsTable">
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
    if ($instructors) {
        foreach ($instructors as $instructor) {
            echo "<tr>";
            echo "<td data-label='Name'>" . htmlspecialchars($instructor['name']) . "</td>";
            echo "<td data-label='Email'>" . htmlspecialchars($instructor['email']) . "</td>";
            echo "<td data-label='Role'>" . htmlspecialchars(ucfirst($instructor['role'])) . "</td>";
            echo "<td data-label='Phone'>" . htmlspecialchars($instructor['phone_number']) . "</td>";
            echo "<td data-label='Actions'>
                    <div class='student-actions'>
                        <button class='action-btn edit-btn' onclick='editInstructor(" . htmlspecialchars($instructor['instructor_id']) . ")'>
                            <i class='fas fa-edit'></i>
                        </button>
                        <button class='action-btn delete-btn' onclick='deleteInstructor(" . htmlspecialchars($instructor['instructor_id']) . ")'>
                            <i class='fas fa-trash'></i>
                        </button>
                    </div>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>No instructors found.</td></tr>";
    }
    ?>
    </tbody>
    </table>
</div>
</main>
</div>

<script>
function editInstructor(id) {
    window.location.href = 'edit_instructor.php?id=' + id;
}

function deleteInstructor(id) {
    if (confirm('Are you sure you want to delete this instructor?')) {
        fetch('delete_instructor.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'instructor_id=' + encodeURIComponent(id)
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
