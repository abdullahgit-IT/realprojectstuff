<?php
/***************************************************
 * PROJECT AUTH TEMPLATE
 * Reusable for any company
 ***************************************************/

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

/* ========= COMPANY CONFIG ========= */
$COMPANY_NAME = "Nova Health Solutions";
$PRIMARY_COLOR = "#2e7d32";
$SECONDARY_COLOR = "#e8f5e9";
$LOGIN_REDIRECT = "dashboard.php";

/* ========= DATABASE CONFIG ========= */
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "project_db";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Database connection failed");
}
$conn->set_charset("utf8mb4");

$message = "";

/* ========= LOGOUT ========= */
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: auth.php");
    exit;
}

/* ========= REGISTER ========= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register"])) {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];

    if ($password !== $confirm) {
        $message = "Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hash);
            $stmt->execute();
            $message = "Registration successful. Please log in.";
            $stmt->close();
        }
        $check->close();
    }
}

/* ========= LOGIN ========= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($uid, $name, $hash);
        $stmt->fetch();

        if (password_verify($password, $hash)) {
            session_regenerate_id(true);
            $_SESSION["user_id"] = $uid;
            $_SESSION["user_name"] = $name;
            header("Location: $LOGIN_REDIRECT");
            exit;
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "Account not found.";
    }
    $stmt->close();
}

/* ========= CHANGE PASSWORD ========= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["change_password"])) {

    $current = $_POST["current"];
    $new = $_POST["new"];
    $confirm = $_POST["confirm_new"];

    if ($new !== $confirm) {
        $message = "New passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION["user_id"]);
        $stmt->execute();
        $stmt->bind_result($hash);
        $stmt->fetch();
        $stmt->close();

        if (!password_verify($current, $hash)) {
            $message = "Current password incorrect.";
        } else {
            $newHash = password_hash($new, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->bind_param("si", $newHash, $_SESSION["user_id"]);
            $update->execute();
            $update->close();
            $message = "Password updated successfully.";
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
<title><?= $COMPANY_NAME ?> – Authentication</title>

<style>
body {
    margin:0;
    font-family:Arial;
    background:<?= $SECONDARY_COLOR ?>;
}
header {
    background:<?= $PRIMARY_COLOR ?>;
    color:white;
    padding:16px;
    display:flex;
    justify-content:space-between;
}
a { color:white; text-decoration:none; margin-left:12px; }
.container {
    max-width:900px;
    margin:40px auto;
    background:white;
    padding:30px;
    border-radius:10px;
}
.forms {
    display:flex;
    gap:40px;
    justify-content:center;
    flex-wrap:wrap;
}
.form-box {
    width:300px;
}
input, button {
    width:100%;
    padding:10px;
    margin-top:10px;
}
button {
    background:<?= $PRIMARY_COLOR ?>;
    color:white;
    border:none;
    cursor:pointer;
}
.message {
    text-align:center;
    font-weight:bold;
    margin-bottom:20px;
}
</style>
</head>

<body>

<header>
    <strong><?= $COMPANY_NAME ?></strong>
    <nav>
        <?php if (!empty($_SESSION["user_id"])): ?>
            Welcome, <?= htmlspecialchars($_SESSION["user_name"]) ?>
            <a href="?logout=1">Logout</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">

<?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if (empty($_SESSION["user_id"])): ?>

<div class="forms">

<div class="form-box">
<h3>Register</h3>
<form method="POST">
<input name="name" placeholder="Full Name" required>
<input name="email" type="email" placeholder="Email" required>
<input name="password" type="password" placeholder="Password" required>
<input name="confirm" type="password" placeholder="Confirm Password" required>
<button name="register">Register</button>
</form>
</div>

<div class="form-box">
<h3>Login</h3>
<form method="POST">
<input name="email" type="email" placeholder="Email" required>
<input name="password" type="password" placeholder="Password" required>
<button name="login">Login</button>
</form>
</div>

</div>

<?php else: ?>

<div class="form-box" style="margin:0 auto;">
<h3>Change Password</h3>
<form method="POST">
<input type="password" name="current" placeholder="Current Password" required>
<input type="password" name="new" placeholder="New Password" required>
<input type="password" name="confirm_new" placeholder="Confirm New Password" required>
<button name="change_password">Update Password</button>
</form>
</div>

<?php endif; ?>

</div>
</body>
</html>
