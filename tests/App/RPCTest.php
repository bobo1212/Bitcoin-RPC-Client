<?php
declare(strict_types=1);

namespace Test\App;

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

    /**
     * @return RpcClient
     */
    public function getRpcClient()
    {
        return $this->rpcClient;
    }

    protected function setUp()
    {

        $this->rpcClient = new RpcClient();
    }

    public function testCanBeCreatedFromValidEmailAddress(): void
    {

        //$this->rpcClient->loadwallet('fikumiku');
        $this->rpcClient->setWolletName('');
        $ret = $this->rpcClient->getwalletinfo();
        $ret = $this->rpcClient->listlabels();
        $ret = $this->rpcClient->getaddressesbylabel("");
        var_dump($ret);
    }

}
