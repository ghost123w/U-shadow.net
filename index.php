<?php
require_once __DIR__ . '/config.php';

if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$signup_error = '';
$signup_success = isset($_GET['signup']) && $_GET['signup'] === 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }

    if (isset($_POST['login_submit'])) {
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
    } elseif (isset($_POST['signup_submit'])) {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $signup_error = 'Invalid email address';
        } elseif ($password !== $confirm_password) {
            $signup_error = 'Passwords do not match';
        } else {
            $users = json_decode(file_get_contents(USERS_FILE), true);
            $exists = false;

            foreach ($users as $user) {
                if ($user['username'] === $username) {
                    $exists = 'Username already exists';
                    break;
                }
                if (($user['email'] ?? '') === $email) {
                    $exists = 'Email already registered';
                    break;
                }
            }

            if ($exists) {
                $signup_error = $exists;
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $users[] = [
                    'username' => $username,
                    'email' => $email,
                    'password' => $hashed_password,
                    'is_admin' => false
                ];
                file_put_contents(USERS_FILE, json_encode($users));
                header('Location: index.php?signup=success');
                exit;
            }
        }
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
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><path d=%22M50 5 L95 50 L50 95 L5 50 Z%22 fill=%22%23333%22 stroke=%22%23eee%22 stroke-width=%225%22/><text x=%2250%22 y=%2265%22 font-size=%2240%22 font-weight=%22bold%22 fill=%22white%22 text-anchor=%22middle%22 font-family=%22Arial%22>U</text></svg>">
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
                    <button type="submit" name="login_submit" class="btn-old-submit">Sign In Now</button>
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
            <div class="panel-header-old">.: Create New Account :.</div>
            <div class="panel-body-old" style="color: #666;">
                <?php if ($signup_error): ?>
                    <div class="status-badge error" style="display: block; margin-bottom: 15px; text-align: center;"><?php echo s($signup_error); ?></div>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <div class="form-group-old">
                        <label style="font-size: 12px; font-weight: 700;">Username</label>
                        <input type="text" name="username" placeholder="Choose Username" required>
                    </div>
                    <div class="form-group-old">
                        <label style="font-size: 12px; font-weight: 700;">Email Address</label>
                        <input type="email" name="email" placeholder="Enter Your Email" required>
                    </div>
                    <div class="form-group-old">
                        <label style="font-size: 12px; font-weight: 700;">Password</label>
                        <input type="password" name="password" placeholder="Create Password" required>
                    </div>
                    <div class="form-group-old">
                        <label style="font-size: 12px; font-weight: 700;">Confirm Password</label>
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    </div>
                    <button type="submit" name="signup_submit" class="btn-old-submit" style="background: #e74c3c; color: #fff; border: none; margin-top: 10px;">Create My Account</button>
                </form>
            </div>
        </section>
    </main>

    <footer class="footer-old">
        Contact Admin: <a href="mailto:<?php echo ADMIN_EMAIL; ?>" style="color: #c0392b; font-weight: 700;"><?php echo ADMIN_EMAIL; ?></a><br>
        Copyright 2010-2025 | This Website Is Devlopped By K24KDX <br>
        <strong>U-SHADOW v3.5 &copy;</strong>
    </footer>
</div>

</body>
</html>
