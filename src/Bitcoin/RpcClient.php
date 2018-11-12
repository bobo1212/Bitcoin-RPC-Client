<?php
declare(strict_types=1);

namespace Bitcoin;

class RpcClient
{
    /**
     * @property string WolletName
     */
    private $wolletName;
    /**
     * @var HttpClientInterface
     */
    private $httpClient;
    /**
     * @var string
     */
    private $user;
    /**
     * @var string
     */
    private $pass;
    /**
     * @var int
     */
    private $port;

    /**
     * RpcClient constructor.
     * @param HttpClientInterface $httpClient
     * @param string $user
     * @param string $pass
     * @param int $port
     */
    public function __construct(
        HttpClientInterface $httpClient,
        string $user,
        string $pass,
        int $port = 18332
    )
    {
        $this->httpClient = $httpClient;
        $this->user = $user;
        $this->pass = $pass;
        $this->port = $port;
    }

    /**
     * @param string $method Nzawa metody RpcClient.
     * @param array $params Parametry metody RpcClient.
     * @return mixed
     * @throws \Exception
     */
    private function get(string $method, $params = [])
    {
        $data = [
            'url' => 'http://' . $this->user . ':' . $this->pass . '@127.0.0.1:' . $this->port . '/wallet/' . $this->wolletName,
            'post' => json_encode([
                'method' => $method,
                'params' => $params
            ])
        ];
        $resJson = $this->httpClient->post($data);
        if (!is_string($resJson)) {
            throw new \Exception('Bitcoin RPC error');
        }
        return json_decode($resJson);

    }

    /**
     * @param string $name
     */
    public function setWolletName(string $name): void
    {
        $this->wolletName = $name;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function help()
    {
        return $this->get('help');
    }

    /**
     * @param $wallet_name
     * @param bool $disable_private_keys
     * @return mixed
     * @throws \Exception
     */
    public function createwallet($wallet_name, $disable_private_keys = false)
    {
        return $this->get('createwallet', [$wallet_name, $disable_private_keys]);
    }

    /**
     * @param string $filename
     * @return mixed
     * @throws \Exception
     */
    public function loadwallet(string $filename)
    {
        return $this->get('loadwallet', [$filename]);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getwalletinfo()
    {
        return $this->get('getwalletinfo');
    }

    /**
     * @param int $minconf
     * @param bool $include_watchonly
     * @return mixed
     * @throws \Exception
     */
    public function getbalance($minconf = 1, $include_watchonly = false)
    {
        return $this->get('getbalance', ["*", $minconf, $include_watchonly]);
    }

    /**
     * @param int $count
     * @param int $skip
     * @param bool $include_watchonly
     * @return mixed
     * @throws \Exception
     */
    public function listtransactions(int $count = 10, int $skip = 0, bool $include_watchonly = false)
    {
        return $this->get('listtransactions', ["*", $count, $skip, $include_watchonly]);
    }
}
