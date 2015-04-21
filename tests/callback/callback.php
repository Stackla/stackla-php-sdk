<?php
require('../bootstrap.php');
require('config.php');

$access_code = $_GET['code'];

$credentials = new Stackla\Core\Credentials($host, null, $stack);
$response = $credentials->generateToken($client_id, $client_secret, $access_code, $callback);
echo "<pre>";
echo "======\n";
if ($response === false) {
    echo "Failed creating access token.\n";
} else {
    file_put_contents('access_token.txt', $credentials->token);
    echo "your access token is '{$credentials->token}'\n";
}
echo "======";
echo "</pre>";
?>
