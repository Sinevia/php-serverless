<?php

/* AUTO */
$roboFunctionName = ""; // !!! Do not change will be modified automatically during deployment
\Sinevia\Registry::setIfNotExists("FUNCTION_NAME", $roboFunctionName);

/* INIT */
\Sinevia\Registry::setIfNotExists("URL_LIVE", 'https://YOUR_LIVE_URL');
\Sinevia\Registry::setIfNotExists("URL_STAGING", 'https://YOUR_STAGING_URL');
\Sinevia\Registry::setIfNotExists("URL_LOCAL", 'http://localhost:32222');
\Sinevia\Registry::setIfNotExists("FUNCTION_LIVE", 'YOUR_FUNCTION');
\Sinevia\Registry::setIfNotExists("FUNCTION_STAGING", 'YOUR_FUNCTION_STAGING');

/* TESTING */
\Sinevia\Registry::setIfNotExists("TESTING_FRAMEWORK", 'TESTIFY'); // Options: TESTIFY, PHPUNIT, NONE

/* ENVIRONMENT */
if (\Sinevia\Registry::equals('FUNCTION_NAME', \Sinevia\Registry::get('FUNCTION_STAGING')) == true) {
    $env = 'staging';
} elseif (\Sinevia\Registry::equals('FUNCTION_NAME', \Sinevia\Registry::get('FUNCTION_LIVE')) == true) {
    $env = 'live';
} elseif (strpos(($_SERVER['HTTP_HOST'] ?? ''), 'localhost') !== false) {
    $env = 'local';
} else if (defined('ENVIRONMENT') and ENVIRONMENT == "testing") {
    $env = 'testing';
} else {
    $env = 'unrecognized';
}
\Sinevia\Registry::setIfNotExists("ENVIRONMENT", $env);

/* URL */
if (\Sinevia\Registry::equals("ENVIRONMENT", "testing")) {
    \Sinevia\Registry::setIfNotExists("URL_BASE", \Sinevia\Registry::get('URL_LOCAL'));
}
if (\Sinevia\Registry::equals("ENVIRONMENT", "local")) {
    \Sinevia\Registry::setIfNotExists("URL_BASE", \Sinevia\Registry::get('URL_LOCAL'));
}
if (\Sinevia\Registry::equals("ENVIRONMENT", "staging")) {
    \Sinevia\Registry::setIfNotExists("URL_BASE", \Sinevia\Registry::get('URL_STAGING'));
}
if (\Sinevia\Registry::equals("ENVIRONMENT", "live")) {
    \Sinevia\Registry::setIfNotExists("URL_BASE", \Sinevia\Registry::get('URL_LIVE'));
}

/* FILE SYSTEM */
\Sinevia\Registry::setIfNotExists("DIR_BASE", __DIR__);
\Sinevia\Registry::setIfNotExists("DIR_APP", __DIR__ . '/app');
\Sinevia\Registry::setIfNotExists("DIR_MIGRATIONS_DIR", __DIR__ . '/app/database/migrations/');

/* 
 * CONFIGURATION
 * The configuration file allows you to add variables speecific for each environment
 * These are located in /app/config
 */
$envConfigFile = __DIR__ . '/app/config/' . $env . '.php';
if(file_exists($envConfigFile)) {
    $envConfigVars = include(__DIR__ . '/app/config/' . $env . '.php');
    
    if(is_array($envConfigVars)){
        foreach ($envConfigVars as $key => $value) {
            \Sinevia\Registry::setIfNotExists($key, $value);
        }
    }
}
