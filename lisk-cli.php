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
		echo "\nAddress:".getAddressFromPublicKey($output['public']);
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
		$amount = LSK_BASE*$parm2;
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
		$amount = LSK_BASE*$parm2;
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
	} else {
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
} else if ($cmd == strtolower('GetDelegateList')) {
    if ($parm1 && $parm2){
        newline();
        echo "List->";
        var_dump(GetDelegateList($parm1,$parm2,$server));
        newline();  
    } else {
        newline();
        method_info();
        newline();
        echo 'php lisk-cli.php GetDelegateList limit offset';
        newline();
        echo 'php lisk-cli.php GetDelegateList 5 101';
        newline();
    }
} else if ($cmd == strtolower('GetDelegateInfo')) {//GetDelegateList
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
} else if ($cmd == strtolower('GetBlockchainInfo')){
        newline();
        echo "Current blockchain info->";
        var_dump(GetBlockchainInfo($server));
} else if($cmd == strtolower('GetDelegatesList')){
    newline();
    echo "First 101 delegates->";
    var_dump(GetDelegatesList($server));
    newline();
    echo "You can also use optional parameters: limit orderBy offset orderType";
    if($parm1 && $parm2 && $parm3){
        var_dump(GetDelegatesList($server,$parm1,$parm2,$parm3));
    }
} else if($cmd == strtolower('GetVotes')){
    if ($parm1){
        newline();
        echo "Account votes->";
        var_dump(GetVotes($parm1,$server));
        newline();
    } else{
        newline();
        method_info();
        newline();
        echo 'php lisk-cli.php GetVotes address';
        newline();
        echo 'php lisk-cli.php GetVotes 11406747278331941053L';
        newline();
    }
} else if($cmd == strtolower('GenerateAccount')){
    if ($parm1){
        newline();
        if ($parm2) {
        	$passphrase = GenerateAccount($parm1,hash('sha256',$parm2));
        } else {
        	$passphrase = GenerateAccount($parm1);
        }
        $output = getKeysFromSecret($passphrase,true);
        newline();
        echo "Account Generated->";
        newline();
        echo "Passphrase:".$passphrase;
        newline();
		echo "Address:".getAddressFromPublicKey($output['public']);
		newline();
		echo "Public:".$output['public'];
		newline();
		echo "Secret:".$output['secret'];
        newline();
        newline();
    } else{
        newline();
        method_info();
        newline();
        echo 'php lisk-cli.php GenerateAccount SeedLenght(12,15,21,24) OptionalEntropy';
        newline();
        echo 'php lisk-cli.php GenerateAccount 12';
        newline();
        echo 'php lisk-cli.php GenerateAccount 12 CUSTOM_ENTROPY_STRING';
        newline();
        echo 'If you want to generate account based on entropy derived from MCRYPT_DEV_URANDOM leave 2nd parm empty. However if you aim to get more unique entropy, please specify parm2 as string without char limit, typing more than 64-128 random chars should make pretty unique entropy. You can also simply do';
        newline();
        echo 'php lisk-cli.php GenerateAccount 12 123';
        newline();
        echo 'However such account will be pretty easy to regenerate';
        newline();
    }
} else if($cmd == strtolower('Vanitygen')){
    if ($parm1){
        newline();
        if ($parm3) {
           if (is_numeric($parm3)){
                if ($parm4 && $parm5) {
                    //Vanitygen($lenght=12,$prefix,$t=1,$special=true,$special_lenght=17,$speciality=4)
                    $account = Vanitygen($parm1,$parm2,$parm3,true,$parm4,$parm5);
                } else {
                    $account = Vanitygen($parm1,$parm2,$parm3);
                }
           } else {
                die("Parm3 must be number");
           }
        } else {
            $account = Vanitygen($parm1,$parm2);
        }
       	newline();
        echo "Found account with [".$parm2."] prefix->";
        newline();
        echo "Passphrase:".$account['passphrase'];
        newline();
		echo "Address:".$account['address'];
		newline();
		echo "Public:".$account['public'];
		newline();
		echo "Secret:".$account['secret'];
        newline();
        newline();
    } else{
        newline();
        method_info();
        newline();
        echo 'php lisk-cli.php Vanitygen SeedLenght(12,15,21,24) Prefix';
        newline();
        echo 'php lisk-cli.php Vanitygen 12 958';
        newline();
        echo 'php lisk-cli.php Vanitygen SeedLenght(12,15,21,24) Prefix 1 17 4';
        newline();
        echo 'php lisk-cli.php Vanitygen 12 958 1 17 4';
        newline();
        echo 'Each character added as prefix in address generation will increase time to find exponentially. Currently Lisk addresses are numeric hence only numbers allowed.';
        newline();
    }
} else if($cmd == strtolower('Create2ndPassphrase')){
    if ($parm1 && $parm2){
        newline();
        if ($parm3) {
        	if (substr_count($parm3, ' ') > 10) {
        		$second_passphrase = $parm3;
        	} else {
        		$second_passphrase = GenerateAccount($parm2,$parm3);
        	}
        } else {
        	$second_passphrase = GenerateAccount($parm2);
        }
        echo 'Second Passphrase:'.$second_passphrase;
       	$tx = Create2ndPassphrase($parm1,$second_passphrase);
       	newline();
       	echo "Transaction->";
		var_dump($tx);
		newline();
		bold_echo("Response->");
		var_dump(SendTransaction(json_encode($tx),$server));
        newline();
        newline();
    } else{
        newline();
        method_info();
        newline();
        echo 'php lisk-cli.php Create2ndPassphrase "passphrase" SeedLenght(12,15,21,24) Optionally(PredefinedSecondPassphrase or custom entropy)';
        newline();
        echo 'Examples';
        newline();
        echo 'Second Signature with 12 word seed lenght';
        newline();
        echo 'php lisk-cli.php Create2ndPassphrase "word word word" 12';
        newline();
        echo 'Second Signature with 24 word seed lenght and custom entropy';
        newline();
        echo 'php lisk-cli.php Create2ndPassphrase "word word word" 24 96CAE35CE8A9B0244178BF28E4966C2CE1B8385723A96A6B838858CDD6CA0A1E';
        newline();
        echo 'Second Signature with custom word seed';
        newline();
        echo 'php lisk-cli.php Create2ndPassphrase "word word word" 1 "word2 word2 word2"';
        newline();
    }
} else if($cmd == strtolower('RegisterDelegate')){
    if ($parm1 && $parm2){
        newline();
        echo 'Delegate Name:'.$parm1;
       	$tx = RegisterDelegate($parm1,$parm2,$parm3);
       	newline();
       	echo "Transaction->";
		var_dump($tx);
		newline();
		bold_echo("Response->");
		var_dump(SendTransaction(json_encode($tx),$server));
        newline();
        newline();
    } else{
        newline();
        method_info();
        newline();
        echo 'php lisk-cli.php RegisterDelegate NAME "passphrase1" Optionally("passphrase2")';
        newline();
        echo 'Examples';
        newline();
        echo 'php lisk-cli.php RegisterDelegate SuperDelegate1 "word word word"';
        newline();
        echo 'php lisk-cli.php RegisterDelegate SuperDelegate2 "word word word" "word2 word2 word2"';
        newline();
    }
} else if($cmd == strtolower('Vote')){
    if ($parm1 && $parm2){
        newline();
        $votes = $parm1;
        $votes_count = substr_count($votes,'+')+substr_count($votes,'-');
        if ($votes_count > 1) {
        	$votes = preg_split("/(\\+|-)/", $votes);
        	$tvotes = array();
        	foreach ($votes as $key => $value) {
        		if (strlen($value)==64) {
					if (strpos($parm1, '+'.$value) !== false) {
						$tvotes[] = '+'.$value;
					} else if (strpos($parm1, '-'.$value) !== false) {
						$tvotes[] = '-'.$value;
					}
        		}
        	}
        	$votes = $tvotes;
        } else {
        	$votes = array($votes);
        }
       	$tx = Vote($votes,$parm2,$parm3);
       	newline();
       	echo "Transaction->";
		var_dump($tx);
		newline();
		bold_echo("Response->");
		var_dump(SendTransaction(json_encode($tx),$server));
        newline();
        newline();
    } else{
        newline();
        method_info();
        newline();
        echo 'php lisk-cli.php Vote VOTES "passphrase1" Optionally("passphrase2")';
        newline();
        echo 'Examples';
        newline();
        echo 'Single vote';
        newline();
        echo 'php lisk-cli.php Vote +b002f58531c074c7190714523eec08c48db8c7cfc0c943097db1a2e82ed87f84 "word word word"';
        newline();
        echo 'Multiple votes (+publicKey-publicKey+publicKey and so on...)';
        newline();
        echo 'php lisk-cli.php Vote +b002f58531c074c7190714523eec08c48db8c7cfc0c943097db1a2e82ed87f84-d112f58531c074c7190714523eec08c48db8c7cfc0c943097db1a2e82ed87f84+3302f58531c074c7190714523eec08c48db8c7cfc0c943097db1a2e82ed87f84 "word word word" "word2 word2 word2"';
        newline();
    }
} else if($cmd == strtolower('SignMessage')){
    if ($parm1 && $parm2){
        newline();
        echo 'Message to sign:'.$parm1;
        $smsg = signMessage($parm1,$parm2,$parm3);
        newline();
        echo $smsg;
        newline();
        newline();
    } else{
        newline();
        method_info();
        newline();
        echo 'php lisk-cli.php SignMessage "message to sign" "passphrase" Optionally("passphrase2")';
        newline();
        echo 'php lisk-cli.php SignMessage "I do hereby confirm... something" "word word word" "word2 word2 word2"';
        newline();
    }
} else if($cmd == strtolower('VerifyMessage')){
    if ($parm1 && $parm2){
        newline();
        echo 'Signed Message:'.$parm1;
        newline();
        echo 'Public Key:'.$parm2;
        $msg = VerifyMessage($parm1,$parm2,$parm3);
        newline();
        echo 'Original Message->';
        newline();
        echo $msg;
        newline();
        newline();
    } else{
        newline();
        method_info();
        newline();
        echo 'php lisk-cli.php VerifyMessage SignedMessage PublicKey Optionally(publicKey2)';
        newline();
        echo 'php lisk-cli.php VerifyMessage 6753b2cd9e6c1ccc79441936af0595ee3040c046bd623496a87381f2a5c8cd5231a6ac104c1fca436227bcc77e8b38e60bb676198be2e097e208d6550f8bd005313233 3e6afd3d2d40df370fc430147b6d5122acd2673af2ebfd01fcd054f35980b0f0';
        newline();
    }
} else if($cmd == strtolower('EncryptMessage')){
    if ($parm1 && $parm2 && $parm3){
        newline();
        echo 'Plain Message:'.$parm1;
        newline();
        echo 'Public Key:'.$parm3;
        $msg = EncryptMessage($parm1,$parm2,$parm3);
        newline();
        echo 'Original Message->';
        newline();
        var_dump($msg);
        newline();
        newline();
    } else{
        newline();
        method_info();
        newline();
        echo 'php lisk-cli.php EncryptMessage "Message" "Passphrase" recipientPublicKey';
        newline();
        echo 'php lisk-cli.php EncryptMessage "secret message" "word word word" 3e6afd3d2d40df370fc430147b6d5122acd2673af2ebfd01fcd054f35980b0f0';
        newline();
    }
} else if($cmd == strtolower('DecryptMessage')){
    if ($parm1 && $parm2 && $parm3 && $parm4){
        newline();
        echo 'Encrypted Message:'.$parm1;
        newline();
        echo 'Message Nonce:'.$parm2;
        newline();
        echo 'Sender Public Key:'.$parm4;
        $msg = DecryptMessage($parm1,$parm2,$parm3,$parm4);
        newline();
        echo 'Original Message->';
        newline();
        var_dump($msg);
        newline();
        newline();
    } else{
        newline();
        method_info();
        newline();
        echo 'php lisk-cli.php DecryptMessage "Encrypted Message" "Message Nonce" "Passphrase" SenderPublicKey';
        newline();
        echo 'php lisk-cli.php DecryptMessage d3f60c5423df43b515d198e459636ed309363cc115d96a79a81f83c9 929a89c5d4a926ac5ec188a3e4a043de79e4c3f08380e9ca "twelve remember resist marine display congress when demise kiwi blur actor biology" 886aad2cdd821657e719bc5a280655a748c2a4a2ba65afa62e58e7607ee52fe7';
        newline();
    }
} else if($cmd == strtolower('EncryptPassphrase')){
    if ($parm1 && $parm2){
        newline();
        echo 'Password:'.$parm2;
        newline();
        echo 'Plain Passphrase:'.$parm1;
        newline();
        $output = getKeysFromSecret($parm1,true);
        echo 'Public Key:'.$output['public'];
        newline();
        echo 'Encrypting...';
        $output = encryptPassphrase($parm1,$parm2);
        newline();
        echo 'Encrypted->';
        newline();
        var_dump($output);
        echo 'Json->';
        newline();
        echo json_encode($output);
        newline();
        newline();
    } else{
        newline();
        method_info();
        newline();
        echo 'php lisk-cli.php EncryptPassphrase "Passphrase" "password"';
        newline();
        echo 'php lisk-cli.php EncryptPassphrase "word word word" "password"';
        newline();
    }
} else if($cmd == strtolower('DecryptPassphrase')){
    if ($parm1 && $parm2){
        newline();
        echo 'Encrypted:'.$parm1;
        newline();
        echo 'Password:'.$parm2;
        newline();
        $output = decryptPassphrase($parm1,$parm2);
        newline();
        echo 'Passphrase->';
        newline();
        var_dump($output);
        newline();
        newline();
    } else{
        newline();
        method_info();
        newline();
        echo 'php lisk-cli.php DecryptPassphrase "Encrypted passphrase cipher" "Password"';
        newline();
        echo 'php lisk-cli.php DecryptPassphrase "iterations=1&salt=476d4299531718af8c88156aab0bb7d6&cipherText=663dde611776d87029ec188dc616d96d813ecabcef62ed0ad05ffe30528f5462c8d499db943ba2ded55c3b7c506815d8db1c2d4c35121e1d27e740dc41f6c405ce8ab8e3120b23f546d8b35823a30639&iv=1a83940b72adc57ec060a648&tag=b5b1e6c6e225c428a4473735bc8f1fc9&version=1" "password"';
        newline();
    }
} else if($cmd == strtolower('ToggleForging')){
    if ($parm1 && $parm2){
        newline();
        echo 'Password:'.$parm1;
        newline();
        echo 'Public Key:'.$parm2;
        newline();
        $output = ToggleForging($parm1,$parm2,$server);
        newline();
        echo 'Forging->';
        newline();
        var_dump($output);
        newline();
        newline();
    } else{
        newline();
        method_info();
        newline();
        echo 'php lisk-cli.php ToggleForging "password" "public key"';
        newline();
        echo 'php lisk-cli.php ToggleForging "test password" 886aad2cdd821657e719bc5a280655a748c2a4a2ba65afa62e58e7607ee52fe7';
        newline();
    }
} else if ($cmd == strtolower('GetPendingTx')) {
    newline();
    $output = GetPendingTx($server);
    newline();
    echo 'Pending tx list->';
    newline();
    var_dump($output);
    newline();
    newline();
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
	echo "\tGenerateAccount       Generates new bip39 account";
	newline();
	echo "\tVanitygen             Generates new bip39 account with desired prefix";
	newline();
	echo "\tCreate2ndPassphrase   Generates second signature to specified account";
	newline();
	echo "\tRegisterDelegate      Registers delegate with specified name";
	newline();
	echo "\tVote                  Vote for specified delegates with publicKey";
	newline();
    echo "\tSignMessage           Signs message with specified account";
    newline();
    echo "\tVerifyMessage         Verify signed message and displays it";
    newline();
    echo "\tEncryptMessage        Encrypts message with recipient publickey";
    newline();
    echo "\tDecryptMessage        Decrypts message with encrypted hex body, nonce and sender publickey";
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
    echo "\tGetBlockchainInfo     Get blockchain constants and info";
    newline();
    echo "\tGetDelegatesList      Get delegates list";
    newline();
    echo "\tGetVotes              Get account votes";
    newline();
    echo "\tToggleForging         Toggle On/Off forging on specified account";
    newline();
    echo "\tEncryptPassphrase     Encrypts specified passphrase for forging toggle";
    newline();
    echo "\tDecryptPassphrase     Decrypts specified passphrase for forging toggle";
    newline();
    echo "\tGetDelegateList       Get list of delegates";
    newline();
    echo "\tGetPendingTx          Get list of pending transactions";
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