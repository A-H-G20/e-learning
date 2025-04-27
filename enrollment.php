<?php
session_start();
include 'config.php'; // Database connection

// Check if course ID is passed
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('No course ID provided.');
}

$courseId = intval($_GET['id']);
$course = null;

// Fetch course data
$sql = "SELECT * FROM courses WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $courseId);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if (!$course) {
    die('Course not found.');
}

// Fetch the instructor who uploaded the course
$instructorName = "Unknown Instructor";
$instructorImage = "instructor.jpg"; // Default image

if (!empty($course['user_id'])) { // if the course has a user_id field
    $userId = intval($course['user_id']);
    $userSql = "SELECT name, image FROM users WHERE id = ?";
    $userStmt = $conn->prepare($userSql);
    $userStmt->bind_param("i", $userId);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    $user = $userResult->fetch_assoc();

    if ($user) {
        $instructorName = $user['name'];
        if (!empty($user['image'])) {
            $instructorImage = $user['image']; // use uploaded instructor image
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Course Enroll - <?= htmlspecialchars($course['title']) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="Css/courseEnroll.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Righteous&family=Secular+One&display=swap');
  </style>
</head>
<body>

<header class="course-hero">
  <h1 class="course-title"><?= htmlspecialchars($course['title']) ?></h1>
  <div class="course-meta-grid">
    <div class="meta-item">
      <div class="meta-icon">
        <i class="fas fa-user-graduate"></i>
      </div>
      <div>
        <div class="meta-label">Enrolled</div>
        <div class="meta-value">24K+ Students</div> <!-- Static, or you can connect to database later -->
      </div>
    </div>
    <div class="meta-item">
      <div class="meta-icon">
        <i class="fas fa-certificate"></i>
      </div>
      <div>
        <div class="meta-label">Certification</div>
        <div class="meta-value">Included</div>
      </div>
    </div>
  </div>
</header>

<main class="course-container">
  <div class="course-main">
    <section class="course-overview">
      <h2 class="section-title">Course Overview</h2>
      <p class="text-lg text-light">
        <?= nl2br(htmlspecialchars($course['description'])) ?>
      </p>

      <div class="instructor-card">
  <img src="<?= htmlspecialchars($instructorImage) ?>" alt="Instructor" class="instructor-avatar">
  <div>
    <h3 class="text-xl font-bold"><?= htmlspecialchars($instructorName) ?></h3>
    <p class="text-light">Instructor</p>
  </div>
</div>

<?php
// Fetch course reviews
$reviews = [];

$reviewSql = "SELECT r.review_text, r.created_at, u.name AS user_name, u.image AS user_image
              FROM reviews r
              JOIN users u ON r.user_id = u.id
              WHERE r.course_id = ?
              ORDER BY r.created_at DESC";

$reviewStmt = $conn->prepare($reviewSql);
$reviewStmt->bind_param("i", $courseId);
$reviewStmt->execute();
$reviewResult = $reviewStmt->get_result();

while ($row = $reviewResult->fetch_assoc()) {
    $reviews[] = $row;
}

?>
     <section class="reviews-section">
  <h2 class="section-title">Student Experiences</h2>

  <?php if (!empty($reviews)): ?>
    <?php foreach ($reviews as $review): ?>
      <div class="review-card">
        <div class="review-header">
          <div class="review-author">
            <?php
              $userImage = !empty($review['user_image']) ? 'uploads/' . $review['user_image'] : 'default-user.png';
            ?>
            <img src="<?= htmlspecialchars($userImage) ?>" alt="<?= htmlspecialchars($review['user_name']) ?>" class="user-avatar">
            <div>
              <h4><?= htmlspecialchars($review['user_name']) ?></h4>
            </div>
          </div>
          <span class="text-light"><?= date('F j, Y', strtotime($review['created_at'])) ?></span>
        </div>
        <p class="review-text">
          <?= nl2br(htmlspecialchars($review['review_text'])) ?>
        </p>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p style="text-align:center; color:gray;">No reviews yet. Be the first to review this course!</p>
  <?php endif; ?>
</section>


<section class="review-form">
  <h2 class="section-title">Share Your Experience</h2>
  <form id="reviewForm" method="POST" action="">
    <div class="form-group">
      <textarea name="review_text" placeholder="Write your review..." maxlength="500" required></textarea>
    </div>
    <div class="form-group">
      <button type="submit" name="submit_review" class="enroll-btn">Submit Review</button>
    </div>
  </form>
</section>
<?php
// Assume user_id is stored in session after login
$user_id = $_SESSION['user_id'] ?? null;

if (isset($_POST['submit_review'])) {
    if (!$user_id) {
        echo "<script>alert('You must be logged in to submit a review.');</script>";
    } else {
        $reviewText = trim($_POST['review_text']);

        if (!empty($reviewText)) {
            $insertReviewSql = "INSERT INTO reviews (course_id, user_id, review_text) VALUES (?, ?, ?)";
            $insertReviewStmt = $conn->prepare($insertReviewSql);
            $insertReviewStmt->bind_param("iis", $courseId, $user_id, $reviewText);
            $insertReviewStmt->execute();

            if ($insertReviewStmt) {
                echo "<script>alert('Review submitted successfully.'); window.location.href=window.location.href;</script>";
            } else {
                echo "<script>alert('Failed to submit review.');</script>";
            }
        } else {
            echo "<script>alert('Please write a review before submitting.');</script>";
        }
    }
}

?>
  </div>

  <div class="enrollment-card">
  <h2 class="text-2xl font-bold mb-4">Enroll Now</h2>
  <div class="price-display">
    <span class="text-4xl font-bold">Free</span>
  </div>
  <ul class="enrollment-features">
    <li>
      <i class="fas fa-infinity text-primary"></i>
      Lifetime Access
    </li>
    <li>
      <i class="fas fa-certificate text-primary"></i>
      Professional Certificate
    </li>
    <li>
      <i class="fas fa-project-diagram text-primary"></i>
      8 Real Projects
    </li>
  </ul>

  <!-- Enroll Form -->
  <form method="POST" action="">
    <button type="submit" name="enroll_now" class="enroll-btn">
      <i class="fas fa-lock-open mr-2"></i>
      Enroll for Free
    </button>
  </form>
</div>
<?php

// Enroll user into course
if (isset($_POST['enroll_now'])) {
  if (!$user_id) {
      echo "<script>alert('You must be logged in to enroll.');</script>";
  } else {
      // Check if already enrolled (prevent duplicate enrollments)
      $checkEnrollSql = "SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?";
      $checkEnrollStmt = $conn->prepare($checkEnrollSql);
      $checkEnrollStmt->bind_param("ii", $user_id, $courseId);
      $checkEnrollStmt->execute();
      $checkEnrollResult = $checkEnrollStmt->get_result();

      if ($checkEnrollResult->num_rows > 0) {
          echo "<script>alert('You are already enrolled in this course.');</script>";
      } else {
          $enrollSql = "INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)";
          $enrollStmt = $conn->prepare($enrollSql);
          $enrollStmt->bind_param("ii", $user_id, $courseId);
          $enrollStmt->execute();

          if ($enrollStmt) {
           Header("Location: courseContent.php?id=" . $courseId); // Redirect to course content page
              exit;
          } else {
              echo "<script>alert('Failed to enroll. Please try again later.');</script>";
          }
      }
  }
}

?>
</main>

<script></script>

</body>
</html>
