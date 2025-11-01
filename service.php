<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'db.php';
$conn = db_connect();

// Safe username display
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Services - AlinQ Laundry</title>
  <link rel="stylesheet" href="service.css">
</head>
<body>

  <nav class="navbar">
    <div class="container">
      <h2 class="logo">AlinQ Laundry</h2>
      <ul class="nav-links">
        <li><a href="index.html">Home</a></li>
        <li><a href="about.html">About</a></li>
        <li><a href="contact.html">Contact</a></li>
        <li><a href="service.php" class="active">Services</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </div>
  </nav>

  <section class="services-section">
    <div class="form-container">
      <h2>Welcome, <?php echo htmlspecialchars($username); ?> 👋</h2>
      <p>Select laundry services to book</p>

      <!-- Send data to price.php -->
      <form action="price.php" method="POST" class="service-form">
        <h2>Book a Laundry Pickup</h2>

        <div class="form-group">
          <label>Washing - Number of Clothes?</label>
          <input type="number" name="washing" value="0" min="0">
        </div>
        <div class="form-group">
          <label>Ironing - Number of Clothes?</label>
          <input type="number" name="ironing" value="0" min="0">
        </div>
        <div class="form-group">
          <label>Wash & Iron - Number of Clothes?</label>
          <input type="number" name="washiron" value="0" min="0">
        </div>

        <!-- Customer Info -->
        <div class="form-group">
          <label>Your Name</label>
          <input type="text" name="name" required>
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="text" name="phone" required>
        </div>
        <div class="form-group">
          <label>Address</label>
          <textarea name="address" required></textarea>
        </div>

        <button type="submit" name="book_service" class="btn">Submit Booking</button>
      </form>
    </div>
  </section>

  <footer class="footer">
    <p>&copy; 2025 AlinQ Laundry. All Rights Reserved.</p>
  </footer>

</body>
</html>