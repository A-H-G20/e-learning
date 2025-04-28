<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>IInstructor Dashboard</title>
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                  <a href="add_course.php" class="nav-link active">
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
    <div class="course-creation-panel">
        <div class="creation-header">
            <h2><i class="fas fa-plus-circle"></i> Create New Course</h2>
        </div>

        <div class="form-section">
            <h3 class="section-title"><i class="fas fa-universal-access"></i> Course Classification</h3>
            <div class="classification-types">
                <div class="type-card" data-type="normal">
                    <i class="fas fa-globe"></i>
                    <h4>Standard Course</h4>
                    <p>For general audiences without specific accessibility needs</p>
                </div>
                <div class="type-card" data-type="deaf">
                    <i class="fas fa-deaf"></i>
                    <h4>Deaf/HoH Focus</h4>
                    <p>Optimized for hearing impaired students</p>
                </div>
                <div class="type-card" data-type="blind">
                    <i class="fas fa-blind"></i>
                    <h4>Blind/Low Vision</h4>
                    <p>Designed for visually impaired students</p>
                </div>
            </div>
        </div>

        <form class="course-creation-form" method="POST" action="create_course.php" enctype="multipart/form-data" onsubmit="return validateForm()">
    <input type="hidden" name="classification" id="classification" value="normal">
    <input type="hidden" name="status" value="draft">

    <!-- Course Title, Description, Category fields -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-info-circle"></i> Course Information</h3>
        <div class="form-row">
            <label class="form-label">Course Title</label>
            <input type="text" class="form-input" name="title" required>
        </div>
        <div class="form-row">
            <label class="form-label">Course Description</label>
            <textarea class="form-input" name="description" rows="4" required></textarea>
        </div>
        <div class="form-row">
            <label class="form-label">Course Category</label>
            <input type="text" class="form-input" name="category" required>
        </div>
    </div>

    <!-- Accessibility Options -->
    <div class="form-section accessibility-features">
        <h3 class="section-title"><i class="fas fa-wheelchair"></i> Accessibility Settings</h3>
        <div class="accessibility-options">
            <!-- checkboxes inside deaf-options, blind-options, general-options -->
            <div class="option-group deaf-options">
                <label><input type="checkbox" name="include_sign_language"> Include Sign Language Interpretation</label><br>
                <label><input type="checkbox" name="add_captions"> Add Closed Captions to All Videos</label>
            </div>
            <div class="option-group blind-options">
                <label><input type="checkbox" name="audio_descriptions"> Audio Descriptions for Visual Content</label><br>
                <label><input type="checkbox" name="screen_reader"> Screen Reader Optimized Content</label>
            </div>
            <div class="option-group general-options">
                <label><input type="checkbox" name="keyboard_navigation"> Enable Keyboard Navigation</label><br>
                <label><input type="checkbox" name="high_contrast"> High Contrast Mode</label>
            </div>
        </div>
    </div>

    <!-- File Upload Section -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-file-upload"></i> Course Content</h3>
        <div class="content-upload">
            <div class="drag-drop-area">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Drag and drop files or <span>browse</span></p>
                <input type="file" id="fileUpload" name="course_files[]" multiple hidden required>
            </div>
        </div>
    </div>

    <!-- Publishing Options -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-rocket"></i> Publishing Options</h3>
        <div class="publishing-options">
            <div class="form-row">
                <label class="form-label">Course Visibility</label>
                <div class="visibility-toggle">
                    <span class="toggle-option active">Draft</span>
                    <span class="toggle-option">Published</span>
                </div>
            </div>
            <div class="form-row">
                <label class="form-label">Schedule Publication</label>
                <input type="datetime-local" class="form-input" name="scheduled_publish_time">
            </div>
        </div>
    </div>

    <!-- Form Submit -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Publish Course</button>
    </div>
</form>

<script>
function validateForm() {
    const fileInput = document.getElementById('fileUpload');
    if (fileInput.files.length === 0) {
        alert('Please upload at least one file before submitting.');
        return false;
    }
    return true;
}
</script>


        
    </div>
</div>
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
    document.addEventListener('DOMContentLoaded', () => {
        // Course Type Selection
        document.querySelectorAll('.type-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                const courseType = this.dataset.type;
                updateAccessibilityOptions(courseType);
            });
        });
    
        function updateAccessibilityOptions(type) {
            document.querySelectorAll('.option-group').forEach(group => {
                group.style.display = 'none';
            });
            
            document.querySelector(`.${type}-options`).style.display = 'block';
            document.querySelector('.general-options').style.display = 'block';
        }
    
        // Add Module/Lesson Functionality
        document.querySelectorAll('.btn-add').forEach(btn => {
            btn.addEventListener('click', function() {
                const newElement = document.createElement('div');
                newElement.className = 'lesson';
                newElement.innerHTML = `
                    <input type="text" class="form-input" placeholder="Lesson Title">
                    <select class="form-input">
                        <option>Video Lecture</option>
                        <option>Reading Material</option>
                        <option>Quiz</option>
                    </select>
                `;
                this.parentElement.insertBefore(newElement, this);
            });
        });
    
        document.querySelector('.drag-drop-area').addEventListener('click', () => {
            document.querySelector('input[type="file"]').click();
        });
    });
    document.querySelectorAll('.toggle-option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('.toggle-option').forEach(o => o.classList.remove('active'));
        this.classList.add('active');
        document.querySelector('input[name="status"]').value = this.textContent.toLowerCase();
    });
});

// Similarly, set the classification type
document.querySelectorAll('.type-card').forEach(card => {
    card.addEventListener('click', function() {
        document.getElementById('classification').value = this.dataset.type;
    });
});

    </script>
</body>
</html>