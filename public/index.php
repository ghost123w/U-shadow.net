<?php
require_once __DIR__ . '/../config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }

    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $password = $_POST['password'] ?? '';

    $users = json_decode(file_get_contents(USERS_FILE), true);
    $authenticated = false;

    foreach ($users as $user) {
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            $authenticated = true;
            break;
        }
    }

    if ($authenticated) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-shadow | Professional Link Hub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">U-SHADOW</div>
    <div style="clear: both;"></div>
</header>

<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="signup.php">Sign Up</a></li>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="main-container">
    <div class="side-branding">
        <div class="logo-large">U-SHADOW</div>
        <p>Professional Link Management & Analytics</p>
    </div>

    <div class="login-panel">
        <div class="panel-header">User Login</div>
        <div class="panel-body">
            <?php if ($error): ?>
                <p class="error"><?php echo s($error); ?></p>
            <?php endif; ?>
            <?php if (isset($_SESSION['user'])): ?>
                <p>Logged in as: <strong><?php echo s($_SESSION['user']['username']); ?></strong></p>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="dashboard.php"><button style="width: 100%;">Go to Dashboard</button></a>
                    <a href="logout.php"><button style="width: 100%; background: #666;">Logout</button></a>
                </div>
            <?php else: ?>
                <form action="index.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit">Sign In</button>
                </form>
                <p style="text-align: center;">New here? <a href="signup.php">Create an account</a></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="info-section">
        <div class="panel-header">.: About U-shadow :.</div>
        <div class="panel-body">
            <p>U-shadow is a professional platform for managing your social media integration links. Track clicks, analyze traffic, and optimize your online presence.</p>
            <ul>
                <li>Custom Integration Links</li>
                <li>Real-time Click Analytics</li>
                <li>Professional Dashboard</li>
                <li>Secure & Private</li>
            </ul>
        </div>
    </div>
</div>

<div class="footer">
    Copyright 2010-2025 | Developed By K24KDX <br>
    U-shadow v3.0 | <a href="admin.php" style="color: #888; text-decoration: none;">Admin Access</a>
</div>

</body>
</html>
