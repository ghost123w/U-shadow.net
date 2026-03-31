<?php
require_once __DIR__ . "/config.php";
$users = json_decode(file_get_contents(USERS_FILE), true);
$users[] = ["username" => "admin", "password" => password_hash("user123", PASSWORD_DEFAULT), "is_admin" => true];
file_put_contents(USERS_FILE, json_encode($users));
?>
