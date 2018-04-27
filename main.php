<?php
/*
LISK-PHP
Made by karek314
https://github.com/karek314/lisk-php
The MIT License (MIT)

Copyright (c) 2017

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
error_reporting(error_reporting() & ~E_NOTICE & ~E_WARNING);
require_once('lib/crypto.php');
require_once('lib/utils.php');
require_once('lib/bip39words.php');
require_once('lib/account.php');
require_once('lib/transaction.php');
require_once('lib/networking.php');
require_once('lib/bytebuffer/main.php');
require_once('lib/assert/lib/Assert/Assertion.php');
require_once('lib/polyfill-mbstring/Mbstring.php');
require_once('lib/polyfill-mbstring/Resources/unidata/lowerCase.php');
require_once('lib/polyfill-mbstring/Resources/unidata/upperCase.php');
require_once('lib/php-aes-gcm/src/AESGCM.php');
require_once('lib/BigInteger.php');
require_once('const.php');


$m = new Memcached();
$m->addServer('localhost', 11211);
$lisk_host = $m->get('lisk_host');
$lisk_port = $m->get('lisk_port');
$lisk_protocol = $m->get('lisk_protocol');
if ($lisk_host && $lisk_port && $lisk_protocol) {
	$server = $lisk_protocol."://";
	if ($lisk_port == 80 || $lisk_port == 443) {
		$server .= $lisk_host."/";
	} else {
		$server .= $lisk_host.":".$lisk_port."/";
	}
} else {
	if (SECURE) {
		$server = "https://";
	} else {
		$server = "http://";
	}
	if (MAINNET) {
		$lisk_public_nodes = array("node01.lisk.io",
            					   "node02.lisk.io",
            					   "node03.lisk.io",
            					   "node04.lisk.io",
            					   "node05.lisk.io",
            					   "node06.lisk.io",
            					   "node07.lisk.io",
            					   "node08.lisk.io");
		$server .= $lisk_public_nodes[array_rand($lisk_public_nodes)]."/";
	} else {
		$server .= "testnet.lisk.io/";
	}
}


?>