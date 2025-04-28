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

// Handle form submission to add a student
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['full_name']) && isset($_POST['email'])) {
    $full_name = htmlspecialchars($_POST['full_name']);
    $email = htmlspecialchars($_POST['email']);
    $accessibility = htmlspecialchars($_POST['accessibility'] ?? '');

    $stmt = $pdo->prepare("INSERT INTO users (name, email, role, gender, phone_number, password) VALUES (?, ?, 'student', '', '', '')");
    $stmt->execute([$full_name, $email]);

    echo "<script>alert('Student added successfully!'); window.location.href=window.location.href;</script>";
    exit;
}

// Fetch only students who enrolled in a course
$fetch_students_stmt = $pdo->prepare("
    SELECT DISTINCT u.id AS student_id, u.name, u.email, u.gender, u.phone_number
    FROM enrollments e
    JOIN users u ON e.user_id = u.id
    ORDER BY u.name ASC
");
$fetch_students_stmt->execute();
$students = $fetch_students_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Instructor Dashboard</title>
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
            <a href="Instructor_Student.php" class="nav-link active">
                <i class="fas fa-users"></i> Students
            </a>
        </li>
        <li class="nav-item">
            <a href="add_course.php" class="nav-link">
                <i class="fas fa-chalkboard-teacher"></i> Add Course
            </a>
        </li>
        <li class="nav-item">
            <a href="Instructor_Settings.php" class="nav-link">
                <i class="fas fa-cog"></i> Settings
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
        <div class="search-filter">
        <input type="text" id="searchInput" class="search-input" placeholder="Search students...">

          
        </div>
    </div>
    <table class="student-table" id="studentsTable">
    <thead>
        <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Gender</th>
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
            echo "<td data-label='Gender'>" . htmlspecialchars($student['gender'] ?: 'N/A') . "</td>";
            echo "<td data-label='Phone Number'>" . htmlspecialchars($student['phone_number'] ?: 'N/A') . "</td>";
            echo "<td data-label='Actions'>
                    <div class='student-actions'>
                        <button class='action-btn delete-btn' onclick='deleteStudent(" . htmlspecialchars($student['student_id']) . ")'>
                            <i class='fas fa-trash'></i>
                        </button>
                    </div>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>No students enrolled yet.</td></tr>";
    }
    ?>
    </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal-content">
    <div class="modal-header">
      <h3><i class="fas fa-user-graduate"></i> Add New Student</h3>
      <button class="close-btn" onclick="closeModal()">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <form id="studentForm" method="POST">
      <div class="form-row">
        <label class="form-label">Full Name</label>
        <input type="text" class="form-input" name="full_name" placeholder="John Doe" required>
      </div>

      <div class="form-row">
        <label class="form-label">Email Address</label>
        <input type="email" class="form-input" name="email" placeholder="john@example.com" required>
      </div>

      <div class="form-row">
        <label class="form-label">Accessibility Needs</label>
        <div class="accessibility-options">
          <div class="accessibility-option" onclick="selectAccessibility('Deaf/HoH')">
            <i class="fas fa-deaf"></i> Deaf/HoH
          </div>
          <div class="accessibility-option" onclick="selectAccessibility('Blind/Low Vision')">
            <i class="fas fa-blind"></i> Blind/Low Vision
          </div>
        </div>
        <input type="hidden" name="accessibility" id="accessibilityInput">
      </div>

      <button type="submit" class="add-student-btn">
        <i class="fas fa-save"></i> Save Student
      </button>
    </form>
  </div>
</div>

</main>
</div>

<script>
function openModal() {
    document.getElementById('modalOverlay').style.display = 'block';
}

function closeModal() {
    document.getElementById('modalOverlay').style.display = 'none';
}

function selectAccessibility(value) {
    document.getElementById('accessibilityInput').value = value;
    const options = document.querySelectorAll('.accessibility-option');
    options.forEach(opt => opt.classList.remove('selected'));
    event.target.classList.add('selected');
}

function deleteStudent(studentId) {
    if (confirm('Are you sure you want to delete this student and their enrollments?')) {
        fetch('delete_student.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'student_id=' + encodeURIComponent(studentId)
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

// ✅ LIVE SEARCH FUNCTION
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#studentsTable tbody tr');

    rows.forEach(row => {
        const rowText = row.textContent.toLowerCase();
        if (rowText.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>

</body>
</html>
<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('#studentsTable tbody tr');

    tableRows.forEach(row => {
        const rowText = row.innerText.toLowerCase();
        if (rowText.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
