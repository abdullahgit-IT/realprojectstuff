<?php
session_start();

/* =========================
   DATABASE CONNECTION
========================= */
$servername = "localhost";
$dbusername = "abdullahcode";
$dbpassword = "Sheffield1!?";
$dbname     = "osc";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

$message = "";

/* =========================
   LOGIN PROCESSING
========================= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login_user"])) {

    $email    = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($email === "" || $password === "") {
        $message = "All fields are required.";
    } else {
        // Prepare query
        $stmt = $conn->prepare("SELECT id, password, username FROM userosc WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($user_id, $storedPassword, $username);
            $stmt->fetch();

            // Verify hashed password
            if (password_verify($password, $storedPassword)) {

                // Set session
                $_SESSION['id'] = $user_id;
                $_SESSION['username'] = $username;

                // Redirect to dashboard
                header("Location: template.php");
                exit;

            } else {
                $message = "Incorrect password.";
            }

        } else {
            $message = "No account found with this email.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<link rel="stylesheet" href="logintemplate.css">
</head>

<body>

<div class="page-wrapper">

  <!-- HEADER -->
  <header class="header">
    <div class="logo">Nova Health Solutions</div>
  </header>

  <!-- LOGIN SECTION -->
  <section class="login-section">

    <div class="login-box">
      <h2>User Login</h2>

      <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>

      <form method="POST">

        <label>Email</label>
        <input type="email" name="email" placeholder="Enter your email" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter your password" required>

        <button type="submit" name="login_user">Login</button>

      </form>

      <p style="text-align:center; margin-top:10px;">
        <a href="forgotpasswordtemplate.php">Forgot Password?</a>
      </p>

      <p style="text-align:center; margin-top:15px;">
        Don't have an account?
        <a href="registertemplate.php">Register</a>
      </p>

    </div>

  </section>

  <!-- FOOTER -->
  <footer class="footer">
    © 2026 Nova Health Solutions. All rights reserved.
  </footer>

</div>

</body>
</html>