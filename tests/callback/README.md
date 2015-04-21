# OAuth2

This is the example on how to generate OAuth2 access token and implementation in stackla.

Create you app using stackla admin portal:
```
https://my.stackla.com/[YOUR_STACK]/admin/stacklaapi/manage_clients
```

Before running any test, please copy config.php.dist to config.php in tests/callback and change the value to match your stack.
```php
<?php
$stack = "YOUR_STACK";
$host  = "https://api.stackla.com/api/";
$client_id = 'YOUR_CLIENT_ID';
$client_secret = 'YOUR_CLIENT_SECRET';
$callback = 'http://localhost:8000/callback.php';
```

Please run, to create simple webserver:
```sh
$ php -S localhost:8000
```

try run this url in browser to authenticate your app:
```
http://localhost:8000/access.php
```
