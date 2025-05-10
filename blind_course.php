<?php

include 'config.php'; // Database connection

// Fetch all blind courses
$courses = [];
$sql = "SELECT * FROM courses WHERE classification = 'blind' AND status = 'published'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Blind Education Programs</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="Css/deafStyle.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Righteous&family=Secular+One&display=swap');

    .download-pdf-btn {
      display: inline-block;
      padding: 10px 15px;
      background-color: #f05423;
      color: white;
      border-radius: 5px;
      text-decoration: none;
      margin-top: 10px;
    }
    .download-pdf-btn:hover {
      background-color: #d6441f;
    }
    .course-video, .course-image {
      width: 100%;
      height: auto;
      border-radius: 8px;
    }
  </style>
</head>
<body>
<?php  include 'navBar.php'; // Include the nav bar file?>
<header class="blind-courses-header" role="banner">
  <div class="header-content">
    <div class="braille-icon" aria-hidden="true">
      ⠠⠉⠳⠗⠎⠑⠎
    </div>
    <h1>Blind Education Programs</h1>
    <p>Experience learning through comprehensive audio descriptions, screen reader compatibility, and tactile learning materials</p>
    <div class="header-features">
      <div class="feature-tag">
        <i class="fas fa-assistive-listening-systems"></i> Audio Descriptions
      </div>
      <div class="feature-tag">
        <i class="fas fa-book-open"></i> Braille Ready
      </div>
      <div class="feature-tag">
        <i class="fas fa-universal-access"></i> Screen Reader Optimized
      </div>
    </div>
  </div>
</header>

<section class="blind-courses" aria-labelledby="blind-courses-heading">
  <div class="courses-container">
    <div class="section-header">
      <h2 id="blind-courses-heading">Tactile Learning Programs</h2>
      <div class="filter-controls">
        <button class="filter-btn active">All Courses</button>
        <button class="filter-btn">Beginner</button>
        <button class="filter-btn">Intermediate</button>
        <button class="filter-btn">Advanced</button>
      </div>
    </div>

    <div class="courses-grid">
      <?php if (!empty($courses)): ?>
        <?php foreach ($courses as $course): ?>
          <article class="course-card">
            <div class="card-header">
              <?php
              $fileDisplay = '<div style="padding:20px; color:gray;">No preview available</div>'; // Default

              if (!empty($course['uploaded_files'])) {
                  $files = json_decode($course['uploaded_files'], true);

                  if (is_array($files) && !empty($files)) {
                      $firstFile = trim($files[0]);
                      $filePath = 'Instructor/uploads/' . $firstFile;
                      $extension = strtolower(pathinfo($firstFile, PATHINFO_EXTENSION));

                      if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                          $fileDisplay = '<img src="' . htmlspecialchars($filePath) . '" alt="Course Image" class="course-image">';
                      } elseif (in_array($extension, ['mp4', 'webm', 'ogg'])) {
                          $fileDisplay = '<video class="course-video" controls>
                                              <source src="' . htmlspecialchars($filePath) . '" type="video/' . htmlspecialchars($extension) . '">
                                              Your browser does not support the video tag.
                                          </video>';
                      } elseif ($extension === 'pdf') {
                          $fileDisplay = '<a href="' . htmlspecialchars($filePath) . '" target="_blank" class="download-pdf-btn">
                                              <i class="fas fa-file-pdf"></i> View PDF
                                          </a>';
                      }
                  }
              }

              echo $fileDisplay;
              ?>
            </div>

            <div class="card-body">
              <h3 class="course-title"><?= htmlspecialchars($course['title']) ?></h3>
              <p class="course-description"><?= htmlspecialchars(mb_strimwidth($course['description'], 0, 120, '...')) ?></p>
              <div class="course-meta">
                <?php if (!empty($course['category'])): ?>
                  <div class="meta-item">
                    <i class="fas fa-signal"></i>
                    <?= htmlspecialchars($course['category']) ?>
                  </div>
                <?php endif; ?>
                <?php if (!empty($course['scheduled_publish_time'])): ?>
                  <div class="meta-item">
                    <i class="fas fa-clock"></i>
                    <?= date('M d, Y', strtotime($course['scheduled_publish_time'])) ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <div class="card-footer">
              <a href="enrollment.php?id=<?= $course['id'] ?>" class="enroll-btn" style="text-decoration:none;">
                Start Learning
                <i class="fas fa-arrow-right"></i>
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align:center;">No blind courses available at the moment.</p>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php  include 'footer.php'; // Include the footer file?>
<script>
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
    });
  });

  document.querySelectorAll('.enroll-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      this.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Enrolling...`;
      setTimeout(() => {
        this.innerHTML = `<i class="fas fa-check"></i> Enrolled!`;
      }, 1500);
    });
  });
</script>

</body>
</html>
