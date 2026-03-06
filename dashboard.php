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
$username   = "abdullahcode";
$password   = "Sheffield1!?";
$dbname     = "osc";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Database connection failed: " . $conn->connect_error);

$message = "";

/* ===== CANCEL BOOKING ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cancel_booking'])) {
    $service      = $_POST['service'] ?? '';
    $booking_date = $_POST['booking_date'] ?? '';
    $booking_time = $_POST['booking_time'] ?? '';

    if ($service && $booking_date && $booking_time) {
        $stmtCancel = $conn->prepare(
            "DELETE FROM bookingg WHERE id=? AND service=? AND booking_date=? AND booking_time=? LIMIT 1"
        );
        $stmtCancel->bind_param("isss", $user_id, $service, $booking_date, $booking_time);
        if ($stmtCancel->execute()) {
            $message = "Booking canceled successfully!";
        } else {
            $message = "Failed to cancel booking.";
        }
        $stmtCancel->close();
    }
}

/* ===== FETCH BOOKINGS ===== */
$stmt = $conn->prepare("SELECT service, booking_date, booking_time FROM bookingg WHERE id=? ORDER BY booking_date, booking_time");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard</title>
<style>
body { margin:0; font-family:Arial; background:#0f0f0f; color:white; }
.header { background:#1a1a1a; padding:20px; display:flex; justify-content:space-between; align-items:center; }
.logo { font-size:20px; font-weight:bold; }
.nav a { margin-left:15px; color:white; text-decoration:none; background:#2d2d2d; padding:8px 14px; border-radius:6px; }
.nav a:hover { background:#444; }
.dashboard { max-width:1000px; margin:auto; padding:40px 20px; }
.dashboard h2 { margin-bottom:30px; }
.cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:20px; }
.card { background:#1c1c1c; padding:20px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.5); }
.card h3 { margin-bottom:10px; }
.card p { color:#ccc; margin:5px 0; }
.card form { margin-top:10px; }
.card button { padding:8px 12px; border:none; border-radius:6px; background:#aa2222; color:white; cursor:pointer; transition:0.2s; }
.card button:hover { background:#cc3333; }
.empty { background:#1c1c1c; padding:20px; border-radius:10px; text-align:center; }
.message { background:#2d2d2d; padding:12px; border-radius:6px; margin-bottom:20px; text-align:center; color:#fff; }
</style>
</head>
<body>

<header class="header">
<div class="logo">Nova Health Solutions</div>
<div class="nav">
<a href="bookingtemplate.php">Book Service</a>
<a href="logintemplate.php">Logout</a>
</div>
</header>

<div class="dashboard">
<h2>Your Bookings</h2>

<?php if (!empty($message)): ?>
<div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if ($result->num_rows > 0): ?>
<div class="cards">
<?php while($row = $result->fetch_assoc()): ?>
<div class="card">
<h3><?= htmlspecialchars($row['service']) ?></h3>
<p>Date: <?= htmlspecialchars($row['booking_date']) ?></p>
<p>Time: <?= htmlspecialchars($row['booking_time']) ?></p>

<form method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
<input type="hidden" name="service" value="<?= htmlspecialchars($row['service']) ?>">
<input type="hidden" name="booking_date" value="<?= htmlspecialchars($row['booking_date']) ?>">
<input type="hidden" name="booking_time" value="<?= htmlspecialchars($row['booking_time']) ?>">
<button type="submit" name="cancel_booking">Cancel Booking</button>
</form>
</div>
<?php endwhile; ?>
</div>
<?php else: ?>
<div class="empty">
<p>You have no bookings yet.</p>
</div>
<?php endif; ?>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>