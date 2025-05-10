<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Dashboard - InclusiveLearn</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="Css/acountDetail.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Righteous&family=Secular+One&display=swap');
        </style>
</head>
<body>
<?php  include 'navBar.php'; // Include the nav bar file?>
    <div class="dashboard-container">
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

// Check if user logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: signIn.php');
    exit;
}

// Fetch current user
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user exists
if (!$user) {
    die("User not found.");
}

// Prepare joined date nicely
$joinedDate = date('F Y', strtotime($user['created_at'] ?? $user['date_created'] ?? 'now'));
?>


<header class="dashboard-header">
    <div class="user-profile">
        <img src="<?php echo htmlspecialchars($user['image']); ?>" alt="User Avatar" class="user-avatar">
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($user['name']); ?></h1>
            <div class="profile-meta">
             
                <span>Joined <?php echo $joinedDate; ?></span>
            </div>
        </div>
    </div>
</header>



<section class="settings-section">
    <h2>Account Settings</h2>

    <form id="accountForm" class="form-grid" action="update_profile.php" method="POST" enctype="multipart/form-data">
        
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

        <div class="form-group">
            <label class="form-label" for="fullName">Full Name</label>
            <input 
                type="text" 
                class="form-input" 
                id="fullName" 
                name="fullName" 
                value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" 
                required
            >
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input 
                type="email" 
                class="form-input" 
                id="email" 
                value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" 
                readonly
            >
        </div>

        <div class="form-group">
            <label class="form-label" for="phone">Phone Number</label>
            <input 
                type="text" 
                class="form-input" 
                id="phone" 
                name="phone" 
                value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>"
            >
        </div>

        <div class="form-group">
            <label class="form-label" for="currentPassword">Current Password</label>
            <input 
                type="password" 
                class="form-input" 
                id="currentPassword" 
                name="currentPassword" 
                placeholder="Enter current password" 
                required
            >
        </div>

        <div class="form-group">
            <label class="form-label" for="password">New Password</label>
            <input 
                type="password" 
                class="form-input" 
                id="password" 
                name="password" 
                placeholder="Enter new password"
            >
        </div>

        <div class="edit-buttons">
            <button type="button" class="btn btn-secondary" onclick="resetForm()">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>

    </form>
</section>

<!-- 👇 Image Preview Script -->
<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('avatarPreview');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

function resetForm() {
    document.getElementById('accountForm').reset();
    // Optional: Reset avatar preview to original
    const originalImage = "<?php echo htmlspecialchars($user['image'] ?? 'user-avatar.jpg'); ?>";
    document.getElementById('avatarPreview').src = originalImage;
}
</script>


<?php
include 'config.php'; // Include your database connection file
$userId = $_SESSION['user_id'] ?? null;

if ($userId) {
    // Fetch user details
    $stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $userResult = $stmt->get_result();
    $user = $userResult->fetch_assoc();

    // Fetch enrollment count
    $stmt2 = $conn->prepare("SELECT COUNT(*) as course_count FROM enrollments WHERE user_id = ?");
    $stmt2->bind_param("i", $userId);
    $stmt2->execute();
    $enrollmentResult = $stmt2->get_result();
    $enrollments = $enrollmentResult->fetch_assoc();
}
?>

<section class="account-details">
    <h2>Account Information</h2>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label">Full Name</div>
            <div class="detail-value"><?php echo htmlspecialchars($user['name']); ?></div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Email Address</div>
            <div class="detail-value"><?php echo htmlspecialchars($user['email']); ?></div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Enrolled Courses</div>
            <div class="detail-value"><?php echo (int)($enrollments['course_count'] ?? 0); ?> Courses</div>
        </div>
    </div>
</section>

<section class="enrolled-courses">
    <?php
    // Assuming you have a valid DB connection in $conn
    // Also assuming you know the current logged-in user's ID in $userId

    // Fetch active enrolled courses for the user
    $query = "
        SELECT c.id, c.title, c.category, c.classification
        FROM enrollments e
        INNER JOIN courses c ON e.course_id = c.id
        WHERE e.user_id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $courseCount = $result->num_rows;
    ?>

    <h2>Active Courses (<?php echo $courseCount; ?>)</h2>

    <div class="courses-grid">
        <?php if ($courseCount > 0): ?>
            <?php while ($course = $result->fetch_assoc()): ?>
                <div class="course-card">
                    <div class="course-thumbnail">
                        <span class="course-badge">
                            <?php echo htmlspecialchars($course['classification']); ?>
                        </span>
                    </div>

                    <div class="course-progress">
                        <!-- For now, we'll just simulate progress. You can replace this with real progress data later -->
                        <div class="progress-bar" style="width: 0%"></div>
                    </div>

                    <div class="course-content">
                        <div class="course-category">
                            <?php echo htmlspecialchars($course['category']); ?>
                        </div>
                        <h3 class="course-title">
                            <?php echo htmlspecialchars($course['title']); ?>
                        </h3>
                        <div class="course-meta">
                         
                            <a href="courseContent.php?id=<?php echo urlencode($course['id']); ?>" class="resume-btn">
                                <i class="fas fa-play"></i>
                                Start
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No active courses found.</p>
        <?php endif; ?>
    </div>
</section>

    </div>
    <?php  include 'footer.php'; // Include the footer file?>
    <script>
        const progressCircle = document.querySelector('.progress-circle circle:last-child');
        const radius = progressCircle.r.baseVal.value;
        const circumference = 2 * Math.PI * radius;
        
        progressCircle.style.strokeDasharray = circumference;
        progressCircle.style.strokeDashoffset = circumference - (0.65 * circumference);

        document.querySelectorAll('.course-card').forEach(card => {
            card.addEventListener('click', (e) => {
                if (!e.target.classList.contains('resume-btn')) {
                    card.classList.toggle('expanded');
                }
            });
        });
                document.getElementById('avatarInput').addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            document.querySelector('.user-avatar').src = e.target.result;
                        }
                        reader.readAsDataURL(file);
                    }
                });
        
                function togglePassword() {
                    const passwordField = document.getElementById('password');
                    const toggleIcon = document.querySelector('.password-toggle');
                    
                    if (passwordField.type === 'password') {
                        passwordField.type = 'text';
                        toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
                    } else {
                        passwordField.type = 'password';
                        toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
                    }
                }
        
                document.getElementById('accountForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = {
                        fullName: document.getElementById('fullName').value,
                        email: document.getElementById('email').value,
                        password: document.getElementById('password').value,
                        currentPassword: document.getElementById('currentPassword').value
                    };
        
                    if (!formData.currentPassword) {
                        alert('Please enter your current password to save changes');
                        return;
                    }
        
                    document.querySelector('.profile-info h1').textContent = formData.fullName;
                    
                    showSuccessMessage();
                });
        
                function resetForm() {
                    document.getElementById('accountForm').reset();
                }
        
                function showSuccessMessage() {
                    const successDiv = document.createElement('div');
                    successDiv.className = 'success-message';
                    successDiv.textContent = 'Changes saved successfully!';
                    
                    successDiv.style.position = 'fixed';
                    successDiv.style.bottom = '2rem';
                    successDiv.style.right = '2rem';
                    successDiv.style.background = '#10b981';
                    successDiv.style.color = 'white';
                    successDiv.style.padding = '1rem 2rem';
                    successDiv.style.borderRadius = '0.75rem';
                    successDiv.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
                    successDiv.style.animation = 'slideIn 0.3s ease';
        
                    document.body.appendChild(successDiv);
        
                    setTimeout(() => {
                        successDiv.remove();
                    }, 3000);
                }
    </script>
</body>
</html>