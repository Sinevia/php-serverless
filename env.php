<?php

/* TESTING */
\Sinevia\Registry::setIfNotExists("TESTING_FRAMEWORK", 'TESTIFY'); // Options: TESTIFY, PHPUNIT, NONE

/* FILE SYSTEM */
\Sinevia\Registry::setIfNotExists("DIR_BASE", __DIR__);
\Sinevia\Registry::setIfNotExists("DIR_APP", __DIR__ . '/app');
\Sinevia\Registry::setIfNotExists("DIR_MIGRATIONS_DIR", __DIR__ . '/app/database/migrations/');

/* ENVIRONMENT */
\Sinevia\Registry::setIfNotExists("ENVIRONMENT", "unrecognized"); // !!! Do not change will be modified automatically during deployment

/* 
 * LOAD ENVIRONMENT CONFIGURATIONS
 * The configuration files allow to add variables specific for each environment
 * These are located in /app/config
 */
function loadEnvConf($environment)
{
    $envConfigFile = __DIR__ . '/app/config/' . $environment . '.php';
    if (file_exists($envConfigFile)) {
        $envConfigVars = include(__DIR__ . '/app/config/' . $environment . '.php');
    
        if (is_array($envConfigVars)) {
            foreach ($envConfigVars as $key => $value) {
                \Sinevia\Registry::setIfNotExists($key, $value);
            }
        }
    }
}

loadEnvConf(\Sinevia\Registry::get('ENVIRONMENT'));
