<?php
require_once __DIR__ . '/../config.php';
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
    <title>U-shadow | Social Link Hub & Analytics</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<section class="landing-hero">
    <div class="hero-container">
        <div class="hero-content">
            <h1>U-SHADOW</h1>
            <p>The all-in-one professional link management and real-time analytics platform for social media creators and business professionals.</p>
            <?php if (!isset($_SESSION['user'])): ?>
                <a href="signup.php" class="btn btn-primary" style="padding: 15px 40px; font-size: 18px;">Get Started for Free</a>
            <?php else: ?>
                <a href="dashboard.php" class="btn btn-primary" style="padding: 15px 40px; font-size: 18px;">Go to My Dashboard</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<div style="background: #fff; padding: 60px 0;">
    <div class="auth-body" style="min-height: auto; background: transparent;">
        <div class="auth-card" style="box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <div class="auth-header">
                <h1>Member Login</h1>
                <p>Welcome back! Sign in to manage your hubs.</p>
            </div>
            <div class="auth-content">
                <?php if ($signup_success): ?>
                    <div class="status-badge success" style="display: block; margin-bottom: 20px; text-align: center;">Account created successfully!</div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="status-badge error" style="display: block; margin-bottom: 20px; text-align: center;"><?php echo s($error); ?></div>
                <?php endif; ?>

                <?php if (isset($_SESSION['user'])): ?>
                    <div style="text-align: center;">
                        <p>Logged in as <strong><?php echo s($_SESSION['user']['username']); ?></strong></p>
                        <a href="dashboard.php" class="btn btn-primary" style="width: 100%; margin-bottom: 10px;">Dashboard</a>
                        <a href="logout.php" class="btn" style="width: 100%; background: #eee; color: #333;">Sign Out</a>
                    </div>
                <?php else: ?>
                    <form action="index.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" placeholder="Enter your username" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" placeholder="Enter your password" required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 10px;">Sign In</button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="auth-footer">
                Don't have an account? <a href="signup.php">Join U-shadow</a>
            </div>
        </div>
    </div>
</div>

<section class="features-grid">
    <div class="feature-card">
        <i class="fas fa-chart-line"></i>
        <h3>Real-time Analytics</h3>
        <p>Monitor every click with precision. See when and where your audience is coming from with our advanced tracking system.</p>
    </div>
    <div class="feature-card">
        <i class="fas fa-link"></i>
        <h3>Branded Link Hubs</h3>
        <p>Create professional landing pages for Facebook, Instagram, TikTok, and more to centralize your social presence.</p>
    </div>
    <div class="feature-card">
        <i class="fas fa-shield-alt"></i>
        <h3>Enterprise Security</h3>
        <p>Your data and privacy are our top priorities. We use industry-standard hashing and secure infrastructure to protect your account.</p>
    </div>
</section>

<footer>
    <div style="margin-bottom: 20px;">
        <strong>U-SHADOW Professional v3.5</strong><br>
        <span style="color: #a2a3b7;">Next-Generation Link Management</span>
    </div>
    <div style="margin-bottom: 20px;">
        <a href="terms.php" style="color: var(--primary); text-decoration: none; margin: 0 10px;">Terms of Service</a>
        <a href="privacy.php" style="color: var(--primary); text-decoration: none; margin: 0 10px;">Privacy Policy</a>
        <a href="admin.php" style="color: var(--primary); text-decoration: none; margin: 0 10px;">Admin Portal</a>
    </div>
    <p>&copy; 2010-2025 U-Shadow. All rights reserved. Developed by K24KDX</p>
</footer>

</body>
</html>
