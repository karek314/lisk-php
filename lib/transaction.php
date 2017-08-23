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


function CreateTransaction($recipientId, $amount, $passphrase1, $passphrase2, $data, $timeOffset, $type=SEND_TRANSACTION_FLAG, $asset=false){
	$keys = getKeysFromSecret($passphrase1);
	if (!$asset) {
		$asset = new stdClass();
	}
	if ($type == SEND_TRANSACTION_FLAG) {
		$fee = SEND_FEE;
	} else if ($type == SECOND_SIG_TRANSACTION_FLAG) {
		$fee = SIG_FEE;
	} else if ($type == DELEGATE_TRANSACTION_FLAG) {
		$fee = DELEGATE_FEE;
	} else if ($type == VOTE_TRANSACTION_FLAG) {
		$fee = VOTE_FEE;
		$recipientId = getAddressFromPublicKey(bin2hex($keys['public']));
	} else if ($type == MULTISIG_TRANSACTION_FLAG) {
		$fee = MULTISIG_FEE;
	} else if ($type == DAPP_TRANSACTION_FLAG) {
		$fee = DAPP_FEE;
	} else if ($type == DAPP_IN_TRANSACTION_FLAG){
		$fee = SEND_FEE;
	} else if ($type == DAPP_OUT_TRANSACTION_FLAG){
		$fee = SEND_FEE;
	}
	$time_difference = GetCurrentLiskTimestamp()+$timeOffset;
	$transaction = array('type' => $type,
						 'amount' => (int)$amount,
						 'fee' => $fee,
						 'recipientId' => $recipientId,
						 'timestamp' => (int)$time_difference,
						 'asset' => $asset
						);
	if ($data) {
		$transaction['data'] = $data;
	}
	$transaction['senderPublicKey'] = bin2hex($keys['public']);
	$signature = signTx($transaction,$keys);
	$transaction['signature'] = bin2hex($signature);
	if ($passphrase2) {
		$secondKeys = getKeysFromSecret($passphrase2);
		$signSignature = signTx($transaction,$secondKeys);
		$transaction['signSignature'] = bin2hex($signSignature);
	}
	$transaction['id'] = getTxId($transaction);
	return array('transaction' => $transaction);
}


function Create2ndPassphrase($passphrase_base,$second_passphrase){
	$publicKey = getKeysFromSecret($second_passphrase,true)['public'];
	$asset['signature']['publicKey'] = $publicKey;
	return CreateTransaction(null, 0, $passphrase_base, null, false, -10, SECOND_SIG_TRANSACTION_FLAG, $asset);
}


function RegisterDelegate($name,$passphrase1,$passphrase2=false){
	$asset['delegate']['username'] = $name;
	$asset['delegate']['publicKey'] = getKeysFromSecret($passphrase1,true)['public'];
	return CreateTransaction(null, 0, $passphrase1, $passphrase2, false, -10, DELEGATE_TRANSACTION_FLAG, $asset);
}


function Vote($votes,$passphrase1,$passphrase2=false){
	$asset['votes'] = $votes;
	return CreateTransaction(null, 0, $passphrase1, $passphrase2, false, -10, VOTE_TRANSACTION_FLAG, $asset);
}


?>