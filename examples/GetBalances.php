<?php
/*
 * Make sure to disable the display of errors in production code!
 */

use Bitcoin\RpcClient;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../vendor/autoload.php";


$rpcClient = new RpcClient(
    getenv('bitcoind_test_user', true),
    getenv('bitcoind_test_password', true),
    'localhost',
    8332
);

$ret = $rpcClient->getbalances();
if (null === $ret->error) {
    echo 'trusted: ' . $ret->result->mine->trusted . "\n";
    echo 'untrusted_pending: ' . $ret->result->mine->untrusted_pending . "\n";
    echo 'immature: ' . $ret->result->mine->immature . "\n";
} else {
    echo 'error: ' . $ret->error . "\n";
}
