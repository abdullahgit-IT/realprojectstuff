<?php
session_start();

/* =========================
   DATABASE CONNECTION
========================= */
$servername = "localhost";
$username   = "abdullahcode";
$password   = "Sheffield1!?";
$dbname     = "riget zoo";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("DB connection failed");
}

$message = "";

/* =========================
   LOGIN PROCESSING (PUT HERE)
   This runs BEFORE HTML loads
========================= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login_user"])) {

    $username = trim($_POST["username"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username === "" || $email === "" || $password === "") {
        $message = "All fields are required.";
    } else {

        $stmt = $conn->prepare("SELECT id, password FROM usertable WHERE username = ? AND email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($user_id, $storedPassword);
            $stmt->fetch();

            /* ✅ REDIRECT AFTER SUCCESSFUL LOGIN */
            if ($password === $storedPassword) {

                $_SESSION['user_id'] = $user_id;

                header("Location: template.php"); // ← CHANGE THIS PAGE
                exit;

            } else {
                $message = "Incorrect password.";
            }

        } else {
            $message = "No matching user found.";
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
    <div class="logo">Your Company</div>
  </header>

  <!-- LOGIN SECTION -->
  <section class="login-section">

    <div class="login-box">
      <h2>User Login</h2>

      <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>

      <form method="POST">

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" name="login_user">Login</button>

      </form>

      <p style="text-align:center; margin-top:15px;">
        Don't have an account?
        <a href="registertemplate.php">Register</a>
      </p>

    </div>

  </section>

  <!-- FOOTER -->
  <footer class="footer">
    © 2025 Your Company. All rights reserved.
  </footer>

</div>

</body>
</html>