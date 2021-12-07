<?php

namespace Bitcoin;

use Bitcoin\RpcType\JsonArray;
use Bitcoin\RpcType\JsonObject;
use Bitcoin\RpcType\NumericOrArray;
use Bitcoin\RpcType\StringOrNumeric;
use Exception;

class RpcClient
{
    /**
     * @var string
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
     * @var string
     */
    private $host;

    /**
     * RpcClient constructor.
     * @param string $user
     * @param string $pass
     * @param string $host
     * @param int $port
     */
    public function __construct(
        string $user,
        string $pass,
        string $host = '127.0.0.1',
        int $port = 18332
    )
    {
        $this->user = $user;
        $this->pass = $pass;
        $this->host = $host;
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
        $host = 'http://' . $this->user . ':' . $this->pass . '@' . $this->host . ':' . $this->port;
        if ($this->wolletName) {
            $host .= '/wallet/' . $this->wolletName;
        }
        $data = [
            'url' => $host,
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
     * https://developer.bitcoin.org/reference/rpc/getbestblockhash.html
     **/
    public function getbestblockhash()
    {
        return $this->get('getbestblockhash');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblock.html
     **/
    public function getblock(string $blockhash, int $verbosity = null)
    {
        return $this->get('getblock', [$blockhash, $verbosity]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblockchaininfo.html
     **/
    public function getblockchaininfo()
    {
        return $this->get('getblockchaininfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblockcount.html
     **/
    public function getblockcount()
    {
        return $this->get('getblockcount');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblockfilter.html
     **/
    public function getblockfilter(string $blockhash, string $filtertype = null)
    {
        return $this->get('getblockfilter', [$blockhash, $filtertype]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblockhash.html
     **/
    public function getblockhash(int $height)
    {
        return $this->get('getblockhash', [$height]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblockheader.html
     **/
    public function getblockheader(string $blockhash, bool $verbose = null)
    {
        return $this->get('getblockheader', [$blockhash, $verbose]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblockstats.html
     **/
    public function getblockstats($hash_or_height, $stats = null)
    {
        return $this->get('getblockstats', [$hash_or_height, $stats]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getchaintips.html
     **/
    public function getchaintips()
    {
        return $this->get('getchaintips');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getchaintxstats.html
     **/
    public function getchaintxstats(int $nblocks = null, string $blockhash = null)
    {
        return $this->get('getchaintxstats', [$nblocks, $blockhash]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getdifficulty.html
     **/
    public function getdifficulty()
    {
        return $this->get('getdifficulty');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getmempoolancestors.html
     **/
    public function getmempoolancestors(string $txid, bool $verbose = null)
    {
        return $this->get('getmempoolancestors', [$txid, $verbose]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getmempooldescendants.html
     **/
    public function getmempooldescendants(string $txid, bool $verbose = null)
    {
        return $this->get('getmempooldescendants', [$txid, $verbose]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getmempoolentry.html
     **/
    public function getmempoolentry(string $txid)
    {
        return $this->get('getmempoolentry', [$txid]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getmempoolinfo.html
     **/
    public function getmempoolinfo()
    {
        return $this->get('getmempoolinfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getrawmempool.html
     **/
    public function getrawmempool(bool $verbose = null, bool $mempool_sequence = null)
    {
        return $this->get('getrawmempool', [$verbose, $mempool_sequence]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/gettxout.html
     **/
    public function gettxout(string $txid, int $n, bool $include_mempool = null)
    {
        return $this->get('gettxout', [$txid, $n, $include_mempool]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/gettxoutproof.html
     **/
    public function gettxoutproof($txids, string $blockhash = null)
    {
        return $this->get('gettxoutproof', [$txids, $blockhash]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/gettxoutsetinfo.html
     **/
    public function gettxoutsetinfo(string $hash_type = null)
    {
        return $this->get('gettxoutsetinfo', [$hash_type]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/preciousblock.html
     **/
    public function preciousblock(string $blockhash)
    {
        return $this->get('preciousblock', [$blockhash]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/pruneblockchain.html
     **/
    public function pruneblockchain(int $height)
    {
        return $this->get('pruneblockchain', [$height]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/savemempool.html
     **/
    public function savemempool()
    {
        return $this->get('savemempool');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/scantxoutset.html
     **/
    public function scantxoutset(string $action, $scanobjects)
    {
        return $this->get('scantxoutset', [$action, $scanobjects]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/verifychain.html
     **/
    public function verifychain(int $checklevel = null, int $nblocks = null)
    {
        return $this->get('verifychain', [$checklevel, $nblocks]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/verifytxoutproof.html
     **/
    public function verifytxoutproof(string $proof)
    {
        return $this->get('verifytxoutproof', [$proof]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getmemoryinfo.html
     **/
    public function getmemoryinfo(string $mode = null)
    {
        return $this->get('getmemoryinfo', [$mode]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getrpcinfo.html
     **/
    public function getrpcinfo()
    {
        return $this->get('getrpcinfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/help.html
     **/
    public function help(string $command = null)
    {
        return $this->get('help', [$command]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/logging.html
     **/
    public function logging($include = null, $exclude = null)
    {
        return $this->get('logging', [$include, $exclude]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/stop.html
     **/
    public function stop()
    {
        return $this->get('stop');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/uptime.html
     **/
    public function uptime()
    {
        return $this->get('uptime');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/generateblock.html
     **/
    public function generateblock(string $output, $transactions)
    {
        return $this->get('generateblock', [$output, $transactions]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/generatetoaddress.html
     **/
    public function generatetoaddress(int $nblocks, string $address, int $maxtries = null)
    {
        return $this->get('generatetoaddress', [$nblocks, $address, $maxtries]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/generatetodescriptor.html
     **/
    public function generatetodescriptor(int $num_blocks, string $descriptor, int $maxtries = null)
    {
        return $this->get('generatetodescriptor', [$num_blocks, $descriptor, $maxtries]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblocktemplate.html
     **/
    public function getblocktemplate($template_request = null)
    {
        return $this->get('getblocktemplate', [$template_request]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getmininginfo.html
     **/
    public function getmininginfo()
    {
        return $this->get('getmininginfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getnetworkhashps.html
     **/
    public function getnetworkhashps(int $nblocks = null, int $height = null)
    {
        return $this->get('getnetworkhashps', [$nblocks, $height]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/prioritisetransaction.html
     **/
    public function prioritisetransaction(string $txid, int $dummy = null, int $fee_delta)
    {
        return $this->get('prioritisetransaction', [$txid, $dummy, $fee_delta]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/submitblock.html
     **/
    public function submitblock(string $hexdata, string $dummy = null)
    {
        return $this->get('submitblock', [$hexdata, $dummy]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/submitheader.html
     **/
    public function submitheader(string $hexdata)
    {
        return $this->get('submitheader', [$hexdata]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/addnode.html
     **/
    public function addnode(string $node, string $command)
    {
        return $this->get('addnode', [$node, $command]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/clearbanned.html
     **/
    public function clearbanned()
    {
        return $this->get('clearbanned');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/disconnectnode.html
     **/
    public function disconnectnode(string $address = null, int $nodeid = null)
    {
        return $this->get('disconnectnode', [$address, $nodeid]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getaddednodeinfo.html
     **/
    public function getaddednodeinfo(string $node = null)
    {
        return $this->get('getaddednodeinfo', [$node]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getconnectioncount.html
     **/
    public function getconnectioncount()
    {
        return $this->get('getconnectioncount');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getnettotals.html
     **/
    public function getnettotals()
    {
        return $this->get('getnettotals');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getnetworkinfo.html
     **/
    public function getnetworkinfo()
    {
        return $this->get('getnetworkinfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getnodeaddresses.html
     **/
    public function getnodeaddresses(int $count = null)
    {
        return $this->get('getnodeaddresses', [$count]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getpeerinfo.html
     **/
    public function getpeerinfo()
    {
        return $this->get('getpeerinfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listbanned.html
     **/
    public function listbanned()
    {
        return $this->get('listbanned');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/ping.html
     **/
    public function ping()
    {
        return $this->get('ping');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/setban.html
     **/
    public function setban(string $subnet, string $command, int $bantime = null, bool $absolute = null)
    {
        return $this->get('setban', [$subnet, $command, $bantime, $absolute]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/setnetworkactive.html
     **/
    public function setnetworkactive(bool $state)
    {
        return $this->get('setnetworkactive', [$state]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/analyzepsbt.html
     **/
    public function analyzepsbt(string $psbt)
    {
        return $this->get('analyzepsbt', [$psbt]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/combinepsbt.html
     **/
    public function combinepsbt($txs)
    {
        return $this->get('combinepsbt', [$txs]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/combinerawtransaction.html
     **/
    public function combinerawtransaction($txs)
    {
        return $this->get('combinerawtransaction', [$txs]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/converttopsbt.html
     **/
    public function converttopsbt(string $hexstring, bool $permitsigdata = null, bool $iswitness = null)
    {
        return $this->get('converttopsbt', [$hexstring, $permitsigdata, $iswitness]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/createpsbt.html
     **/
    public function createpsbt($inputs, $outputs, int $locktime = null, bool $replaceable = null)
    {
        return $this->get('createpsbt', [$inputs, $outputs, $locktime, $replaceable]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/createrawtransaction.html
     **/
    public function createrawtransaction($inputs, $outputs, int $locktime = null, bool $replaceable = null)
    {
        return $this->get('createrawtransaction', [$inputs, $outputs, $locktime, $replaceable]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/decodepsbt.html
     **/
    public function decodepsbt(string $psbt)
    {
        return $this->get('decodepsbt', [$psbt]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/decoderawtransaction.html
     **/
    public function decoderawtransaction(string $hexstring, bool $iswitness = null)
    {
        return $this->get('decoderawtransaction', [$hexstring, $iswitness]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/decodescript.html
     **/
    public function decodescript(string $hexstring)
    {
        return $this->get('decodescript', [$hexstring]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/finalizepsbt.html
     **/
    public function finalizepsbt(string $psbt, bool $extract = null)
    {
        return $this->get('finalizepsbt', [$psbt, $extract]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/fundrawtransaction.html
     **/
    public function fundrawtransaction(string $hexstring, $options = null, bool $iswitness = null)
    {
        return $this->get('fundrawtransaction', [$hexstring, $options, $iswitness]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getrawtransaction.html
     **/
    public function getrawtransaction(string $txid, bool $verbose = null, string $blockhash = null)
    {
        return $this->get('getrawtransaction', [$txid, $verbose, $blockhash]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/joinpsbts.html
     **/
    public function joinpsbts($txs)
    {
        return $this->get('joinpsbts', [$txs]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/sendrawtransaction.html
     **/
    public function sendrawtransaction(string $hexstring, $maxfeerate = null)
    {
        return $this->get('sendrawtransaction', [$hexstring, $maxfeerate]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/signrawtransactionwithkey.html
     **/
    public function signrawtransactionwithkey(string $hexstring, $privkeys, $prevtxs = null, string $sighashtype = null)
    {
        return $this->get('signrawtransactionwithkey', [$hexstring, $privkeys, $prevtxs, $sighashtype]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/testmempoolaccept.html
     **/
    public function testmempoolaccept($rawtxs, $maxfeerate = null)
    {
        return $this->get('testmempoolaccept', [$rawtxs, $maxfeerate]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/utxoupdatepsbt.html
     **/
    public function utxoupdatepsbt(string $psbt, $descriptors = null)
    {
        return $this->get('utxoupdatepsbt', [$psbt, $descriptors]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/createmultisig.html
     **/
    public function createmultisig(int $nrequired, $keys, string $address_type = null)
    {
        return $this->get('createmultisig', [$nrequired, $keys, $address_type]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/deriveaddresses.html
     **/
    public function deriveaddresses(string $descriptor, $range = null)
    {
        return $this->get('deriveaddresses', [$descriptor, $range]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/estimatesmartfee.html
     **/
    public function estimatesmartfee(int $conf_target, string $estimate_mode = null)
    {
        return $this->get('estimatesmartfee', [$conf_target, $estimate_mode]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getdescriptorinfo.html
     **/
    public function getdescriptorinfo(string $descriptor)
    {
        return $this->get('getdescriptorinfo', [$descriptor]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getindexinfo.html
     **/
    public function getindexinfo(string $index_name = null)
    {
        return $this->get('getindexinfo', [$index_name]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/signmessagewithprivkey.html
     **/
    public function signmessagewithprivkey(string $privkey, string $message)
    {
        return $this->get('signmessagewithprivkey', [$privkey, $message]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/validateaddress.html
     **/
    public function validateaddress(string $address)
    {
        return $this->get('validateaddress', [$address]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/verifymessage.html
     **/
    public function verifymessage(string $address, string $signature, string $message)
    {
        return $this->get('verifymessage', [$address, $signature, $message]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/abandontransaction.html
     **/
    public function abandontransaction(string $txid)
    {
        return $this->get('abandontransaction', [$txid]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/abortrescan.html
     **/
    public function abortrescan()
    {
        return $this->get('abortrescan');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/addmultisigaddress.html
     **/
    public function addmultisigaddress(int $nrequired, $keys, string $label = null, string $address_type = null)
    {
        return $this->get('addmultisigaddress', [$nrequired, $keys, $label, $address_type]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/backupwallet.html
     **/
    public function backupwallet(string $destination)
    {
        return $this->get('backupwallet', [$destination]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/bumpfee.html
     **/
    public function bumpfee(string $txid, $options = null)
    {
        return $this->get('bumpfee', [$txid, $options]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/createwallet.html
     **/
    public function createwallet(string $wallet_name, bool $disable_private_keys = null, bool $blank = null, string $passphrase, bool $avoid_reuse = null, bool $descriptors = null, bool $load_on_startup = null)
    {
        return $this->get('createwallet', [$wallet_name, $disable_private_keys, $blank, $passphrase, $avoid_reuse, $descriptors, $load_on_startup]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/dumpprivkey.html
     **/
    public function dumpprivkey(string $address)
    {
        return $this->get('dumpprivkey', [$address]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/dumpwallet.html
     **/
    public function dumpwallet(string $filename)
    {
        return $this->get('dumpwallet', [$filename]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/encryptwallet.html
     **/
    public function encryptwallet(string $passphrase)
    {
        return $this->get('encryptwallet', [$passphrase]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getaddressesbylabel.html
     **/
    public function getaddressesbylabel(string $label)
    {
        return $this->get('getaddressesbylabel', [$label]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getaddressinfo.html
     **/
    public function getaddressinfo(string $address)
    {
        return $this->get('getaddressinfo', [$address]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getbalance.html
     **/
    public function getbalance(string $dummy = null, int $minconf = null, bool $include_watchonly = null, bool $avoid_reuse = null)
    {
        return $this->get('getbalance', [$dummy, $minconf, $include_watchonly, $avoid_reuse]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getbalances.html
     **/
    public function getbalances()
    {
        return $this->get('getbalances');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getnewaddress.html
     **/
    public function getnewaddress(string $label = null, string $address_type = null)
    {
        return $this->get('getnewaddress', [$label, $address_type]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getrawchangeaddress.html
     **/
    public function getrawchangeaddress(string $address_type = null)
    {
        return $this->get('getrawchangeaddress', [$address_type]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getreceivedbyaddress.html
     **/
    public function getreceivedbyaddress(string $address, int $minconf = null)
    {
        return $this->get('getreceivedbyaddress', [$address, $minconf]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getreceivedbylabel.html
     **/
    public function getreceivedbylabel(string $label, int $minconf = null)
    {
        return $this->get('getreceivedbylabel', [$label, $minconf]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/gettransaction.html
     **/
    public function gettransaction(string $txid, bool $include_watchonly = null, bool $verbose = null)
    {
        return $this->get('gettransaction', [$txid, $include_watchonly, $verbose]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getunconfirmedbalance.html
     **/
    public function getunconfirmedbalance()
    {
        return $this->get('getunconfirmedbalance');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getwalletinfo.html
     **/
    public function getwalletinfo()
    {
        return $this->get('getwalletinfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importaddress.html
     **/
    public function importaddress(string $address, string $label = null, bool $rescan = null, bool $p2sh = null)
    {
        return $this->get('importaddress', [$address, $label, $rescan, $p2sh]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importdescriptors.html
     **/
    public function importdescriptors($requests)
    {
        return $this->get('importdescriptors', [$requests]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importmulti.html
     **/
    public function importmulti($requests, $options = null)
    {
        return $this->get('importmulti', [$requests, $options]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importprivkey.html
     **/
    public function importprivkey(string $privkey, string $label = null, bool $rescan = null)
    {
        return $this->get('importprivkey', [$privkey, $label, $rescan]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importprunedfunds.html
     **/
    public function importprunedfunds(string $rawtransaction, string $txoutproof)
    {
        return $this->get('importprunedfunds', [$rawtransaction, $txoutproof]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importpubkey.html
     **/
    public function importpubkey(string $pubkey, string $label = null, bool $rescan = null)
    {
        return $this->get('importpubkey', [$pubkey, $label, $rescan]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importwallet.html
     **/
    public function importwallet(string $filename)
    {
        return $this->get('importwallet', [$filename]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/keypoolrefill.html
     **/
    public function keypoolrefill(int $newsize = null)
    {
        return $this->get('keypoolrefill', [$newsize]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listaddressgroupings.html
     **/
    public function listaddressgroupings()
    {
        return $this->get('listaddressgroupings');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listlabels.html
     **/
    public function listlabels(string $purpose = null)
    {
        return $this->get('listlabels', [$purpose]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listlockunspent.html
     **/
    public function listlockunspent()
    {
        return $this->get('listlockunspent');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listreceivedbyaddress.html
     **/
    public function listreceivedbyaddress(int $minconf = null, bool $include_empty = null, bool $include_watchonly = null, string $address_filter = null)
    {
        return $this->get('listreceivedbyaddress', [$minconf, $include_empty, $include_watchonly, $address_filter]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listreceivedbylabel.html
     **/
    public function listreceivedbylabel(int $minconf = null, bool $include_empty = null, bool $include_watchonly = null)
    {
        return $this->get('listreceivedbylabel', [$minconf, $include_empty, $include_watchonly]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listsinceblock.html
     **/
    public function listsinceblock(string $blockhash = null, int $target_confirmations = null, bool $include_watchonly = null, bool $include_removed = null)
    {
        return $this->get('listsinceblock', [$blockhash, $target_confirmations, $include_watchonly, $include_removed]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listtransactions.html
     **/
    public function listtransactions(string $label = null, int $count = null, int $skip = null, bool $include_watchonly = null)
    {
        return $this->get('listtransactions', [$label, $count, $skip, $include_watchonly]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listunspent.html
     **/
    public function listunspent(int $minconf = null, int $maxconf = null, $addresses = null, bool $include_unsafe = null, $query_options = null)
    {
        return $this->get('listunspent', [$minconf, $maxconf, $addresses, $include_unsafe, $query_options]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listwalletdir.html
     **/
    public function listwalletdir()
    {
        return $this->get('listwalletdir');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listwallets.html
     **/
    public function listwallets()
    {
        return $this->get('listwallets');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/loadwallet.html
     **/
    public function loadwallet(string $filename, bool $load_on_startup = null)
    {
        return $this->get('loadwallet', [$filename, $load_on_startup]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/lockunspent.html
     **/
    public function lockunspent(bool $unlock, $transactions = null)
    {
        return $this->get('lockunspent', [$unlock, $transactions]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/psbtbumpfee.html
     **/
    public function psbtbumpfee(string $txid, $options = null)
    {
        return $this->get('psbtbumpfee', [$txid, $options]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/removeprunedfunds.html
     **/
    public function removeprunedfunds(string $txid)
    {
        return $this->get('removeprunedfunds', [$txid]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/rescanblockchain.html
     **/
    public function rescanblockchain(int $start_height = null, int $stop_height = null)
    {
        return $this->get('rescanblockchain', [$start_height, $stop_height]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/send.html
     **/
    public function send($outputs, int $conf_target = null, string $estimate_mode = null, $fee_rate = null, $options = null)
    {
        return $this->get('send', [$outputs, $conf_target, $estimate_mode, $fee_rate, $options]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/sendmany.html
     **/
    public function sendmany(string $dummy, $amounts, int $minconf = null, string $comment = null, $subtractfeefrom = null, bool $replaceable = null, int $conf_target = null, string $estimate_mode = null, $fee_rate = null)
    {
        return $this->get('sendmany', [$dummy, $amounts, $minconf, $comment, $subtractfeefrom, $replaceable, $conf_target, $estimate_mode, $fee_rate]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/sendtoaddress.html
     **/
    public function sendtoaddress(string $address, $amount, string $comment = null, string $comment_to = null, bool $subtractfeefromamount = null, bool $replaceable = null, int $conf_target = null, string $estimate_mode = null, bool $avoid_reuse = null)
    {
        return $this->get('sendtoaddress', [$address, $amount, $comment, $comment_to, $subtractfeefromamount, $replaceable, $conf_target, $estimate_mode, $avoid_reuse]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/sethdseed.html
     **/
    public function sethdseed(bool $newkeypool = null, string $seed = null)
    {
        return $this->get('sethdseed', [$newkeypool, $seed]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/setlabel.html
     **/
    public function setlabel(string $address, string $label)
    {
        return $this->get('setlabel', [$address, $label]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/settxfee.html
     **/
    public function settxfee($amount)
    {
        return $this->get('settxfee', [$amount]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/setwalletflag.html
     **/
    public function setwalletflag(string $flag, bool $value = null)
    {
        return $this->get('setwalletflag', [$flag, $value]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/signmessage.html
     **/
    public function signmessage(string $address, string $message)
    {
        return $this->get('signmessage', [$address, $message]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/signrawtransactionwithwallet.html
     **/
    public function signrawtransactionwithwallet(string $hexstring, $prevtxs = null, string $sighashtype = null)
    {
        return $this->get('signrawtransactionwithwallet', [$hexstring, $prevtxs, $sighashtype]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/unloadwallet.html
     **/
    public function unloadwallet(string $wallet_name = null, bool $load_on_startup = null)
    {
        return $this->get('unloadwallet', [$wallet_name, $load_on_startup]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/upgradewallet.html
     **/
    public function upgradewallet(int $version = null)
    {
        return $this->get('upgradewallet', [$version]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/walletcreatefundedpsbt.html
     **/
    public function walletcreatefundedpsbt($inputs = null, $outputs, int $locktime = null, $options = null, bool $bip32derivs = null)
    {
        return $this->get('walletcreatefundedpsbt', [$inputs, $outputs, $locktime, $options, $bip32derivs]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/walletlock.html
     **/
    public function walletlock()
    {
        return $this->get('walletlock');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/walletpassphrase.html
     **/
    public function walletpassphrase(string $passphrase, int $timeout)
    {
        return $this->get('walletpassphrase', [$passphrase, $timeout]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/walletpassphrasechange.html
     **/
    public function walletpassphrasechange(string $oldpassphrase, string $newpassphrase)
    {
        return $this->get('walletpassphrasechange', [$oldpassphrase, $newpassphrase]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/walletprocesspsbt.html
     **/
    public function walletprocesspsbt(string $psbt, bool $sign = null, string $sighashtype = null, bool $bip32derivs = null)
    {
        return $this->get('walletprocesspsbt', [$psbt, $sign, $sighashtype, $bip32derivs]);
    }
}
