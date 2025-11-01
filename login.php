<?php
session_start();
require 'db.php';

$error = "";
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_SECONDS', 15 * 60); // 15 minutes

if (isset($_POST['login'])) {
    $conn = db_connect();

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Fetch user record including attempt counters
    $stmt = $conn->prepare("SELECT id, password, login_attempts, last_attempt, is_admin FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hash, $login_attempts, $last_attempt, $is_admin);

    if ($stmt->num_rows === 0) {
        // don't reveal whether username exists
        $error = "❌ Invalid username or password.";
    } else {
        $stmt->fetch();

        // Check lockout
        $locked = false;
        if ($last_attempt !== null) {
            $last_time = strtotime($last_attempt);
            $elapsed = time() - $last_time;
            if ($login_attempts >= MAX_LOGIN_ATTEMPTS && $elapsed < LOCKOUT_SECONDS) {
                $locked = true;
                $remaining = LOCKOUT_SECONDS - $elapsed;
                $mins = floor($remaining / 60);
                $secs = $remaining % 60;
                $error = "⏳ Account locked. Try again in {$mins}m {$secs}s.";
            } elseif ($elapsed >= LOCKOUT_SECONDS) {
                // Reset attempts after lockout period
                $reset = $conn->prepare("UPDATE users SET login_attempts = 0, last_attempt = NULL WHERE id = ?");
                $reset->bind_param("i", $id);
                $reset->execute();
                $reset->close();
                $login_attempts = 0;
                $last_attempt = null;
            }
        }

        if (!$locked) {
            if (password_verify($password, $hash)) {
                // successful login -> reset attempts
                $reset = $conn->prepare("UPDATE users SET login_attempts = 0, last_attempt = NULL WHERE id = ?");
                $reset->bind_param("i", $id);
                $reset->execute();
                $reset->close();

                // set sessions
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['is_admin'] = (int)$is_admin;

                header("Location: service.php");
                exit();
            } else {
                // failed login -> increment attempts
                $now = date('Y-m-d H:i:s');
                if ($last_attempt === null) {
                    $login_attempts = 1;
                } else {
                    $login_attempts = $login_attempts + 1;
                }
                $upd = $conn->prepare("UPDATE users SET login_attempts = ?, last_attempt = ? WHERE id = ?");
                $upd->bind_param("isi", $login_attempts, $now, $id);
                $upd->execute();
                $upd->close();

                if ($login_attempts >= MAX_LOGIN_ATTEMPTS) {
                    $error = "⛔ Too many failed attempts. Account locked for 15 minutes.";
                } else {
                    $remaining = MAX_LOGIN_ATTEMPTS - $login_attempts;
                    $error = "❌ Invalid username or password. {$remaining} attempt(s) left.";
                }
            }
        }
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Login - AlinQ Laundry</title>
  <link rel="stylesheet" href="login.css">
  <script src="validation.js" defer></script>
</head>
<body>
  <div class="form-container">
    <h2>Login</h2>
    <?php if ($error) echo "<p style='color:red'>{$error}</p>"; ?>
    <form method="POST" class="login-form" novalidate>
      <input type="text" name="username" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit" name="login" class="btn">Login</button>
    </form>
    <p>Don't have an account? <a href="signup.php">Signup</a></p>
  </div>
</body>
</html>