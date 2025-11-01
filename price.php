<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "alinq";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$prices = ["washing" => 30, "ironing" => 20, "washiron" => 50];
$total = 0; 
$summary = [];
$name = $phone = $address = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_service'])) {
    $name = htmlspecialchars($_POST['name']);
    $phone = htmlspecialchars($_POST['phone']);
    $address = htmlspecialchars($_POST['address']);
    $user_id = $_SESSION['user_id'];

    $washing_qty = isset($_POST['washing']) ? intval($_POST['washing']) : 0;
    $ironing_qty = isset($_POST['ironing']) ? intval($_POST['ironing']) : 0;
    $washiron_qty = isset($_POST['washiron']) ? intval($_POST['washiron']) : 0;

    // Calculate & prepare summary + Insert into DB
    if ($washing_qty > 0) {
        $amount = $washing_qty * $prices['washing'];
        $total += $amount;
        $summary[] = ["Washing", $washing_qty, $prices['washing'], $amount];

        $stmt = $conn->prepare("INSERT INTO orders (user_id, service_type, quantity) VALUES (?, ?, ?)");
        $service = "Washing";
        $stmt->bind_param("isi", $user_id, $service, $washing_qty);
        $stmt->execute();
    }

    if ($ironing_qty > 0) {
        $amount = $ironing_qty * $prices['ironing'];
        $total += $amount;
        $summary[] = ["Ironing", $ironing_qty, $prices['ironing'], $amount];

        $stmt = $conn->prepare("INSERT INTO orders (user_id, service_type, quantity) VALUES (?, ?, ?)");
        $service = "Ironing";
        $stmt->bind_param("isi", $user_id, $service, $ironing_qty);
        $stmt->execute();
    }

    if ($washiron_qty > 0) {
        $amount = $washiron_qty * $prices['washiron'];
        $total += $amount;
        $summary[] = ["Wash & Iron", $washiron_qty, $prices['washiron'], $amount];

        $stmt = $conn->prepare("INSERT INTO orders (user_id, service_type, quantity) VALUES (?, ?, ?)");
        $service = "Wash & Iron";
        $stmt->bind_param("isi", $user_id, $service, $washiron_qty);
        $stmt->execute();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Booking Summary - AlinQ Laundry</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      background:#f9f9f9;
      font-family:'Poppins',sans-serif;
    }
    .summary-container {
      max-width:900px;
      margin:40px auto;
      padding:30px;
      background:#fff;
      border-radius:12px;
      box-shadow:0 6px 15px rgba(0,0,0,0.1);
    }
    table {
      width:100%;
      border-collapse:collapse;
      margin-top:20px;
    }
    th,td {
      padding:12px;
      text-align:center;
      border-bottom:1px solid #eee;
    }
    th {
      background:linear-gradient(135deg,#42a5f5,#7e57c2);
      color:white;
    }
    .total-row td {
      font-weight:bold;
      background:#f1f1f1;
    }
    .btn {
      display:inline-block;
      margin-top:20px;
      padding:12px 24px;
      background:#42a5f5;
      color:white;
      text-decoration:none;
      border-radius:8px;
      border:none;
      cursor:pointer;
    }
    .btn:hover {
      background:#7e57c2;
    }
  </style>
</head>
<body>
  <div class="summary-container">
    <h2>Booking Summary</h2>
    <p><strong>Name:</strong> <?php echo $name; ?></p>
    <p><strong>Phone:</strong> <?php echo $phone; ?></p>
    <p><strong>Address:</strong> <?php echo $address; ?></p>

    <?php if (count($summary) > 0) { ?>
      <table>
        <tr><th>Service</th><th>Quantity</th><th>Price per Item</th><th>Amount</th></tr>
        <?php foreach ($summary as $row) { ?>
          <tr>
            <td><?php echo $row[0]; ?></td>
            <td><?php echo $row[1]; ?></td>
            <td>₹<?php echo $row[2]; ?></td>
            <td>₹<?php echo $row[3]; ?></td>
          </tr>
        <?php } ?>
        <tr class="total-row"><td colspan="3">Total Amount</td><td>₹<?php echo $total; ?></td></tr>
      </table>

      <!-- Payment Redirect -->
      <form action="payment.php" method="POST">
        <input type="hidden" name="total" value="<?php echo $total; ?>">
        <input type="hidden" name="name" value="<?php echo $name; ?>">
        <input type="hidden" name="phone" value="<?php echo $phone; ?>">
        <input type="hidden" name="address" value="<?php echo $address; ?>">
        <button type="submit" class="btn">Proceed to Payment</button>
      </form>
    <?php } else { ?>
      <p><em>No services selected.</em></p>
      <a href="services.php" class="btn">Back to Services</a>
    <?php } ?>
  </div>
</body>
</html>