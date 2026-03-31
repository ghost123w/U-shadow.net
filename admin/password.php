<?php
require_once __DIR__ . '/../config.php';
if (!isset($_SESSION['user']) || !($_SESSION['user']['is_admin'] ?? false)) {
    header('Location: /admin/login.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }

    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $users = json_decode(file_get_contents(USERS_FILE), true);
    $found_index = -1;
    foreach ($users as $idx => $user) {
        if ($user['username'] === $_SESSION['user']['username']) {
            $found_index = $idx;
            break;
        }
    }

    if ($found_index !== -1 && password_verify($current_password, $users[$found_index]['password'])) {
        if (strlen($new_password) < 6) {
            $error = 'New password must be at least 6 characters.';
        } elseif ($new_password !== $confirm_password) {
            $error = 'New passwords do not match.';
        } else {
            $users[$found_index]['password'] = password_hash($new_password, PASSWORD_DEFAULT);
            file_put_contents(USERS_FILE, json_encode($users));
            $success = 'Password updated successfully!';
        }
    } else {
        $error = 'Incorrect current password.';
    }
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-shadow | Admin - Change Password</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">

<div class="sidebar">
    <div class="sidebar-brand">U-SHADOW</div>
    <ul class="sidebar-menu">
        <li><a href="/admin/index.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="/admin/users.php"><i class="fas fa-users"></i> User Management</a></li>
        <li><a href="/admin/categories.php"><i class="fas fa-plus-circle"></i> Manage Hubs</a></li>
        <li class="active"><a href="/admin/password.php"><i class="fas fa-key"></i> Security</a></li>
        <li style="margin-top: 100px;"><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a></li>
    </ul>
</div>

<main class="main-content">
    <div class="top-bar">
        <div class="page-title">
            <h1>Administrative Security</h1>
        </div>
    </div>

    <section class="section" style="max-width: 500px;">
        <div class="section-header">
            <h2>Change Admin Password</h2>
        </div>
        <div class="section-content">
            <?php if ($success): ?>
                <div class="status-badge success" style="display: block; margin-bottom: 20px; text-align: center;"><?php echo s($success); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="status-badge error" style="display: block; margin-bottom: 20px; text-align: center;"><?php echo s($error); ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Update Password</button>
            </form>
        </div>
    </section>
</main>

</body>
</html>
