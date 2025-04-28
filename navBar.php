<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Navigation Bar</title>
    <link rel="stylesheet" href="css/navBar.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Righteous&family=Secular+One&display=swap');
      </style>
  </head>
  <body>
    <nav class="nav">
      <div class="nav__logo">
    <h2>E-Learn</h2>
      </div>

      <div class="nav__links">
        <a href="index.php" class="nav__link">Home</a>
        
        
<?php if (isset($_SESSION['user_id'])): ?>
    <a href="deaf_course.php" class="nav__link">Deaf Courses</a>
<?php endif; ?>
<?php if (isset($_SESSION['user_id'])): ?>
        <a href="blind_course" class="nav__link">Blind Courses</a>
<?php endif; ?>
<?php if (isset($_SESSION['user_id'])): ?>
        <a href="normal_course.php" class="nav__link">Normal Courses</a>
<?php endif; ?>
        <a href="aboutUs.php" class="nav__link">About us</a>
        <div class="nav__mobile-actions">
        <?php if (isset($_SESSION['user_id'])): ?>
    <a href="account_details.php" class="nav__icon-link" aria-label="User Account">
        <i class="fas fa-user"></i>
    </a>
<?php endif; ?>
         

<?php if (isset($_SESSION['user_id'])): ?>
    <a href="logout.php" class="nav__button">Logout</a>
<?php else: ?>
    <a href="signIn.php" class="nav__button">Sign In</a>
<?php endif; ?>

        </div>
      </div>

      <div class="nav__actions">
      <?php if (isset($_SESSION['user_id'])): ?>
    <a href="account_details.php" class="nav__icon-link" aria-label="User Account">
        <i class="fas fa-user"></i>
    </a>
<?php endif; ?>
        

<?php if (isset($_SESSION['user_id'])): ?>
    <a href="logout.php" class="nav__button">Logout</a>
<?php else: ?>
    <a href="signIn.php" class="nav__button">Sign In</a>
<?php endif; ?>

        <button class="nav__hamburger" aria-label="Toggle menu">☰</button>
      </div>
    </nav>

    <script>
      const hamburger = document.querySelector(".nav__hamburger");
      const navLinks = document.querySelector(".nav__links");
      const isMobile = () => window.innerWidth <= 768;


      let lastWidth = window.innerWidth;
      window.addEventListener("resize", () => {
        if (
          (lastWidth <= 768 && window.innerWidth > 768) ||
          (lastWidth > 768 && window.innerWidth <= 768)
        ) {
          resetMenus();
          navLinks.classList.remove("nav__links--active");
        }
        lastWidth = window.innerWidth;
      });

      hamburger.addEventListener("click", () => {
        navLinks.classList.toggle("nav__links--active");
      });

     


      const mediaQuery = window.matchMedia("(min-width: 769px)");
      let hoverListeners = new Map();



      mediaQuery.addListener(handleHoverEffects);
      handleHoverEffects(mediaQuery);

      document.addEventListener("click", (e) => {
        if (!e.target.closest(".nav")) {
          navLinks.classList.remove("nav__links--active");
          resetMenus();
        }
      });
    </script>
  </body>
</html>
