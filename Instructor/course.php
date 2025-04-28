<?php
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

// Fetch all courses
$stmt = $pdo->query("SELECT * FROM courses ORDER BY created_at DESC");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Instructor Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="../AdminHtml/Css/admin.css" />
    <style>
    @import url("https://fonts.googleapis.com/css2?family=Righteous&family=Secular+One&display=swap");
    </style>
</head>
<body>

<button class="mobile-menu-toggle"><i class="fas fa-bars"></i></button>

<div class="dashboard-container">
<nav class="sidebar">
              <div class="sidebar-header">
                <div class="logo">E-Learn</div>
              </div>
      
              <ul class="nav-menu">
                <li class="nav-item">
                  <a href="index.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    Dashboard
                  </a>
                </li>
                <li class="nav-item">
                  <a href="course.php" class="nav-link active">
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

    <div class="main-content">
        <div class="courses-management-panel">
            <div class="management-header">
                <h2><i class="fas fa-book-open"></i> Manage Courses</h2>
                <div class="header-actions">
                    <div class="search-filter">
                        <input type="text" class="search-input" placeholder="Search courses..." />
                        <select class="filter-select">
                            <option>All Courses</option>
                            <option>Published</option>
                            <option>Drafts</option>
                            <option>Archived</option>
                        </select>
                    </div>
                    <div>
  <a href="add_course.php" class="btn-add-course">Add</a>
</div>

                </div>
            </div>

            <div class="courses-list">
                <?php foreach ($courses as $course): ?>
                <div class="course-card">
                    <div class="course-header">
                        <div class="course-meta">
                            <span class="course-status <?= strtolower($course['status']) ?>"><?= htmlspecialchars(ucfirst($course['status'])) ?></span>
                            <div class="course-classification <?= htmlspecialchars($course['classification']) ?>">
                                <?php if ($course['classification'] == 'deaf'): ?>
                                    <i class="fas fa-deaf"></i> Deaf/HoH Focus
                                <?php elseif ($course['classification'] == 'blind'): ?>
                                    <i class="fas fa-blind"></i> Blind/Low Vision
                                <?php else: ?>
                                    <i class="fas fa-globe"></i> Standard
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="course-actions">
                        <a href="edit_course.php?id=<?= $course['id'] ?>" class="btn btn-icon">
    <i class="fas fa-edit"></i>
</a>

                            <button class="btn btn-icon delete-btn">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="course-body">
                        <h3 class="course-title"><?= htmlspecialchars($course['title']) ?></h3>
                        <p class="course-description"><?= htmlspecialchars($course['description']) ?></p>

                        <div class="course-stats">
                            <div class="stat">
                                <i class="fas fa-users"></i> 0 Students <!-- you can fetch real students count later -->
                            </div>
                        </div>

                        <?php if (!empty($course['uploaded_files'])): ?>
                            <div class="course-files">
                                <h4>Files:</h4>
                                <ul>
                                    <?php foreach (json_decode($course['uploaded_files']) as $file): ?>
                                        <li><a href="../Instructor/uploads/<?= htmlspecialchars($file) ?>" target="_blank"><?= htmlspecialchars($file) ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if (count($courses) == 0): ?>
                    <p style="padding:20px;">No courses found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
const dashboardContainer = document.querySelector('.dashboard-container');

mobileMenuToggle.addEventListener('click', () => {
    dashboardContainer.classList.toggle('sidebar-active');
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function() {
        document.querySelector('.nav-link.active').classList.remove('active');
        this.classList.add('active');
    });
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.search-input').addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.course-card').forEach(card => {
            const title = card.querySelector('.course-title').textContent.toLowerCase();
            card.style.display = title.includes(searchTerm) ? 'block' : 'none';
        });
    });

    document.querySelector('.filter-select').addEventListener('change', (e) => {
        const filter = e.target.value.toLowerCase();
        document.querySelectorAll('.course-card').forEach(card => {
            const status = card.querySelector('.course-status').textContent.toLowerCase();
            card.style.display = (filter === 'all courses' || status === filter) ? 'block' : 'none';
        });
    });
});
</script>

</body>
</html>
<style>
.btn-add-course {
    display: inline-block;
    padding: 10px 25px;
    background-color: #ee6c4d; /* Nice green */
    color: white;
    font-size: 16px;
    font-weight: bold;
    border-radius: 8px;
    text-decoration: none;
    transition: background-color 0.3s ease, transform 0.3s ease;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.btn-add-course:hover {
    background-color: #ee6c4d;
    transform: translateY(-2px);
}
</style>
