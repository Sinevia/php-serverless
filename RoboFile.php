<?php

require_once __DIR__ . '/vendor/autoload.php';

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    private $testingFramework = null;

    public function __construct()
    {
        $this->testingFramework = \Sinevia\Registry::get('TESTING_FRAMEWORK', 'TESTIFY'); // Options: TESTIFY, PHPUNIT, NONE
    }

    /**
     * Installs the serverless framework
     */
    function init()
    {
        if (is_dir(__DIR__ . '/node_modules') == true) {
            return true;
        }
        $isSuccessful = $this->taskExec('npm')
            ->arg('update')
            ->run()->wasSuccessful();

        if ($isSuccessful == false) {
            return $this->say('Failed.');
        }
    }

    /**
     * Runs the tests
     * @return boolean true if tests successful, false otherwise
     */
    public function test()
    {
        $this->init();

        if ($this->testingFramework == "TESTIFY") {
            return $this->testWithTestify();
        }

        if ($this->testingFramework == "PHPUNIT") {
            return $this->testWithPhpUnit();
        }

        return true;
    }

    /**
     * Testing with PHPUnit
     * @url https://phpunit.de/index.html
     * @return boolean true if tests successful, false otherwise
     */
    function testWithPhpUnit()
    {
        $this->say('Running PHPUnit tests...');

        $isSuccessful = $this->taskExec('composer')
            ->arg('update')
            ->option('prefer-dist')
            ->option('optimize-autoloader')
            ->run()
            ->wasSuccessful();

        // 1. Run composer
        $isSuccessful = $this->taskExec('composer')
            ->arg('update')
            ->option('prefer-dist')
            ->option('optimize-autoloader')
            ->run()->wasSuccessful();

        if ($isSuccessful == false) {
            return false;
        }

        // 2. Run tests
        $isSuccessful = $this->taskExec('phpunit')
            ->dir('vendor/bin')
            ->option('configuration', '../../phpunit.xml')
            ->run()
            ->wasSuccessful();

        if ($isSuccessful == false) {
            return false;
        }

        return true;
    }

    /**
     * Testing with Testify
     * @url https://github.com/BafS/Testify.php
     * @return boolean true if tests successful, false otherwise
     */
    private function testWithTestify()
    {
        if (file_exists(__DIR__ . '/tests/test.php') == false) {
            $this->say('Tests Skipped. Not test file at: ' . __DIR__ . '/tests/test.php');
            return true;
        }

        $this->say('Running tests...');

        $isSuccessful = $this->taskExec('composer')
            ->arg('update')
            ->option('prefer-dist')
            ->option('optimize-autoloader')
            ->run()
            ->wasSuccessful();

        $result = $this->taskExec('php')
            ->dir('tests')
            ->arg('test.php')
            ->printOutput(true)
            ->run();

        $output = trim($result->getMessage());

        if ($result->wasSuccessful() == false) {
            $this->say('Test Failed');
            return false;
        }

        if ($output == "") {
            $output = shell_exec('php tests/test.php'); // Re-test, as no output on Linux Mint
            if (trim($output == "")) {
                $this->say('Tests Failed. No output');
                return false;
            }
        }

        if (strpos($output, 'Tests: [fail]') > -1) {
            $this->say('Tests Failed');
            return false;
        }

        $this->say('Tests Successful');

        return true;
    }

    public function migrate($environment)
    {
        // 1. Does the configuration file exists? No => Exit
        $this->say('1. Checking configuration...');
        $envConfigFile = \Sinevia\Registry::get('DIR_CONFIG') . '/' . $environment . '.php';

        if (file_exists($envConfigFile) == false) {
            return $this->say('Configuration file for environment "' . $environment . '" missing at: ' . $envConfigFile);
        }

        // 2. Load the configuration file for the enviroment
        \Sinevia\Registry::set("ENVIRONMENT", $environment);
        loadEnvConf(\Sinevia\Registry::get("ENVIRONMENT"));


        require 'app/functions.php';

        \Sinevia\Migrate::setDirectoryMigration(\Sinevia\Registry::get('DIR_MIGRATIONS_DIR'));
        \Sinevia\Migrate::setDatabase(db());
        \Sinevia\Migrate::$verbose = false;
        \Sinevia\Migrate::up();
    }


    public function deploy($environment)
    {
        // 1. Does the configuration file exists? No => Exit
        $this->say('1. Checking configuration...');
        $envConfigFile = \Sinevia\Registry::get('DIR_CONFIG') . '/' . $environment . '.php';

        if (file_exists($envConfigFile) == false) {
            return $this->say('Configuration file for environment "' . $environment . '" missing at: ' . $envConfigFile);
        }

        // 2. Load the configuration file for the enviroment
        \Sinevia\Registry::set("ENVIRONMENT", $environment);
        loadEnvConf(\Sinevia\Registry::get("ENVIRONMENT"));

        // 3. Check if serverless function name is set
        $functionName = \Sinevia\Registry::get('SERVERLESS_FUNCTION_NAME', '');

        if ($functionName == "") {
            return $this->say('SERVERLESS_FUNCTION_NAME not set for environment "' . $environment . '"');
        }

        if ($functionName == "{YOUR_LIVE_SERVERLESS_FUNCTION_NAME}") {
            return $this->say('SERVERLESS_FUNCTION_NAME not set for environment "' . $environment . '"');
        }

        // 4. Run tests
        $this->say('2. Running tests...');
        $isSuccessful = $this->test();

        if ($isSuccessful == false) {
            return $this->say('Failed');
        }

        // 5. Run composer (no-dev)
        $this->say('3. Updating composer dependncies...');
        $isSuccessful = $this->taskExec('composer')
            ->arg('update')
            ->option('prefer-dist')
            ->option('optimize-autoloader')
            ->run()->wasSuccessful();

        if ($isSuccessful == false) {
            return $this->say('Failed.');
        }

        // 6. Prepare for deployment
        $this->say('4. Prepare for deployment...');
        $this->taskReplaceInFile('env.php')
            ->from('("ENVIRONMENT", "unrecognized")')
            ->to('("ENVIRONMENT", "' . $environment . '")')
            ->run();

        $this->taskReplaceInFile('serverless.yaml')
            ->from('{YOURFUNCTION}')
            ->to($functionName)
            ->run();


        // 7. Deploy
        try {
            $this->say('5. Deploying...');
            $this->taskExec('sls')
                ->arg('deploy')
                ->option('function', $functionName)
                ->run();
        } catch (\Exception $e) {
            $this->say('There was an exception: ' . $e->getMessage());
        }

        // 8. Cleanup after deployment
        $this->say('6. Cleaning up...');
        $this->taskReplaceInFile('env.php')
            ->from('("ENVIRONMENT", "' . $environment . '")')
            ->to('("ENVIRONMENT", "unrecognized")')
            ->run();
        $this->taskReplaceInFile('serverless.yaml')
            ->from($functionName)
            ->to('{YOURFUNCTION}')
            ->run();
    }

    /**
     * Retrieves the logs from serverless
     */
    public function logs($environment)
    {
        /* START: Reload enviroment */
        \Sinevia\Registry::set("ENVIRONMENT", $environment);
        loadEnvConf(\Sinevia\Registry::get("ENVIRONMENT"));
        /* END: Reload enviroment */

        $functionName = \Sinevia\Registry::get('SERVERLESS_FUNCTION_NAME', '');
        if ($functionName == "") {
            return $this->say('SERVERLESS_FUNCTION_NAME not set for ' . $environment);
        }

        $this->taskExec('sls')
            ->arg('logs')
            ->option('function', $functionName)
            ->run();
    }

    public function open($environment)
    {
        /* START: Reload enviroment */
        \Sinevia\Registry::set("ENVIRONMENT", $environment);
        loadEnvConf(\Sinevia\Registry::get("ENVIRONMENT"));
        /* END: Reload enviroment */

        $url = \Sinevia\Registry::get('URL_BASE', '');
        if ($url == "") {
            return $this->say('URL_BASE not set for ' . $environment);
        }

        if (self::isWindows()) {
            $isSuccessful = $this->taskExec('start')
                ->arg('firefox')
                ->arg($url)
                ->run();
        }
        if (self::isWindows() == false) {
            $isSuccessful = $this->taskExec('firefox')
                ->arg($url)
                ->run();
        }
    }

    /**
     * Serves the application locally using the PHP built-in server
     * @return void
     */
    public function serve()
    {
        $this->init();

        /* START: Reload enviroment */
        \Sinevia\Registry::set("ENVIRONMENT", 'local');
        loadEnvConf(\Sinevia\Registry::get("ENVIRONMENT"));
        /* END: Reload enviroment */

        $url = \Sinevia\Registry::get('URL_BASE', '');
        if ($url == "") {
            return $this->say('URL_BASE not set for local');
        }

        $domain = str_replace('http://', '', $url);

        $isSuccessful = $this->taskExec('php')
            ->arg('-S')
            ->arg($domain)
            ->arg('index.php')
            ->run();
    }

    private static function isWindows()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return true;
        }
        return false;
    }
}
