<?php
// google-login.php - Start Google OAuth Flow

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/_base.php';

$client = new Google_Client();
$client->setAuthConfig('credentials.json');           // Make sure this file exists
$client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);
$client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);

// IMPORTANT: Must be exactly the same as in Google Cloud Console
$client->setRedirectUri('http://localhost:8080/callback.php');

$authUrl = $client->createAuthUrl();
header("Location: " . $authUrl);
exit();