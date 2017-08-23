# Lisk-PHP
Interface to interact with Lisk network via PHP
CLI Tool and library for php projects

## Requirements
* PHP 7.0.0 and up
* dependencies from [setup.sh](https://github.com/karek314/lisk-php/blob/master/setup.sh)

## Installation
```sh
git clone https://github.com/karek314/lisk-php
cd lisk-php
bash setup.sh
```

## Usage
This is library for PHP projects and CLI tool. Which allows to interact with Lisk network including signing transactions and other cryptographic activities.

### Lisk-CLI.php
Lisk-CLI can be used on linux with regular installation or on any platform supported by Docker.
Thanks to [5an1ty](https://github.com/5an1ty/) and [docker-lisk-php](https://github.com/5an1ty/docker-lisk-php)
You can without hassle use <b>Lisk-cli</b>
```sh
docker pull 5an1ty/lisk-php
docker run --rm 5an1ty/lisk-php --help
 ```

Help
```sh
Lisk-PHP CLI Tool 1.0
Method:help
Networking:https://node06.lisk.io/

Help Message
  lisk-php - Interface to interact with Lisk network via PHP
  CLI Tool and library for php projects
  Copyright 2017 Karek314

CLI USAGE:
  php lisk-cli.php METHOD parm1 parm2 parm3...

INFO:
  Each METHOD can be executed without any parms to get more detailed help information which parms are needed.

METHODS:
  help                  Display this help message
  GenerateAccount       Generates new bip39 account
  Vanitygen             Generates new bip39 account with desired prefix
  Create2ndPassphrase   Generates second signature to specified account
  RegisterDelegate      Registers delegate with specified name
  Vote                  Vote for specified delegates with publicKey
  SignMessage           Signs message with specified account
  VerifyMessage         Verify signed message and displays it
  EncryptMessage        Encrypts message with recipient publickey
  DecryptMessage        Decrypts message with encrypted hex body, nonce and sender publickey
  getKeysFromSecret     Generates public and secret from passphrase
  CreateTransaction     Creates signed, ready to broadcast transaction
  SendTransaction       Creates signed, ready to broadcast transaction and then broadcast it
  NodeStatus            Get current selected node status
  Account               Get specified account details
  GetBlock              Get specified block details
  GetBlocksBy           Get blocks forged by
  GetVotersFor          Get list of voters for delegate
  GetDelegateInfo       Get delegate info
  GetFees               Get blockchain fees
  GetSupply             Get Lisk supply
  NetworkStatus         Get network height, epoch , fee, reward, supply
  GetForgedByAccount    Get amount of Lisk forged by an delegate
  GetDelegatesList      Get delegates list
  GetVotes              Get account votes
  ```
  
  <b>Lisk vanitygen</b><br>
  Looks for address starting with 1312223 with 24 words seed
  ```sh
  php lisk-cli.php Vanitygen 24 1312223
  ```
  
  ```sh
  [Done:2271633] [Accounts per sec: 23.404] [Elapsed time: 1d 3h 11min 55s]
Found account with [1312223] prefix->
Passphrase:fluid ostrich moral until nose slim level tumble excess winter border ready allow reflect skill acid proud possible arm fade guide among myself myself
Address:13122233654116561038L
Public:553538b4dd31a4996cfa143713661bdcdf4ec27c9e8155159d3eefdf480acfe2
Secret:168b85c9704dc8c1c87d74db4f0dc95869af5fd05c8ae43bc9e8273855f90bd8553538b4dd31a4996cfa143713661bdcdf4ec27c9e8155159d3eefdf480acfe2
  ```


  Generate new account with 24 words seed
  ```sh
  php lisk-cli.php GenerateAccount 24
  ```
  
  ### As library in project
  Import is as easy as
```php
require_once('main.php');
```
  #### Example usage
  Generating public and secret keys from passphrase
  ```php
  $output = getKeysFromSecret('passphrase',true);
  echo "\nPublic:".$output['public'];
  echo "\nSecret:".$output['secret'];
  ```
  Create signed transaction
  ```php
  $tx = CreateTransaction('9507408743015643357L', '100000000', 'passphrase', false, false, -10);
  $tx = CreateTransaction('9507408743015643357L', '100000000', 'passphrase1', 'passphrase2', false, -10);
  $tx = CreateTransaction('9507408743015643357L', '100000000', 'passphrase1', 'passphrase2', 'custom data', -10);
  ```
  Create and send signed transaction
  ```php
  $tx = CreateTransaction('9507408743015643357L', '100000000', 'passphrase', false, false, -10);
  $result = SendTransaction(json_encode($tx),$server);
  var_dump($result);
  ```
  Get node status
  ```php
  var_dump(NodeStatus($server));
  ```
  More examples can be found in [Lisk-cli.php](https://github.com/karek314/lisk-php/blob/master/lisk-cli.php)
  
  ## Custom configuration
  By default library use one of predefined nodes, however this can be overridden by setting [memcached](https://memcached.org) appropriate values for keys <b>lisk_host</b>, <b>lisk_protocol</b> and <b>lisk_port</b>.
  You can switch to testnet changing the <b>MAINNET</b> and <b>NETWORK_HASH</b> values in const.php.

 ## To do
- [ ] Implement more endpoints
- [ ] Implement more transaction types
- [ ] Forging with new secure method
  
 
 ## Contributing
If you want to contribute, fork and pull request or open issue.
        

## License
Licensed under the [MIT license](https://github.com/karek314/lisk-php/blob/master/LICENSE)