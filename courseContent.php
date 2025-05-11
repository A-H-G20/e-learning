  <?php

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
  <?php include 'navBar.php'; // Include the nav bar file ?>
  
  <div class="page-wrapper">
    <!-- Main course content container -->
    <div class="course-container">
      <!-- Left column - Video and Content Tabs -->
      <div class="course-main-content">
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
            
            <div class="video-overlay">
              <div class="video-controls">
                <button class="control-btn"><i class="fas fa-bookmark"></i></button>
                <button class="control-btn"><i class="fas fa-share"></i></button>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <!-- Content Tabs Section -->
        <div class="content-tabs">
          <div class="tab-nav">
            <button class="tab-btn active" data-tab="overview">Overview</button>
            <button class="tab-btn" data-tab="resources">Resources</button>
           
          </div>
          
          <!-- Overview Tab Content -->
          <div class="tab-content active" id="overview-tab">
            <div class="overview-header">
              <h3 class="overview-title"><?= htmlspecialchars($course['title']) ?></h3>
              <div class="lesson-meta">
                <div class="meta-item"><i class="fas fa-clock"></i> Duration: <?= $course['duration'] ?? '1 hour' ?></div>
                <div class="meta-item"><i class="fas fa-user"></i> Instructor: <?= $course['instructor_name'] ?? 'Instructor' ?></div>
              </div>
            </div>
            
            <div class="course-description">
              <p><?= htmlspecialchars($course['description'] ?? 'No description available for this course.') ?></p>
            </div>
            
            <div class="learning-objectives">
              <h3>Learning Objectives</h3>
              <ul>
                <li>Understand the key concepts covered in this course</li>
                <li>Apply the techniques learned to real-world scenarios</li>
                <li>Complete the assignments to test your knowledge</li>
                <li>Earn a certificate upon successful completion</li>
              </ul>
            </div>
          </div>
          
          <!-- Resources Tab Content -->
          <div class="tab-content" id="resources-tab">
            <h3 class="section-title">Course Resources</h3>
            <div class="resources-grid">
              <?php if (!empty($materials)): ?>
                <?php foreach ($materials as $index => $file): ?>
                  <?php
                  $filePath = 'Instructor/uploads/' . trim($file);
                  $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                  $iconClass = 'fa-file';
                  
                  if (in_array($extension, ['mp4', 'webm', 'ogg'])) {
                      $iconClass = 'fa-video';
                  } elseif ($extension === 'pdf') {
                      $iconClass = 'fa-file-pdf';
                  } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                      $iconClass = 'fa-image';
                  }
                  ?>
                  <div class="resource-card">
                    <div class="resource-icon">
                      <i class="fas <?= $iconClass ?>"></i>
                    </div>
                    <div class="resource-content">
                      <h4 class="resource-title">Material <?= $index + 1 ?></h4>
                      <p class="resource-desc"><?= ucfirst($extension) ?> file</p>
                      <a href="<?= htmlspecialchars($filePath) ?>" download class="download-btn">
                        <i class="fas fa-download"></i> Download
                      </a>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="empty-state">
                  <i class="fas fa-folder-open"></i>
                  <p>No resources available for this course.</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <!-- Discussion Tab Content -->
          <div class="tab-content" id="discussion-tab">
            <div class="discussion-form">
              <div class="comment-box">
                <h3 class="section-title">Join the Discussion</h3>
                <form action="submit_comment.php" method="post" class="centered-form">
                  <div class="form-group">
                    <textarea name="comment" placeholder="Share your thoughts or questions about this course..." required></textarea>
                  </div>
                  <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Post Comment
                  </button>
                </form>
              </div>
            </div>
            
            <div class="comments-section">
              <h3 class="section-title">Student Discussions</h3>
              <div class="empty-state">
                <i class="fas fa-comments"></i>
                <p>Be the first to start a discussion about this course!</p>
              </div>
            </div>
          </div>
          
          <!-- Notes Tab Content -->
          <div class="tab-content" id="notes-tab">
            <h3 class="section-title">Your Notes</h3>
            <div class="notes-editor">
              <form action="save_notes.php" method="post" class="centered-form">
                <div class="form-group">
                  <textarea name="notes" placeholder="Take notes as you learn..." class="editor-content"></textarea>
                </div>
                <button type="submit" class="submit-btn">
                  <i class="fas fa-save"></i> Save Notes
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Right column - Course Materials Sidebar -->
      <div class="course-sidebar">
        <div class="sidebar-header">
          <h3>Course Materials</h3>
          <div class="progress-container">
            <svg class="progress-ring" width="40" height="40">
              <circle r="15" cx="20" cy="20" stroke-dasharray="94.2" stroke-dashoffset="47.1"></circle>
            </svg>
            <span class="progress-text">50%</span>
          </div>
        </div>

        <div class="materials-list">
          <?php if (!empty($materials)): ?>
            <!-- Module-based organization -->
            <div class="module-card expanded">
              <div class="module-header">
                <div class="module-title">Module 1: Introduction</div>
                <div class="expand-toggle"></div>
              </div>
              <div class="lesson-list">
                <?php 
                $videoCount = 0;
                $pdfCount = 0;
                $imageCount = 0;
                
                foreach ($materials as $index => $file): 
                  $filePath = 'Instructor/uploads/' . trim($file);
                  $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                  
                  if (in_array($extension, ['mp4', 'webm', 'ogg'])) {
                    $videoCount++;
                    $type = "video";
                    $itemTitle = "Video $videoCount";
                    $icon = "fa-play";
                  } elseif ($extension === 'pdf') {
                    $pdfCount++;
                    $type = "pdf";
                    $itemTitle = "PDF Document $pdfCount";
                    $icon = "fa-file-pdf";
                  } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $imageCount++;
                    $type = "image";
                    $itemTitle = "Image $imageCount";
                    $icon = "fa-image";
                  } else {
                    $type = "other";
                    $itemTitle = "Material " . ($index + 1);
                    $icon = "fa-file";
                  }
                  
                  $isActive = ($type === "video" && $videoCount === 1) ? "active" : "";
                ?>
                  <div class="lesson-item <?= $isActive ?>" data-file="<?= htmlspecialchars($filePath) ?>" data-type="<?= $type ?>">
                    <div class="lesson-thumbnail">
                      <i class="fas <?= $icon ?>"></i>
                    </div>
                    <div class="lesson-info"><?= $itemTitle ?></div>
                    <div class="lesson-duration">
                      <?php if ($type === "video"): ?>
                        <span>3:45</span>
                      <?php elseif ($type === "pdf"): ?>
                        <i class="fas fa-download"></i>
                      <?php else: ?>
                        <i class="fas fa-eye"></i>
                      <?php endif; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
            
            <!-- Individual material cards for compatibility -->
            <?php foreach ($materials as $index => $file): ?>
              <?php
              $filePath = 'Instructor/uploads/' . trim($file);
              $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
              ?>
              <div class="material-card" data-type="<?= in_array($extension, ['mp4', 'webm', 'ogg']) ? 'video' : ($extension === 'pdf' ? 'pdf' : (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']) ? 'image' : 'other')) ?>">
                <div class="material-title">Material <?= $index + 1 ?> <div class="expand-toggle"></div></div>
                
                <div class="material-content">
                  <?php if (in_array($extension, ['mp4', 'webm', 'ogg'])): ?>
                    <!-- We already showed the first video above, you can skip or show again -->
                    <a href="#" class="play-video-btn" data-src="<?= htmlspecialchars($filePath) ?>">
                      <i class="fas fa-play"></i> Play Video
                    </a>
                  <?php elseif ($extension === 'pdf'): ?>
                    <iframe src="<?= htmlspecialchars($filePath) ?>" width="100%" height="300px"></iframe>
                    <a href="<?= htmlspecialchars($filePath) ?>" download class="download-btn">
                      <i class="fas fa-download"></i> Download PDF
                    </a>
                  <?php elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                    <img src="<?= htmlspecialchars($filePath) ?>" alt="Course Image" class="course-image">
                  <?php else: ?>
                    <p style="color:red;">Unsupported file type: <?= htmlspecialchars($extension) ?></p>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty-state">
              <i class="fas fa-folder-open"></i>
              <p>No materials available for this course.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; // Include the footer file ?>

  <!-- Add JavaScript for interactivity -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Tab switching functionality
      const tabButtons = document.querySelectorAll('.tab-btn');
      const tabContents = document.querySelectorAll('.tab-content');
      
      tabButtons.forEach(button => {
        button.addEventListener('click', () => {
          const tabId = button.getAttribute('data-tab');
          
          // Remove active class from all tabs and contents
          tabButtons.forEach(btn => btn.classList.remove('active'));
          tabContents.forEach(content => content.classList.remove('active'));
          
          // Add active class to current tab
          button.classList.add('active');
          document.getElementById(tabId + '-tab').classList.add('active');
        });
      });
      
      // Toggle material cards
      const materialTitles = document.querySelectorAll('.material-title');
      materialTitles.forEach(title => {
        title.addEventListener('click', () => {
          const card = title.closest('.material-card');
          card.classList.toggle('expanded');
        });
      });
      
      // Module expansion
      const moduleHeaders = document.querySelectorAll('.module-header');
      moduleHeaders.forEach(header => {
        header.addEventListener('click', () => {
          const moduleCard = header.closest('.module-card');
          moduleCard.classList.toggle('expanded');
        });
      });
      
      // Video switching from lesson items
      const lessonItems = document.querySelectorAll('.lesson-item');
      const courseVideo = document.getElementById('course-video');
      
      lessonItems.forEach(item => {
        item.addEventListener('click', () => {
          const fileType = item.getAttribute('data-type');
          const filePath = item.getAttribute('data-file');
          
          // Remove active class from all items
          lessonItems.forEach(i => i.classList.remove('active'));
          
          // Add active class to clicked item
          item.classList.add('active');
          
          if (fileType === 'video' && courseVideo) {
            courseVideo.querySelector('source').src = filePath;
            courseVideo.load();
            courseVideo.play();
            
            // Scroll to video section
            document.querySelector('.video-section').scrollIntoView({
              behavior: 'smooth'
            });
          }
        });
      });
      
      // Play video buttons
      const playButtons = document.querySelectorAll('.play-video-btn');
      playButtons.forEach(button => {
        button.addEventListener('click', (e) => {
          e.preventDefault();
          const videoSrc = button.getAttribute('data-src');
          
          if (courseVideo) {
            courseVideo.querySelector('source').src = videoSrc;
            courseVideo.load();
            courseVideo.play();
            
            // Scroll to video section
            document.querySelector('.video-section').scrollIntoView({
              behavior: 'smooth'
            });
          }
        });
      });
    });
  </script>
</body>

</html>