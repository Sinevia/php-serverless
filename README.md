# SERVERLESS FRAMEWORK

The first fully functional PHP framework for serverless.

## INSTALLATION ##
```
composer create-project --prefer-dist sinevia/php-serverless .
```

## DEVELOPMENT ##

To start working on the project run the built in PHP server:

```
php -S localhost:32222
```

or using the heper function


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
