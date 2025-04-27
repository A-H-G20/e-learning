<?php
session_start();
include 'config.php'; // Database connection file

// Check if the 'email' parameter is passed in the URL
if (isset($_GET['email'])) {
    $userEmail = $_GET['email']; // Get the email from the URL
} else {
    die("Email parameter is missing.");
}

if (isset($_POST['verify_email'])) {
    $enteredCode = $_POST['code1'] . $_POST['code2'] . $_POST['code3'] . $_POST['code4'] . $_POST['code5'] . $_POST['code6'];

    // Retrieve the verification code stored in the database for the given email
    $sql = "SELECT verification_code, verified FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result) {
        $storedCode = $result['verification_code'];
        $verified = $result['verified'];

        if ($verified) {
            echo "Your email is already verified.";
        } else {
            if ($enteredCode == $storedCode) {
                // Update the user as verified and set the current timestamp
                $currentDate = date("Y-m-d H:i:s");
                $updateSql = "UPDATE users SET verified = 1, verified_at = ? WHERE email = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("ss", $currentDate, $userEmail);
                $updateStmt->execute();

                // Nullify the verification code in the database
                $nullifyCodeSql = "UPDATE users SET verification_code = NULL WHERE email = ?";
                $nullifyStmt = $conn->prepare($nullifyCodeSql);
                $nullifyStmt->bind_param("s", $userEmail);
                $nullifyStmt->execute();

                header("Location: signIn.php");
                exit();
            } else {
                echo "Invalid verification code. Please try again.";
            }
        }
    } else {
        echo "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Verify</title>
  <link rel="stylesheet" href="./css/verify.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />
  <link href="photos/logo.png" rel="icon" />
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Righteous&family=Secular+One&display=swap');
  </style>
</head>
<body>
  <div class="opt-form">
    <div class="header-form">
      <div class="auth-icon">
        <i class="fa-regular fa-bell"></i>
      </div>
      <h4>OTP Verification</h4>
      <p>Please enter the verification code sent to your email.</p>
    </div>
    <form action="" method="POST">
      <input type="hidden" name="email" value="<?php echo htmlspecialchars($userEmail); ?>" />
      <div class="auth-pin-wrap">
        <input type="number" name="code1" class="code-input" required />
        <input type="number" name="code2" class="code-input" required />
        <input type="number" name="code3" class="code-input" required />
        <input type="number" name="code4" class="code-input" required />
        <input type="number" name="code5" class="code-input" required />
        <input type="number" name="code6" class="code-input" required />
      </div>
      <div class="btn-wrap">
        <button type="submit" name="verify_email">Confirm</button>
      </div>
    </form>
  </div>
  <script>
    const inputs = document.querySelectorAll(".code-input");

    inputs.forEach((input, index) => {
      input.addEventListener("input", (e) => {
        const value = e.target.value;
        if (value.length > 1) e.target.value = value.slice(0, 1); // Only allow 1 digit
        if (value && index < inputs.length - 1) inputs[index + 1].focus(); // Focus next
      });

      input.addEventListener("keydown", (e) => {
        if (e.key === "Backspace" && !e.target.value && index > 0) inputs[index - 1].focus();
      });
    });
  </script>
</body>
</html>
