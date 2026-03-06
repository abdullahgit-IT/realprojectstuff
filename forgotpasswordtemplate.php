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
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";
$messageClass = "";

/* =========================
   FORM PROCESS
========================= */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $newPassword = trim($_POST["new_password"] ?? "");
    $confirmPassword = trim($_POST["confirm_password"] ?? "");

    if ($email === "" || $newPassword === "" || $confirmPassword === "") {
        $message = "All fields are required.";
        $messageClass = "error";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $messageClass = "error";
    }
    elseif (strlen($newPassword) < 6) {
        $message = "Password must be at least 6 characters.";
        $messageClass = "error";
    }
    elseif ($newPassword !== $confirmPassword) {
        $message = "Passwords do not match.";
        $messageClass = "error";
    } else {

        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE userosc SET password=? WHERE email=?");
        $stmt->bind_param("ss", $hashedPassword, $email);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $message = "Password successfully updated. You can now <a href='logintemplate.php'>login</a>.";
            $messageClass = "success";
        } else {
            $message = "No account found with that email.";
            $messageClass = "error";
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
<title>Reset Password</title>
<link rel="stylesheet" href="forgotpasswordtemplate.css">
</head>

<body>

<div class="login-box">

<h2>Reset Password</h2>

<?php if (!empty($message)) { ?>
<div class="message <?= $messageClass ?>">
    <?= $message ?>
</div>
<?php } ?>

<form method="POST">

<label>Email</label>
<input type="email" name="email" placeholder="Enter your registered email" required>

<label>New Password</label>
<input type="password" name="new_password" placeholder="Enter new password" required>

<label>Confirm Password</label>
<input type="password" name="confirm_password" placeholder="Confirm new password" required>

<button type="submit">Reset Password</button>

</form>

<p style="text-align:center; margin-top:15px;">
<a href="logintemplate.php">Back to Login</a>
</p>

</div>

</body>
</html>