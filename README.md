# A simple PHP client for bitcoin rpc.

## Installation with Composer

Add following lines to composer.json
```json
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/bobo1212/bitcoin-rpc-client.git"
        }
    ]
```

and run ```composer require bobo1212/bitcoin-rpc-client``` in your project directory.

## Getting started

```php
    /**
     * Include composer autoloader by uncommenting line below
     * if you're not already done it anywhere else in your project.
     **/
    // require 'vendor/autoload.php';

    $rpc = new \Bitcoin\RpcClient(
        'my_user_name',
        'my_password',
        '127.0.0.1',
        8332
    );
    
    $balances = $rpc->getbalances();
    var_dump($balances);
```
## How to send bitcoins
```php

    $rpc = new \Bitcoin\RpcClient(
        'my_user_name',
        'my_password',
        '127.0.0.1',
        8332
    );
    
    $ret = $rpc->sendtoaddress('38kXJgKubEEojpzQe91T3dU6BKiwgN2euo', 0.0001);
    var_dump($ret);
```
## Donations
If you like this project, please consider donating:<br>
**BTC**: 38kXJgKubEEojpzQe91T3dU6BKiwgN2euo<br>
❤Thanks for your support!❤