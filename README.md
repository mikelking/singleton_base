# Singleton_Base

Version 1.0.8

### We are looking for a new name

- This project has undergone a number of changes over the years since is began as a single class. Now that the composer piece has been riddled out it is time for this project to take on a new name. The three top contenders are Project: Scotch, Project: Bacon or Project Viking because that single class has evolved into a framework.

### Bacon is the front runner

- Bacon sums up the essence of this framework. Like bacon this framework builds some usefully tasty tools but they are intended to help you build even more amazingly delicious projects. So this framework should like bacon make your projects better. Think of your project like a grilled cheese sandwich and adding this framework (like bacon) just kicks that sandwich up to a whole new level.

note to self: https://git-scm.com/book/en/v2/Git-Basics-Tagging

#### This singleton class is intended for use in php projects and by serendipitous coincidence is a great base for use in WordPress plugins. This has evolved into a library of classes aimed at improving plugin development and reduction of technical debt.

- The code in the project is licensed under BSD(3-clause) http://opensource.org/licenses/BSD-3-Clause because there is nothing WordPress specific and it is intended to apply to a larger audience. You are free to incorporate this library subsystem code into your projects in the same way that WordPress has incorporated several other BSD3 licensed subsystems into the core project. These subsystems retain their licensing because BSD3 projects are happily compatible with the GPL goodness of the rest of the project. In short this code *must* remain BSD3 and distributed with it's license references intact, but you are free to license *your* code as you see fit.

- This system has been crafted in an attempt to make it ~~submodule~~ composer installable. The main goal it so simplify the repository structure and not store superfluous copies of the WordPress core or plugins that can be installed via other means. As you can see the submodule plan has been abandoned in lieu of the more flexible composer. This is because as a submodule this project would hijack the entire mu-plugins path clobbering anything else you want to install there. As a composer module it will ignore pre-existing files.

- When you run `composer update` is basically checks out the project repo and installs it into the wordpress/wp-content/mu-plugins/ directory. This means you get everything including this README.md and the plugin-stub directory. You may use the plugin-stup to build your own plugins dependent upon this `yet to be named` framework. The following is a list of the files and directory you should see added to mu-plugins.

```
	000-singleton-base.php
	005-debug.php
	005-wp-exception.php
	010-base-plugin.php
	015-wp-base.php
	020-admin-message.php
	020-advanced_blog_data.php
	020-cookie-controller.php
	020-cpt-controller.php
	020-tax-controller.php
	020-url-magick.php
	020-variation-base.php
	LICENSE
	README.md
	composer.json
	cookie-controller
	plugin-stub
```

- Simply `copy -r plugin-stub ../plugins/your-new-plugin-name` and you can start modifying the plugin.php to build your plugin on top of this framework. Don't worry about the raw plugin-stub directory sitting in your mu-plugins path, because WordPress intentionally ignores ALL subdirectories in that space.

- This system relies on you having properly set your timezone and error reporting level in PHP. While there are numerous ways in which to do this the best practice is either in the php.ini or vhost config. The php.ini if extremely well documented so I will only cover the other options here;
```php
php_value date.timezone "America/New_York"
php_flag log_errors On
php_value error_reporting "E_STRICT"
php_flag display_errors Off
php_value error_log 'PATH/TO/THE/php_error.log'
```
- In some cases php_value error_reporting requires an integer value like 32767 in liue of the 'E_STRICT' constant modifiers.

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

    ###### These files are intended to be used as mu-plugins because it will be automatically loaded by WordPress on startup. This will make the class available to the entirety of WordPress and immediately resolve any namespace conflicts.

There is a huge advantage to using the mu based solution in that if you develop multiple plugins based on this singleton class and the project is updated or improved you simple drop in the single file and all of your plugins immediately receive the benefits.

### That's about all there is to it, because mu-plugins are kind of set it and forget it utilities. In addition because they have not on/off switches you do not have to worry about someone inadvertently deactivating ALL of your plugins that depend on it.

 Ok so you are wondering what's the catch? Well for one WordPress does not install the mu-plugins tree; therefore, you must do that manually. In addition you must manually keep it up to date. Finally mu-plugins do not normally (although there are ways around this) work from within subdirectories like regular plugins. They are meant to have limited scope and minimal (generally zero) configuration.
