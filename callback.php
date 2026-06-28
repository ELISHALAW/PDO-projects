<?php
// callback.php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/_base.php';   // This loads session_start() and your $_db connection

// Use modern namespaced classes to clear VS Code warnings
$client = new Google\Client();
$client->setAuthConfig('credentials.json');
$client->addScope(Google\Service\Oauth2::USERINFO_EMAIL);
$client->addScope(Google\Service\Oauth2::USERINFO_PROFILE);
$client->setRedirectUri('http://localhost:8080/callback.php');  

if (isset($_GET['code'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token);

        $oauth2 = new Google\Service\Oauth2($client);
        $userInfo = $oauth2->userinfo->get();

        $email = strtolower($userInfo->getEmail());
        $name  = $userInfo->getName();

        // 1. Corrected to use $_db and your actual table name 'user'
        $stmt = $_db->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Existing user → Reset login counters if they were locked out
            $update = $_db->prepare("UPDATE user SET login_attempts = 0, block_until = NULL WHERE user_id = ?");
            $update->execute([$user['user_id']]);
        } else {
            // New user → Register automatically with required fields
            // Generate a fallback username from their email prefix
            $username = explode('@', $email)[0];
            $dummyPassword = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
            $defaultStatus = 'user';

            $stmt = $_db->prepare("INSERT INTO user (name, username, email, password, status, login_attempts) 
                                  VALUES (?, ?, ?, ?, ?, 0)");
            $stmt->execute([$name, $username, $email, $dummyPassword, $defaultStatus]);
            
            // Re-fetch the row to obtain all database defaults
            $stmt = $_db->prepare("SELECT * FROM user WHERE user_id = ?");
            $stmt->execute([$_db->lastInsertId()]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // 2. Synchronized Session Keys to match your native login.php exactly
        $_SESSION['login']    = true;
        $_SESSION['id']       = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['status']   = $user['status'];

        // 3. Dynamic routing based on account privileges
        $location = ($user['status'] === 'admin') ? "./Admin/adminHomepage.php" : "index.php";
        header("Location: $location");
        exit();

    } catch (Exception $e) {
        echo "Google Login Error: " . htmlspecialchars($e->getMessage());
    }
} else {
    header("Location: login.php");
    exit();
}