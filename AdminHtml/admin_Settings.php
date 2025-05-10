<?php
session_start();

include 'config.php'; // Include your database connection file
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user data based on the logged-in user's ID
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update the user profile details
    $username = $_POST['username'];
    $email = $_POST['email'];

    // If an avatar image is uploaded, handle the file upload
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $avatarPath = '../uploads/' . basename($_FILES['avatar']['name']);
        move_uploaded_file($_FILES['avatar']['tmp_name'], $avatarPath);
    } else {
        $avatarPath = $user['image']; // Keep the old image if no new one is uploaded
    }

    // Update user information in the database
    $updateQuery = "UPDATE users SET name = ?, email = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('sssi', $username, $email, $avatarPath, $user_id);
    $stmt->execute();

    // Redirect or display a success message
    header('Location: admin_Settings.php?status=success');
}
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
    <a href="admin_User.php" class="nav-link">
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
    <a href="admin_settings.php" class="nav-link active">
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
      <div class="settings-panel">
        <div class="panel-header">
          <h2><i class="fas fa-cog"></i> Instructor Settings</h2>
        </div>

        <div class="panel-body">
          <form action="" method="POST" enctype="multipart/form-data">
            <div class="settings-section">
              <h3 class="section-title">
                <i class="fas fa-user-shield"></i> Profile Settings
              </h3>
              <div class="avatar-upload">
                <img 
                    src="<?php echo htmlspecialchars($user['image'] ?? 'user-avatar.jpg'); ?>" 
                    alt="User Avatar" 
                    class="user-avatar" 
                    id="avatarPreview"
                >
                <input 
                    type="file" 
                    accept="image/*" 
                    id="avatarInput" 
                    name="avatar"
                    onchange="previewImage(event)" 
                >
              </div>

              <div class="settings-grid">
                <div class="form-row">
                  <label class="form-label">Username</label>
                  <input type="text" class="form-input" name="username" value="<?php echo htmlspecialchars($user['name']); ?>" />
                </div>
                <div class="form-row">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-input" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" />
                </div>
              </div>
            </div>

            <div class="panel-footer">
              <button type="submit" class="btn btn-save">
                <i class="fas fa-save"></i> Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>

  <script>
    const mobileMenuToggle = document.querySelector(".mobile-menu-toggle");
    const dashboardContainer = document.querySelector(".dashboard-container");

    mobileMenuToggle.addEventListener("click", () => {
      dashboardContainer.classList.toggle("sidebar-active");
    });

    document.addEventListener("click", (e) => {
      if (
        window.innerWidth <= 1024 &&
        !e.target.closest(".sidebar") &&
        !e.target.closest(".mobile-menu-toggle")
      ) {
        dashboardContainer.classList.remove("sidebar-active");
      }
    });

    document.querySelectorAll(".nav-link").forEach((link) => {
      link.addEventListener("click", function (e) {
        document.querySelector(".nav-link.active").classList.remove("active");
        this.classList.add("active");
      });
    });

    function previewImage(event) {
      const file = event.target.files[0];
      const reader = new FileReader();

      reader.onload = function () {
        const preview = document.getElementById("avatarPreview");
        preview.src = reader.result;
      };
      
      reader.readAsDataURL(file);
    }
  </script>
</body>
</html>
