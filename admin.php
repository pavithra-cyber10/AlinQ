<?php
session_start();
require 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Database connection
$conn = db_connect();

// Check if user is admin
$uid = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$stmt->bind_result($is_admin);
$stmt->fetch();
$stmt->close();

if (!$is_admin) {
    // not admin
    header('HTTP/1.1 403 Forbidden');
    echo "403 Forbidden - Admins only.";
    exit();
}

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token mismatch.');
    }

    $order_id = intval($_POST['order_id']);
    $new_status = ($_POST['status'] === 'Done') ? 'Done' : 'Pending';

    $upd = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $upd->bind_param("si", $new_status, $order_id);
    $upd->execute();
    $upd->close();

    // redirect to avoid resubmission
    header("Location: admin.php");
    exit();
}

// Fetch all orders with user info
$query = "
    SELECT orders.id, users.username, orders.service_type, orders.quantity, orders.status, orders.created_at
    FROM orders 
    JOIN users ON orders.user_id = users.id
    ORDER BY orders.created_at DESC
";
$res = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Admin - All Orders | AlinQ Laundry</title>
  <link rel="stylesheet" href="admin.css" />
</head>
<body>
  <nav class="navbar">
    <div class="container">
      <h2 class="logo">AlinQ Laundry - Admin</h2>
      <ul class="nav-links">
        <li><a href="admin.php" class="active">Orders</a></li>
        <li><a href="services.php">Services</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </div>
  </nav>

  <section class="admin-section">
    <div class="form-container">
      <h2>All Customer Orders</h2>

      <?php if ($res->num_rows > 0): ?>
      <table class="orders-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Service</th>
            <th>Quantity</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php while ($row = $res->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['service_type']); ?></td>
            <td><?php echo (int)$row['quantity']; ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            <td>
              <form method="POST" class="status-form">
                <input type="hidden" name="order_id" value="<?php echo (int)$row['id']; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <select name="status">
                  <option value="Pending" <?php if ($row['status']=='Pending') echo 'selected'; ?>>Pending</option>
                  <option value="Done" <?php if ($row['status']=='Done') echo 'selected'; ?>>Done</option>
                </select>
                <button type="submit" name="update_status" class="btn">Update</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
      <?php else: ?>
        <p>No orders found.</p>
      <?php endif; ?>
    </div>
  </section>

  <footer class="footer">
    <p>&copy; 2025 AlinQ Laundry. All Rights Reserved.</p>
  </footer>
</body>
</html>

<?php
$res->close();
$conn->close();
?>