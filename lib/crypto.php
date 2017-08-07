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


function getKeysFromSecret($secret,$string=false) {
	$hash = hex2bin(hash('sha256', $secret));
	$kp = \Sodium\crypto_sign_seed_keypair($hash);
	$secret = \Sodium\crypto_sign_secretkey($kp);
	$public = \Sodium\crypto_sign_publickey($kp);
	if ($string) {
		$keys = array('public' => bin2hex($public),'secret' => bin2hex($secret));
	} else {
		$keys = array('public' => $public,'secret' => $secret);
	}
	return $keys;
}


function signTx($transaction, $keys) {
	$hash = hex2bin(getSignedTxBody($transaction));
	$signature = \Sodium\crypto_sign_detached($hash, $keys['secret']);
	return $signature;
}


function getSignedTxBody($transaction){
	$bytes = getTxAssetBytes($transaction);
	$assetSize = $bytes['assetSize'];
	$assetBytes = $bytes['assetBytes'];
	$body = assignTransactionBuffer($transaction, $assetSize, $assetBytes,'');
	return hash('sha256', $body);
}


function getTxId($transaction) {
	$bytes = getTxAssetBytes($transaction);
	$assetSize = $bytes['assetSize'];
	$assetBytes = $bytes['assetBytes'];
	$body = assignTransactionBuffer($transaction, $assetSize, $assetBytes,'');
	$hash = hash('sha256', $body);
	$byte_array = str_split($hash,2);
	$tmp = array();
	for ($i = 0; $i < 8; $i++) {
		$tmp[$i] = $byte_array[7 - $i];
	}
	$tmp = implode("",$tmp);
	$tmp = bchexdec($tmp);
	return $tmp;
}


function assignHexToTransactionBytes($transactionBuffer, $hexValue) {
	$hexBuffer = str_split($hexValue,2);
	foreach ($hexBuffer as $key => $value) {
		$byte = bchexdec($value);
		$transactionBuffer->writeBytes([$byte]);
	}
	return $transactionBuffer;
}


function assignTransactionBuffer($transaction, $assetSize, $assetBytes, $options) {
		$transactionBuffer = BBStream::factory('');
		$transactionBuffer->isLittleEndian = false;
		$transactionBuffer->writeInt($transaction['type'], 8);//1
		$transactionBuffer->writeInt($transaction['timestamp']); //4
		$transactionBuffer = assignHexToTransactionBytes($transactionBuffer, $transaction['senderPublicKey']);
		if (array_key_exists('requesterPublicKey', $transaction)) {//32
			assignHexToTransactionBytes($transactionBuffer, $transaction['requesterPublicKey']);
		}
		if (array_key_exists('recipientId', $transaction)){ //8
			$recipient = $transaction['recipientId'];
			$recipient = substr($recipient, 0, -1);
			$recipient_bi = new Math_BigInteger($recipient);
			$bytes = unpack('C*',$recipient_bi->toBytes());
			for ($i = 1; $i <= 8; $i++) {
				$transactionBuffer->writeBytes([$bytes[$i]]);
			}
		} else {
			for ($i = 0; $i < 8; $i++) {
				$transactionBuffer->writeBytes([0]);
			}
		}
		$bytes = BBUtils::intToBytes($transaction['amount'],64);
		$bytes = array_reverse($bytes);
		$transactionBuffer->writeBytes($bytes);
		if (array_key_exists('data', $transaction)) {//64
			$transactionBuffer = assignHexToTransactionBytes($transactionBuffer, $transaction['data']);//64
		}
		if ($assetSize > 0) {
			for ($i = 0; $i < $assetSize; $i++) {
				$transactionBuffer->writeBytes([$assetBytes[$i]]);
			}
		}
		if($options != 'multisignature') {
			if (array_key_exists('signature', $transaction)) {
				$transactionBuffer = assignHexToTransactionBytes($transactionBuffer, $transaction['signature']);//64
			}
			if (array_key_exists('signSignature', $transaction)) {
				$transactionBuffer = assignHexToTransactionBytes($transactionBuffer, $transaction['signSignature']);//64
			}
		}
		$transactionBuffer->rewind();
		$size = $transactionBuffer->size();
		$bytes = $transactionBuffer->readBytes($size);
		if (DEBUG) {
			$string = "";
			foreach ($bytes as $chr) {
				$string .= $chr;
			}
			echo "\n";
			var_dump($string);
		}
		$string = call_user_func_array("pack", array_merge(array("C*"), $bytes));
		return $string;
}


function getTxAssetBytes($transaction){
	if ($transaction['type'] == 0) {
		$tmp = array('assetBytes' => null,
					 'assetSize' => 0);
	}
	return $tmp;
}


function bchexdec($hex){
    $dec = 0;
    $len = strlen($hex);
    for ($i = 1; $i <= $len; $i++) {
        $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
    }
    return $dec;
}


function bcdechex($dec) {
    $hex = '';
    do {    
        $last = bcmod($dec, 16);
        $hex = dechex($last).$hex;
        $dec = bcdiv(bcsub($dec, $last), 16);
    } while($dec>0);
    return $hex;
}


?>