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

// Check if instructor is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$instructor_id = $_SESSION['user_id'];

// Fetch total students (students enrolled in instructor's courses)
$total_students_stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT e.user_id) AS total_students
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    WHERE c.user_id = ?
");
$total_students_stmt->execute([$instructor_id]);
$total_students = $total_students_stmt->fetchColumn();

// Fetch active courses count (courses added by instructor)
$active_courses_stmt = $pdo->prepare("
    SELECT COUNT(*) AS total_courses
    FROM courses
    WHERE user_id = ?
");
$active_courses_stmt->execute([$instructor_id]);
$total_courses = $active_courses_stmt->fetchColumn();

// Fetch recent enrollments (limit 4)
$recent_enrollments_stmt = $pdo->prepare("
    SELECT u.name AS student_name, e.enrolled_at, c.title AS course_title, u.id AS student_id
    FROM enrollments e
    JOIN users u ON e.user_id = u.id
    JOIN courses c ON e.course_id = c.id
    WHERE c.user_id = ?
    ORDER BY e.enrolled_at DESC
    LIMIT 4
");
$recent_enrollments_stmt->execute([$instructor_id]);
$recent_enrollments = $recent_enrollments_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Instructor Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../AdminHtml/Css/admin.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.4.0/main.min.css">
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Righteous&family=Secular+One&display=swap");
  </style>
</head>
<body>

<div class="dashboard-container">
  <button class="mobile-menu-toggle">
    <i class="fas fa-bars"></i>
  </button>

  <nav class="sidebar">
              <div class="sidebar-header">
                <div class="logo">E-Learn</div>
              </div>
      
              <ul class="nav-menu">
                <li class="nav-item">
                  <a href="index.php" class="nav-link active">
                    <i class="fas fa-home"></i>
                    Dashboard
                  </a>
                </li>
                <li class="nav-item">
                  <a href="course.php" class="nav-link">
                    <i class="fas fa-book-open"></i>
                    Courses
                  </a>
                </li>
                <li class="nav-item">
                  <a href="Instructor_Student.php" class="nav-link">
                    <i class="fas fa-users"></i>
                    Students
                  </a>
                </li>
                <li class="nav-item">
                  <a href="add_course.php" class="nav-link ">
                    <i class="fas fa-chalkboard-teacher"></i>
                    Add Course
                  </a>
                </li>
                <li class="nav-item">
                  <a href="Instructor_Settings.php" class="nav-link ">
                    <i class="fas fa-cog"></i>
                    Settings
                  </a>
                </li>
                <li class="nav-item">
                    <a href="../logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </li>
              </ul>
            </nav>

  <main class="main-content">
    <div class="dashboard-header">
      <h1>Instructor Dashboard</h1>
    </div>

    <div class="stats-grid instructor-stats">
      <div class="stat-card">
        <div class="stat-content">
          <div class="stat-meta">
            <span class="stat-title">Total Students</span>
            <i class="fas fa-users"></i>
          </div>
          <div class="stat-value"><?= htmlspecialchars($total_students) ?></div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-content">
          <div class="stat-meta">
            <span class="stat-title">Active Courses</span>
            <i class="fas fa-book-open"></i>
          </div>
          <div class="stat-value"><?= htmlspecialchars($total_courses) ?></div>
        </div>
      </div>
    </div>

    <div class="recent-activity">
      <div class="table-header">
        <h3>Recent Enrollments</h3>
        <a href="Instructor_Student.php" class="btn">View All</a>
      </div>
      <table>
        <thead>
          <tr>
            <th>Student</th>
            <th>Course</th>
            <th>Enrollment Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($recent_enrollments): ?>
            <?php foreach ($recent_enrollments as $enrollment): ?>
              <tr>
                <td><?= htmlspecialchars($enrollment['student_name']) ?></td>
                <td><?= htmlspecialchars($enrollment['course_title']) ?></td>
                <td><?= htmlspecialchars(date('Y-m-d', strtotime($enrollment['enrolled_at']))) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="3">No recent enrollments.</td>
            </tr>
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
