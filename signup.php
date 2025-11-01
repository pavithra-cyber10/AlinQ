<?php 
session_start();
require 'db.php';
$conn = db_connect();

// Initialize error variable
$error = "";

if (isset($_POST['signup'])) {
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "alinq";

    // DB connection
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "❌ Passwords do not match!";
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "❌ This username is already taken. Please choose another one.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt_insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt_insert->bind_param("ss", $username, $hashed_password);

            if ($stmt_insert->execute()) {
                // Store session variables
                $_SESSION['user_id'] = $stmt_insert->insert_id;
                $_SESSION['username'] = $username;

                header("Location: service.php");
                exit();
            } else {
                $error = "❌ Error: " . $stmt_insert->error;
            }
            $stmt_insert->close();
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Signup - AlinQ Laundry</title>
  <link rel="stylesheet" href="signup.css">
<script src="validation.js" defer></script>
</head>
<body>

  <nav class="navbar">
    <div class="container">
      <h2 class="logo">AlinQ Laundry</h2>
      <ul class="nav-links">
        <li><a href="index.html">Home</a></li>
        <li><a href="about.html">About</a></li>
        <li><a href="contact.html">Contact</a></li>
        <li><a href="service.php">Services</a></li>
        <li><a href="signup.php" class="active">Signup</a></li>
        <li><a href="login.php">Login</a></li>
      </ul>
    </div>
  </nav>

  <section class="signup-section">
    <div class="form-container">
      <h2>Create an Account</h2>
      <p>Join us and enjoy fresh laundry services!</p>

      <?php if(!empty($error)) { ?>
        <p style="color:red; text-align:center;"><?php echo $error; ?></p>
      <?php } ?>

      <form action="signup.php" method="POST" class="signup-form">
        <input type="text" name="username" placeholder="Username" required />
        <input type="text" name="Email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <input type="password" name="confirm_password" placeholder="Confirm Password" required />
        <button type="submit" name="signup" class="btn">Signup</button>
      </form>

      <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
    </div>
  </section>

  <footer class="footer">
    <p>&copy; 2025 AlinQ Laundry. All Rights Reserved.</p>
  </footer>

</body>
</html>