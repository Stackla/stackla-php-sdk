<?php

include 'config.php';

if (is_readable('.access_token')) {
    $accessToken = file_get_contents('.access_token');
}

$params = array(
    'grant_type' => 'exchange_token'
);
$params['access_token'] = $accessToken;
$params['stack'] = $stack;
$params['client_id'] = $client_id;

// exchange short term token for session
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"{$host}oemsession?" . http_build_query($params));
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = json_decode(curl_exec($ch), 1);

curl_close ($ch);

$sessionId = null;
if ($response && !empty($response['data'])) {
    $sessionId = $response['data'];
}
?>
<html>
    <head>
        <style>
        body{
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
        }
        </style>
    </head>
    </body>
        <div style="overflow: auto; display: none;">
            <pre>
            <?= print_r($response) ?>
            </pre>
        </div>
        <div style="width:20%; max-width: 20%; display: inline-block;float: left;">
            <ul>
                <li><a class="stackla-menu" href="#content">Content</a></li>
                <li><a class="stackla-menu" href="#filters">Filters</a></li>
                <li><a class="stackla-menu" href="#tags">Tags</a></li>
            </ul>
        </div>
        <div class="stackla-oem-iframe" data-stack-name="<?= $stack ?>" data-session-id="<?= $sessionId ?>"></div>
        <script type='text/javascript'>
        window.StacklaOemConfig = {
            cdn: '<?= $oemHost ?>',
            domain: '<?= $oemHost ?>'
        };

        (function (d) {
            // embed.js CDN
            var cdn = window.StacklaOemConfig ? window.StacklaOemConfig.cdn : 'my.stackla.com';
            var t = d.createElement('script');
            t.type = 'text/javascript';
            t.src = 'https://' + cdn + '/media/js/common/oem_iframe_embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(t);
        }(document));
        </script>
    </body>
</html>
