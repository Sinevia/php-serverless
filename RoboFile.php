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

    /**
     * Deploys the app to serverless
     */
    function deploy() {
        // 1. Run composer
        $isSuccessful = $this->taskExec('composer')
                ->arg('update')
                ->option('prefer-dist')
                ->option('optimize-autoloader')
                ->run()->wasSuccessful();
        
        if ($isSuccessful == false) {
            return $this->say('Failed.');
        }

        // 2. Run tests
        $isSuccessful = $this->taskExec('phpunit')
                ->dir('vendor/bin')
                ->option('configuration', '../../phpunit.xml')
                ->run()
                ->wasSuccessful();
                
        if ($isSuccessful == false) {
            return $this->say('Failed');
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
        $isSuccessful = $this->taskExec('php')
                ->arg('-S')
                ->arg($this->devUrl)
                ->run();
    }

}
