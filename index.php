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
                <h3>Normal Access</h3>
                <p>Multi-format content delivery with using books </p>
                <button class="details-button" aria-label="View details about Universal Access">
                    View Details
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-hands"></i>
                </div>
                <h3>Deaf Access</h3>
                <p>Integrated videos for easier learning</p>
                <button class="details-button" aria-label="View details about Sign Language Support">
                    View Details
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-braille"></i>
                </div>
                <h3>Blind Access</h3>
                <p>Integrated audio materials for blind students</p>
                <button class="details-button" aria-label="View details about Tactile Learning">
                    View Details
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </section>

    <section class="courses" id="courses">
        <div class="section-title">
            <h2>Specialized Learning Paths</h2>
            <p>Tailored courses for different learning needs</p>
        </div>
    
        <div class="course-category" aria-labelledby="deaf-courses">
            <div class="category-header">
                <i class="fas fa-deaf" aria-hidden="true"></i>
                <h3 id="deaf-courses">Courses for Deaf Students</h3>
            </div>
            <div class="courses-grid">
                <article class="course-card">
                    <div class="course-image" style="background-image: url('')">
                        <span class="accessibility-tag">Sign Language Available</span>
                    </div>
                    <div class="course-content">
                        <h4>Visual Communication Mastery</h4>
                        <p>Learn through video tutorials with closed captions and sign language interpreters</p>
                        <div class="course-meta">
                            <span class="duration"><i class="fas fa-clock"></i> 15 Hours</span>
                            <span class="level"><i class="fas fa-signal"></i> Beginner</span>
                        </div>
                    </div>
                </article>
            </div>
            <button class="view-more-btn" data-category="deaf">
                View More
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
    
        <div class="course-category" aria-labelledby="blind-courses">
            <div class="category-header">
                <i class="fas fa-blind" aria-hidden="true"></i>
                <h3 id="blind-courses">Courses for Blind Students</h3>
            </div>
            <div class="courses-grid">
                <article class="course-card">
                    <div class="course-image" style="background-image: url('')">
                        <span class="accessibility-tag">Audio Described</span>
                    </div>
                    <div class="course-content">
                        <h4>Audio-Based Programming</h4>
                        <p>Learn coding through audio lessons and screen reader-friendly materials</p>
                        <div class="course-meta">
                            <span class="duration"><i class="fas fa-clock"></i> 20 Hours</span>
                            <span class="level"><i class="fas fa-signal"></i> Intermediate</span>
                        </div>
                    </div>
                </article>
            </div>
            <button class="view-more-btn" data-category="deaf">
                View More
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
    
        <div class="course-category" aria-labelledby="general-courses">
            <div class="category-header">
                <i class="fas fa-universal-access" aria-hidden="true"></i>
                <h3 id="general-courses">General Courses</h3>
            </div>
            <div class="courses-grid">
                <article class="course-card">
                    <div class="course-image" style="background-image: url('')">
                        <span class="accessibility-tag">Multi-format</span>
                    </div>
                    <div class="course-content">
                        <h4>Inclusive Design Fundamentals</h4>
                        <p>Universal design principles for all learners</p>
                        <div class="course-meta">
                            <span class="duration"><i class="fas fa-clock"></i> 10 Hours</span>
                            <span class="level"><i class="fas fa-signal"></i> All Levels</span>
                        </div>
                    </div>
                </article>
            </div>
            <button class="view-more-btn" data-category="deaf">
                View More
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
    </section>
    <div class="language-switcher">
        <button class="lang-button" id="langButton" aria-label="Change language">
            <i class="fas fa-language"></i>
        </button>
        <div class="lang-dropdown" id="langDropdown">
            <button class="lang-option" data-lang="en">
                English <span class="checkmark">✓</span>
            </button>
            <button class="lang-option" data-lang="es">
                Español
            </button>
            <button class="lang-option" data-lang="fr">
                Français
            </button>
            <button class="lang-option" data-lang="ar">
                العربية
            </button>
        </div>
    </div>


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