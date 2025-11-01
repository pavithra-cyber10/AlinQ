<?php
// success.php
$payment_id = isset($_GET['payment_id']) ? trim($_GET['payment_id']) : '';
$amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 0;
$order = isset($_GET['order']) ? htmlspecialchars($_GET['order']) : '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Payment Success - AlinQ Laundry</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f0fff0; text-align:center; padding:60px; }
    .card { display:inline-block; padding:28px 36px; background:#fff; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.08); }
    h1 { color:#28a745; margin:0 0 10px; }
    p { color:#333; margin:6px 0; }
    a.btn { display:inline-block; margin-top:14px; padding:10px 18px; background:#28a745; color:#fff; border-radius:8px; text-decoration:none; }
  </style>
</head>
<body>
  <div class="card">
    <h1>✅ Payment Successful</h1>
    <p><strong>Order:</strong> <?php echo $order ? $order : '--'; ?></p>
    <p><strong>Amount:</strong> ₹<?php echo number_format($amount, 2); ?></p>
    <p><strong>Payment ID (test):</strong> <?php echo $payment_id ? htmlspecialchars($payment_id) : '--'; ?></p>

    <p>Your payment in Razorpay Test Mode is successful. This is a test payment id only.</p>

    <a href="index.html" class="btn">Back to Home</a>
  </div>
</body>
</html>
