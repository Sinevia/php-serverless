<?php

define("ENVIRONMENT", 'testing');

require dirname(__DIR__) . '/vendor/autoload.php';

/* START: Set your test settings here */
\Sinevia\Registry::set("ENVIRONMENT", "testing");
\Sinevia\Registry::set("DB_TYPE", "sqlite");
\Sinevia\Registry::set("DB_HOST", ":memory:");
\Sinevia\Registry::set("DB_NAME", ":memory:");
\Sinevia\Registry::set("DB_USER", "test");
\Sinevia\Registry::set("DB_PASS", "");
/* END: Set your test settings here */

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
    $tf->assertEquals('YOUR_FUNCTION', \Sinevia\Registry::get("FUNCTION_LIVE"));
    $tf->assertEquals('YOUR_FUNCTION_STAGING', \Sinevia\Registry::get("FUNCTION_STAGING"));
});

$tf->test("Testing home page", function (\Testify\Testify $tf) {
    $response = get('/');
    $tf->assert(isset($response['body']), "Response contains 'body'");
    $tf->assert(\Sinevia\StringUtils::hasSubstring($response['body'], 'Hello world'));
});

$tf();
