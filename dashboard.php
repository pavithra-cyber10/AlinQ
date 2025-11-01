<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$conn = db_connect();
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, service_type, quantity, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>My Orders - AlinQ Laundry</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
  <nav class="navbar">
    <div class="container">
      <h2 class="logo">AlinQ Laundry</h2>
      <ul class="nav-links">
        <li><a href="services.php">Services</a></li>
        <li><a href="dashboard.php" class="active">My Orders</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </div>
  </nav>

  <section class="dashboard-section">
    <div class="form-container">
      <h2>My Booked Services</h2>
      <p>Below are your bookings and their current status.</p>

      <table class="orders-table">
        <thead>
          <tr>
            <th>Service</th>
            <th>Quantity</th>
            <th>Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
              <td><?php echo htmlspecialchars($row['service_type']); ?></td>
              <td><?php echo (int)$row['quantity']; ?></td>
              <td><?php echo htmlspecialchars($row['status']); ?></td>
              <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>

      <a class="btn" href="services.php">Book More Services</a>
    </div>
  </section>

  <footer class="footer">
    <p>&copy; 2025 AlinQ Laundry</p>
  </footer>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>