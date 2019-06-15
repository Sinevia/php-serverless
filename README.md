# SERVERLESS FRAMEWORK

The first fully functional PHP framework for serverless.

## INSTALLATION ##
```
composer create-project --prefer-dist sinevia/php-serverless .
```

## AFTER INSTALLATION ##
- Delete the phpunit.xml file, if you are not going to use PHPUnit for testing
- Change the settings in /serverless.yaml
- Change the settings in /RoboFile
- Change the settings in /env.php


## DEVELOPMENT ##

To start working on the project run the built in PHP server:

```
php -S localhost:32222
```

or using the helper function

```
vendor/bin/robo serve
```

Then open in browser: http://localhost:32222/


## DEPLOYMENT ##
```
vendor/bin/robo init
vendor/bin/robo deploy
```

## HELPER FUNCTIONS ##

A RoboFile exists with some helper functionality. 

- Serve the site for development

```
vendor/bin/robo serve
```

- Open dev url from terminal

```
vendor/bin/robo open:dev
```

- Open live url from terminal

```
vendor/bin/robo open:live
```

## TESTING ##

Two testing frameworks supported out of the box - Testify.php (preferred, and preinstaled) and PHPUnit.

To decide which modify the setting in the RoboFile.

### Testing with Testify.php ###

Testify is a small PHP testing library with no extenal dependencies: https://github.com/BafS/Testify.php

Place your tests in /tests/test.php

To run the tests

```
php tests/test.php
```
or
```
vendor/bin/robo test
```

### Testing with PHPUnit ###

PHPUnit is a huge PHP testing library with lots of usually "unneeded" dependencies: https://phpunit.de/

To install the framework with all the dependencies

```
composer require --dev phpunit/phpunit
```

Place your settigs in /phpunit.xml. Place your tests in /tests

To run the tests

```
vendor/bin/phpunit
```
or
```
vendor/bin/robo test
```


## SERVING STATIC FILES ##

Multiple options

Local CSS and JavaScript files are best to be served minified inline. Helper functions are added

```
<?php echo joinCss(['/css/main.css','/css/secondary.css']); ?>
```

Small images serve inline using as Base64 data.

```
<img src="<?php echo base64ed('/public/img/avatar.png'); ?>" />
```

To serve static files separately place them in the public directory.

```
/public/css/main.css
```

For remote static files use CDN, S3 or other storage.


## FUNCTIONS ##

Functions are defined in file /app/functions.php.

### basePath($path = '') ###

Returns the top most (root, base) path of the application

### baseUrl($path = '') ###
Returns the top most (root, base) URL of the application

### db() ###
Returns a database instance


### env($key, $default = '') ###
Returns an env variable from OPEN WHISK

### htmlFormatPriceWithCurrencySymbol($amount, $currency) ###

### image2DataUri($imagePath) ###
Converts an image path to data URI

### isGet ###
Checks if this is a GET request

### isPost ###
Checks if this is a POST request

### joinCss($styles, $options = []) ###
Joins multiple CSS files, and optionally minifies them

### joinJs($scripts, $options = []) ###
Joins multiple JavaScript files, and optionally minifies them

### redirect($url) ###
Redirects to the specified URL

### req($name, $default = null, $functions = []) ###
Returns the requested $_REQUEST name-value pair if it exists

### sess($name, $default = null, $functions = [], $options = []) ###
Returns the requested $_SESSION name-value pair if it exists

### once($name, $default = null, $functions = [], $options = []) ###
Returns a once value if it exists in $_SESSION. After the value is returned, it is deleted

### function reqOrSess($name, $default = null, $functions = []) ###

### ui($view, $vars = array(), $options = array()) ###
Renders a template from app/views. If no extension is specified .phtml is added

### view($view, $data) ###
Renders a Blade template from /views. A /cache folder is required. If using Eloquent for data management this function will clash, delete it.

To use this function you must install Blade:
```
composer require jenssegers/blade
```
