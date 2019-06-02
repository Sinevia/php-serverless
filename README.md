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

Then open in browser: http://localhost:32222/


## DEPLOYMENT ##
```
vendor/bin/robo init
vendor/bin/robo deploy
```

## HELPER FUNCTIONS ##

- Open dev url from terminal

```
vendor/bin/robo open:dev
```

- Open live url from terminal

```
vendor/bin/robo open:live
```
