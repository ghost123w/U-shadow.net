<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-shadow | Dashboard</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<header>
    <div class="logo">U-SHADOW</div>
</header>

<div style="text-align: center;">
    <a href="#" class="telegram-btn">Join Our Telegram</a>
</div>

<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="signup.php">Sign Up</a></li>
        <li><a href="#">Short Your Link</a></li>
        <li><a href="#">Facebook</a></li>
        <li><a href="#">Contact</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="main-container">
    <div class="victimes-control">
        <div class="panel-header">.: Dashboard :.</div>
        <div class="panel-body">
            <p>Welcome back, <strong><?php echo htmlspecialchars($user['username']); ?></strong>!</p>
            <p>You can now access your victims and manage your account.</p>
            <div style="border: 1px solid #ddd; padding: 20px; background-color: #f9f9f9;">
                <h3>Your Statistics</h3>
                <p>Victims: 0</p>
                <p>Links generated: 0</p>
            </div>
            <p><a href="logout.php">Logout</a></p>
        </div>
    </div>
</div>

<div class="footer">
    Copyright 2010-2025 | This Website Is Developed By K24KDX <br>
    U-shadow v2.3 ©
</div>

</body>
</html>
