<?php
require 'db.php';
$conn = db_connect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $message = trim($_POST['message']);

  $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $email, $message);
  if ($stmt->execute()) {
    echo "<script>alert('✅ Message sent successfully!'); window.location='contact.html';</script>";
  } else {
    echo "<script>alert('❌ Error sending message'); window.location='contact.html';</script>";
  }
  $stmt->close();
  $conn->close();
}
?>

