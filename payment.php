<?php
// payment.php
// This page expects total sent by POST from your price/summary page.
// For quick tests you can also pass ?total=300 in the URL (GET fallback).

$total = 0.00;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['total'])) {
    $total = floatval($_POST['total']);
} elseif (isset($_GET['total'])) {
    $total = floatval($_GET['total']);
}

// Protect from negative or missing values
if ($total < 0) $total = 0.00;

// Generate a simple order id to display (optional)
$orderId = "ALINQ" . time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Payment - AlinQ Laundry</title>

  <!-- Razorpay Checkout script -->
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

  <style>
    body {
      font-family: 'Poppins', Arial, sans-serif;
      background: #f3f7fb;
      margin: 0;
      padding: 0;
    }
    header.hero {
      background: linear-gradient(135deg, #42a5f5, #7e57c2);
      color: white;
      padding: 18px;
      text-align: center;
    }
    .container { max-width: 980px; margin: 0 auto; padding: 16px; }
    .payment-box {
      max-width: 520px;
      margin: 30px auto;
      padding: 28px;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.08);
      text-align: center;
    }
    .payment-box h2 { color: #2b7be3; margin-bottom: 8px; }
    .payment-options { display:flex; justify-content:center; gap:12px; margin-top: 18px; }
    .option-btn {
      background: linear-gradient(135deg,#42a5f5,#7e57c2);
      color:#fff; padding:10px 18px; border-radius: 10px; border: none; cursor: pointer;
      font-size: 15px;
    }
    .option-btn:hover { transform: scale(1.02); }
    .qr-box { margin-top:20px; display:none; }
    .offline-info { margin-top:20px; display:none; }
    footer { text-align:center; padding:18px; color:#555; margin-top:24px; }
  </style>
</head>
<body>
  <header class="hero">
    <div class="container">
      <h1>AlinQ Laundry</h1>
      <p>Secure & Easy Payments</p>
    </div>
  </header>

  <main class="container">
    <div class="payment-box">
      <h2>Total Amount: ₹<?php echo number_format($total, 2); ?></h2>
      <p>Order ID: <strong><?php echo htmlspecialchars($orderId); ?></strong></p>

      <h3>Select Payment Method</h3>
      <div class="payment-options">
        <button class="option-btn" id="onlineBtn">Online Payment </button>
        <button class="option-btn" id="offlineBtn">Offline Payment (Cash On Delivery)</button>
      </div>

      <!-- Optional inline message for online -->
      <div id="onlineBox" class="qr-box" aria-hidden="true">
        <p>Click <strong>Pay with Razorpay</strong> to open the test checkout popup.</p>
        <button class="option-btn" id="rzpPayBtn" style="margin-top:10px;">Pay with Razorpay</button>
        <p id="statusMsg" style="margin-top:12px;color:#777"></p>
      </div>

      <!-- Offline info -->
      <div id="offlineBox" class="offline-info" aria-hidden="true">
        <h4>Offline Payment</h4>
        <p>Please pay cash at pickup or delivery. Your booking will be confirmed after payment.</p>
      </div>
    </div>
  </main>

  <footer>
    &copy; <?php echo date('Y'); ?> AlinQ Laundry. All rights reserved.
  </footer>

  <script>
    // Replace this with your Razorpay TEST Key ID
    const RAZORPAY_KEY_ID = "rzp_test_RXBXoa1c1PHzR1"; // <-- PUT YOUR TEST KEY ID HERE

    // Amount from PHP (INR)
    const amountInRupees = <?php echo json_encode($total); ?>;
    // Convert to paise (integer)
    const amountInPaise = Math.round(amountInRupees * 100);

    const orderId = <?php echo json_encode($orderId); ?>;

    const onlineBtn = document.getElementById('onlineBtn');
    const offlineBtn = document.getElementById('offlineBtn');
    const onlineBox = document.getElementById('onlineBox');
    const offlineBox = document.getElementById('offlineBox');
    const rzpPayBtn = document.getElementById('rzpPayBtn');
    const statusMsg = document.getElementById('statusMsg');

    onlineBtn.addEventListener('click', function(){
      onlineBox.style.display = 'block';
      offlineBox.style.display = 'none';
      statusMsg.innerText = '';
    });

    offlineBtn.addEventListener('click', function(){
      onlineBox.style.display = 'none';
      offlineBox.style.display = 'block';
      statusMsg.innerText = '';
    });

    // Main Razorpay Checkout open
    rzpPayBtn.addEventListener('click', function(e){
      e.preventDefault();

      if (!RAZORPAY_KEY_ID || RAZORPAY_KEY_ID.includes('tQCwCV2Y0ghYpmMrynSVa01y')) {
        alert('Please replace RAZORPAY_KEY_ID in the code with your test key from Razorpay dashboard.');
        return;
      }

      // Build options
      const options = {
        "key": RAZORPAY_KEY_ID,
        "amount": amountInPaise, // amount in paise
        "currency": "INR",
        "name": "AlinQ Laundry",
        "description": "Payment for Order " + orderId,
        // "order_id": "" // optional: if you create an order server-side, pass razorpay order id here
        "handler": function (response){
          // response contains razorpay_payment_id (test id)
          // Redirect to success page and pass payment id and amount (for display)
          const params = new URLSearchParams();
          params.append('payment_id', response.razorpay_payment_id || '');
          params.append('amount', amountInRupees);
          params.append('order', orderId);
          // You can also POST to server to mark order paid (not implemented here)
          window.location.href = 'success.php?' + params.toString();
        },
        "prefill": {
          "name": "",
          "email": "",
          "contact": ""
        },
        "theme": {
          "color": "#42a5f5"
        },
        "modal": {
          "ondismiss": function(){
            statusMsg.innerText = 'Payment popup closed. You can try again.';
          }
        }
      };

      try {
        const rzp = new Razorpay(options);
        rzp.open();
      } catch (err) {
        console.error('Razorpay error:', err);
        alert('Could not open Razorpay checkout. See console for details.');
      }
    });
  </script>
</body>
</html>
