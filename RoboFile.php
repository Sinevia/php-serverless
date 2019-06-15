<?php

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks {
    private $function = 'YOURFUNCTION';
    private $liveUrl = 'https://eu-gb.functions.cloud.ibm.com/api/v1/web/YOURNAMESPACE/default/YOURFUNCTION';
    private $devUrl = 'http://localhost:32222';
    private $testingFramework = "TESTIFY"; // Options: TESTIFY, PHPUNIT, NONE
    
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
     * Deploys the app to serverless
     */
    function deploy() {
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

        // 3. Deploy
        $this->taskExec('sls')
                ->arg('deploy')
                ->option('function', $this->function)
                ->run();
    }
    
    /**
     * Retrieves the logs from serverless
     */
    function logs() {
        $this->taskExec('sls')
                ->arg('logs')
                ->option('function', $this->function)
                ->run();
    }
    
    public function openLive() {
        $isSuccessful = $this->taskExec('start')
                ->arg('firefox')
                ->arg($this->liveUrl)
                ->run();
    }

    public function openDev() {
        $isSuccessful = $this->taskExec('start')
                ->arg('firefox')
                ->arg($this->devUrl)
                ->run();
    }

    public function serve() {
        $domain = str_replace('http://','',$this->devUrl);
        
        $isSuccessful = $this->taskExec('php')
                ->arg('-S')
                ->arg($domain)
                ->arg('index.php')
                ->run();
    }

}
