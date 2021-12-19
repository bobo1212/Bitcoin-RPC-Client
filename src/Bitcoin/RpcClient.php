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

        if ($res['httpCode'] != '200') {
            $msg = explode("\n", $res['header'], 2)[0];
            throw new Exception($msg);
        }
        $jsonRes = json_decode($res['body']);
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
    private function post(array $data): array
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $data['url']);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data['post']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        $response = curl_exec($curl);

        if (false === $response) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new Exception($error);
        }
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return [
            'header' => substr($response, 0, $headerSize),
            'body' => substr($response, $headerSize),
            'httpCode' => $httpCode
        ];
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
     * getbestblockhash
     * Returns the hash of the best (tip) block in the most-work fully-validated chain.
     */
    public function getbestblockhash()
    {
        return $this->request('getbestblockhash');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblock.html
     * getblock "blockhash" ( verbosity )
     * If verbosity is 0, returns a string that is serialized, hex-encoded data for block ‘hash’.
     * If verbosity is 1, returns an Object with information about block ‘hash’.
     * If verbosity is 2, returns an Object with information about block ‘hash’ and information about each transaction.
     */
    public function getblock(string $blockhash, int $verbosity = null)
    {
        return $this->request('getblock', [$blockhash, $verbosity]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblockchaininfo.html
     * getblockchaininfo
     * Returns an object containing various state info regarding blockchain processing.
     */
    public function getblockchaininfo()
    {
        return $this->request('getblockchaininfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblockcount.html
     * getblockcount
     * Returns the height of the most-work fully-validated chain.
     * The genesis block has height 0.
     */
    public function getblockcount()
    {
        return $this->request('getblockcount');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblockfilter.html
     * getblockfilter "blockhash" ( "filtertype" )
     * Retrieve a BIP 157 content filter for a particular block.
     */
    public function getblockfilter(string $blockhash, string $filtertype = null)
    {
        return $this->request('getblockfilter', [$blockhash, $filtertype]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblockhash.html
     * getblockhash height
     * Returns hash of block in best-block-chain at height provided.
     */
    public function getblockhash(int $height)
    {
        return $this->request('getblockhash', [$height]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblockheader.html
     * getblockheader "blockhash" ( verbose )
     * If verbose is false, returns a string that is serialized, hex-encoded data for blockheader ‘hash’.
     * If verbose is true, returns an Object with information about blockheader ‘hash’.
     */
    public function getblockheader(string $blockhash, bool $verbose = null)
    {
        return $this->request('getblockheader', [$blockhash, $verbose]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblockstats.html
     * getblockstats hash_or_height ( stats )
     * Compute per block statistics for a given window. All amounts are in satoshis.
     * It won’t work for some heights with pruning.
     */
    public function getblockstats($hash_or_height, $stats = null)
    {
        return $this->request('getblockstats', [$hash_or_height, $stats]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getchaintips.html
     * getchaintips
     * Return information about all known tips in the block tree, including the main chain as well as orphaned branches.
     */
    public function getchaintips()
    {
        return $this->request('getchaintips');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getchaintxstats.html
     * getchaintxstats ( nblocks "blockhash" )
     * Compute statistics about the total number and rate of transactions in the chain.
     */
    public function getchaintxstats(int $nblocks = null, string $blockhash = null)
    {
        return $this->request('getchaintxstats', [$nblocks, $blockhash]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getdifficulty.html
     * getdifficulty
     * Returns the proof-of-work difficulty as a multiple of the minimum difficulty.
     */
    public function getdifficulty()
    {
        return $this->request('getdifficulty');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getmempoolancestors.html
     * getmempoolancestors "txid" ( verbose )
     * If txid is in the mempool, returns all in-mempool ancestors.
     */
    public function getmempoolancestors(string $txid, bool $verbose = null)
    {
        return $this->request('getmempoolancestors', [$txid, $verbose]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getmempooldescendants.html
     * getmempooldescendants "txid" ( verbose )
     * If txid is in the mempool, returns all in-mempool descendants.
     */
    public function getmempooldescendants(string $txid, bool $verbose = null)
    {
        return $this->request('getmempooldescendants', [$txid, $verbose]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getmempoolentry.html
     * getmempoolentry "txid"
     * Returns mempool data for given transaction
     */
    public function getmempoolentry(string $txid)
    {
        return $this->request('getmempoolentry', [$txid]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getmempoolinfo.html
     * getmempoolinfo
     * Returns details on the active state of the TX memory pool.
     */
    public function getmempoolinfo()
    {
        return $this->request('getmempoolinfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getrawmempool.html
     * getrawmempool ( verbose mempool_sequence )
     * Returns all transaction ids in memory pool as a json array of string transaction ids.
     * Hint: use getmempoolentry to fetch a specific transaction from the mempool.
     */
    public function getrawmempool(bool $verbose = null, bool $mempool_sequence = null)
    {
        return $this->request('getrawmempool', [$verbose, $mempool_sequence]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/gettxout.html
     * gettxout "txid" n ( include_mempool )
     * Returns details about an unspent transaction output.
     */
    public function gettxout(string $txid, int $n, bool $include_mempool = null)
    {
        return $this->request('gettxout', [$txid, $n, $include_mempool]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/gettxoutproof.html
     * gettxoutproof ["txid",...] ( "blockhash" )
     * Returns a hex-encoded proof that “txid” was included in a block.
     * NOTE: By default this function only works sometimes. This is when there is an
     * unspent output in the utxo for this transaction. To make it always work,
     * you need to maintain a transaction index, using the -txindex command line option or
     * specify the block in which the transaction is included manually (by blockhash).
     */
    public function gettxoutproof($txids, string $blockhash = null)
    {
        return $this->request('gettxoutproof', [$txids, $blockhash]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/gettxoutsetinfo.html
     * gettxoutsetinfo ( "hash_type" )
     * Returns statistics about the unspent transaction output set.
     * Note this call may take some time.
     */
    public function gettxoutsetinfo(string $hash_type = null)
    {
        return $this->request('gettxoutsetinfo', [$hash_type]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/preciousblock.html
     * preciousblock "blockhash"
     * Treats a block as if it were received before others with the same work.
     * A later preciousblock call can override the effect of an earlier one.
     * The effects of preciousblock are not retained across restarts.
     */
    public function preciousblock(string $blockhash)
    {
        return $this->request('preciousblock', [$blockhash]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/pruneblockchain.html
     * pruneblockchain height
     */
    public function pruneblockchain(int $height)
    {
        return $this->request('pruneblockchain', [$height]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/savemempool.html
     * savemempool
     * Dumps the mempool to disk. It will fail until the previous dump is fully loaded.
     */
    public function savemempool()
    {
        return $this->request('savemempool');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/scantxoutset.html
     * scantxoutset "action" ( [scanobjects,...] )
     * EXPERIMENTAL warning: this call may be removed or changed in future releases.
     * Scans the unspent transaction output set for entries that match certain output descriptors.
     * Examples of output descriptors are:
     * In the above, <pubkey> either refers to a fixed public key in hexadecimal notation, or to an xpub/xprv optionally followed by one
     * or more path elements separated by “/”, and optionally ending in “/*” (unhardened), or “/*’” or “/*h” (hardened) to specify all
     * unhardened or hardened child keys.
     * In the latter case, a range needs to be specified by below if different from 1000.
     * For more information on output descriptors, see the documentation in the doc/descriptors.md file.
     */
    public function scantxoutset(string $action, $scanobjects)
    {
        return $this->request('scantxoutset', [$action, $scanobjects]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/verifychain.html
     * verifychain ( checklevel nblocks )
     * Verifies blockchain database.
     */
    public function verifychain(int $checklevel = null, int $nblocks = null)
    {
        return $this->request('verifychain', [$checklevel, $nblocks]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/verifytxoutproof.html
     * verifytxoutproof "proof"
     * Verifies that a proof points to a transaction in a block, returning the transaction it commits to
     * and throwing an RPC error if the block is not in our best chain
     */
    public function verifytxoutproof(string $proof)
    {
        return $this->request('verifytxoutproof', [$proof]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getmemoryinfo.html
     * getmemoryinfo ( "mode" )
     * Returns an object containing information about memory usage.
     */
    public function getmemoryinfo(string $mode = null)
    {
        return $this->request('getmemoryinfo', [$mode]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getrpcinfo.html
     * getrpcinfo
     * Returns details of the RPC server.
     */
    public function getrpcinfo()
    {
        return $this->request('getrpcinfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/help.html
     * help ( "command" )
     * List all commands, or get help for a specified command.
     */
    public function help(string $command = null)
    {
        return $this->request('help', [$command]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/logging.html
     * logging ( ["include_category",...] ["exclude_category",...] )
     * Gets and sets the logging configuration.
     * When called without an argument, returns the list of categories with status that are currently being debug logged or not.
     * When called with arguments, adds or removes categories from debug logging and return the lists above.
     * The arguments are evaluated in order “include”, “exclude”.
     * If an item is both included and excluded, it will thus end up being excluded.
     * The valid logging categories are: net, tor, mempool, http, bench, zmq, walletdb, rpc, estimatefee, addrman, selectcoins, reindex, cmpctblock, rand, prune, proxy, mempoolrej, libevent, coindb, qt, leveldb, validation
     * In addition, the following are available as category names with special meanings:
     */
    public function logging($include = null, $exclude = null)
    {
        return $this->request('logging', [$include, $exclude]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/stop.html
     * stop
     * Request a graceful shutdown of Bitcoin Core.
     */
    public function stop()
    {
        return $this->request('stop');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/uptime.html
     * uptime
     * Returns the total uptime of the server.
     */
    public function uptime()
    {
        return $this->request('uptime');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/generateblock.html
     * generateblock "output" ["rawtx/txid",...]
     * Mine a block with a set of ordered transactions immediately to a specified address or descriptor (before the RPC call returns)
     */
    public function generateblock(string $output, $transactions)
    {
        return $this->request('generateblock', [$output, $transactions]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/generatetoaddress.html
     * generatetoaddress nblocks "address" ( maxtries )
     * Mine blocks immediately to a specified address (before the RPC call returns)
     */
    public function generatetoaddress(int $nblocks, string $address, int $maxtries = null)
    {
        return $this->request('generatetoaddress', [$nblocks, $address, $maxtries]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/generatetodescriptor.html
     * generatetodescriptor num_blocks "descriptor" ( maxtries )
     * Mine blocks immediately to a specified descriptor (before the RPC call returns)
     */
    public function generatetodescriptor(int $num_blocks, string $descriptor, int $maxtries = null)
    {
        return $this->request('generatetodescriptor', [$num_blocks, $descriptor, $maxtries]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getblocktemplate.html
     * getblocktemplate ( "template_request" )
     * If the request parameters include a ‘mode’ key, that is used to explicitly select between the default ‘template’ request or a ‘proposal’.
     * It returns data needed to construct a block to work on.
     * For full specification, see BIPs 22, 23, 9, and 145:
     */
    public function getblocktemplate($template_request = null)
    {
        return $this->request('getblocktemplate', [$template_request]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getmininginfo.html
     * getmininginfo
     * Returns a json object containing mining-related information.
     */
    public function getmininginfo()
    {
        return $this->request('getmininginfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getnetworkhashps.html
     * getnetworkhashps ( nblocks height )
     * Returns the estimated network hashes per second based on the last n blocks.
     * Pass in [blocks] to override # of blocks, -1 specifies since last difficulty change.
     * Pass in [height] to estimate the network speed at the time when a certain block was found.
     */
    public function getnetworkhashps(int $nblocks = null, int $height = null)
    {
        return $this->request('getnetworkhashps', [$nblocks, $height]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/prioritisetransaction.html
     * prioritisetransaction "txid" ( dummy ) fee_delta
     * Accepts the transaction into mined blocks at a higher (or lower) priority
     */
    public function prioritisetransaction(string $txid, int $dummy = null, int $fee_delta)
    {
        return $this->request('prioritisetransaction', [$txid, $dummy, $fee_delta]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/submitblock.html
     * submitblock "hexdata" ( "dummy" )
     * Attempts to submit new block to network.
     * See https://en.bitcoin.it/wiki/BIP_0022 for full specification.
     */
    public function submitblock(string $hexdata, string $dummy = null)
    {
        return $this->request('submitblock', [$hexdata, $dummy]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/submitheader.html
     * submitheader "hexdata"
     * Decode the given hexdata as a header and submit it as a candidate chain tip if valid.
     * Throws when the header is invalid.
     */
    public function submitheader(string $hexdata)
    {
        return $this->request('submitheader', [$hexdata]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/addnode.html
     * addnode "node" "command"
     * Attempts to add or remove a node from the addnode list.
     * Or try a connection to a node once.
     * Nodes added using addnode (or -connect) are protected from DoS disconnection and are not required to be
     * full nodes/support SegWit as other outbound peers are (though such peers will not be synced from).
     */
    public function addnode(string $node, string $command)
    {
        return $this->request('addnode', [$node, $command]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/clearbanned.html
     * clearbanned
     * Clear all banned IPs.
     */
    public function clearbanned()
    {
        return $this->request('clearbanned');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/disconnectnode.html
     * disconnectnode ( "address" nodeid )
     * Immediately disconnects from the specified peer node.
     * Strictly one out of ‘address’ and ‘nodeid’ can be provided to identify the node.
     * To disconnect by nodeid, either set ‘address’ to the empty string, or call using the named ‘nodeid’ argument only.
     */
    public function disconnectnode(string $address = null, int $nodeid = null)
    {
        return $this->request('disconnectnode', [$address, $nodeid]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getaddednodeinfo.html
     * getaddednodeinfo ( "node" )
     * Returns information about the given added node, or all added nodes
     * (note that onetry addnodes are not listed here)
     */
    public function getaddednodeinfo(string $node = null)
    {
        return $this->request('getaddednodeinfo', [$node]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getconnectioncount.html
     * getconnectioncount
     * Returns the number of connections to other nodes.
     */
    public function getconnectioncount()
    {
        return $this->request('getconnectioncount');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getnettotals.html
     * getnettotals
     * Returns information about network traffic, including bytes in, bytes out,
     * and current time.
     */
    public function getnettotals()
    {
        return $this->request('getnettotals');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getnetworkinfo.html
     * getnetworkinfo
     * Returns an object containing various state info regarding P2P networking.
     */
    public function getnetworkinfo()
    {
        return $this->request('getnetworkinfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getnodeaddresses.html
     * getnodeaddresses ( count )
     * Return known addresses which can potentially be used to find new nodes in the network
     */
    public function getnodeaddresses(int $count = null)
    {
        return $this->request('getnodeaddresses', [$count]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getpeerinfo.html
     * getpeerinfo
     * Returns data about each connected network node as a json array of objects.
     */
    public function getpeerinfo()
    {
        return $this->request('getpeerinfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listbanned.html
     * listbanned
     * List all manually banned IPs/Subnets.
     */
    public function listbanned()
    {
        return $this->request('listbanned');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/ping.html
     * ping
     * Requests that a ping be sent to all other nodes, to measure ping time.
     * Results provided in getpeerinfo, pingtime and pingwait fields are decimal seconds.
     * Ping command is handled in queue with all other commands, so it measures processing backlog, not just network ping.
     */
    public function ping()
    {
        return $this->request('ping');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/setban.html
     * setban "subnet" "command" ( bantime absolute )
     * Attempts to add or remove an IP/Subnet from the banned list.
     */
    public function setban(string $subnet, string $command, int $bantime = null, bool $absolute = null)
    {
        return $this->request('setban', [$subnet, $command, $bantime, $absolute]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/setnetworkactive.html
     * setnetworkactive state
     * Disable/enable all p2p network activity.
     */
    public function setnetworkactive(bool $state)
    {
        return $this->request('setnetworkactive', [$state]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/analyzepsbt.html
     * analyzepsbt "psbt"
     * Analyzes and provides information about the current status of a PSBT and its inputs
     */
    public function analyzepsbt(string $psbt)
    {
        return $this->request('analyzepsbt', [$psbt]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/combinepsbt.html
     * combinepsbt ["psbt",...]
     * Combine multiple partially signed Bitcoin transactions into one transaction.
     * Implements the Combiner role.
     */
    public function combinepsbt($txs)
    {
        return $this->request('combinepsbt', [$txs]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/combinerawtransaction.html
     * combinerawtransaction ["hexstring",...]
     * Combine multiple partially signed transactions into one transaction.
     * The combined transaction may be another partially signed transaction or a
     * fully signed transaction.
     */
    public function combinerawtransaction($txs)
    {
        return $this->request('combinerawtransaction', [$txs]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/converttopsbt.html
     * converttopsbt "hexstring" ( permitsigdata iswitness )
     * Converts a network serialized transaction to a PSBT. This should be used only with createrawtransaction and fundrawtransaction
     * createpsbt and walletcreatefundedpsbt should be used for new applications.
     */
    public function converttopsbt(string $hexstring, bool $permitsigdata = null, bool $iswitness = null)
    {
        return $this->request('converttopsbt', [$hexstring, $permitsigdata, $iswitness]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/createpsbt.html
     * createpsbt [{"txid":"hex","vout":n,"sequence":n},...] [{"address":amount},{"data":"hex"},...] ( locktime replaceable )
     * Creates a transaction in the Partially Signed Transaction format.
     * Implements the Creator role.
     */
    public function createpsbt($inputs, $outputs, int $locktime = null, bool $replaceable = null)
    {
        return $this->request('createpsbt', [$inputs, $outputs, $locktime, $replaceable]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/createrawtransaction.html
     * createrawtransaction [{"txid":"hex","vout":n,"sequence":n},...] [{"address":amount},{"data":"hex"},...] ( locktime replaceable )
     * Create a transaction spending the given inputs and creating new outputs.
     * Outputs can be addresses or data.
     * Returns hex-encoded raw transaction.
     * Note that the transaction’s inputs are not signed, and
     * it is not stored in the wallet or transmitted to the network.
     */
    public function createrawtransaction($inputs, $outputs, int $locktime = null, bool $replaceable = null)
    {
        return $this->request('createrawtransaction', [$inputs, $outputs, $locktime, $replaceable]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/decodepsbt.html
     * decodepsbt "psbt"
     * Return a JSON object representing the serialized, base64-encoded partially signed Bitcoin transaction.
     */
    public function decodepsbt(string $psbt)
    {
        return $this->request('decodepsbt', [$psbt]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/decoderawtransaction.html
     * decoderawtransaction "hexstring" ( iswitness )
     * Return a JSON object representing the serialized, hex-encoded transaction.
     */
    public function decoderawtransaction(string $hexstring, bool $iswitness = null)
    {
        return $this->request('decoderawtransaction', [$hexstring, $iswitness]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/decodescript.html
     * decodescript "hexstring"
     * Decode a hex-encoded script.
     */
    public function decodescript(string $hexstring)
    {
        return $this->request('decodescript', [$hexstring]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/finalizepsbt.html
     * finalizepsbt "psbt" ( extract )
     * Finalize the inputs of a PSBT. If the transaction is fully signed, it will produce a
     * network serialized transaction which can be broadcast with sendrawtransaction. Otherwise a PSBT will be
     * created which has the final_scriptSig and final_scriptWitness fields filled for inputs that are complete.
     * Implements the Finalizer and Extractor roles.
     */
    public function finalizepsbt(string $psbt, bool $extract = null)
    {
        return $this->request('finalizepsbt', [$psbt, $extract]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/fundrawtransaction.html
     * fundrawtransaction "hexstring" ( options iswitness )
     * If the transaction has no inputs, they will be automatically selected to meet its out value.
     * It will add at most one change output to the outputs.
     * No existing outputs will be modified unless “subtractFeeFromOutputs” is specified.
     * Note that inputs which were signed may need to be resigned after completion since in/outputs have been added.
     * Note that all existing inputs must have their previous output transaction be in the wallet.
     * Note that all inputs selected must be of standard form and P2SH scripts must be
     * in the wallet using importaddress or addmultisigaddress (to calculate fees).
     * You can see whether this is the case by checking the “solvable” field in the listunspent output.
     * Only pay-to-pubkey, multisig, and P2SH versions thereof are currently supported for watch-only
     */
    public function fundrawtransaction(string $hexstring, $options = null, bool $iswitness = null)
    {
        return $this->request('fundrawtransaction', [$hexstring, $options, $iswitness]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getrawtransaction.html
     * getrawtransaction "txid" ( verbose "blockhash" )
     * Return the raw transaction data.
     * By default this function only works for mempool transactions. When called with a blockhash
     * argument, getrawtransaction will return the transaction if the specified block is available and
     * the transaction is found in that block. When called without a blockhash argument, getrawtransaction
     * will return the transaction if it is in the mempool, or if -txindex is enabled and the transaction
     * is in a block in the blockchain.
     * Hint: Use gettransaction for wallet transactions.
     * If verbose is ‘true’, returns an Object with information about ‘txid’.
     * If verbose is ‘false’ or omitted, returns a string that is serialized, hex-encoded data for ‘txid’.
     */
    public function getrawtransaction(string $txid, bool $verbose = null, string $blockhash = null)
    {
        return $this->request('getrawtransaction', [$txid, $verbose, $blockhash]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/joinpsbts.html
     * joinpsbts ["psbt",...]
     * Joins multiple distinct PSBTs with different inputs and outputs into one PSBT with inputs and outputs from all of the PSBTs
     * No input in any of the PSBTs can be in more than one of the PSBTs.
     */
    public function joinpsbts($txs)
    {
        return $this->request('joinpsbts', [$txs]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/sendrawtransaction.html
     * sendrawtransaction "hexstring" ( maxfeerate )
     * Submit a raw transaction (serialized, hex-encoded) to local node and network.
     * Note that the transaction will be sent unconditionally to all peers, so using this
     * for manual rebroadcast may degrade privacy by leaking the transaction’s origin, as
     * nodes will normally not rebroadcast non-wallet transactions already in their mempool.
     * Also see createrawtransaction and signrawtransactionwithkey calls.
     */
    public function sendrawtransaction(string $hexstring, $maxfeerate = null)
    {
        return $this->request('sendrawtransaction', [$hexstring, $maxfeerate]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/signrawtransactionwithkey.html
     * signrawtransactionwithkey "hexstring" ["privatekey",...] ( [{"txid":"hex","vout":n,"scriptPubKey":"hex","redeemScript":"hex","witnessScript":"hex","amount":amount},...] "sighashtype" )
     * Sign inputs for raw transaction (serialized, hex-encoded).
     * The second argument is an array of base58-encoded private
     * keys that will be the only keys used to sign the transaction.
     * The third optional argument (may be null) is an array of previous transaction outputs that
     * this transaction depends on but may not yet be in the block chain.
     */
    public function signrawtransactionwithkey(string $hexstring, $privkeys, $prevtxs = null, string $sighashtype = null)
    {
        return $this->request('signrawtransactionwithkey', [$hexstring, $privkeys, $prevtxs, $sighashtype]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/testmempoolaccept.html
     * testmempoolaccept ["rawtx",...] ( maxfeerate )
     * Returns result of mempool acceptance tests indicating if raw transaction (serialized, hex-encoded) would be accepted by mempool.
     * This checks if the transaction violates the consensus or policy rules.
     * See sendrawtransaction call.
     */
    public function testmempoolaccept($rawtxs, $maxfeerate = null)
    {
        return $this->request('testmempoolaccept', [$rawtxs, $maxfeerate]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/utxoupdatepsbt.html
     * utxoupdatepsbt "psbt" ( ["",{"desc":"str","range":n or [n,n]},...] )
     * Updates all segwit inputs and outputs in a PSBT with data from output descriptors, the UTXO set or the mempool.
     */
    public function utxoupdatepsbt(string $psbt, $descriptors = null)
    {
        return $this->request('utxoupdatepsbt', [$psbt, $descriptors]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/createmultisig.html
     * createmultisig nrequired ["key",...] ( "address_type" )
     * Creates a multi-signature address with n signature of m keys required.
     * It returns a json object with the address and redeemScript.
     */
    public function createmultisig(int $nrequired, $keys, string $address_type = null)
    {
        return $this->request('createmultisig', [$nrequired, $keys, $address_type]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/deriveaddresses.html
     * deriveaddresses "descriptor" ( range )
     * Derives one or more addresses corresponding to an output descriptor.
     * Examples of output descriptors are:
     * In the above, <pubkey> either refers to a fixed public key in hexadecimal notation, or to an xpub/xprv optionally followed by one
     * or more path elements separated by “/”, where “h” represents a hardened child key.
     * For more information on output descriptors, see the documentation in the doc/descriptors.md file.
     */
    public function deriveaddresses(string $descriptor, $range = null)
    {
        return $this->request('deriveaddresses', [$descriptor, $range]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/estimatesmartfee.html
     * estimatesmartfee conf_target ( "estimate_mode" )
     * Estimates the approximate fee per kilobyte needed for a transaction to begin
     * confirmation within conf_target blocks if possible and return the number of blocks
     * for which the estimate is valid. Uses virtual transaction size as defined
     * in BIP 141 (witness data is discounted).
     */
    public function estimatesmartfee(int $conf_target, string $estimate_mode = null)
    {
        return $this->request('estimatesmartfee', [$conf_target, $estimate_mode]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getdescriptorinfo.html
     * getdescriptorinfo "descriptor"
     * Analyses a descriptor.
     */
    public function getdescriptorinfo(string $descriptor)
    {
        return $this->request('getdescriptorinfo', [$descriptor]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getindexinfo.html
     * getindexinfo ( "index_name" )
     * Returns the status of one or all available indices currently running in the node.
     */
    public function getindexinfo(string $index_name = null)
    {
        return $this->request('getindexinfo', [$index_name]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/signmessagewithprivkey.html
     * signmessagewithprivkey "privkey" "message"
     * Sign a message with the private key of an address
     */
    public function signmessagewithprivkey(string $privkey, string $message)
    {
        return $this->request('signmessagewithprivkey', [$privkey, $message]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/validateaddress.html
     * validateaddress "address"
     * Return information about the given bitcoin address.
     */
    public function validateaddress(string $address)
    {
        return $this->request('validateaddress', [$address]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/verifymessage.html
     * verifymessage "address" "signature" "message"
     * Verify a signed message
     */
    public function verifymessage(string $address, string $signature, string $message)
    {
        return $this->request('verifymessage', [$address, $signature, $message]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/abandontransaction.html
     * abandontransaction "txid"
     * Mark in-wallet transaction <txid> as abandoned
     * This will mark this transaction and all its in-wallet descendants as abandoned which will allow
     * for their inputs to be respent.  It can be used to replace “stuck” or evicted transactions.
     * It only works on transactions which are not included in a block and are not currently in the mempool.
     * It has no effect on transactions which are already abandoned.
     */
    public function abandontransaction(string $txid)
    {
        return $this->request('abandontransaction', [$txid]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/abortrescan.html
     * abortrescan
     * Stops current wallet rescan triggered by an RPC call, e.g. by an importprivkey call.
     * Note: Use “getwalletinfo” to query the scanning progress.
     */
    public function abortrescan()
    {
        return $this->request('abortrescan');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/addmultisigaddress.html
     * addmultisigaddress nrequired ["key",...] ( "label" "address_type" )
     * Add an nrequired-to-sign multisignature address to the wallet. Requires a new wallet backup.
     * Each key is a Bitcoin address or hex-encoded public key.
     * This functionality is only intended for use with non-watchonly addresses.
     * See importaddress for watchonly p2sh address support.
     * If ‘label’ is specified, assign address to that label.
     */
    public function addmultisigaddress(int $nrequired, $keys, string $label = null, string $address_type = null)
    {
        return $this->request('addmultisigaddress', [$nrequired, $keys, $label, $address_type]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/backupwallet.html
     * backupwallet "destination"
     * Safely copies current wallet file to destination, which can be a directory or a path with filename.
     */
    public function backupwallet(string $destination)
    {
        return $this->request('backupwallet', [$destination]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/bumpfee.html
     * bumpfee "txid" ( options )
     * Bumps the fee of an opt-in-RBF transaction T, replacing it with a new transaction B.
     * An opt-in RBF transaction with the given txid must be in the wallet.
     * The command will pay the additional fee by reducing change outputs or adding inputs when necessary.
     * It may add a new change output if one does not already exist.
     * All inputs in the original transaction will be included in the replacement transaction.
     * The command will fail if the wallet or mempool contains a transaction that spends one of T’s outputs.
     * By default, the new fee will be calculated automatically using the estimatesmartfee RPC.
     * The user can specify a confirmation target for estimatesmartfee.
     * Alternatively, the user can specify a fee rate in sat/vB for the new transaction.
     * At a minimum, the new fee rate must be high enough to pay an additional new relay fee (incrementalfee
     * returned by getnetworkinfo) to enter the node’s mempool.
     * * WARNING: before version 0.21, fee_rate was in BTC/kvB. As of 0.21, fee_rate is in sat/vB. *
     */
    public function bumpfee(string $txid, $options = null)
    {
        return $this->request('bumpfee', [$txid, $options]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/createwallet.html
     * createwallet "wallet_name" ( disable_private_keys blank "passphrase" avoid_reuse descriptors load_on_startup )
     * Creates and loads a new wallet.
     */
    public function createwallet(string $wallet_name, bool $disable_private_keys = null, bool $blank = null, string $passphrase, bool $avoid_reuse = null, bool $descriptors = null, bool $load_on_startup = null)
    {
        return $this->request('createwallet', [$wallet_name, $disable_private_keys, $blank, $passphrase, $avoid_reuse, $descriptors, $load_on_startup]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/dumpprivkey.html
     * dumpprivkey "address"
     * Reveals the private key corresponding to ‘address’.
     * Then the importprivkey can be used with this output
     */
    public function dumpprivkey(string $address)
    {
        return $this->request('dumpprivkey', [$address]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/dumpwallet.html
     * dumpwallet "filename"
     * Dumps all wallet keys in a human-readable format to a server-side file. This does not allow overwriting existing files.
     * Imported scripts are included in the dumpfile, but corresponding BIP173 addresses, etc. may not be added automatically by importwallet.
     * Note that if your wallet contains keys which are not derived from your HD seed (e.g. imported keys), these are not covered by
     * only backing up the seed itself, and must be backed up too (e.g. ensure you back up the whole dumpfile).
     */
    public function dumpwallet(string $filename)
    {
        return $this->request('dumpwallet', [$filename]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/encryptwallet.html
     * encryptwallet "passphrase"
     * Encrypts the wallet with ‘passphrase’. This is for first time encryption.
     * After this, any calls that interact with private keys such as sending or signing
     * will require the passphrase to be set prior the making these calls.
     * Use the walletpassphrase call for this, and then walletlock call.
     * If the wallet is already encrypted, use the walletpassphrasechange call.
     */
    public function encryptwallet(string $passphrase)
    {
        return $this->request('encryptwallet', [$passphrase]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getaddressesbylabel.html
     * getaddressesbylabel "label"
     * Returns the list of addresses assigned the specified label.
     */
    public function getaddressesbylabel(string $label)
    {
        return $this->request('getaddressesbylabel', [$label]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getaddressinfo.html
     * getaddressinfo "address"
     * Return information about the given bitcoin address.
     * Some of the information will only be present if the address is in the active wallet.
     */
    public function getaddressinfo(string $address)
    {
        return $this->request('getaddressinfo', [$address]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getbalance.html
     * getbalance ( "dummy" minconf include_watchonly avoid_reuse )
     * Returns the total available balance.
     * The available balance is what the wallet considers currently spendable, and is
     * thus affected by options which limit spendability such as -spendzeroconfchange.
     */
    public function getbalance(string $dummy = null, int $minconf = null, bool $include_watchonly = null, bool $avoid_reuse = null)
    {
        return $this->request('getbalance', [$dummy, $minconf, $include_watchonly, $avoid_reuse]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getbalances.html
     * getbalances
     * Returns an object with all balances in BTC.
     */
    public function getbalances()
    {
        return $this->request('getbalances');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getnewaddress.html
     * getnewaddress ( "label" "address_type" )
     * Returns a new Bitcoin address for receiving payments.
     * If ‘label’ is specified, it is added to the address book
     * so payments received with the address will be associated with ‘label’.
     */
    public function getnewaddress(string $label = null, string $address_type = null)
    {
        return $this->request('getnewaddress', [$label, $address_type]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getrawchangeaddress.html
     * getrawchangeaddress ( "address_type" )
     * Returns a new Bitcoin address, for receiving change.
     * This is for use with raw transactions, NOT normal use.
     */
    public function getrawchangeaddress(string $address_type = null)
    {
        return $this->request('getrawchangeaddress', [$address_type]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getreceivedbyaddress.html
     * getreceivedbyaddress "address" ( minconf )
     * Returns the total amount received by the given address in transactions with at least minconf confirmations.
     */
    public function getreceivedbyaddress(string $address, int $minconf = null)
    {
        return $this->request('getreceivedbyaddress', [$address, $minconf]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getreceivedbylabel.html
     * getreceivedbylabel "label" ( minconf )
     * Returns the total amount received by addresses with <label> in transactions with at least [minconf] confirmations.
     */
    public function getreceivedbylabel(string $label, int $minconf = null)
    {
        return $this->request('getreceivedbylabel', [$label, $minconf]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/gettransaction.html
     * gettransaction "txid" ( include_watchonly verbose )
     * Get detailed information about in-wallet transaction <txid>
     */
    public function gettransaction(string $txid, bool $include_watchonly = null, bool $verbose = null)
    {
        return $this->request('gettransaction', [$txid, $include_watchonly, $verbose]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getunconfirmedbalance.html
     * getunconfirmedbalance
     * DEPRECATED
     * Identical to getbalances().mine.untrusted_pending
     */
    public function getunconfirmedbalance()
    {
        return $this->request('getunconfirmedbalance');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/getwalletinfo.html
     * getwalletinfo
     * Returns an object containing various wallet state info.
     */
    public function getwalletinfo()
    {
        return $this->request('getwalletinfo');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importaddress.html
     * importaddress "address" ( "label" rescan p2sh )
     * Adds an address or script (in hex) that can be watched as if it were in your wallet but cannot be used to spend. Requires a new wallet backup.
     * Note: This call can take over an hour to complete if rescan is true, during that time, other rpc calls
     * may report that the imported address exists but related transactions are still missing, leading to temporarily incorrect/bogus balances and unspent outputs until rescan completes.
     * If you have the full public key, you should call importpubkey instead of this.
     * Hint: use importmulti to import more than one address.
     * Note: If you import a non-standard raw script in hex form, outputs sending to it will be treated
     * as change, and not show up in many RPCs.
     * Note: Use “getwalletinfo” to query the scanning progress.
     */
    public function importaddress(string $address, string $label = null, bool $rescan = null, bool $p2sh = null)
    {
        return $this->request('importaddress', [$address, $label, $rescan, $p2sh]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importdescriptors.html
     * importdescriptors "requests"
     * Import descriptors. This will trigger a rescan of the blockchain based on the earliest timestamp of all descriptors being imported. Requires a new wallet backup.
     * Note: This call can take over an hour to complete if using an early timestamp; during that time, other rpc calls
     * may report that the imported keys, addresses or scripts exist but related transactions are still missing.
     */
    public function importdescriptors($requests)
    {
        return $this->request('importdescriptors', [$requests]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importmulti.html
     * importmulti "requests" ( "options" )
     * Import addresses/scripts (with private or public keys, redeem script (P2SH)), optionally rescanning the blockchain from the earliest creation time of the imported scripts. Requires a new wallet backup.
     * If an address/script is imported without all of the private keys required to spend from that address, it will be watchonly. The ‘watchonly’ option must be set to true in this case or a warning will be returned.
     * Conversely, if all the private keys are provided and the address/script is spendable, the watchonly option must be set to false, or a warning will be returned.
     * Note: This call can take over an hour to complete if rescan is true, during that time, other rpc calls
     * may report that the imported keys, addresses or scripts exist but related transactions are still missing.
     * Note: Use “getwalletinfo” to query the scanning progress.
     */
    public function importmulti($requests, $options = null)
    {
        return $this->request('importmulti', [$requests, $options]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importprivkey.html
     * importprivkey "privkey" ( "label" rescan )
     * Adds a private key (as returned by dumpprivkey) to your wallet. Requires a new wallet backup.
     * Hint: use importmulti to import more than one private key.
     * Note: This call can take over an hour to complete if rescan is true, during that time, other rpc calls
     * may report that the imported key exists but related transactions are still missing, leading to temporarily incorrect/bogus balances and unspent outputs until rescan completes.
     * Note: Use “getwalletinfo” to query the scanning progress.
     */
    public function importprivkey(string $privkey, string $label = null, bool $rescan = null)
    {
        return $this->request('importprivkey', [$privkey, $label, $rescan]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importprunedfunds.html
     * importprunedfunds "rawtransaction" "txoutproof"
     * Imports funds without rescan. Corresponding address or script must previously be included in wallet. Aimed towards pruned wallets. The end-user is responsible to import additional transactions that subsequently spend the imported outputs or rescan after the point in the blockchain the transaction is included.
     */
    public function importprunedfunds(string $rawtransaction, string $txoutproof)
    {
        return $this->request('importprunedfunds', [$rawtransaction, $txoutproof]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importpubkey.html
     * importpubkey "pubkey" ( "label" rescan )
     * Adds a public key (in hex) that can be watched as if it were in your wallet but cannot be used to spend. Requires a new wallet backup.
     * Hint: use importmulti to import more than one public key.
     * Note: This call can take over an hour to complete if rescan is true, during that time, other rpc calls
     * may report that the imported pubkey exists but related transactions are still missing, leading to temporarily incorrect/bogus balances and unspent outputs until rescan completes.
     * Note: Use “getwalletinfo” to query the scanning progress.
     */
    public function importpubkey(string $pubkey, string $label = null, bool $rescan = null)
    {
        return $this->request('importpubkey', [$pubkey, $label, $rescan]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/importwallet.html
     * importwallet "filename"
     * Imports keys from a wallet dump file (see dumpwallet). Requires a new wallet backup to include imported keys.
     * Note: Use “getwalletinfo” to query the scanning progress.
     */
    public function importwallet(string $filename)
    {
        return $this->request('importwallet', [$filename]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/keypoolrefill.html
     * keypoolrefill ( newsize )
     * Fills the keypool.
     * Requires wallet passphrase to be set with walletpassphrase call if wallet is encrypted.
     */
    public function keypoolrefill(int $newsize = null)
    {
        return $this->request('keypoolrefill', [$newsize]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listaddressgroupings.html
     * listaddressgroupings
     * Lists groups of addresses which have had their common ownership
     * made public by common use as inputs or as the resulting change
     * in past transactions
     */
    public function listaddressgroupings()
    {
        return $this->request('listaddressgroupings');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listlabels.html
     * listlabels ( "purpose" )
     * Returns the list of all labels, or labels that are assigned to addresses with a specific purpose.
     */
    public function listlabels(string $purpose = null)
    {
        return $this->request('listlabels', [$purpose]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listlockunspent.html
     * listlockunspent
     * Returns list of temporarily unspendable outputs.
     * See the lockunspent call to lock and unlock transactions for spending.
     */
    public function listlockunspent()
    {
        return $this->request('listlockunspent');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listreceivedbyaddress.html
     * listreceivedbyaddress ( minconf include_empty include_watchonly "address_filter" )
     * List balances by receiving address.
     */
    public function listreceivedbyaddress(int $minconf = null, bool $include_empty = null, bool $include_watchonly = null, string $address_filter = null)
    {
        return $this->request('listreceivedbyaddress', [$minconf, $include_empty, $include_watchonly, $address_filter]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listreceivedbylabel.html
     * listreceivedbylabel ( minconf include_empty include_watchonly )
     * List received transactions by label.
     */
    public function listreceivedbylabel(int $minconf = null, bool $include_empty = null, bool $include_watchonly = null)
    {
        return $this->request('listreceivedbylabel', [$minconf, $include_empty, $include_watchonly]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listsinceblock.html
     * listsinceblock ( "blockhash" target_confirmations include_watchonly include_removed )
     * Get all transactions in blocks since block [blockhash], or all transactions if omitted.
     * If “blockhash” is no longer a part of the main chain, transactions from the fork point onward are included.
     * Additionally, if include_removed is set, transactions affecting the wallet which were removed are returned in the “removed” array.
     */
    public function listsinceblock(string $blockhash = null, int $target_confirmations = null, bool $include_watchonly = null, bool $include_removed = null)
    {
        return $this->request('listsinceblock', [$blockhash, $target_confirmations, $include_watchonly, $include_removed]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listtransactions.html
     * listtransactions ( "label" count skip include_watchonly )
     * If a label name is provided, this will return only incoming transactions paying to addresses with the specified label.
     * Returns up to ‘count’ most recent transactions skipping the first ‘from’ transactions.
     */
    public function listtransactions(string $label = null, int $count = null, int $skip = null, bool $include_watchonly = null)
    {
        return $this->request('listtransactions', [$label, $count, $skip, $include_watchonly]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listunspent.html
     * listunspent ( minconf maxconf ["address",...] include_unsafe query_options )
     * Returns array of unspent transaction outputs
     * with between minconf and maxconf (inclusive) confirmations.
     * Optionally filter to only include txouts paid to specified addresses.
     */
    public function listunspent(int $minconf = null, int $maxconf = null, $addresses = null, bool $include_unsafe = null, $query_options = null)
    {
        return $this->request('listunspent', [$minconf, $maxconf, $addresses, $include_unsafe, $query_options]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listwalletdir.html
     * listwalletdir
     * Returns a list of wallets in the wallet directory.
     */
    public function listwalletdir()
    {
        return $this->request('listwalletdir');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/listwallets.html
     * listwallets
     * Returns a list of currently loaded wallets.
     * For full information on the wallet, use “getwalletinfo”
     */
    public function listwallets()
    {
        return $this->request('listwallets');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/loadwallet.html
     * loadwallet "filename" ( load_on_startup )
     * Loads a wallet from a wallet file or directory.
     * Note that all wallet command-line options used when starting bitcoind will be
     * applied to the new wallet (eg -rescan, etc).
     */
    public function loadwallet(string $filename, bool $load_on_startup = null)
    {
        return $this->request('loadwallet', [$filename, $load_on_startup]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/lockunspent.html
     * lockunspent unlock ( [{"txid":"hex","vout":n},...] )
     * Updates list of temporarily unspendable outputs.
     * Temporarily lock (unlock=false) or unlock (unlock=true) specified transaction outputs.
     * If no transaction outputs are specified when unlocking then all current locked transaction outputs are unlocked.
     * A locked transaction output will not be chosen by automatic coin selection, when spending bitcoins.
     * Manually selected coins are automatically unlocked.
     * Locks are stored in memory only. Nodes start with zero locked outputs, and the locked output list
     * is always cleared (by virtue of process exit) when a node stops or fails.
     * Also see the listunspent call
     */
    public function lockunspent(bool $unlock, $transactions = null)
    {
        return $this->request('lockunspent', [$unlock, $transactions]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/psbtbumpfee.html
     * psbtbumpfee "txid" ( options )
     * Bumps the fee of an opt-in-RBF transaction T, replacing it with a new transaction B.
     * Returns a PSBT instead of creating and signing a new transaction.
     * An opt-in RBF transaction with the given txid must be in the wallet.
     * The command will pay the additional fee by reducing change outputs or adding inputs when necessary.
     * It may add a new change output if one does not already exist.
     * All inputs in the original transaction will be included in the replacement transaction.
     * The command will fail if the wallet or mempool contains a transaction that spends one of T’s outputs.
     * By default, the new fee will be calculated automatically using the estimatesmartfee RPC.
     * The user can specify a confirmation target for estimatesmartfee.
     * Alternatively, the user can specify a fee rate in sat/vB for the new transaction.
     * At a minimum, the new fee rate must be high enough to pay an additional new relay fee (incrementalfee
     * returned by getnetworkinfo) to enter the node’s mempool.
     * * WARNING: before version 0.21, fee_rate was in BTC/kvB. As of 0.21, fee_rate is in sat/vB. *
     */
    public function psbtbumpfee(string $txid, $options = null)
    {
        return $this->request('psbtbumpfee', [$txid, $options]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/removeprunedfunds.html
     * removeprunedfunds "txid"
     * Deletes the specified transaction from the wallet. Meant for use with pruned wallets and as a companion to importprunedfunds. This will affect wallet balances.
     */
    public function removeprunedfunds(string $txid)
    {
        return $this->request('removeprunedfunds', [$txid]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/rescanblockchain.html
     * rescanblockchain ( start_height stop_height )
     * Rescan the local blockchain for wallet related transactions.
     * Note: Use “getwalletinfo” to query the scanning progress.
     */
    public function rescanblockchain(int $start_height = null, int $stop_height = null)
    {
        return $this->request('rescanblockchain', [$start_height, $stop_height]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/send.html
     * send [{"address":amount},{"data":"hex"},...] ( conf_target "estimate_mode" fee_rate options )
     * EXPERIMENTAL warning: this call may be changed in future releases.
     * Send a transaction.
     */
    public function send($outputs, int $conf_target = null, string $estimate_mode = null, $fee_rate = null, $options = null)
    {
        return $this->request('send', [$outputs, $conf_target, $estimate_mode, $fee_rate, $options]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/sendmany.html
     * sendmany "" {"address":amount} ( minconf "comment" ["address",...] replaceable conf_target "estimate_mode" fee_rate verbose )
     * Send multiple times. Amounts are double-precision floating point numbers.
     * Requires wallet passphrase to be set with walletpassphrase call if wallet is encrypted.
     */
    public function sendmany(string $dummy, $amounts, int $minconf = null, string $comment = null, $subtractfeefrom = null, bool $replaceable = null, int $conf_target = null, string $estimate_mode = null, $fee_rate = null)
    {
        return $this->request('sendmany', [$dummy, $amounts, $minconf, $comment, $subtractfeefrom, $replaceable, $conf_target, $estimate_mode, $fee_rate]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/sendtoaddress.html
     * sendtoaddress "address" amount ( "comment" "comment_to" subtractfeefromamount replaceable conf_target "estimate_mode" avoid_reuse fee_rate verbose )
     * Send an amount to a given address.
     * Requires wallet passphrase to be set with walletpassphrase call if wallet is encrypted.
     */
    public function sendtoaddress(string $address, $amount, string $comment = null, string $comment_to = null, bool $subtractfeefromamount = null, bool $replaceable = null, int $conf_target = null, string $estimate_mode = null, bool $avoid_reuse = null)
    {
        return $this->request('sendtoaddress', [$address, $amount, $comment, $comment_to, $subtractfeefromamount, $replaceable, $conf_target, $estimate_mode, $avoid_reuse]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/sethdseed.html
     * sethdseed ( newkeypool "seed" )
     * Set or generate a new HD wallet seed. Non-HD wallets will not be upgraded to being a HD wallet. Wallets that are already
     * HD will have a new HD seed set so that new keys added to the keypool will be derived from this new seed.
     * Note that you will need to MAKE A NEW BACKUP of your wallet after setting the HD wallet seed.
     * Requires wallet passphrase to be set with walletpassphrase call if wallet is encrypted.
     */
    public function sethdseed(bool $newkeypool = null, string $seed = null)
    {
        return $this->request('sethdseed', [$newkeypool, $seed]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/setlabel.html
     * setlabel "address" "label"
     * Sets the label associated with the given address.
     */
    public function setlabel(string $address, string $label)
    {
        return $this->request('setlabel', [$address, $label]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/settxfee.html
     * settxfee amount
     * Set the transaction fee per kB for this wallet. Overrides the global -paytxfee command line parameter.
     * Can be deactivated by passing 0 as the fee. In that case automatic fee selection will be used by default.
     */
    public function settxfee($amount)
    {
        return $this->request('settxfee', [$amount]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/setwalletflag.html
     * setwalletflag "flag" ( value )
     * Change the state of the given wallet flag for a wallet.
     */
    public function setwalletflag(string $flag, bool $value = null)
    {
        return $this->request('setwalletflag', [$flag, $value]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/signmessage.html
     * signmessage "address" "message"
     * Sign a message with the private key of an address
     * Requires wallet passphrase to be set with walletpassphrase call if wallet is encrypted.
     */
    public function signmessage(string $address, string $message)
    {
        return $this->request('signmessage', [$address, $message]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/signrawtransactionwithwallet.html
     * signrawtransactionwithwallet "hexstring" ( [{"txid":"hex","vout":n,"scriptPubKey":"hex","redeemScript":"hex","witnessScript":"hex","amount":amount},...] "sighashtype" )
     * Sign inputs for raw transaction (serialized, hex-encoded).
     * The second optional argument (may be null) is an array of previous transaction outputs that
     * this transaction depends on but may not yet be in the block chain.
     * Requires wallet passphrase to be set with walletpassphrase call if wallet is encrypted.
     */
    public function signrawtransactionwithwallet(string $hexstring, $prevtxs = null, string $sighashtype = null)
    {
        return $this->request('signrawtransactionwithwallet', [$hexstring, $prevtxs, $sighashtype]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/unloadwallet.html
     * unloadwallet ( "wallet_name" load_on_startup )
     * Unloads the wallet referenced by the request endpoint otherwise unloads the wallet specified in the argument.
     * Specifying the wallet name on a wallet endpoint is invalid.
     */
    public function unloadwallet(string $wallet_name = null, bool $load_on_startup = null)
    {
        return $this->request('unloadwallet', [$wallet_name, $load_on_startup]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/upgradewallet.html
     * upgradewallet ( version )
     * Upgrade the wallet. Upgrades to the latest version if no version number is specified.
     * New keys may be generated and a new wallet backup will need to be made.
     */
    public function upgradewallet(int $version = null)
    {
        return $this->request('upgradewallet', [$version]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/walletcreatefundedpsbt.html
     * walletcreatefundedpsbt ( [{"txid":"hex","vout":n,"sequence":n},...] ) [{"address":amount},{"data":"hex"},...] ( locktime options bip32derivs )
     * Creates and funds a transaction in the Partially Signed Transaction format.
     * Implements the Creator and Updater roles.
     */
    public function walletcreatefundedpsbt($inputs = null, $outputs, int $locktime = null, $options = null, bool $bip32derivs = null)
    {
        return $this->request('walletcreatefundedpsbt', [$inputs, $outputs, $locktime, $options, $bip32derivs]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/walletlock.html
     * walletlock
     * Removes the wallet encryption key from memory, locking the wallet.
     * After calling this method, you will need to call walletpassphrase again
     * before being able to call any methods which require the wallet to be unlocked.
     */
    public function walletlock()
    {
        return $this->request('walletlock');
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/walletpassphrase.html
     * walletpassphrase "passphrase" timeout
     * Stores the wallet decryption key in memory for ‘timeout’ seconds.
     * This is needed prior to performing transactions related to private keys such as sending bitcoins
     * Note:
     * Issuing the walletpassphrase command while the wallet is already unlocked will set a new unlock
     * time that overrides the old one.
     */
    public function walletpassphrase(string $passphrase, int $timeout)
    {
        return $this->request('walletpassphrase', [$passphrase, $timeout]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/walletpassphrasechange.html
     * walletpassphrasechange "oldpassphrase" "newpassphrase"
     * Changes the wallet passphrase from ‘oldpassphrase’ to ‘newpassphrase’.
     */
    public function walletpassphrasechange(string $oldpassphrase, string $newpassphrase)
    {
        return $this->request('walletpassphrasechange', [$oldpassphrase, $newpassphrase]);
    }

    /**
     * https://developer.bitcoin.org/reference/rpc/walletprocesspsbt.html
     * walletprocesspsbt "psbt" ( sign "sighashtype" bip32derivs )
     * Update a PSBT with input information from our wallet and then sign inputs
     * that we can sign for.
     * Requires wallet passphrase to be set with walletpassphrase call if wallet is encrypted.
     */
    public function walletprocesspsbt(string $psbt, bool $sign = null, string $sighashtype = null, bool $bip32derivs = null)
    {
        return $this->request('walletprocesspsbt', [$psbt, $sign, $sighashtype, $bip32derivs]);
    }

}
