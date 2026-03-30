<?php
require_once __DIR__ . '/../config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }

    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $password = $_POST['password'] ?? '';

    // Fixed admin credentials for demonstration, but checking against USERS_FILE for flexibility
    $users = json_decode(file_get_contents(USERS_FILE), true);
    $authenticated = false;

    // Hardcoded admin for initial setup or use user-defined admins
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['user'] = ['username' => 'admin', 'is_admin' => true];
        $authenticated = true;
    } else {
        foreach ($users as $user) {
            if ($user['username'] === $username && ($user['is_admin'] ?? false) && password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                $authenticated = true;
                break;
            }
        }
    }

    if ($authenticated) {
        header('Location: admin_categories.php');
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
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #eee; }
        .login-box { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 300px; }
        h2 { text-align: center; margin-top: 0; }
        input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #333; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .error { color: red; text-align: center; font-size: 14px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Portal</h2>
        <?php if ($error): ?><p class="error"><?php echo s($error); ?></p><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="text" name="username" placeholder="Admin Username" required>
            <input type="password" name="password" placeholder="Admin Password" required>
            <button type="submit">Access Control</button>
        </form>
    </div>
</body>
</html>
