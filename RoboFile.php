<?php

require_once __DIR__ . '/vendor/autoload.php';

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks {
    private $functionLive = null;
    private $functionStaging = null;
    private $urlLive = null;
    private $urlStaging = null;
    private $urlLocal = null;
    private $testingFramework = null;

    public function __construct()
    {
        $this->functionLive = \Sinevia\Registry::get('FUNCTION_LIVE');
        $this->functionStaging = \Sinevia\Registry::get('FUNCTION_STAGING');
        $this->urlLive = \Sinevia\Registry::get('URL_LIVE');
        $this->urlStaging = \Sinevia\Registry::get('URL_STAGING');
        $this->urlLocal = \Sinevia\Registry::get('URL_LOCAL');
        $this->testingFramework = Registry::get('TESTING_FRAMEWORK', 'TESTIFY'); // Options: TESTIFY, PHPUNIT, NONE
    }
    
    /**
     * Installs the serverless framework
     */
    function init() {
        $isSuccessful = $this->taskExec('npm')
                ->arg('update')
                ->run()->wasSuccessful();
        
        if ($isSuccessful == false) {
            return $this->say('Failed.');
        }
    }
    
    function test()
    {
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
        
        $isSuccessful = $this->taskExec('composer')
            ->arg('update')
            ->option('prefer-dist')
            ->option('optimize-autoloader')
            ->run()
            ->wasSuccessful();

        $this->say('Running tests...');

        $result = $this->taskExec('php')
            ->dir('tests')
            ->arg('test.php')
            ->printed(true)
            ->run();

        $output = $result->getMessage();

        if ($result->wasSuccessful() == false) {
            $this->say('Test Failed');
            return false;
        }

        if (strpos($output, 'Tests: [fail]') > -1) {
            $this->say('Tests Failed');
            return false;
        }

        $this->say('Tests Successful');

        return true;
    }
    
    /**
     * Deploys the app to serverless live
     */
    public function deployLive()
    {
        $this->taskReplaceInFile('env.php')
            ->from('ROBO_FUNCTION_NAME')
            ->to($this->functionLive)
            ->run();

        // 1. Run tests
        $isSuccessful = $this->test();

        if ($isSuccessful == false) {
            return $this->say('Failed');
        }

        // 2. Run composer (no-dev)
        $isSuccessful = $this->taskExec('composer')
            ->arg('update')
            ->option('prefer-dist')
            ->option('optimize-autoloader')
            ->run()->wasSuccessful();

        if ($isSuccessful == false) {
            return $this->say('Failed.');
        }

        $this->taskReplaceInFile('env.php')
            ->from('$roboFunctionName = "";')
            ->to('$roboFunctionName = "' . $this->functionLive . '";')
            ->run();

        // 3. Deploy
        $this->taskExec('sls')
            ->arg('deploy')
            ->option('function', $this->functionLive)
            ->run();

            $this->taskReplaceInFile('env.php')
            ->from('$roboFunctionName = "' . $this->functionLive . '";')
            ->to('$roboFunctionName = "";')
            ->run();
    }

    /**
     * Deploys the app to serverless live
     */
    public function deployStaging()
    {
        // 1. Run tests
        $isSuccessful = $this->test();

        if ($isSuccessful == false) {
            return $this->say('Failed');
        }

        // 2. Run composer (no-dev)
        $isSuccessful = $this->taskExec('composer')
            ->arg('update')
            ->option('prefer-dist')
            ->option('optimize-autoloader')
            ->run()->wasSuccessful();

        if ($isSuccessful == false) {
            return $this->say('Failed.');
        }

        $this->taskReplaceInFile('env.php')
            ->from('$roboFunctionName = "";')
            ->to('$roboFunctionName = "' . $this->functionStaging . '";')
            ->run();

        // 3. Deploy
        $this->taskExec('sls')
            ->arg('deploy')
            ->option('function', $this->functionStaging)
            ->run();

        $this->taskReplaceInFile('env.php')
            ->from('$roboFunctionName = "' . $this->functionStaging . '";')
            ->to('$roboFunctionName = "";')
            ->run();
    }

    /**
     * Retrieves the logs from serverless
     */
    public function logsLive()
    {
        $this->taskExec('sls')
            ->arg('logs')
            ->option('function', $this->functionLive)
            ->run();
    }

    /**
     * Retrieves the logs from serverless
     */
    public function logsStaging()
    {
        $this->taskExec('sls')
            ->arg('logs')
            ->option('function', $this->functionStaging)
            ->run();
    }

    public function openLive()
    {
        $isSuccessful = $this->taskExec('start')
            ->arg('firefox')
            ->arg($this->urlLive)
            ->run();
    }

    public function openStaging()
    {
        $isSuccessful = $this->taskExec('start')
            ->arg('firefox')
            ->arg($this->urlStaging)
            ->run();
    }

    public function openLocal()
    {
        $isSuccessful = $this->taskExec('start')
            ->arg('firefox')
            ->arg($this->urlLocal)
            ->run();
    }

    public function serve()
    {
        $domain = str_replace('http://', '', $this->urlLocal);

        $isSuccessful = $this->taskExec('php')
            ->arg('-S')
            ->arg($domain)
            ->arg('index.php')
            ->run();
    }

}
