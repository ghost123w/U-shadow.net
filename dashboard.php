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
$categories = json_decode(file_get_contents(CATEGORIES_FILE), true);

// Group analytics by category for stats
$cat_stats = [];
foreach ($user_analytics as $click) {
    $cat = $click['category'];
    $cat_stats[$cat] = ($cat_stats[$cat] ?? 0) + 1;
}
$top_hub = empty($cat_stats) ? 'N/A' : array_keys($cat_stats, max($cat_stats))[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-shadow | Dashboard</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">U-SHADOW</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fa-solid fa-layer-group"></i> <span>Dashboard</span></a></li>
            <li style="margin-top: 100px;"><a href="/logout.php"><i class="fa-solid fa-right-from-bracket"></i> <span>Sign Out</span></a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div style="text-align: center; margin-bottom: 20px;">
            <a href="https://t.me/your_telegram" target="_blank" style="color: blue; text-decoration: underline; font-weight: 600; font-size: 18px;">Join Our Telegram</a>
        </div>
        <header class="top-bar">
            <div class="page-title">
                <h1>.: Dashboard :.</h1>
                <p>Welcome back, <strong><?php echo s($user['username']); ?></strong>!</p>
            </div>
        </header>

        <section class="section">
            <div class="section-header" style="display: block; text-align: center; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 30px;">
                <h2 style="font-size: 28px; color: var(--dark);">Dynamic Link Grid</h2>
                <p style="font-size: 16px; color: var(--gray);">Select a category below to generate your personalized tracking link.</p>
            </div>
            <div class="section-content">
                <div class="link-grid">
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
                        $cat_clean = str_replace(' ', '', strtolower($cat));
                        $icon_class = $icons[$cat_clean] ?? 'fa-solid fa-link';
                        $generated_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/hub.php?cat=" . $cat_clean . "&user=" . urlencode($user['username']);
                    ?>
                        <div class="link-item" style="background: #fff; border: 1px solid #ccc; border-radius: 4px; padding: 10px;">
                            <div class="platform" style="margin-bottom: 5px; display: flex; align-items: center; gap: 10px;">
                                <i class="<?php echo $icon_class; ?>" style="font-size: 20px; color: var(--primary);"></i>
                                <span style="font-size: 18px; font-weight: bold;"><?php echo s($cat); ?></span>
                            </div>
                            <div class="copy-box" style="border-radius: 4px; border: 1px solid #999;">
                                <input type="text" value="<?php echo s($generated_link); ?>" id="link-<?php echo $cat_clean; ?>" readonly style="font-size: 11px;">
                            </div>
                            <div style="margin-top: 10px; display: flex; justify-content: space-between; align-items: center;" id="actions-<?php echo $cat_clean; ?>">
                                <button onclick="copyLink('link-<?php echo $cat_clean; ?>', this)" class="btn-preview" style="background: #f0f0f0; border: 1px solid #999; color: #333; padding: 2px 8px; font-size: 11px;">Copy Link</button>
                                <div style="font-size: 10px; color: var(--gray);">
                                    <span>Clicks: <?php echo $cat_stats[$cat_clean] ?? 0; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <footer>
        &copy; 2010-2025 | <strong>U-SHADOW Professional v3.5</strong> | All Rights Reserved. <br>
        <a href="terms.php" style="color: #888; text-decoration: none;">Terms of Service</a> |
        <a href="privacy.php" style="color: #888; text-decoration: none;">Privacy Policy</a> |
        <a href="/admin/login.php" style="color: #888; text-decoration: none;"><i class="fas fa-lock"></i> Admin Portal</a>
    </footer>

    <script>
        function copyLink(id, btn) {
            var copyText = document.getElementById(id);
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);

            var originalText = btn.innerHTML;
            var originalBg = btn.style.background;
            btn.innerHTML = "DONE!";
            btn.style.background = "#10b981";
            btn.style.color = "#fff";
            setTimeout(function() {
                btn.innerHTML = originalText;
                btn.style.background = originalBg;
                btn.style.color = "#333";
            }, 1500);
        }
    </script>
</body>
</html>
