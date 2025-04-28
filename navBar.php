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
        <a href="/" class="nav__link">Home</a>
        <a href="/" class="nav__link">Deaf Courses</a>
        <a href="/" class="nav__link">Blind Courses</a>
        <a href="/" class="nav__link">Normal Courses</a>
        <div class="nav__mobile-actions">
          <a href="/account" class="nav__link nav__icon-link">
            <i class="fas fa-user"></i>
          </a>
          <a href="/login" class="nav__button">Sign In</a>
        </div>
      </div>

      <div class="nav__actions">
        <a href="/account" class="nav__icon-link" aria-label="User Account">
          <i class="fas fa-user"></i>
        </a>
        <a href="/login" class="nav__button">Sign up</a>
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
