<?php
session_start();
if (!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_category = trim($_POST['category_name'] ?? '');
    if ($new_category) {
        $categories = json_decode(file_get_contents('categories.json'), true);
        if (!in_array($new_category, $categories)) {
            $categories[] = $new_category;
            file_put_contents('categories.json', json_encode($categories));
            $file_name = strtolower($new_category) . ".php";
            if (!file_exists($file_name)) {
                copy('template.php', $file_name);
            }
            $success = "Category '$new_category' added successfully!";
        } else {
            $error = "Category already exists.";
        }
    } else {
        $error = "Category name cannot be empty.";
    }
}

$categories = json_decode(file_get_contents('categories.json'), true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-shadow | Admin Categories</title>
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
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="main-container">
    <div class="victimes-control" style="flex: 1;">
        <div class="panel-header">Create New Category</div>
        <div class="panel-body" style="max-width: 500px; margin: 0 auto;">
            <?php if ($error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
            <form action="admin_categories.php" method="POST">
                <input type="text" name="category_name" placeholder="Enter Category Name" required>
                <button type="submit">Add Category</button>
            </form>
            <hr>
            <h3>Existing Categories</h3>
            <ul>
                <?php foreach ($categories as $cat): ?>
                    <li><?php echo htmlspecialchars($cat); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<div class="footer">
    Copyright 2010-2025 | This Website Is Developed By K24KDX <br>
    U-shadow v2.3 ©
</div>

</body>
</html>
