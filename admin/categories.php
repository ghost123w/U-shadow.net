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

    $action = $_POST['action'] ?? 'create';
    $categories = json_decode(file_get_contents(CATEGORIES_FILE), true);

    if ($action === 'create') {
        $new_cat_name = trim($_POST['category'] ?? '');
        if ($new_cat_name && preg_match('/^[a-zA-Z0-9 ]+$/', $new_cat_name)) {
            $exists = false;
            foreach ($categories as $cat) {
                $c_name = is_array($cat) ? ($cat['name'] ?? '') : $cat;
                if (strcasecmp($c_name, $new_cat_name) === 0) {
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                $image_path = '';
                if (isset($_FILES['hub_image']) && $_FILES['hub_image']['error'] === UPLOAD_ERR_OK) {
                    $file_tmp = $_FILES['hub_image']['tmp_name'];
                    $file_ext = strtolower(pathinfo($_FILES['hub_image']['name'], PATHINFO_EXTENSION));
                    if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $new_file_name = uniqid('hub_', true) . '.' . $file_ext;
                        if (move_uploaded_file($file_tmp, UPLOADS_DIR . $new_file_name)) {
                            $image_path = '/uploads/' . $new_file_name;
                        }
                    }
                }
                $categories[] = ['name' => $new_cat_name, 'image' => $image_path];
                file_put_contents(CATEGORIES_FILE, json_encode($categories));
                $success = "Logo Hub added successfully!";
            } else { $error = "Hub already exists."; }
        } else { $error = "Invalid characters in hub name."; }
    }
    elseif ($action === 'edit') {
        $old_name = $_POST['old_name'] ?? '';
        $new_name = trim($_POST['category'] ?? '');
        $found_index = -1;
        foreach ($categories as $idx => $cat) {
            $c_name = is_array($cat) ? ($cat['name'] ?? '') : $cat;
            if ($c_name === $old_name) {
                $found_index = $idx;
                break;
            }
        }

        if ($found_index !== -1 && $new_name && preg_match('/^[a-zA-Z0-9 ]+$/', $new_name)) {
            if (!is_array($categories[$found_index])) {
                $categories[$found_index] = ['name' => $categories[$found_index], 'image' => ''];
            }
            $categories[$found_index]['name'] = $new_name;

            if (isset($_FILES['hub_image']) && $_FILES['hub_image']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['hub_image']['tmp_name'];
                $file_ext = strtolower(pathinfo($_FILES['hub_image']['name'], PATHINFO_EXTENSION));
                if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    // Delete old image if exists
                    if ($categories[$found_index]['image']) {
                        @unlink(__DIR__ . '/..' . $categories[$found_index]['image']);
                    }
                    $new_file_name = uniqid('hub_', true) . '.' . $file_ext;
                    if (move_uploaded_file($file_tmp, UPLOADS_DIR . $new_file_name)) {
                        $categories[$found_index]['image'] = '/uploads/' . $new_file_name;
                    }
                }
            }
            file_put_contents(CATEGORIES_FILE, json_encode($categories));
            $success = "Logo Hub updated successfully!";
        } else { $error = "Failed to update hub."; }
    }
    elseif ($action === 'delete') {
        $del_name = $_POST['hub_name'] ?? '';
        $new_categories = [];
        foreach ($categories as $cat) {
            $c_name = is_array($cat) ? ($cat['name'] ?? '') : $cat;
            if ($c_name === $del_name) {
                if (is_array($cat) && $cat['image']) {
                    @unlink(__DIR__ . '/..' . $cat['image']);
                }
                continue;
            }
            $new_categories[] = $cat;
        }
        file_put_contents(CATEGORIES_FILE, json_encode($new_categories));
        $success = "Logo Hub deleted successfully!";
    }
}

$categories_raw = json_decode(file_get_contents(CATEGORIES_FILE), true);
$categories = [];
foreach ($categories_raw as $cat) {
    if (is_array($cat)) {
        $categories[] = $cat;
    } else {
        $categories[] = ['name' => $cat, 'image' => ''];
    }
}

$edit_hub = null;
if (isset($_GET['edit'])) {
    foreach ($categories as $cat) {
        if ($cat['name'] === $_GET['edit']) {
            $edit_hub = $cat;
            break;
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
    <title>U-shadow | Admin - Manage Hubs</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><path d=%22M50 5 L95 50 L50 95 L5 50 Z%22 fill=%22%23333%22 stroke=%22%23eee%22 stroke-width=%225%22/><text x=%2250%22 y=%2265%22 font-size=%2240%22 font-weight=%22bold%22 fill=%22white%22 text-anchor=%22middle%22 font-family=%22Arial%22>U</text></svg>">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">

<div class="sidebar">
    <div class="sidebar-brand">U-SHADOW</div>
    <ul class="sidebar-menu">
        <li><a href="/admin/index.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="/admin/victims.php"><i class="fas fa-skull"></i> Victims Log</a></li>
        <li><a href="/admin/users.php"><i class="fas fa-users"></i> User Management</a></li>
        <li class="active"><a href="/admin/categories.php"><i class="fas fa-plus-circle"></i> Manage Hubs</a></li>
        <li><a href="/admin/password.php"><i class="fas fa-key"></i> Security</a></li>
        <li><a href="/dashboard.php" style="color: #6ed3ff;"><i class="fas fa-arrow-left"></i> User Dashboard</a></li>
        <li style="margin-top: 100px;"><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a></li>
    </ul>
</div>

<main class="main-content">
    <div style="text-align: center; margin-bottom: 20px;">
        <a href="https://t.me/your_telegram" target="_blank" style="color: blue; text-decoration: underline; font-weight: 600; font-size: 18px;">Join Our Telegram</a>
    </div>

    <div class="top-bar">
        <div class="page-title">
            <h1>LOGO HUB MANAGEMENT</h1>
        </div>
        <div class="user-profile">
            <div class="user-info">
                <span class="name"><?php echo s($_SESSION['user']['username']); ?></span>
                <span class="role">Administrator</span>
            </div>
            <div class="avatar">A</div>
        </div>
    </div>

    <?php if ($edit_hub): ?>
    <section class="section" style="max-width: 600px; margin-bottom: 40px; border: 2px solid var(--primary);">
        <div class="section-header">
            <h2><i class="fas fa-edit"></i> Edit Logo Hub: <?php echo s($edit_hub['name']); ?></h2>
            <a href="/admin/categories.php" class="btn-preview">CANCEL</a>
        </div>
        <div class="section-content">
            <form action="/admin/categories.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="old_name" value="<?php echo s($edit_hub['name']); ?>">
                <div class="form-group">
                    <label>Update Hub Name</label>
                    <input type="text" name="category" value="<?php echo s($edit_hub['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Replace Hub Image</label>
                    <?php if ($edit_hub['image']): ?>
                        <div style="margin-bottom: 10px;"><img src="<?php echo s($edit_hub['image']); ?>" style="height: 50px;"></div>
                    <?php endif; ?>
                    <input type="file" name="hub_image" accept="image/*" style="padding: 10px; background: #fff; border: 1px solid #ddd; width: 100%;">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Update Logo Hub</button>
            </form>
        </div>
    </section>
    <?php else: ?>
    <section class="section" style="max-width: 600px; margin-bottom: 40px;">
        <div class="section-header">
            <h2><i class="fas fa-plus"></i> Add New Brand Logo Hub</h2>
        </div>
        <div class="section-content">
            <?php if ($success): ?>
                <div class="status-badge success" style="display: block; margin-bottom: 20px; text-align: center;"><?php echo s($success); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="status-badge error" style="display: block; margin-bottom: 20px; text-align: center;"><?php echo s($error); ?></div>
            <?php endif; ?>

            <form action="/admin/categories.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="create">
                <div class="form-group">
                    <label>Hub Display Name</label>
                    <input type="text" name="category" placeholder="e.g. Discord, Slack, Pinterest" required>
                </div>
                <div class="form-group">
                    <label>Hub Image (Icon/Logo)</label>
                    <input type="file" name="hub_image" accept="image/*" style="padding: 10px; background: #fff; border: 1px solid #ddd; width: 100%;">
                    <small style="color: var(--gray); display: block; margin-top: 5px;">Optional. Recommended square image.</small>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Create Logo Hub</button>
            </form>
        </div>
    </section>
    <?php endif; ?>

    <section class="section">
        <div class="section-header">
            <h2><i class="fas fa-th-large"></i> Active Logo Hubs</h2>
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
                    $cat_name = $cat['name'];
                    $cat_image = $cat['image'];
                    $cat_clean = str_replace(' ', '', strtolower($cat_name));
                    $icon_class = $icons[$cat_clean] ?? 'fa-solid fa-link';
                ?>
                    <div class="link-item" style="border-left: 4px solid var(--primary); padding: 15px;">
                        <div style="font-weight: 700; font-size: 18px; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; height: 100px; background: #f0f4f8; border-radius: 8px; overflow: hidden;">
                            <?php if ($cat_image): ?>
                                <img src="<?php echo s($cat_image); ?>" alt="<?php echo s($cat_name); ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            <?php else: ?>
                                <i class="<?php echo $icon_class; ?>" style="font-size: 40px; color: var(--primary);"></i>
                            <?php endif; ?>
                        </div>
                        <div style="text-align: center; font-weight: bold; margin-bottom: 10px;"><?php echo s($cat_name); ?></div>
                        <div class="copy-box" style="margin-top: 10px;">
                            <input type="text" value="hub.php?cat=<?php echo s($cat_clean); ?>" readonly style="font-family: monospace;">
                        </div>
                        <div style="margin-top: 15px; display: flex; justify-content: space-between; align-items: center;">
                             <div>
                                <a href="?edit=<?php echo urlencode($cat_name); ?>" class="btn-preview" style="margin-right: 5px;"><i class="fas fa-edit"></i> EDIT</a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this hub? This cannot be undone.');">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="hub_name" value="<?php echo s($cat_name); ?>">
                                    <button type="submit" class="btn-preview" style="color: var(--danger); border-color: var(--danger); background: transparent; cursor: pointer;"><i class="fas fa-trash"></i> DEL</button>
                                </form>
                             </div>
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
