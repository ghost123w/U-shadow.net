<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $users = json_decode(file_get_contents('users.json'), true);
    $exists = false;

    foreach ($users as $user) {
        if ($user['username'] === $username) {
            $exists = true;
            break;
        }
    }

    if ($exists) {
        $error = 'Username already exists';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $users[] = ['username' => $username, 'password' => $hashed_password, 'is_admin' => false];
        file_put_contents('users.json', json_encode($users));
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-shadow | Sign Up</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<header>
    <div class="logo">U-SHADOW</div>
</header>

<div style="text-align: center;">
    <a href="index.php" class="telegram-btn">Join Our Telegram</a>
</div>

<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="signup.php">Sign Up</a></li>
        <li><a href="#">Short Your Link</a></li>
        <li><a href="#">Facebook</a></li>
        <li><a href="#">Contact</a></li>
    </ul>
</nav>

<div class="main-container">
    <div class="victimes-control" style="flex: 1;">
        <div class="panel-header">Create Account</div>
        <div class="panel-body" style="max-width: 400px; margin: 0 auto;">
            <?php if ($error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form action="signup.php" method="POST">
                <p>Username:</p>
                <input type="text" name="username" placeholder="Username" required>
                <p>Password:</p>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Sign Up</button>
            </form>
            <p>Already have an account? <a href="index.php">Login here</a></p>
        </div>
    </div>
</div>

<div class="footer">
    Copyright 2010-2025 | This Website Is Developed By K24KDX <br>
    U-shadow v2.3 ©
</div>

</body>
</html>
