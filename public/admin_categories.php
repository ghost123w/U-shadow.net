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
    <title>U-shadow | Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">

<div class="sidebar">
    <div class="sidebar-brand">
        U-SHADOW
    </div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li class="active"><a href="admin_categories.php"><i class="fas fa-plus-circle"></i> Manage Hubs</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a></li>
    </ul>
</div>

<main class="main-content">
    <div class="top-bar">
        <div class="page-title">
            <h1>Admin: Manage Integration Hubs</h1>
        </div>
        <div class="user-profile" style="display: flex; align-items: center;">
            <div class="user-info" style="margin-right: 15px; text-align: right;">
                <span class="name" style="display: block; font-weight: 600; font-size: 14px;"><?php echo s($_SESSION['user']['username']); ?></span>
                <span class="role" style="font-size: 12px; color: var(--gray);">Administrator</span>
            </div>
            <div class="avatar">A</div>
        </div>
    </div>

    <section class="section" style="max-width: 600px;">
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
            <h2><i class="fas fa-list"></i> Existing Integration Hubs</h2>
        </div>
        <div class="section-content">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Platform Name</th>
                        <th>Filename</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td style="font-weight: 600;"><?php echo s($cat); ?></td>
                        <td><code>hub.php?cat=<?php echo s(str_replace(' ', '', strtolower($cat))); ?></code></td>
                        <td><span class="badge badge-success">ACTIVE</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <footer class="dashboard-footer">
        &copy; 2010-2025 | <strong>U-SHADOW Professional v3.5</strong> | All Rights Reserved.<br>
        <a href="terms.php" style="color: #666; text-decoration: none;">Terms of Service</a> | <a href="privacy.php" style="color: #666; text-decoration: none;">Privacy Policy</a>
    </footer>
</main>

</body>
</html>
