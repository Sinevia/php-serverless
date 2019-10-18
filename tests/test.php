<?php

define("ENVIRONMENT", 'testing');

require dirname(__DIR__) . '/vendor/autoload.php';

/* START: Load test config settings */
\Sinevia\Registry::set("ENVIRONMENT", "testing");
loadEnvConf(\Sinevia\Registry::get("ENVIRONMENT"));
/* END: Load test config settings */

include dirname(__DIR__) . '/serverless.php';

function get($path, $data = [])
{
    $_REQUEST = $data;
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = $path;
    return main();
}

function post($path, $data = [])
{
    $_REQUEST = $data;
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['REQUEST_URI'] = $path;
    return main();
}

\Sinevia\Migrate::setDatabase(db());
// db()->debug = true;

$tf = new \Testify\Testify("My Test Suite");

$tf->beforeEach(function (\Testify\Testify $tf) {
    \Sinevia\Migrate::setDirectoryMigration(\Sinevia\Registry::get('DIR_MIGRATIONS_DIR'));
    \Sinevia\Migrate::setDatabase(db());
    \Sinevia\Migrate::$verbose = false;
    \Sinevia\Migrate::up();
});

$tf->test("Testing environment is testing", function (\Testify\Testify $tf) {
    $tf->assertEquals('testing', \Sinevia\Registry::get("ENVIRONMENT"));
});

$tf->test("Testing framework is Testify", function (\Testify\Testify $tf) {
    $tf->assertEquals('TESTIFY', \Sinevia\Registry::get("TESTING_FRAMEWORK"));
});

$tf->test("Testing function name is set", function (\Testify\Testify $tf) {
    //$tf->assertEquals('YOUR_FUNCTION', \Sinevia\Registry::get("FUNCTION_LIVE"));
    //$tf->assertEquals('YOUR_FUNCTION_STAGING', \Sinevia\Registry::get("FUNCTION_STAGING"));
});

$tf->test("Testing home page", function (\Testify\Testify $tf) {
    $response = get('/');
    $tf->assertArray($response, "Is response an array");
    $tf->assertArrayHasKey($response, 'body', "Response contains 'body'");
    $tf->assertStringContainsString($response['body'], '<title>Home | Serverless</title>');
});

//include("api.php");

$tf();
