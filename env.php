<?php

/* ENVIRONMENT */
$env = (strpos(($_SERVER['HTTP_HOST'] ?? ''), 'localhost') === false) ? 'live' : 'local'; // Default is live
\Sinevia\Registry::setIfNotExists("ENVIRONMENT", $env);

/* URL */
$liveUrl = 'https://YOURURL';
$localUrl = 'http://localhost:32222';
$url = \Sinevia\Registry::equals("ENVIRONMENT", "live") ? $liveUrl : $localUrl;
\Sinevia\Registry::setIfNotExists("URL_BASE", $url);

/* FILE SYSTEM */
\Sinevia\Registry::setIfNotExists("DIR_BASE", __DIR__);
\Sinevia\Registry::setIfNotExists("DIR_APP", __DIR__ . '/app');
\Sinevia\Registry::setIfNotExists("DIR_MIGRATIONS_DIR", __DIR__ . '/app/database/migrations/');

/* DATABASE */
$dbType = \Sinevia\Registry::equals("ENVIRONMENT", "live") ? 'mysql' : 'mysql';
$dbHost = \Sinevia\Registry::equals("ENVIRONMENT", "live") ? 'LIVEHOST' : 'DEVHOST';
$dbName = \Sinevia\Registry::equals("ENVIRONMENT", "live") ? 'LIVENAME' : 'DEVNAME';
$dbUser = \Sinevia\Registry::equals("ENVIRONMENT", "live") ? 'LIVEUSER' : 'DEVUSER';
$dbPass = \Sinevia\Registry::equals("ENVIRONMENT", "live") ? 'LIVEPASS' : 'DEVPASS';
$dbPort = \Sinevia\Registry::equals("ENVIRONMENT", "live") ? '3306' : '3306';

\Sinevia\Registry::setIfNotExists("DB_TYPE", $dbType);
\Sinevia\Registry::setIfNotExists("DB_HOST", $dbHost);
\Sinevia\Registry::setIfNotExists("DB_NAME", $dbName);
\Sinevia\Registry::setIfNotExists("DB_USER", $dbUser);
\Sinevia\Registry::setIfNotExists("DB_PASS", $dbPass);
\Sinevia\Registry::setIfNotExists("DB_PORT", $dbPort);
