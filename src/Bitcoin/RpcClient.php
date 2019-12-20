<?php
declare(strict_types=1);

namespace Bitcoin;

use Exception;

class RpcClient
{
    /**
     * @property string WolletName
     */
    private $wolletName;

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
     * @param string $user
     * @param string $pass
     * @param int $port
     */
    public function __construct(
        string $user,
        string $pass,
        int $port = 18332
    )
    {
        $this->user = $user;
        $this->pass = $pass;
        $this->port = $port;
    }

    /**
     * @param string $method Nzawa metody RpcClient.
     * @param array $params Parametry metody RpcClient.
     * @return mixed
     * @throws Exception
     */
    private function get(string $method, array $params = [])
    {
        $data = [
            'url' => 'http://' . $this->user . ':' . $this->pass . '@127.0.0.1:' . $this->port . '/wallet/' . $this->wolletName,
            'post' => json_encode([
                'method' => $method,
                'params' => $params
            ])
        ];
        $resJson = $this->post($data);
        if (!is_string($resJson)) {
            throw new Exception('Bitcoin RPC error');
        }
        return json_decode($resJson);

    }

    /**
     * @param array $data
     * @return string
     * @throws Exception
     */
    private function post(array $data): string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $data['url']);
        if (array_key_exists('post', $data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data['post']);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $strona = curl_exec($curl);

        if (false === $strona) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new Exception($error);
        }

        curl_close($curl);
        return $strona;
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
     * @throws Exception
     */
    public function help()
    {
        return $this->get('help');
    }

    /**
     * @param string $wallet_name
     * @param bool $disable_private_keys
     * @return mixed
     * @throws Exception
     */
    public function createwallet(string $wallet_name, $disable_private_keys = false)
    {
        return $this->get('createwallet', [$wallet_name, $disable_private_keys]);
    }

    /**
     * @param string $filename
     * @return mixed
     * @throws Exception
     */
    public function loadwallet(string $filename)
    {
        return $this->get('loadwallet', [$filename]);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getwalletinfo()
    {
        return $this->get('getwalletinfo');
    }

    /**
     * @param int $minconf
     * @param bool $include_watchonly
     * @return mixed
     * @throws Exception
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
     * @throws Exception
     */
    public function listtransactions(int $count = 10, int $skip = 0, bool $include_watchonly = false)
    {
        return $this->get('listtransactions', ["*", $count, $skip, $include_watchonly]);
    }

    /**
     * @param string $address (string, required) The bitcoin address to send to.
     * @param string $amount (numeric or string, required) The amount in BTC to send. eg 0.1
     * @param string $comment (string, optional) A comment used to store what the transaction is for. This is not part of the transaction, just kept in your wallet.
     * @param string $commentTo (string, optional) A comment to store the name of the person or organization
     *                                          to which you're sending the transaction. This is not part of the
     *                                          transaction, just kept in your wallet.
     * @param boolean $subtractfeefromamount (boolean, optional, default=false) The fee will be deducted from the amount being sent.
     *                                          The recipient will receive less bitcoins than you enter in the amount field.
     * @param boolean $replaceable (boolean, optional) Allow this transaction to be replaced by a transaction with higher fees via BIP 125
     * @param int $confTarget (numeric, optional) Confirmation target (in blocks)
     * @param string $estimate_mode (string, optional, default=UNSET) The fee estimate mode, must be one of:
     *                                          "UNSET"
     *                                          "ECONOMICAL"
     *                                          "CONSERVATIVE"
     * @return mixed
     * @throws Exception
     */
    public function sendtoaddress(
        string $address,
        string $amount,
        string $comment = "",
        string $commentTo = "",
        bool $subtractfeefromamount = false,
        bool $replaceable = false,
        int $confTarget = 1,
        string $estimate_mode = 'UNSET')
    {
        return $this->get('sendtoaddress', [
            $address,
            $amount,
            $comment,
            $commentTo,
            $subtractfeefromamount,
            $replaceable,
            $confTarget,
            $estimate_mode
        ]);

    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function listlabels()
    {
        return $this->get('listlabels');
    }

    /**
     * @param string $label
     * @return mixed
     * @throws Exception
     */
    public function getaddressesbylabel(string $label)
    {
        return $this->get('getaddressesbylabel', [$label]);
    }

    /**
     * @param string $label (string, optional) The label name for the address to be linked to. If not provided, the default label "" is used. It can also be set to the empty string "" to represent the default label. The label does not need to exist, it will be created if there is no label by the given name.
     * @param string $addressType (string, optional) The address type to use. Options are "legacy", "p2sh-segwit", and "bech32". Default is set by -addresstype.
     * @return mixed
     * @throws Exception
     */
    public function getnewaddress(string $label = '', string $addressType = null)
    {
        return $this->get('getnewaddress', [$label, $addressType]);
    }

    /**
     * @param string $txid
     * @param bool $includeWatchonly
     * @return mixed
     * @throws Exception
     */
    public function gettransaction(string $txid, bool $includeWatchonly = false)
    {
        return $this->get('gettransaction', [$txid, $includeWatchonly]);
    }

    /**
     * @param string $address
     * @return mixed
     * @throws Exception
     */
    public function dumpprivkey(string $address)
    {
        return $this->get('dumpprivkey', [$address]);
    }

    /**
     * @param string $privkey
     * @param string $label
     * @param bool $rescan
     * @return mixed
     * @throws Exception
     */
    public function importprivkey(string $privkey, $label = '', bool $rescan = true)
    {
        return $this->get('importprivkey', [$privkey, $label, $rescan]);
    }

    /**
     * @param string $txid The transaction id.
     * @param bool $verbose If false, return a string, otherwise return a json object
     * @param string|null $blockhash The block in which to look for the transaction
     * @return  mixed
     * @throws Exception
     */
    public function getrawtransaction(string $txid, bool $verbose = false, string $blockhash = null)
    {
        return $this->get('getrawtransaction', [$txid, $verbose, $blockhash]);
    }

    /**
     * @param string hexstring (string, required) The transaction hex string
     * @param boolean iswitness (boolean, optional) Whether the transaction hex is a serialized witness transaction
     * @return  mixed
     * @throws Exception
     */
    public function decoderawtransaction(string $hexstring, bool $iswitness = null)
    {
        return $this->get('decoderawtransaction', [$hexstring, $iswitness]);
    }

    /**
     * Returns a list of currently loaded wallets.
     *
     * @return mixed
     * @throws Exception
     */
    public function listwallets()
    {
        return $this->get('listwallets');
    }

    /**
     * Dumps all wallet keys in a human-readable format to a server-side file. This does not allow overwriting existing files.
     * Imported scripts are included in the dumpfile, but corresponding BIP173 addresses, etc. may not be added automatically by importwallet.
     * Note that if your wallet contains keys which are not derived from your HD seed (e.g. imported keys),
     * these are not covered by only backing up the seed itself, and must be backed up too (e.g. ensure you back up the whole dumpfile).
     *
     * @param string $filename
     * @return mixed
     * @throws Exception
     */
    public function dumpwallet(string $filename)
    {
        return $this->get('dumpwallet', [$filename]);
    }

    /**
     * @param array $addresses
     * @param int $minconf
     * @param int $maxconf
     * @return mixed
     * @throws Exception
     */
    public function listunspent(array $addresses, $minconf = 1, $maxconf = 9999999)
    {
        return $this->get('listunspent', [$minconf, $maxconf, $addresses]);
    }

    /**
     * Create a transaction spending the given inputs and creating new outputs.
     * Outputs can be addresses or data.
     * Returns hex-encoded raw transaction.
     * Note that the transaction's inputs are not signed, and
     * it is not stored in the wallet or transmitted to the network.
     *
     * @param array $inputs
     * @param array $outputs
     * @param integer $locktime
     * @param boolean $replaceable
     *
     * @return mixed
     * @throws Exception
     */
    public function createrawtransaction(array $inputs, array $outputs, int $locktime = 0, bool $replaceable = false)
    {
        return $this->get('createrawtransaction', [$inputs, $outputs, $locktime, $replaceable]);
    }

    /**
     * Sign inputs for raw transaction (serialized, hex-encoded).
     * The second optional argument (may be null) is an array of previous transaction outputs that
     * this transaction depends on but may not yet be in the block chain.
     *
     * @param string $hexstring
     * @param array $prevtxs
     * @param string $sighashtype
     * @return mixed
     * @throws Exception
     */
    public function signrawtransactionwithwallet(string $hexstring, array $prevtxs = [], string $sighashtype = 'ALL')
    {
        return $this->get('signrawtransactionwithwallet', [$hexstring, $prevtxs, $sighashtype]);
    }

    /**
     * Returns if raw transaction (serialized, hex-encoded) would be accepted by mempool.
     *
     * @param array $rawtxs
     * @param bool $allowhighfees
     * @return mixed
     * @throws Exception
     */
    public function testmempoolaccept(array $rawtxs, bool $allowhighfees = false)
    {
        return $this->get('testmempoolaccept', [$rawtxs, $allowhighfees]);
    }

    /**
     * Submits raw transaction (serialized, hex-encoded) to local node and network.
     *
     * @param string $hexstring
     * @param bool $allowhighfees
     * @return mixed
     * @throws Exception
     */
    public function sendrawtransaction(string $hexstring, bool $allowhighfees = false)
    {
        return $this->get('sendrawtransaction', [$hexstring, $allowhighfees]);
    }

    /**
     * @param float $toSpend
     * @return array
     * @throws Exception
     */
    public function getUnspendable(float $toSpend)
    {
        $listunspent = $this->listunspent([]);
        $unspent = [];
        $sumToSpent = 0;
        foreach ($listunspent->result as $txo) {
            if($txo->spendable != true){
                continue;
            }
            $unspent[] = $txo;
            $sumToSpent += $txo->amount;
            if ($sumToSpent >= $toSpend) {
                return $unspent;
            }
        }
        throw new Exception('Ou tof money toSpend:' . $toSpend . ' sendable: ' . $sumToSpent);
    }

    /**
     * @param array $unspent
     * @return array
     */
    private function createRawTransactionInputs(array $unspent): array
    {
        $inputs = [];
        foreach ($unspent as $txo) {
            if($txo->spendable != true){
                continue;
            }
            $inputs[] = [
                'txid' => $txo->txid,
                'vout' => $txo->vout,
                //sequence - parametr opcjonalny
            ];
        }
        return $inputs;
    }

    /**
     * @param array $listunspent
     * @param float $sumAmountFee
     * @return float|int
     */
    private function giveTheChange(array $listunspent, float $sumAmountFee)
    {
        $sumToSpent = 0;
        foreach ($listunspent as $txo) {
            $sumToSpent += $txo->amount;
        }
        // Wylicza reszte z tranzakcji to trzeba wysłać spowrotem na swoj adres bo inaczje bedzie jako opłata i przepadnie !!
        return $sumToSpent - $sumAmountFee;
    }

    /**
     * @param float|null $trnsactionSizeInByte
     * @return float
     * @throws Exception
     */
    public function getFee(float $trnsactionSizeInByte = null): float
    {
        $page = file_get_contents('https://bitcoinfees.earn.com/api/v1/fees/recommended');
        $bitcoinFees = json_decode($page, true);
        if (!is_array($bitcoinFees) || !array_key_exists('hourFee', $bitcoinFees)) {
            throw new Exception('Nieda się ustalić opłaty za transfer bitkojna');
        }
        $averageTrnsactionSizeInByte = 256;
        if ($trnsactionSizeInByte !== null) {
            $averageTrnsactionSizeInByte = $trnsactionSizeInByte;
        }
        return ($bitcoinFees['hourFee'] / 100000000) * $averageTrnsactionSizeInByte;
    }

    /**
     * @param string $adress
     * @param float $amount
     * @param float $fee
     * @return mixed
     * @throws Exception
     */
    public function sendToAddressRawTransaction(string $adress, float $amount, float $fee)
    {
        $sumAmountFee = $amount + $fee;
        $listunspent = $this->getUnspendable($sumAmountFee);
        $inputs = $this->createRawTransactionInputs($listunspent);
        $outputs[$adress] = number_format($amount, 8, '.', '');

        $change = $this->giveTheChange($listunspent, $sumAmountFee);
        if ($change > 0) {
            $returnAddress = $this->getnewaddress()->result;
            $outputs[$returnAddress] = number_format($change, 8, '.', '');
        }

        $rawtransaction = $this->createrawtransaction($inputs, $outputs);
        $signrawtransaction = $this->signrawtransactionwithwallet($rawtransaction->result);
        return $this->sendrawtransaction($signrawtransaction->result->hex);
    }
}
