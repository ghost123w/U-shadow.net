<?php
require_once __DIR__ . '/../config.php';

// Redirect to external site to "demonstrate" the link hub functionality
// In a real scenario, this would track the click first
$category = basename($_SERVER['PHP_SELF'], '.php');
$user = $_GET['user'] ?? 'anonymous';

$url = "https://www.google.com/search?q=" . urlencode($category);

// Log click
$analytics = json_decode(file_get_contents(ANALYTICS_FILE), true);
$analytics[] = [
    'timestamp' => date('Y-m-d H:i:s'),
    'category' => $category,
    'user_id' => $user,
    'source_ip' => $_SERVER['REMOTE_ADDR']
];
file_put_contents(ANALYTICS_FILE, json_encode($analytics));

header("Location: $url");
exit();
?>
