<?php
session_start();

/* ===== CHECK LOGIN ===== */
if (!isset($_SESSION['user_id'])) {
    header("Location: logintemplate.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* ===== DATABASE CONNECTION ===== */
$servername = "localhost";
$dbusername = "abdullahcode";
$dbpassword = "Sheffield1!?";
$dbname     = "osc";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) die("DB connection failed: " . $conn->connect_error);

$message = "";

/* ===== HANDLE BOOKING ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["book_submit"])) {

    $service      = trim($_POST["service"] ?? "");
    $booking_date = trim($_POST["booking_date"] ?? "");
    $booking_time = trim($_POST["booking_time"] ?? "");

    if ($service === "" || $booking_date === "" || $booking_time === "") {
        $message = "All fields are required.";
    } else {

        // Insert booking
        $stmt = $conn->prepare("INSERT INTO bookingg (id, service, booking_date, booking_time) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $service, $booking_date, $booking_time);

        if ($stmt->execute()) {
            $message = "Booking successful!";
        } else {
            $message = "Booking failed: " . $stmt->error;
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
<title>Book Service</title>
<link rel="stylesheet" href="bookingtemplate.css">
</head>
<body>
<div class="page-wrapper">

<header class="header">
  <div class="logo">Nova Health Solutions</div>
  <nav class="navbar">
    <a href="home.php">Home</a>
    <a href="bookingtemplate.php">Services</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="logintemplate.php">Logout</a>
  </nav>
</header>

<section class="hero">
  <h1>Book a Professional Session</h1>
  <p>Choose a service, date, and time for your appointment.</p>
</section>

<section class="form-section">
  <div class="form-box">
    <h2>Book Appointment</h2>

    <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Service</label>
      <select name="service" required>
        <option value="">Select Service</option>
        <option value="Consultation">Consultation</option>
        <option value="Fitness Session">Fitness Session</option>
        <option value="Mental Wellness Session">Mental Wellness Session</option>
      </select>

      <label>Date</label>
      <input type="date" name="booking_date" required min="<?= date('Y-m-d') ?>">

      <label>Time</label>
      <input type="time" name="booking_time" required>

      <button type="submit" name="book_submit">Submit Booking</button>
    </form>
  </div>
</section>

<footer class="footer">
  © 2026 Nova Health Solutions
</footer>
</div>
</body>
</html>