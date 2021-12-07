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
    public function request(string $method, array $params = [])
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
        $res = $this->post($data);
        $jsonRes = json_decode($res);
        if ($jsonRes === null) {
            throw new Exception('Bitcoin RPC error');
        }
        return $jsonRes;
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
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data['post']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $curlExecRet = curl_exec($curl);

        if (false === $curlExecRet) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new Exception($error);
        }

        curl_close($curl);
        return $curlExecRet;
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
        return $this->request('getbestblockhash');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblock.html
     **/
    public function getblock(string $blockhash, int $verbosity = null)
    {
        return $this->request('getblock', [$blockhash, $verbosity]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblockchaininfo.html
     **/
    public function getblockchaininfo()
    {
        return $this->request('getblockchaininfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblockcount.html
     **/
    public function getblockcount()
    {
        return $this->request('getblockcount');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblockfilter.html
     **/
    public function getblockfilter(string $blockhash, string $filtertype = null)
    {
        return $this->request('getblockfilter', [$blockhash, $filtertype]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblockhash.html
     **/
    public function getblockhash(int $height)
    {
        return $this->request('getblockhash', [$height]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblockheader.html
     **/
    public function getblockheader(string $blockhash, bool $verbose = null)
    {
        return $this->request('getblockheader', [$blockhash, $verbose]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblockstats.html
     **/
    public function getblockstats($hash_or_height, $stats = null)
    {
        return $this->request('getblockstats', [$hash_or_height, $stats]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getchaintips.html
     **/
    public function getchaintips()
    {
        return $this->request('getchaintips');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getchaintxstats.html
     **/
    public function getchaintxstats(int $nblocks = null, string $blockhash = null)
    {
        return $this->request('getchaintxstats', [$nblocks, $blockhash]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getdifficulty.html
     **/
    public function getdifficulty()
    {
        return $this->request('getdifficulty');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getmempoolancestors.html
     **/
    public function getmempoolancestors(string $txid, bool $verbose = null)
    {
        return $this->request('getmempoolancestors', [$txid, $verbose]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getmempooldescendants.html
     **/
    public function getmempooldescendants(string $txid, bool $verbose = null)
    {
        return $this->request('getmempooldescendants', [$txid, $verbose]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getmempoolentry.html
     **/
    public function getmempoolentry(string $txid)
    {
        return $this->request('getmempoolentry', [$txid]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getmempoolinfo.html
     **/
    public function getmempoolinfo()
    {
        return $this->request('getmempoolinfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getrawmempool.html
     **/
    public function getrawmempool(bool $verbose = null, bool $mempool_sequence = null)
    {
        return $this->request('getrawmempool', [$verbose, $mempool_sequence]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/gettxout.html
     **/
    public function gettxout(string $txid, int $n, bool $include_mempool = null)
    {
        return $this->request('gettxout', [$txid, $n, $include_mempool]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/gettxoutproof.html
     **/
    public function gettxoutproof($txids, string $blockhash = null)
    {
        return $this->request('gettxoutproof', [$txids, $blockhash]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/gettxoutsetinfo.html
     **/
    public function gettxoutsetinfo(string $hash_type = null)
    {
        return $this->request('gettxoutsetinfo', [$hash_type]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/preciousblock.html
     **/
    public function preciousblock(string $blockhash)
    {
        return $this->request('preciousblock', [$blockhash]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/pruneblockchain.html
     **/
    public function pruneblockchain(int $height)
    {
        return $this->request('pruneblockchain', [$height]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/savemempool.html
     **/
    public function savemempool()
    {
        return $this->request('savemempool');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/scantxoutset.html
     **/
    public function scantxoutset(string $action, $scanobjects)
    {
        return $this->request('scantxoutset', [$action, $scanobjects]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/verifychain.html
     **/
    public function verifychain(int $checklevel = null, int $nblocks = null)
    {
        return $this->request('verifychain', [$checklevel, $nblocks]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/verifytxoutproof.html
     **/
    public function verifytxoutproof(string $proof)
    {
        return $this->request('verifytxoutproof', [$proof]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getmemoryinfo.html
     **/
    public function getmemoryinfo(string $mode = null)
    {
        return $this->request('getmemoryinfo', [$mode]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getrpcinfo.html
     **/
    public function getrpcinfo()
    {
        return $this->request('getrpcinfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/help.html
     **/
    public function help(string $command = null)
    {
        return $this->request('help', [$command]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/logging.html
     **/
    public function logging($include = null, $exclude = null)
    {
        return $this->request('logging', [$include, $exclude]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/stop.html
     **/
    public function stop()
    {
        return $this->request('stop');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/uptime.html
     **/
    public function uptime()
    {
        return $this->request('uptime');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/generateblock.html
     **/
    public function generateblock(string $output, $transactions)
    {
        return $this->request('generateblock', [$output, $transactions]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/generatetoaddress.html
     **/
    public function generatetoaddress(int $nblocks, string $address, int $maxtries = null)
    {
        return $this->request('generatetoaddress', [$nblocks, $address, $maxtries]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/generatetodescriptor.html
     **/
    public function generatetodescriptor(int $num_blocks, string $descriptor, int $maxtries = null)
    {
        return $this->request('generatetodescriptor', [$num_blocks, $descriptor, $maxtries]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblocktemplate.html
     **/
    public function getblocktemplate($template_request = null)
    {
        return $this->request('getblocktemplate', [$template_request]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getmininginfo.html
     **/
    public function getmininginfo()
    {
        return $this->request('getmininginfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getnetworkhashps.html
     **/
    public function getnetworkhashps(int $nblocks = null, int $height = null)
    {
        return $this->request('getnetworkhashps', [$nblocks, $height]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/prioritisetransaction.html
     **/
    public function prioritisetransaction(string $txid, int $dummy = null, int $fee_delta)
    {
        return $this->request('prioritisetransaction', [$txid, $dummy, $fee_delta]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/submitblock.html
     **/
    public function submitblock(string $hexdata, string $dummy = null)
    {
        return $this->request('submitblock', [$hexdata, $dummy]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/submitheader.html
     **/
    public function submitheader(string $hexdata)
    {
        return $this->request('submitheader', [$hexdata]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/addnode.html
     **/
    public function addnode(string $node, string $command)
    {
        return $this->request('addnode', [$node, $command]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/clearbanned.html
     **/
    public function clearbanned()
    {
        return $this->request('clearbanned');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/disconnectnode.html
     **/
    public function disconnectnode(string $address = null, int $nodeid = null)
    {
        return $this->request('disconnectnode', [$address, $nodeid]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getaddednodeinfo.html
     **/
    public function getaddednodeinfo(string $node = null)
    {
        return $this->request('getaddednodeinfo', [$node]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getconnectioncount.html
     **/
    public function getconnectioncount()
    {
        return $this->request('getconnectioncount');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getnettotals.html
     **/
    public function getnettotals()
    {
        return $this->request('getnettotals');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getnetworkinfo.html
     **/
    public function getnetworkinfo()
    {
        return $this->request('getnetworkinfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getnodeaddresses.html
     **/
    public function getnodeaddresses(int $count = null)
    {
        return $this->request('getnodeaddresses', [$count]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getpeerinfo.html
     **/
    public function getpeerinfo()
    {
        return $this->request('getpeerinfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listbanned.html
     **/
    public function listbanned()
    {
        return $this->request('listbanned');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/ping.html
     **/
    public function ping()
    {
        return $this->request('ping');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/setban.html
     **/
    public function setban(string $subnet, string $command, int $bantime = null, bool $absolute = null)
    {
        return $this->request('setban', [$subnet, $command, $bantime, $absolute]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/setnetworkactive.html
     **/
    public function setnetworkactive(bool $state)
    {
        return $this->request('setnetworkactive', [$state]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/analyzepsbt.html
     **/
    public function analyzepsbt(string $psbt)
    {
        return $this->request('analyzepsbt', [$psbt]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/combinepsbt.html
     **/
    public function combinepsbt($txs)
    {
        return $this->request('combinepsbt', [$txs]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/combinerawtransaction.html
     **/
    public function combinerawtransaction($txs)
    {
        return $this->request('combinerawtransaction', [$txs]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/converttopsbt.html
     **/
    public function converttopsbt(string $hexstring, bool $permitsigdata = null, bool $iswitness = null)
    {
        return $this->request('converttopsbt', [$hexstring, $permitsigdata, $iswitness]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/createpsbt.html
     **/
    public function createpsbt($inputs, $outputs, int $locktime = null, bool $replaceable = null)
    {
        return $this->request('createpsbt', [$inputs, $outputs, $locktime, $replaceable]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/createrawtransaction.html
     **/
    public function createrawtransaction($inputs, $outputs, int $locktime = null, bool $replaceable = null)
    {
        return $this->request('createrawtransaction', [$inputs, $outputs, $locktime, $replaceable]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/decodepsbt.html
     **/
    public function decodepsbt(string $psbt)
    {
        return $this->request('decodepsbt', [$psbt]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/decoderawtransaction.html
     **/
    public function decoderawtransaction(string $hexstring, bool $iswitness = null)
    {
        return $this->request('decoderawtransaction', [$hexstring, $iswitness]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/decodescript.html
     **/
    public function decodescript(string $hexstring)
    {
        return $this->request('decodescript', [$hexstring]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/finalizepsbt.html
     **/
    public function finalizepsbt(string $psbt, bool $extract = null)
    {
        return $this->request('finalizepsbt', [$psbt, $extract]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/fundrawtransaction.html
     **/
    public function fundrawtransaction(string $hexstring, $options = null, bool $iswitness = null)
    {
        return $this->request('fundrawtransaction', [$hexstring, $options, $iswitness]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getrawtransaction.html
     **/
    public function getrawtransaction(string $txid, bool $verbose = null, string $blockhash = null)
    {
        return $this->request('getrawtransaction', [$txid, $verbose, $blockhash]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/joinpsbts.html
     **/
    public function joinpsbts($txs)
    {
        return $this->request('joinpsbts', [$txs]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/sendrawtransaction.html
     **/
    public function sendrawtransaction(string $hexstring, $maxfeerate = null)
    {
        return $this->request('sendrawtransaction', [$hexstring, $maxfeerate]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/signrawtransactionwithkey.html
     **/
    public function signrawtransactionwithkey(string $hexstring, $privkeys, $prevtxs = null, string $sighashtype = null)
    {
        return $this->request('signrawtransactionwithkey', [$hexstring, $privkeys, $prevtxs, $sighashtype]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/testmempoolaccept.html
     **/
    public function testmempoolaccept($rawtxs, $maxfeerate = null)
    {
        return $this->request('testmempoolaccept', [$rawtxs, $maxfeerate]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/utxoupdatepsbt.html
     **/
    public function utxoupdatepsbt(string $psbt, $descriptors = null)
    {
        return $this->request('utxoupdatepsbt', [$psbt, $descriptors]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/createmultisig.html
     **/
    public function createmultisig(int $nrequired, $keys, string $address_type = null)
    {
        return $this->request('createmultisig', [$nrequired, $keys, $address_type]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/deriveaddresses.html
     **/
    public function deriveaddresses(string $descriptor, $range = null)
    {
        return $this->request('deriveaddresses', [$descriptor, $range]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/estimatesmartfee.html
     **/
    public function estimatesmartfee(int $conf_target, string $estimate_mode = null)
    {
        return $this->request('estimatesmartfee', [$conf_target, $estimate_mode]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getdescriptorinfo.html
     **/
    public function getdescriptorinfo(string $descriptor)
    {
        return $this->request('getdescriptorinfo', [$descriptor]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getindexinfo.html
     **/
    public function getindexinfo(string $index_name = null)
    {
        return $this->request('getindexinfo', [$index_name]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/signmessagewithprivkey.html
     **/
    public function signmessagewithprivkey(string $privkey, string $message)
    {
        return $this->request('signmessagewithprivkey', [$privkey, $message]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/validateaddress.html
     **/
    public function validateaddress(string $address)
    {
        return $this->request('validateaddress', [$address]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/verifymessage.html
     **/
    public function verifymessage(string $address, string $signature, string $message)
    {
        return $this->request('verifymessage', [$address, $signature, $message]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/abandontransaction.html
     **/
    public function abandontransaction(string $txid)
    {
        return $this->request('abandontransaction', [$txid]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/abortrescan.html
     **/
    public function abortrescan()
    {
        return $this->request('abortrescan');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/addmultisigaddress.html
     **/
    public function addmultisigaddress(int $nrequired, $keys, string $label = null, string $address_type = null)
    {
        return $this->request('addmultisigaddress', [$nrequired, $keys, $label, $address_type]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/backupwallet.html
     **/
    public function backupwallet(string $destination)
    {
        return $this->request('backupwallet', [$destination]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/bumpfee.html
     **/
    public function bumpfee(string $txid, $options = null)
    {
        return $this->request('bumpfee', [$txid, $options]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/createwallet.html
     **/
    public function createwallet(string $wallet_name, bool $disable_private_keys = null, bool $blank = null, string $passphrase, bool $avoid_reuse = null, bool $descriptors = null, bool $load_on_startup = null)
    {
        return $this->request('createwallet', [$wallet_name, $disable_private_keys, $blank, $passphrase, $avoid_reuse, $descriptors, $load_on_startup]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/dumpprivkey.html
     **/
    public function dumpprivkey(string $address)
    {
        return $this->request('dumpprivkey', [$address]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/dumpwallet.html
     **/
    public function dumpwallet(string $filename)
    {
        return $this->request('dumpwallet', [$filename]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/encryptwallet.html
     **/
    public function encryptwallet(string $passphrase)
    {
        return $this->request('encryptwallet', [$passphrase]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getaddressesbylabel.html
     **/
    public function getaddressesbylabel(string $label)
    {
        return $this->request('getaddressesbylabel', [$label]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getaddressinfo.html
     **/
    public function getaddressinfo(string $address)
    {
        return $this->request('getaddressinfo', [$address]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getbalance.html
     **/
    public function getbalance(string $dummy = null, int $minconf = null, bool $include_watchonly = null, bool $avoid_reuse = null)
    {
        return $this->request('getbalance', [$dummy, $minconf, $include_watchonly, $avoid_reuse]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getbalances.html
     **/
    public function getbalances()
    {
        return $this->request('getbalances');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getnewaddress.html
     **/
    public function getnewaddress(string $label = null, string $address_type = null)
    {
        return $this->request('getnewaddress', [$label, $address_type]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getrawchangeaddress.html
     **/
    public function getrawchangeaddress(string $address_type = null)
    {
        return $this->request('getrawchangeaddress', [$address_type]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getreceivedbyaddress.html
     **/
    public function getreceivedbyaddress(string $address, int $minconf = null)
    {
        return $this->request('getreceivedbyaddress', [$address, $minconf]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getreceivedbylabel.html
     **/
    public function getreceivedbylabel(string $label, int $minconf = null)
    {
        return $this->request('getreceivedbylabel', [$label, $minconf]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/gettransaction.html
     **/
    public function gettransaction(string $txid, bool $include_watchonly = null, bool $verbose = null)
    {
        return $this->request('gettransaction', [$txid, $include_watchonly, $verbose]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getunconfirmedbalance.html
     **/
    public function getunconfirmedbalance()
    {
        return $this->request('getunconfirmedbalance');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getwalletinfo.html
     **/
    public function getwalletinfo()
    {
        return $this->request('getwalletinfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importaddress.html
     **/
    public function importaddress(string $address, string $label = null, bool $rescan = null, bool $p2sh = null)
    {
        return $this->request('importaddress', [$address, $label, $rescan, $p2sh]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importdescriptors.html
     **/
    public function importdescriptors($requests)
    {
        return $this->request('importdescriptors', [$requests]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importmulti.html
     **/
    public function importmulti($requests, $options = null)
    {
        return $this->request('importmulti', [$requests, $options]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importprivkey.html
     **/
    public function importprivkey(string $privkey, string $label = null, bool $rescan = null)
    {
        return $this->request('importprivkey', [$privkey, $label, $rescan]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importprunedfunds.html
     **/
    public function importprunedfunds(string $rawtransaction, string $txoutproof)
    {
        return $this->request('importprunedfunds', [$rawtransaction, $txoutproof]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importpubkey.html
     **/
    public function importpubkey(string $pubkey, string $label = null, bool $rescan = null)
    {
        return $this->request('importpubkey', [$pubkey, $label, $rescan]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importwallet.html
     **/
    public function importwallet(string $filename)
    {
        return $this->request('importwallet', [$filename]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/keypoolrefill.html
     **/
    public function keypoolrefill(int $newsize = null)
    {
        return $this->request('keypoolrefill', [$newsize]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listaddressgroupings.html
     **/
    public function listaddressgroupings()
    {
        return $this->request('listaddressgroupings');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listlabels.html
     **/
    public function listlabels(string $purpose = null)
    {
        return $this->request('listlabels', [$purpose]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listlockunspent.html
     **/
    public function listlockunspent()
    {
        return $this->request('listlockunspent');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listreceivedbyaddress.html
     **/
    public function listreceivedbyaddress(int $minconf = null, bool $include_empty = null, bool $include_watchonly = null, string $address_filter = null)
    {
        return $this->request('listreceivedbyaddress', [$minconf, $include_empty, $include_watchonly, $address_filter]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listreceivedbylabel.html
     **/
    public function listreceivedbylabel(int $minconf = null, bool $include_empty = null, bool $include_watchonly = null)
    {
        return $this->request('listreceivedbylabel', [$minconf, $include_empty, $include_watchonly]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listsinceblock.html
     **/
    public function listsinceblock(string $blockhash = null, int $target_confirmations = null, bool $include_watchonly = null, bool $include_removed = null)
    {
        return $this->request('listsinceblock', [$blockhash, $target_confirmations, $include_watchonly, $include_removed]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listtransactions.html
     **/
    public function listtransactions(string $label = null, int $count = null, int $skip = null, bool $include_watchonly = null)
    {
        return $this->request('listtransactions', [$label, $count, $skip, $include_watchonly]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listunspent.html
     **/
    public function listunspent(int $minconf = null, int $maxconf = null, $addresses = null, bool $include_unsafe = null, $query_options = null)
    {
        return $this->request('listunspent', [$minconf, $maxconf, $addresses, $include_unsafe, $query_options]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listwalletdir.html
     **/
    public function listwalletdir()
    {
        return $this->request('listwalletdir');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listwallets.html
     **/
    public function listwallets()
    {
        return $this->request('listwallets');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/loadwallet.html
     **/
    public function loadwallet(string $filename, bool $load_on_startup = null)
    {
        return $this->request('loadwallet', [$filename, $load_on_startup]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/lockunspent.html
     **/
    public function lockunspent(bool $unlock, $transactions = null)
    {
        return $this->request('lockunspent', [$unlock, $transactions]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/psbtbumpfee.html
     **/
    public function psbtbumpfee(string $txid, $options = null)
    {
        return $this->request('psbtbumpfee', [$txid, $options]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/removeprunedfunds.html
     **/
    public function removeprunedfunds(string $txid)
    {
        return $this->request('removeprunedfunds', [$txid]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/rescanblockchain.html
     **/
    public function rescanblockchain(int $start_height = null, int $stop_height = null)
    {
        return $this->request('rescanblockchain', [$start_height, $stop_height]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/send.html
     **/
    public function send($outputs, int $conf_target = null, string $estimate_mode = null, $fee_rate = null, $options = null)
    {
        return $this->request('send', [$outputs, $conf_target, $estimate_mode, $fee_rate, $options]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/sendmany.html
     **/
    public function sendmany(string $dummy, $amounts, int $minconf = null, string $comment = null, $subtractfeefrom = null, bool $replaceable = null, int $conf_target = null, string $estimate_mode = null, $fee_rate = null)
    {
        return $this->request('sendmany', [$dummy, $amounts, $minconf, $comment, $subtractfeefrom, $replaceable, $conf_target, $estimate_mode, $fee_rate]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/sendtoaddress.html
     **/
    public function sendtoaddress(string $address, $amount, string $comment = null, string $comment_to = null, bool $subtractfeefromamount = null, bool $replaceable = null, int $conf_target = null, string $estimate_mode = null, bool $avoid_reuse = null)
    {
        return $this->request('sendtoaddress', [$address, $amount, $comment, $comment_to, $subtractfeefromamount, $replaceable, $conf_target, $estimate_mode, $avoid_reuse]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/sethdseed.html
     **/
    public function sethdseed(bool $newkeypool = null, string $seed = null)
    {
        return $this->request('sethdseed', [$newkeypool, $seed]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/setlabel.html
     **/
    public function setlabel(string $address, string $label)
    {
        return $this->request('setlabel', [$address, $label]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/settxfee.html
     **/
    public function settxfee($amount)
    {
        return $this->request('settxfee', [$amount]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/setwalletflag.html
     **/
    public function setwalletflag(string $flag, bool $value = null)
    {
        return $this->request('setwalletflag', [$flag, $value]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/signmessage.html
     **/
    public function signmessage(string $address, string $message)
    {
        return $this->request('signmessage', [$address, $message]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/signrawtransactionwithwallet.html
     **/
    public function signrawtransactionwithwallet(string $hexstring, $prevtxs = null, string $sighashtype = null)
    {
        return $this->request('signrawtransactionwithwallet', [$hexstring, $prevtxs, $sighashtype]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/unloadwallet.html
     **/
    public function unloadwallet(string $wallet_name = null, bool $load_on_startup = null)
    {
        return $this->request('unloadwallet', [$wallet_name, $load_on_startup]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/upgradewallet.html
     **/
    public function upgradewallet(int $version = null)
    {
        return $this->request('upgradewallet', [$version]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/walletcreatefundedpsbt.html
     **/
    public function walletcreatefundedpsbt($inputs = null, $outputs, int $locktime = null, $options = null, bool $bip32derivs = null)
    {
        return $this->request('walletcreatefundedpsbt', [$inputs, $outputs, $locktime, $options, $bip32derivs]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/walletlock.html
     **/
    public function walletlock()
    {
        return $this->request('walletlock');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/walletpassphrase.html
     **/
    public function walletpassphrase(string $passphrase, int $timeout)
    {
        return $this->request('walletpassphrase', [$passphrase, $timeout]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/walletpassphrasechange.html
     **/
    public function walletpassphrasechange(string $oldpassphrase, string $newpassphrase)
    {
        return $this->request('walletpassphrasechange', [$oldpassphrase, $newpassphrase]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/walletprocesspsbt.html
     **/
    public function walletprocesspsbt(string $psbt, bool $sign = null, string $sighashtype = null, bool $bip32derivs = null)
    {
        return $this->request('walletprocesspsbt', [$psbt, $sign, $sighashtype, $bip32derivs]);
    }
}
