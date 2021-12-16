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
    
    $user = 'my_user_name';
    $password = 'my_password';
    $host = 'localhost';
    $port = 8332;
    
    $rpc = new \Bitcoin\RpcClient(
        $user,
        $password,
        $host,
        $port
    );
    
    $ret = $rpcClient->getbalances();
    
    if ($ret->error) {
        echo 'error: ' . $ret->error . "\n";
    } else {
        echo 'trusted: ' . $ret->result->mine->trusted . "\n";
        echo 'untrusted_pending: ' . $ret->result->mine->untrusted_pending . "\n";
        echo 'immature: ' . $ret->result->mine->immature . "\n";
    }
```
## Send 0.0001 bitcoin to address 38kXJgKubEEojpzQe91T3dU6BKiwgN2euo:
```php

    $user = 'my_user_name';
    $password = 'my_password';
    $host = 'localhost';
    $port = 8332;
    
    $rpc = new \Bitcoin\RpcClient(
        $user,
        $password,
        $host,
        $port
    );
    
    $ret = $rpc->sendtoaddress('38kXJgKubEEojpzQe91T3dU6BKiwgN2euo', 0.0001);
    var_dump($ret);
```
## Donations
If you like this project, please consider donating:<br>
**BTC**: 38kXJgKubEEojpzQe91T3dU6BKiwgN2euo<br>
<p>
  <img src="assets/qrcode.png">
</p>
❤Thanks for your support!❤


## Contact
For business inquiries: bobo1212@wp.pl