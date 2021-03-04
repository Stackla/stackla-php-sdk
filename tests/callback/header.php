<?php

session_start();

require('../bootstrap.php');
require('config.php');

$credentials = new Stackla\Core\Credentials($host, null, $stack);
$access_uri = $credentials->getAccessUri($client_id, $client_secret, $callback);

?>
<div>
  <ul>
<?php if (!empty($_SESSION['token'])): ?>
    <li><a href="/oem.php">OEM Page</a></li>
<?php else: ?>
    <li><a href="<?=$access_uri?>">Login</a></li>
<?php endif; ?>
  </ul>
</div>
