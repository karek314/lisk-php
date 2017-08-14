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
require_once('main.php');
bold_echo("\nLisk-PHP CLI Tool ".number_format(LISK_PHP_VER,1));
$cmd = strtolower($argv[1]);
bold_echo("\nMethod:".$cmd);
bold_echo("\nNetworking:".$server."\n");
$parm1 = $argv[2];
$parm2 = $argv[3];
$parm3 = $argv[4];
$parm4 = $argv[5];
$parm5 = $argv[6];
$parm6 = $argv[7];
$parm7 = $argv[8];
if ($cmd == strtolower('getKeysFromSecret')) {
	if ($parm1) {
		echo "Passphrase:".$parm1;
		$output = getKeysFromSecret($parm1,true);
		echo "\nPublic:".$output['public'];
		echo "\nSecret:".$output['secret'];
		newline();
	} else {
		newline();
		method_info();
		newline();
		echo 'php lisk-cli.php getKeysFromSecret "passphrase"';
		newline();
		echo 'php lisk-cli.php getKeysFromSecret "word word word"';
		newline();
	}
} else if ($cmd == strtolower('CreateTransaction')) {
	if ($parm1 && $parm2 && $parm3) {
		echo "Amount:".$parm2."\n";
		echo "Recipient:".$parm1."\n";
		$amount = 100000000*$parm2;
		if ($parm4) {
			echo "Signing with 2 passphrases\n";
		} else {
			echo "Signing with 1 passphrase\n";
		}
		$tx = CreateTransaction($parm1, $amount, $parm3, $parm4, false, -10);
		newline();
		echo "Transaction->";
		var_dump($tx);
		newline();
		bold_echo('Plain json string to broadcast');
		newline();
		echo json_encode($tx);
		newline();
		bold_echo("End of plain json string");
		newline();
	} else {
		transaction_helper('CreateTransaction');
		die("\n");
	}
} else if ($cmd == strtolower('SendTransaction')) {
	if ($parm1 && $parm2 && $parm3) {
		echo "Amount:".$parm2."\n";
		echo "Recipient:".$parm1."\n";
		$amount = 100000000*$parm2;
		if ($parm4) {
			echo "Signing with 2 passphrases\n";
		} else {
			echo "Signing with 1 passphrase\n";
		}
		$tx = CreateTransaction($parm1, $amount, $parm3, $parm4, false, -10);
		newline();
		echo "Transaction->";
		var_dump($tx);
		newline();
		bold_echo("Response->");
		var_dump(SendTransaction(json_encode($tx),$server));
		newline();
	} else {
		transaction_helper('SendTransaction');
		die("\n");
	}
} else if ($cmd == strtolower('NodeStatus')) {
	newline();
	echo "Status->";
	var_dump(NodeStatus($server));
	newline();
} else if ($cmd == strtolower('Account')) {
	if ($parm1){
		newline();
		echo "Account->";
		var_dump(AccountForAddress($parm1,$server));
		newline();	
	} else {
		newline();
		method_info();
		newline();
		echo 'php lisk-cli.php Account address';
		newline();
		echo 'php lisk-cli.php Account 14034346393178966836L';
		newline();
	}
} else if ($cmd == strtolower('GetBlocksBy')) {
	if ($parm1){
		newline();
		echo "Blocks->";
		var_dump(GetBlocksBy($parm1, $server));
		newline();	
	}
	else
	{
		newline();
		method_info();
		newline();
		echo 'php lisk-cli.php GetBlocksBy';
		newline();
		echo 'php lisk-cli.php GetBlocksBy b002f58531c074c7190714523eec08c48db8c7cfc0c943097db1a2e82ed87f84';
		newline();
    }
} else if ($cmd == strtolower('GetVotersFor')) {
	if ($parm1){
		newline();
		echo "Voters->";
		var_dump(GetVotersFor($parm1,$server));
		newline();	
	} else {
		newline();
		method_info();
		newline();
		echo 'php lisk-cli.php GetVotersFor publicKey';
		newline();
		echo 'php lisk-cli.php GetVotersFor b002f58531c074c7190714523eec08c48db8c7cfc0c943097db1a2e82ed87f84';
		newline();
	}
} else if ($cmd == strtolower('GetDelegateInfo')) {
	if ($parm1){
		newline();
		echo "Info->";
		var_dump(GetDelegateInfo($parm1,$server));
		newline();	
	} else {
		newline();
		method_info();
		newline();
		echo 'php lisk-cli.php GetDelegateInfo publicKey';
		newline();
		echo 'php lisk-cli.php GetDelegateInfo b002f58531c074c7190714523eec08c48db8c7cfc0c943097db1a2e82ed87f84';
		newline();
	}
} else if ($cmd == strtolower('GetBlock')){
    if ($parm1){
        newline();
        echo "Block info->";
        var_dump(GetBlock($parm1,$server));
        newline();
    } else {
        newline();
        method_info();
        newline();
        echo 'php lisk-cli.php GetBlock';
        newline();
        echo 'php lisk-cli.php GetBlock 6866824482577335503';
        newline();
    }
} else if ($cmd == strtolower('GetFees')){
        newline();
        echo "Current blockchain fees->";
        var_dump(GetFees($server));
} else if ($cmd == strtolower('GetSupply')){
        newline();
        echo "Total Lisk Supply->";
        var_dump(GetSupply($server));
} else if ($cmd == strtolower('help')) {
	help_message();
} else {
	help_message();
}


function transaction_helper($method){
	newline();
	bold_echo('Below you can see pattern and example for parms');
	newline();
	echo 'php lisk-cli.php '.$method.' recipient 1.50 "passphrase1" "passphrase2"';
	newline();
	echo 'php lisk-cli.php '.$method.' 2928783316108674201L 1.50 "word word word" "word word word"';
	newline();
	echo 'Second pasphrase is optional, use it only for account with enabled second signature.';
}


function help_message(){
	newline();
	bold_echo('Help Message');
	newline();
	echo "\tlisk-php - Interface to interact with Lisk network via PHP";
	newline();
	echo "\tCLI Tool and library for php projects";
	newline();
	echo "\tCopyright 2017 Karek314";
	newline();
	newline();
	bold_echo('CLI USAGE:');
	newline();
	echo "\tphp lisk-cli.php METHOD parm1 parm2 parm3...";
	newline();
	newline();
	bold_echo('INFO:');
	newline();
	echo "\tEach METHOD can be executed without any parms to get more detailed help information which parms are needed.";
	newline();
	newline();
	bold_echo('METHODS:');
	newline();
	echo "\thelp                  Display this help message";
	newline();
	echo "\tgetKeysFromSecret     Generates public and secret from passphrase";
	newline();
	echo "\tCreateTransaction     Creates signed, ready to broadcast transaction";
	newline();
	echo "\tSendTransaction       Creates signed, ready to broadcast transaction and then broadcast it";
	newline();
	echo "\tNodeStatus            Get current selected node status";
	newline();
	echo "\tAccount               Get specified account details";
	newline();
    echo "\tGetBlock              Get specified block details";
    newline();
	echo "\tGetBlocksBy           Get blocks forged by";
	newline();
	echo "\tGetVotersFor          Get list of voters for delegate";
	newline();
	echo "\tGetDelegateInfo       Get delegate info";
    newline();
    echo "\tGetFees               Get blockchain fees";
	newline();
	newline();
}


function newline(){
	echo PHP_EOL;
}


function bold_echo($msg){
	echo "\033[1m".$msg."\033[0m";
}


function method_info(){
	bold_echo('Below you can see pattern and example for parms');
}


?>