<?php
require 'config.php'; // Database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Validation function for password
function validatePassword($password) {
    if (strlen($password) < 8) return "Password must be at least 8 characters.";
    if (!preg_match('/[A-Z]/', $password)) return "Password must include at least one uppercase letter.";
    if (!preg_match('/[a-z]/', $password)) return "Password must include at least one lowercase letter.";
    if (!preg_match('/\\d/', $password)) return "Password must include at least one digit.";
    return true;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $accessibility = $_POST['accessibility'] ?? 'normal';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['cpassword'] ?? '';

    $mail = new PHPMailer(true);

    try {
        if (empty($username) || empty($email) || empty($phone) || empty($password) || empty($confirmPassword)) {
            $error = "All fields are required.";
        } elseif ($password !== $confirmPassword) {
            $error = "Passwords do not match.";
        } else {
            $passwordValidation = validatePassword($password);
            if ($passwordValidation !== true) {
                $error = $passwordValidation;
            } else {
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone_number = ?");
                $stmt->bind_param("ss", $email, $phone);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $error = "Email or Phone Number already exists.";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

                    // Insert user
                    $stmt = $conn->prepare("
                        INSERT INTO users (name, email, role, gender, phone_number, password, verification_code)
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->bind_param("sssssss", $username, $email, $accessibility, $accessibility, $phone, $hashed_password, $verification_code);

                    if ($stmt->execute()) {
                        // Send verification email
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'ahmadghosen20@gmail.com'; // Replace with your email
                        $mail->Password = 'bbievwnemblpxuqt'; // Replace with your password or app password

                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        $mail->setFrom('your_email@gmail.com', 'E-Learn Platform');
                        $mail->addAddress($email, $username);
                        $mail->isHTML(true);
                        $mail->Subject = 'Verify Your Email';
                        $mail->Body = "
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto;'>
                            <h2 style='color: #4b6cfb;'>E-Learn Email Verification</h2>
                            <p>Hello <b>{$username}</b>,</p>
                            <p>Thank you for registering! Your role is: <b>{$accessibility}</b>.</p>
                            <p>Please use the following verification code:</p>
                            <h3 style='color: #f05423;'>{$verification_code}</h3>
                            <p>If you did not register, please ignore this email.</p>
                        </div>";

                        $mail->send();

                        header("Location: email-verification.php?email=" . urlencode($email));
                        exit();
                    } else {
                        $error = "Something went wrong: " . $stmt->error;
                    }
                }
                $stmt->close();
            }
        }
    } catch (Exception $e) {
        $error = "Mailer Error: {$mail->ErrorInfo}";
    } catch (mysqli_sql_exception $e) {
        $error = "Database Error: {$e->getMessage()}";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Signup</title>

  <!-- External CSS & Font Links -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <link rel="stylesheet" href="Css/signUp.css" />
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Righteous&family=Secular+One&display=swap');
  </style>
</head>
<body>
<header class="small-header">
  <div class="nav__logo">
    <h2>E-Learn</h2>
  </div>
</header>

<main class="login-container">
  <section class="form-section">
    <header class="login-header">
      <h1>Sign Up</h1>
      <p>Welcome to our website! Please sign up to create an account.</p>
    </header>

    <?php if (!empty($error)): ?>
      <div class="error-message" style="color:red; text-align:center; margin-bottom:10px;"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autocomplete="username" />
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="text" id="email" name="email" required autocomplete="email" />
      </div>

      <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="text" id="phone" name="phone" required autocomplete="tel" />
      </div>

      <div class="form-group">
        <label>Accessibility Needs</label>
        <div class="accessibility-options">
          <label class="accessibility-option normal">
            <input type="radio" name="accessibility" value="normal" checked hidden>
            <div class="option-content">
              <i class="fas fa-user"></i>
              <span>Standard</span>
            </div>
          </label>
          <label class="accessibility-option deaf">
            <input type="radio" name="accessibility" value="deaf" hidden>
            <div class="option-content">
              <i class="fas fa-deaf"></i>
              <span>Deaf/Hard of Hearing</span>
            </div>
          </label>
          <label class="accessibility-option blind">
            <input type="radio" name="accessibility" value="blind" hidden>
            <div class="option-content">
              <i class="fas fa-blind"></i>
              <span>Blind/Low Vision</span>
            </div>
          </label>
        </div>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <div class="password-input">
          <input type="password" id="password" name="password" required autocomplete="new-password" />
          <span class="password-toggle" onclick="togglePassword()"><i class="fas fa-eye"></i></span>
        </div>
      </div>

      <div class="form-group">
        <label for="cpassword">Confirm Password</label>
        <div class="password-input">
          <input type="password" id="cpassword" name="cpassword" required autocomplete="new-password" />
        </div>
      </div>

      <button type="submit">Sign Up</button>

      <footer class="register-link">
        <p>Already have an account? <a href="login.php">Login now</a></p>
      </footer>
    </form>
  </section>
</main>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.querySelector('.password-toggle i');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}
</script>
</body>
</html>
