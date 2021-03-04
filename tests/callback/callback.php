<?php
session_start();

require('../bootstrap.php');
require('config.php');

$access_code = $_GET['code'];

$credentials = new Stackla\Core\Credentials($host, null, $stack);
$response = $credentials->generateToken($client_id, $client_secret, $access_code, $callback);
if ($response === false) {
  echo "<pre>";
  echo "======\n";
  echo "Failed creating access token.\n";
  echo "======";
  echo "</pre>";
} else {
  $_SESSION['token'] = $credentials->token;
  header("Location: /");
  die();
  echo "your access token is '{$credentials->token}'\n";
}

