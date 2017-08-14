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
	getKeysFromSecret     Generates public and secret from passphrase
	CreateTransaction     Creates signed, ready to broadcast transaction
	SendTransaction       Creates signed, ready to broadcast transaction and then broadcast it
	NodeStatus            Get current selected node status
	Account               Get specified account details
	GetBlocksBy           Get blocks forged by
	GetVotersFor          Get list of voters for delegate
	GetDelegateInfo       Get delegate info
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
- [ ] Forging with offline signing
  
 
 ## Contributing
If you want to contribute, fork and pull request or open issue.
  		  

## License
Licensed under the [MIT license](https://github.com/karek314/lisk-php/blob/master/LICENSE)
