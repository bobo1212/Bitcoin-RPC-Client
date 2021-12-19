<?php
/*
 * Make sure to disable the display of errors in production code!
 */

use Bitcoin\RpcClient;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../vendor/autoload.php";

//Send 0.1 BTC with a confirmation target of 6 blocks in economical fee estimate mode using positional arguments:

$rpc = new RpcClient(
    getenv('bitcoind_test_user', true),
    getenv('bitcoind_test_password', true),
    'localhost',
    8332
);

$ret = $rpc->sendtoaddress(
    '38kXJgKubEEojpzQe91T3dU6BKiwgN2euo',
    0.0001,
    'donation',
    "sean's outpost",
    false,
    true,
    6,
    'economical'
);

var_dump($ret);
