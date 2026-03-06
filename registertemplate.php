<?php
session_start();

/* ===== DATABASE CONNECTION ===== */
$servername = "localhost";
$dbusername = "abdullahcode";
$dbpassword = "Sheffield1!?";
$dbname     = "osc";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

$message = "";

/* ===== HANDLE REGISTER ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register_user"])) {

    $username = trim($_POST["username"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    /* ===== VALIDATION ===== */
    if ($username === "" || strlen($username) < 3) {
        $message = "Username must be at least 3 characters.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long.";
    } elseif (!preg_match("/[A-Z]/", $password)) {
        $message = "Password must contain at least one uppercase letter.";
    } elseif (!preg_match("/[0-9]/", $password)) {
        $message = "Password must contain at least one number.";
    } else {
        /* ===== CHECK IF EMAIL EXISTS ===== */
        $check = $conn->prepare("SELECT id FROM userosc WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Email already registered.";
        } else {
            /* ===== HASH PASSWORD ===== */
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            /* ===== INSERT NEW USER ===== */
            $stmt = $conn->prepare("INSERT INTO userosc (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashedPassword);

            if ($stmt->execute()) {
                /* ===== GET GENERATED ID ===== */
                $user_id = $conn->insert_id;

                /* ===== SUCCESS MESSAGE ===== */
                $message = "Registration successful! Your user ID is: <strong>$user_id</strong>. Please <a href='logintemplate.php'>login here</a>.";

            } else {
                $message = "Registration failed. Please try again.";
            }

            $stmt->close();
        }

        $check->close();
    }
}

$conn->close();
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
<div class="message"><?= $message ?></div>
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