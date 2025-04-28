<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Righteous&family=Secular+One&display=swap');
        </style>
  
  <link rel="stylesheet" href="index.css">
</head>
<body>

<?php  include 'navBar.php'; // Include the nav bar file?>
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Education Without Barriers</h1>
                <p>An inclusive learning platform designed for normal, blind, and deaf students with universal accessibility features.</p>
                <a href="#" class="cta-button">Get Started</a>
            </div>
            <div class="hero-image">
                <img src="" 
                     alt="Diverse group of students learning together" 
                     style="width: 100%; height: auto;">
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="section-title">
            <h2>Our Inclusive Features</h2>
            <p>Designed for universal accessibility and learning success</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-universal-access"></i>
                </div>
                <h3>Normal Course</h3>
                <p>Multi-format content delivery with using books </p>
                <button class="details-button" aria-label="View details about Universal Access" onclick="window.location.href='normal_course.php'">
                    View Details
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-hands"></i>
                </div>
                <h3>Deaf Course</h3>
                <p>Integrated videos for easier learning</p>
                <button class="details-button" aria-label="View details about Sign Language Support" onclick="window.location.href='deaf_course.php'">
                    View Details
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-braille"></i>
                </div>
                <h3>Blind Course</h3>
                <p>Integrated audio materials for blind students</p>
                <button class="details-button" aria-label="View details about Tactile Learning" onclick="window.location.href='blind_course.php'"> >
                    View Details
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </section>

   
   
    <?php  include 'footer.php'; // Include the footer file?>

    <script>
    const langButton = document.getElementById('langButton');
    const langDropdown = document.getElementById('langDropdown');
    let currentLang = 'en';

    langButton.addEventListener('click', function(e) {
        e.stopPropagation();
        langDropdown.classList.toggle('show');
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.language-switcher')) {
            langDropdown.classList.remove('show');
        }
    });

    document.querySelectorAll('.lang-option').forEach(option => {
        option.addEventListener('click', function() {
            const selectedLang = this.dataset.lang;
            currentLang = selectedLang;
            
            document.querySelectorAll('.lang-option').forEach(opt => {
                opt.querySelector('.checkmark').style.display = 'none';
            });
            this.querySelector('.checkmark').style.display = 'inline';
         
            console.log(`Language changed to: ${selectedLang}`);
  
        });
    });


    function handleRTL(isRTL) {
        document.documentElement.dir = isRTL ? 'rtl' : 'ltr';
        document.documentElement.lang = currentLang;
    }
    </script>
</body>
</html>