<?php
session_start();
include 'config.php'; // Database connection

// Check if course ID is passed
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('No course selected.');
}

$courseId = intval($_GET['id']);

// Fetch course details
$sql = "SELECT * FROM courses WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $courseId);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if (!$course) {
    die('Course not found.');
}

// Decode uploaded files
$materials = [];
$firstVideo = ''; // Will store first video file if found

if (!empty($course['uploaded_files'])) {
    $materials = json_decode($course['uploaded_files'], true);

    if (is_array($materials)) {
        foreach ($materials as $file) {
            $filePath = 'Instructor/uploads/' . trim($file);
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

            if (in_array($extension, ['mp4', 'webm', 'ogg'])) {
                $firstVideo = $filePath;
                break; // Stop after finding the first video
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($course['title']) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="Css/courseContent.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Righteous&family=Secular+One&display=swap');
  </style>
</head>
<body>

<div class="course-container">

    <?php if (!empty($firstVideo)): ?>
    <div class="video-section">
        <div class="video-header">
            <h2 class="course-title"><?= htmlspecialchars($course['title']) ?></h2>
        </div>

        <div class="video-container">
            <video id="course-video" controls poster="video-thumbnail.jpg">
                <source src="<?= htmlspecialchars($firstVideo) ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    </div>
    <?php endif; ?>

    <div class="course-sidebar">
        <div class="sidebar-header">
            <h3>Course Materials</h3>
        </div>

        <div class="materials-list">
          <?php if (!empty($materials)): ?>
            <?php foreach ($materials as $index => $file): ?>
              <?php
                $filePath = 'Instructor/uploads/' . trim($file);
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
              ?>
              <div class="material-card">
                <div class="material-title">Material <?= $index + 1 ?></div>

                <?php if (in_array($extension, ['mp4', 'webm', 'ogg'])): ?>
                  <!-- We already showed the first video above, you can skip or show again -->
                <?php elseif ($extension === 'pdf'): ?>
                  <iframe src="<?= htmlspecialchars($filePath) ?>" width="100%" height="500px" style="border:none;"></iframe>
                  <a href="<?= htmlspecialchars($filePath) ?>" download class="download-btn">
                    <i class="fas fa-download"></i> Download PDF
                  </a>

                <?php elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                  <img src="<?= htmlspecialchars($filePath) ?>" alt="Course Image" class="course-image">

                <?php else: ?>
                  <p style="color:red;">Unsupported file type: <?= htmlspecialchars($extension) ?></p>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p>No materials available for this course.</p>
          <?php endif; ?>
        </div>
    </div>

</div>

</body>
</html>
