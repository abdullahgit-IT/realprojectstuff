<?php
session_start();

/* ===== DATABASE CONNECTION ===== *//////// CHANGE
$servername = "localhost";
$username   = "abdullahcode";
$password   = "Sheffield1!?";
$dbname     = "riget zoo";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("DB connection failed: " . htmlspecialchars($conn->connect_error));
}

$message = "";

/* ===== HANDLE REGISTER ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register_user"])) {

    $username = trim($_POST["username"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username === "" || $email === "" || $password === "") {
        $message = "All fields are required.";
    } else {

        /* check if email already exists */
        $check = $conn->prepare("SELECT id FROM usertable WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Email already registered.";
        } else {

            /* insert new user */
            $stmt = $conn->prepare("INSERT INTO usertable (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password);

            if ($stmt->execute()) {
                header("Location: logintemplate.php"); 
                exit;
            } else {
                $message = "Registration failed.";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>

<link rel="stylesheet" href="registercss.css">
</head>

<body>

<div class="page-wrapper">

<header class="header">
  <div class="logo">Nova Health Solutions</div>
</header>

<section class="login-section">

  <div class="login-box">
    <h2>Create Account</h2>

    <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">

      <input type="text" name="username" placeholder="Username" required>

      <input type="email" name="email" placeholder="Email" required>

      <input type="password" name="password" placeholder="Password" required>

      <button type="submit" name="register_user">Register</button>

    </form>

    <p style="margin-top:15px; text-align:center;">
      Already have an account?
      <a href="logintemplate.php">Login</a>   
    </p>

  </div>

</section>

</div>

<footer class="footer">
  <p>© 2026 Nova Health Solutions</p>
</footer>

</body>
</html>