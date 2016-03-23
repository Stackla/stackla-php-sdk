# OAuth2

This is an example of how to generate an OAuth2 access token and implement it in Stackla.

Before running any test, please copy config.php.dist to config.php in tests/callback and change the value to match your stack.
```php
<?php
$stack = "YOUR_STACK";
$host  = "https://api.stackla.com/api/";
$client_id = 'YOUR_CLIENT_ID';
$client_secret = 'YOUR_CLIENT_SECRET';
$callback = 'http://localhost:8000/callback.php';
```

Please run the following to create a simple webserver:
```sh
$ php -S localhost:8000
```

Then run this url in your browser to authenticate your app:
```
http://localhost:8000/access.php
```
