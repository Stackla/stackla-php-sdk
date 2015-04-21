<?php
require('../bootstrap.php');
require('config.php');

$credentials = new Stackla\Core\Credentials($host, null, $stack);
$access_uri = $credentials->getAccessUri($client_id, $client_secret, $callback);

?>
<html>
    <body>
        <a href="<?php echo $access_uri; ?>">Generate Access Token</a>
    </body>
</html>
