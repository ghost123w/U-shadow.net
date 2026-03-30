<?php
require_once __DIR__ . '/../config.php';
if (!isset($_SESSION['user']) || !($_SESSION['user']['is_admin'] ?? false)) {
    header('Location: index.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }

    $new_cat = trim($_POST['category'] ?? '');

    // Validate: only alphanumeric and space (for display), and not empty
    if ($new_cat && preg_match('/^[a-zA-Z0-9 ]+$/', $new_cat)) {
        $categories = json_decode(file_get_contents(CATEGORIES_FILE), true);
        if (!in_array($new_cat, $categories)) {
            $categories[] = $new_cat;
            file_put_contents(CATEGORIES_FILE, json_encode($categories));
            $success = "Link Hub for '$new_cat' added successfully!";
        } else {
            $error = "Hub already exists.";
        }
    } else if ($new_cat) {
        $error = "Invalid characters in hub name. Use letters and numbers only.";
    }
}

$categories = json_decode(file_get_contents(CATEGORIES_FILE), true);
$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-shadow | Admin - Manage Hubs</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">

<div class="sidebar">
    <div class="sidebar-brand">U-SHADOW</div>
    <ul class="sidebar-menu">
        <li><a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="admin_users.php"><i class="fas fa-users"></i> User Management</a></li>
        <li class="active"><a href="admin_categories.php"><i class="fas fa-plus-circle"></i> Manage Hubs</a></li>
        <li style="margin-top: 100px;"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a></li>
    </ul>
</div>

<main class="main-content">
    <div style="text-align: center; margin-bottom: 20px;">
        <a href="https://t.me/your_telegram" target="_blank" style="color: blue; text-decoration: underline; font-weight: 600; font-size: 18px;">Join Our Telegram</a>
    </div>

    <div class="top-bar">
        <div class="page-title">
            <h1>Manage Integration Hubs</h1>
        </div>
        <div class="user-profile">
            <div class="user-info">
                <span class="name"><?php echo s($_SESSION['user']['username']); ?></span>
                <span class="role">Administrator</span>
            </div>
            <div class="avatar">A</div>
        </div>
    </div>

    <section class="section" style="max-width: 600px; margin-bottom: 40px;">
        <div class="section-header">
            <h2><i class="fas fa-plus"></i> Add New Integration Hub</h2>
        </div>
        <div class="section-content">
            <?php if ($success): ?>
                <div class="status-badge success" style="display: block; margin-bottom: 20px; text-align: center;"><?php echo s($success); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="status-badge error" style="display: block; margin-bottom: 20px; text-align: center;"><?php echo s($error); ?></div>
            <?php endif; ?>

            <form action="admin_categories.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <div class="form-group">
                    <label>Hub Display Name</label>
                    <input type="text" name="category" placeholder="e.g. Discord, Slack, Pinterest" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Create Integration Hub</button>
            </form>
        </div>
    </section>

    <section class="section">
        <div class="section-header">
            <h2><i class="fas fa-th-large"></i> Existing Integration Hubs</h2>
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
                    'youtube' => 'fa-brands fa-youtube',
                    'pinterest' => 'fa-brands fa-pinterest',
                    'linkedin' => 'fa-brands fa-linkedin',
                    'whatsapp' => 'fa-brands fa-whatsapp',
                    'spotify' => 'fa-brands fa-spotify'
                ];
                foreach ($categories as $cat):
                    $cat_clean = str_replace(' ', '', strtolower($cat));
                    $icon_class = $icons[$cat_clean] ?? 'fa-solid fa-link';
                ?>
                    <div class="link-item" style="border-left: 4px solid var(--primary); padding: 15px;">
                        <div style="font-weight: 700; font-size: 18px; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; height: 50px; background: #f0f4f8; border-radius: 8px;">
                            <?php echo s($cat); ?>
                        </div>
                        <div class="copy-box" style="margin-top: 10px;">
                            <input type="text" value="hub.php?cat=<?php echo s($cat_clean); ?>" readonly style="font-family: monospace;">
                        </div>
                        <div style="margin-top: 15px; display: flex; justify-content: space-between; align-items: center;">
                             <span class="badge badge-success">ACTIVE</span>
                             <i class="<?php echo $icon_class; ?>" style="color: var(--gray);"></i>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer class="dashboard-footer">
        &copy; 2010-2025 | <strong>U-SHADOW Professional v3.5</strong> | All Rights Reserved.
    </footer>
</main>

</body>
</html>
