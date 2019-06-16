<?php
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
//\App\Models\Content\Node::getDatabase()->debug = true;

$tf = new \Testify\Testify("My Test Suite");

$tf->beforeEach(function ($tf) {
    \Sinevia\Migrate::setDirectoryMigration(\Sinevia\Registry::get('DIR_MIGRATIONS_DIR'));
    \Sinevia\Migrate::setDatabase(db());
    \Sinevia\Migrate::$verbose = false;
    \Sinevia\Migrate::up();
});

$tf->test("Testing environment is testing", function ($tf) {
    $tf->assertEquals('testing', \Sinevia\Registry::get("ENVIRONMENT"));
});

$tf->test("Testing home page", function ($tf) {
    $response = get('/');
    $tf->assert(isset($response['body']), "Response contains 'body'");
    $tf->assert(\Sinevia\StringUtils::hasSubstring($response['body'], 'Hello world'));
    
});

$tf();
