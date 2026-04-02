<?php
require_once __DIR__ . '/config.php';

$category = trim($_GET['cat'] ?? '');
$user = trim($_GET['user'] ?? 'anonymous');

// Basic validation for category
$categories = json_decode(file_get_contents(CATEGORIES_FILE), true);
$cat_found = false;
$cat_image = '';
foreach ($categories as $cat) {
    $c_name = is_array($cat) ? ($cat['name'] ?? '') : $cat;
    if (strcasecmp(str_replace(' ', '', $c_name), $category) === 0) {
        $cat_found = true;
        $display_cat = $c_name;
        $cat_image = is_array($cat) ? ($cat['image'] ?? '') : '';
        break;
    }
}

if (!$cat_found) {
    die('Invalid hub category.');
}

// Log click for analytics
$analytics = json_decode(file_get_contents(ANALYTICS_FILE), true);
$analytics[] = [
    'timestamp' => date('Y-m-d H:i:s'),
    'category' => strtolower($category),
    'user_id' => $user,
    'source_ip' => $_SERVER['REMOTE_ADDR']
];
file_put_contents(ANALYTICS_FILE, json_encode($analytics));

// Define platform icons for the hub page
$icons = [
    'facebook' => 'fa-brands fa-facebook',
    'instagram' => 'fa-brands fa-instagram',
    'tiktok' => 'fa-brands fa-tiktok',
    'snapchat' => 'fa-brands fa-snapchat',
    'telegram' => 'fa-brands fa-telegram',
    'discord' => 'fa-brands fa-discord',
    'twitter' => 'fa-brands fa-twitter',
    'x' => 'fa-brands fa-x-twitter',
    'youtube' => 'fa-brands fa-youtube',
    'pinterest' => 'fa-brands fa-pinterest',
    'linkedin' => 'fa-brands fa-linkedin',
    'whatsapp' => 'fa-brands fa-whatsapp',
    'spotify' => 'fa-brands fa-spotify'
];
$icon = $icons[strtolower($category)] ?? 'fa-solid fa-link';
$redirect_url = "https://www.google.com/search?q=" . urlencode($display_cat);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-SHADOW | <?php echo s($display_cat); ?> Logo Hub</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><path d=%22M50 5 L95 50 L50 95 L5 50 Z%22 fill=%22%23333%22 stroke=%22%23eee%22 stroke-width=%225%22/><text x=%2250%22 y=%2265%22 font-size=%2240%22 font-weight=%22bold%22 fill=%22white%22 text-anchor=%22middle%22 font-family=%22Arial%22>U</text></svg>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .card { background: #fff; padding: 50px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); text-align: center; max-width: 450px; width: 90%; }
        .logo-circle { width: 80px; height: 80px; background: #4361ee15; color: #4361ee; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 25px; }
        h1 { font-size: 24px; margin: 0 0 10px; color: #1e1e2d; }
        p { color: #64748b; line-height: 1.6; font-size: 15px; margin-bottom: 30px; }
        .btn-proceed { display: block; padding: 15px; background: #4361ee; color: #fff; text-decoration: none; border-radius: 10px; font-weight: 600; transition: all 0.3s; }
        .btn-proceed:hover { background: #3f37c9; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3); }
        .footer-note { margin-top: 30px; font-size: 12px; color: #94a3b8; }
        .footer-note strong { color: #4361ee; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo-circle" style="<?php echo $cat_image ? 'background: transparent;' : ''; ?>">
            <?php if ($cat_image): ?>
                <img src="<?php echo s($cat_image); ?>" alt="<?php echo s($display_cat); ?>" style="width: 100%; height: 100%; object-fit: contain; border-radius: 12px;">
            <?php else: ?>
                <i class="<?php echo $icon; ?>"></i>
            <?php endif; ?>
        </div>
        <h1><?php echo s($display_cat); ?> Hub</h1>
        <p>You are connecting to a professional hub managed by <strong><?php echo s($user); ?></strong>. Your visit is being tracked for performance optimization.</p>

        <a href="<?php echo s($redirect_url); ?>" class="btn-proceed">Proceed to <?php echo s($display_cat); ?></a>

        <div class="footer-note">
            Powered by <strong>U-SHADOW Professional v3.5</strong><br>
            Secure Link Management & Real-time Analytics
        </div>
    </div>
</body>
</html>
