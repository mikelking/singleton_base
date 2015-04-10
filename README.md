# Singleton_Base

#### This singleton class is intended for use in php projects and by serendipitous coincidence is a great base for use in WordPress plugins.

- The code in the project is licensed under BSD(3-clause) http://opensource.org/licenses/BSD-3-Clause because there is nothing WordPress specific and it is intended to apply to a larger audience. You are free to incorporate this subsystem code into your projects in the same way that WordPress has incorporated several other BSD3 licensed subsystems into the core project. These subsystems retain their licensing because BSD3 and are happily compatible with the GPL goodness of the rest of the project. In short this code *must* remain BSD3 and distributed with it's license references intact, but you are free to license your code as you see fit.    

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
- The third and least recommended method is to hack the .htaccess to include the appropriate settings. Ultimately the method you choose is up to you. 

- To use this class with WordPress:

    ###### The way this file is intended to be used is as a mu-plugin because it will be automatically loaded by WordPress on startup. This will make the class available to the entirety of WordPress and immediately resolve any namespace conflicts. 
    
    ###### However, if you intend on building a standalone plugin that might be published on WordPress.org then you will need start with the standalone-base implementation and carefully set the namespace accordingly to avoid clashing with anyone else who has already used this implementation. 
    
The standalone plugin is a framework guide on usage. The difference between a standalone plugin and a mu based one is the additional namespacing requirement and the require statment;
 
```php
<?php
namespace YOUR_PLUGIN_NAMESPACE
require(__DIR__ . '/inc/singleton-base.php');
```

There is a huge advantage to using the mu based solution in that if you develop multiple plugins based on this singleton class and the project is updated or improved you simple drop in the single file and all of your plugins immediately receive the benefits.