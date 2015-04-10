# singleton_base
A singleton class for use as base for plugins like used in WordPress

- This system replies on you having properly set your timezone and error reporting level in PHP. While there are numerous ways in which to do this the best practice is either in the php.ini or vhost config. The php.ini if extremely well documented so I will only cover the other options here;
```php
php_value date.timezone "America/New_York"
php_flag log_errors On
php_value error_reporting "E_STRICT"
php_flag display_errors Off
php_value error_log 'PATH/TO/THE/php_error.log'
```
- Another option is to set these in the wp-config.php which is good for the entire site.
```php
define('DEFAULT_TIMEZONE', 'America/New_York');
define('DEFAULT_ERROR_LEVEL', 'E_SRICT');
```
- Or if you prefer to keep the error localized you can add the following to your plugin controller.
```php
date_default_timezone_set("America/New_York");
error_reporting(E_STRICT);
```
 - To use this class with WordPress:

    The way this file is intended to be used is as a mu-plugin because it will be automatically loaded by WordPress on startup. This will make the class available to the entirety of WordPress