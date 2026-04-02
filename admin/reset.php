<?php
require_once __DIR__ . '/../config.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }

    $token = $_POST['reset_token'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $new_password = $_POST['new_password'] ?? '';

    if ($token !== ADMIN_RESET_TOKEN) {
        $error = 'Invalid reset token.';
    } elseif (strlen($new_password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $users = json_decode(file_get_contents(USERS_FILE), true);
        $found = false;
        foreach ($users as &$user) {
            if ($user['username'] === $username && ($user['is_admin'] ?? false)) {
                $user['password'] = password_hash($new_password, PASSWORD_DEFAULT);
                $found = true;
                break;
            }
        }

        if ($found) {
            file_put_contents(USERS_FILE, json_encode($users));
            $success = 'Admin password has been reset successfully. <a href="login.php">Login here</a>';
        } else {
            $error = 'Admin user not found.';
        }
    }
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-shadow | Admin Reset</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><path d=%22M50 5 L95 50 L50 95 L5 50 Z%22 fill=%22%23333%22 stroke=%22%23eee%22 stroke-width=%225%22/><text x=%2250%22 y=%2265%22 font-size=%2240%22 font-weight=%22bold%22 fill=%22white%22 text-anchor=%22middle%22 font-family=%22Arial%22>U</text></svg>">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-body">

<div class="auth-card">
    <div class="auth-header">
        <h1>Password Reset</h1>
        <p>Administrative Recovery</p>
    </div>
    <div class="auth-content">
        <?php if ($error): ?>
            <div class="status-badge error" style="display: block; margin-bottom: 20px; text-align: center;"><?php echo s($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="status-badge success" style="display: block; margin-bottom: 20px; text-align: center;"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="form-group">
                <label>Admin Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Reset Token</label>
                <input type="password" name="reset_token" placeholder="Enter security token" required>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 10px;">Reset Password</button>
        </form>
    </div>
    <div class="auth-footer">
        <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
    </div>
</div>

</body>
</html>
