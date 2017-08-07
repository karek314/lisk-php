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


function CreateTransaction($recipientId, $amount, $passphrase1, $passphrase2, $data, $timeOffset){
	$time_difference = GetCurrentLiskTimestamp()+$timeOffset;
	$transaction = array('type' => 0,
						 'amount' => (int)$amount,
						 'fee' => NETWORK_FEE,
						 'recipientId' => (string)$recipientId,
						 'timestamp' => (int)$time_difference,
						 'asset' => new stdClass()
						);
	if ($data) {
		$transaction['data'] = $data;
	}
	$keys = getKeysFromSecret($passphrase1);
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


?>