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

// Fetch total students (role = user)
$total_students_stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'user'");
$total_students_stmt->execute();
$total_students = $total_students_stmt->fetchColumn();

// Fetch deaf students (gender = deaf)
$deaf_students_stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE gender = 'deaf'");
$deaf_students_stmt->execute();
$deaf_students = $deaf_students_stmt->fetchColumn();

// Fetch blind students (gender = blind)
$blind_students_stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE gender = 'blind'");
$blind_students_stmt->execute();
$blind_students = $blind_students_stmt->fetchColumn();

// Fetch instructors
$instructors_stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'instructor'");
$instructors_stmt->execute();
$instructors = $instructors_stmt->fetchColumn();

// Fetch active courses
$active_courses_stmt = $pdo->prepare("SELECT COUNT(*) FROM courses");
$active_courses_stmt->execute();
$active_courses = $active_courses_stmt->fetchColumn();

// Fetch recent enrollments
$recent_enrollments_stmt = $pdo->prepare("
    SELECT u.name AS student_name, u.gender, c.title AS course_title, e.enrolled_at
    FROM enrollments e
    JOIN users u ON e.user_id = u.id
    JOIN courses c ON e.course_id = c.id
    ORDER BY e.enrolled_at DESC
    LIMIT 5
");
$recent_enrollments_stmt->execute();
$recent_enrollments = $recent_enrollments_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
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
        <li class="nav-item"><a href="adminDash.php" class="nav-link active"><i class="fas fa-home"></i> Dashboard</a></li>
        <li class="nav-item"><a href="adminCourse.php" class="nav-link"><i class="fas fa-book-open"></i> Courses</a></li>
        <li class="nav-item"><a href="adminUser.php" class="nav-link"><i class="fas fa-users"></i> Students</a></li>
        <li class="nav-item"><a href="adminInstruct.php" class="nav-link"><i class="fas fa-chalkboard-teacher"></i> Instructors</a></li>
        <li class="nav-item"><a href="adminSettings.php" class="nav-link"><i class="fas fa-cog"></i> Settings</a></li>
    </ul>
</nav>

<main class="main-content">
<div class="top-header">
    <h1 class="header-title">Dashboard Overview</h1>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-title"><i class="fas fa-users"></i> Total Students</div>
        <div class="stat-value"><?= htmlspecialchars($total_students) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-title"><i class="fas fa-deaf"></i> Deaf Students</div>
        <div class="stat-value"><?= htmlspecialchars($deaf_students) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-title"><i class="fas fa-blind"></i> Blind Students</div>
        <div class="stat-value"><?= htmlspecialchars($blind_students) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-title"><i class="fas fa-chalkboard-teacher"></i> Instructors</div>
        <div class="stat-value"><?= htmlspecialchars($instructors) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-title"><i class="fas fa-book-open"></i> Active Courses</div>
        <div class="stat-value"><?= htmlspecialchars($active_courses) ?></div>
    </div>
</div>

<div class="recent-activity">

  <table>
    <thead>
      <tr>
        <th>Student</th>
        <th>Accessibility Need</th>
        <th>Course</th>
        <th>Enrollment Date</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($recent_enrollments): ?>
        <?php foreach ($recent_enrollments as $enroll): ?>
        <tr>
            <td><?= htmlspecialchars($enroll['student_name']) ?></td>
            <td>
                <span class="accessibility-tag <?= $enroll['gender'] == 'deaf' ? 'deaf' : ($enroll['gender'] == 'blind' ? 'blind' : '') ?>">
                    <?php if ($enroll['gender'] == 'deaf'): ?>
                        <i class="fas fa-deaf"></i> Deaf
                    <?php elseif ($enroll['gender'] == 'blind'): ?>
                        <i class="fas fa-blind"></i> Blind
                    <?php else: ?>
                        None
                    <?php endif; ?>
                </span>
            </td>
            <td><?= htmlspecialchars($enroll['course_title']) ?></td>
            <td><?= htmlspecialchars(date('Y-m-d', strtotime($enroll['enrolled_at']))) ?></td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="4">No recent enrollments found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

</div>
</main>
</div>

<script>
const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
const dashboardContainer = document.querySelector('.dashboard-container');

mobileMenuToggle.addEventListener('click', () => {
    dashboardContainer.classList.toggle('sidebar-active');
});

document.addEventListener('click', (e) => {
    if (window.innerWidth <= 1024 && 
        !e.target.closest('.sidebar') && 
        !e.target.closest('.mobile-menu-toggle')) {
        dashboardContainer.classList.remove('sidebar-active');
    }
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        document.querySelector('.nav-link.active').classList.remove('active');
        this.classList.add('active');
    });
});
</script>

</body>
</html>
