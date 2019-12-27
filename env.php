<?php
if (isLocal()) {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
}

/* TESTING */
\Sinevia\Registry::setIfNotExists("TESTING_FRAMEWORK", 'TESTIFY'); // Options: TESTIFY, PHPUNIT, NONE

/* FILE SYSTEM */
\Sinevia\Registry::setIfNotExists("DIR_BASE", __DIR__);
\Sinevia\Registry::setIfNotExists("DIR_APP", __DIR__ . '/app');
\Sinevia\Registry::setIfNotExists("DIR_CONFIG", __DIR__ . '/app/config');
\Sinevia\Registry::setIfNotExists("DIR_MIGRATIONS_DIR", __DIR__ . '/app/database/migrations/');

/* ENVIRONMENT */
\Sinevia\Registry::setIfNotExists("ENVIRONMENT", isLocal() ? "local" : "unrecognized"); // !!! Do not change will be modified automatically during deployment

/* 
 * LOAD ENVIRONMENT CONFIGURATIONS
 * The configuration files allow to add variables specific for each environment
 * These are located in /app/config
 */
loadEnvConf(\Sinevia\Registry::get('ENVIRONMENT'));


/* REQUIRED FUNCTIONS */

/**
 * Loads the environment configuration variables
 * @param string $environment
 * @return void
 */
function loadEnvConf($environment)
{
    $envConfigFile = \Sinevia\Registry::get('DIR_CONFIG') . '/' . $environment . '.php';

    if (file_exists($envConfigFile)) {
        $envConfigVars = include($envConfigFile);

        if (is_array($envConfigVars)) {
            foreach ($envConfigVars as $key => $value) {
                \Sinevia\Registry::setIfNotExists($key, $value);
            }
        }
    }
}

/**
 * Checks whether the script runs on localhost
 * @return boolean
 */
function isLocal()
{
    if (isset($_SERVER['REMOTE_ADDR']) == false) {
        return false;
    }

    $whitelist = array(
        '127.0.0.1',
        '::1'
    );

    if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
        return true;
    }

    false;
}