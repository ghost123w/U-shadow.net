<?php
require_once __DIR__ . '/../config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }

    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        $users = json_decode(file_get_contents(USERS_FILE), true);
        $exists = false;

        foreach ($users as $user) {
            if ($user['username'] === $username) {
                $exists = true;
                break;
            }
        }

        if ($exists) {
            $error = 'Username already exists';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $users[] = ['username' => $username, 'password' => $hashed_password, 'is_admin' => false];
            file_put_contents(USERS_FILE, json_encode($users));
            header('Location: index.php?signup=success');
            exit;
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
    <title>U-shadow | Sign Up</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function validateForm() {
            var pass = document.getElementById("password").value;
            var confirm = document.getElementById("confirm_password").value;
            if (pass != confirm) {
                alert("Passwords do not match.");
                return false;
            }
            return true;
        }
    </script>
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
    </ul>
</nav>

<div class="main-container">
    <div class="victimes-control" style="flex: 1;">
        <div class="panel-header">Create New Member Account</div>
        <div class="panel-body" style="max-width: 400px; margin: 0 auto;">
            <?php if ($error): ?>
                <p class="error"><?php echo s($error); ?></p>
            <?php endif; ?>
            <form action="signup.php" method="POST" onsubmit="return validateForm()">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <p>Username:</p>
                <input type="text" name="username" placeholder="Username" required>
                <p>Password:</p>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <p>Confirm Password:</p>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">Sign Up</button>
            </form>
            <p>Already have an account? <a href="index.php">Login here</a></p>
        </div>
    </div>
</div>

<div class="footer">
    Copyright 2010-2025 | Developed By K24KDX <br>
    U-shadow v3.0
</div>

</body>
</html>
