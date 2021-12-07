<?php
declare(strict_types=1);

namespace Bitcoin\Tests\App;

use PHPUnit\Framework\TestCase;
use Bitcoin\RpcClient;

final class RPCTest extends TestCase
{
    /**
     * RpcClient Client.
     *
     * @var  RpcClient $rpcClient
     */
    private $rpcClient;

    protected function setUp()
    {
        $this->rpcClient = new RpcClient(
            getenv('bitcoind_test_user'),
            getenv('bitcoind_test_password'),
            '127.0.0.1',
            8332
        );
    }

    public function testCanGetBalance(): void
    {
        $ret = $this->rpcClient->getbalances();
        $this->assertEquals(null,$ret->error);
        $this->assertTrue(is_numeric($ret->result->mine->trusted));
    }
}
