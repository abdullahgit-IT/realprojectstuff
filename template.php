<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nova Health Solutions</title>

<link rel="stylesheet" href="templatecss.css">
</head>

<body>

<div class="page-wrapper">

<!-- ===== NAVBAR ===== -->
<header class="header">
  <div class="logo">Nova Health Solutions</div>

  <nav class="navbar">
    <a href="home.php">Home</a>
    <a href="about.php">About</a>
    <a href="services.php">Services</a>
    <a href="contact.php">Contact</a>

    <?php if (!empty($_SESSION["user_id"])): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="logintemplate.php" class="btn-outline">Logout</a>
    <?php else: ?>
        <a href="logintemplate.php" class="btn-outline">Login</a>
    <?php endif; ?>
  </nav>
</header>

<!-- ===== HERO SECTION ===== -->
<section class="hero">
  <h1>PAGE TITLE HERE</h1>
  <p>Short description of this page.</p>
</section>

<!-- ===== MAIN CONTENT ===== -->
<section class="content">

  <h2>Section Heading</h2>

  <p>
    Replace this content with text for your page.
    This template works for:
    About page, Contact page, Services page,
    Dashboard page, or any website section.
  </p>

  <img src="images/sample.jpg" alt="Example" class="main-image">

</section>

<!-- ===== FEATURE CARDS ===== -->
<section class="cards">

  <div class="card">
    <h3>Feature One</h3>
    <p>Description of this feature.</p>
  </div>

  <div class="card">
    <h3>Feature Two</h3>
    <p>Description of this feature.</p>
  </div>

  <div class="card">
    <h3>Feature Three</h3>
    <p>Description of this feature.</p>
  </div>

</section>

</div>

<!-- ===== FOOTER ===== -->
<footer class="footer">
  <p>© 2026 Nova Health Solutions. All rights reserved.</p>
</footer>

</body>
</html>