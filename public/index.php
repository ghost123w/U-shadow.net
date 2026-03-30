<?php
require_once __DIR__ . '/../config.php';
$error = '';
$signup_success = isset($_GET['signup']) && $_GET['signup'] === 'success';

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
        <p style="font-size: 14px; margin-top: 20px;">
            Empowering your social presence with tracked, branded integration hubs. Join thousands of users optimizing their link click-through rates.
        </p>
    </div>

    <div class="login-panel">
        <div class="panel-header">User Login</div>
        <div class="panel-body">
            <?php if ($signup_success): ?>
                <p style="color: green; text-align: center; font-weight: bold; margin-bottom: 15px;">Account created! Please sign in.</p>
            <?php endif; ?>
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
</div>

<div class="main-container" style="margin-top: 0;">
    <div class="info-section">
        <div class="panel-header">.: Why Choose U-shadow? :.</div>
        <div class="panel-body">
            <div style="display: flex; gap: 20px;">
                <div style="flex: 1;">
                    <h4>Real-time Analytics</h4>
                    <p>Monitor every click with precision. See when and where your audience is coming from.</p>
                </div>
                <div style="flex: 1;">
                    <h4>Branded Links</h4>
                    <p>Create professional landing pages for all major social platforms: Facebook, Instagram, TikTok, and more.</p>
                </div>
                <div style="flex: 1;">
                    <h4>Secure Infrastructure</h4>
                    <p>Your data and privacy are our top priorities. We use industry-standard encryption for all user accounts.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main-container" style="margin-top: 0;">
    <div class="info-section" style="flex: 1;">
        <div class="panel-header">.: How it Works :.</div>
        <div class="panel-body" style="text-align: center;">
            <p>1. Sign up for a free account. <br>
               2. Choose from our professional integration hub templates. <br>
               3. Generate and share your unique tracking link. <br>
               4. View detailed click reports in your private dashboard.</p>
        </div>
    </div>
</div>

<div class="footer">
    Copyright 2010-2025 | Developed By K24KDX <br>
    <a href="terms.php" style="color: #888; text-decoration: none;">Terms of Service</a> |
    <a href="privacy.php" style="color: #888; text-decoration: none;">Privacy Policy</a> |
    U-shadow v3.0 | <a href="admin.php" style="color: #888; text-decoration: none;">Admin Access</a>
</div>

</body>
</html>
