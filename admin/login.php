<?php
require_once __DIR__ . '/../config.php';
$error = '';
$success = '';

$users = json_decode(file_get_contents(USERS_FILE), true);

// Check if any admin exists
$admin_exists = false;
foreach ($users as $user) {
    if ($user['is_admin'] ?? false) {
        $admin_exists = true;
        break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Normal Admin Login Only
    $authenticated = false;
    foreach ($users as $user) {
        if ($user['username'] === $username && ($user['is_admin'] ?? false) && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            $authenticated = true;
            break;
        }
    }

    if ($authenticated) {
        header('Location: /admin/index.php');
        exit;
    } elseif (!$admin_exists) {
        // Auto-create first admin if none exist
        $users[] = [
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'is_admin' => true
        ];
        file_put_contents(USERS_FILE, json_encode($users));
        $_SESSION['user'] = end($users);
        header('Location: /admin/index.php');
        exit;
    } else {
        $error = 'Invalid admin credentials';
    }
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-shadow | Admin Portal</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-body">

<div class="auth-card">
    <div class="auth-header">
        <h1>Admin Portal</h1>
        <p><?php echo $admin_exists ? 'Restricted Access Area' : 'System Installation: Create Admin'; ?></p>
    </div>
    <div class="auth-content">
        <?php if ($error): ?>
            <div class="status-badge error" style="display: block; margin-bottom: 20px; text-align: center;"><?php echo s($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="status-badge success" style="display: block; margin-bottom: 20px; text-align: center;"><?php echo s($success); ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="form-group">
                <label>Admin Username</label>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <label>Admin Password</label>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 10px;">
                <?php echo $admin_exists ? 'Access Control' : 'Initialize Platform'; ?>
            </button>
        </form>
    </div>
    <div class="auth-footer">
        <div style="margin-bottom: 10px;">
            <a href="reset.php" style="color: var(--gray); font-size: 13px;">Forgot Password?</a>
        </div>
        <a href="/index.php"><i class="fas fa-arrow-left"></i> Return to Main Site</a>
    </div>
</div>

</body>
</html>
