<?php

$tf = new \Testify\Testify("API Test Suite");

$tf->beforeEach(function (\Testify\Testify $tf) {
    \Sinevia\Migrate::setDirectoryMigration(\Sinevia\Registry::get('DIR_MIGRATIONS_DIR'));
    \Sinevia\Migrate::setDatabase(db());
    \Sinevia\Migrate::$verbose = false;
    \Sinevia\Migrate::up();
});

$tf->test("Testing email verification endpoint (no token)", function (\Testify\Testify $tf) {
    $response = get('/api/auth/email-verify');
    // DEBUG: var_dump($response);
    $tf->assertArrayHasKey($response, 'body', "Response contains 'body'");
    $contents = $response['body'];
    $tf->assertJson($contents, 'Testing contents is JSON');
    $response = json_decode($contents, true);
    $tf->assertArray($response, 'Testing response is array');
    $tf->assertArrayHasKey($response, 'status', 'Testing response has key "status"');
    $tf->assertArrayHasKey($response, 'message', 'Testing response has key "message"');
    $tf->assertEquals($response['status'], 'error', 'Testing response status is "error"');
    $tf->assertEquals($response['message'], 'Verification failed. Token is required', 'Testing message is "Verification failed. Token is required"');
});

$tf->test("Testing login endpoint (no date)", function (\Testify\Testify $tf) {
    $response = get('/api/auth/login');
    // DEBUG: var_dump($response);
    $tf->assertArrayHasKey($response, 'body', "Response contains 'body'");
    $contents = $response['body'];
    $tf->assertJson($contents, 'Testing contents is JSON');
    $response = json_decode($contents, true);
    $tf->assertArray($response, 'Testing response is array');
    $tf->assertArrayHasKey($response, 'status', 'Testing response has key "status"');
    $tf->assertArrayHasKey($response, 'message', 'Testing response has key "message"');
    $tf->assertEquals($response['status'], 'error', 'Testing response status is "error"');
    $tf->assertEquals($response['message'], 'E-mail is required field', 'Testing message is "E-mail is required field"');
});

$tf->test("Testing register endpoint (no date)", function (\Testify\Testify $tf) {
    $response = get('/api/auth/register');
    // DEBUG: var_dump($response);
    $tf->assertArrayHasKey($response, 'body', "Response contains 'body'");
    $contents = $response['body'];
    $tf->assertJson($contents, 'Testing contents is JSON');
    $response = json_decode($contents, true);
    $tf->assertArray($response, 'Testing response is array');
    $tf->assertArrayHasKey($response, 'status', 'Testing response has key "status"');
    $tf->assertArrayHasKey($response, 'message', 'Testing response has key "message"');
    $tf->assertEquals($response['status'], 'error', 'Testing response status is "error"');
    $tf->assertEquals($response['message'], 'E-mail is required field', 'Testing message is "E-mail is required field"');
});

$tf();
