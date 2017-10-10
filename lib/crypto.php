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


function EncryptMessage($message, $passphrase, $recipientPublicKey) {
	$senderPrivateKey = getKeysFromSecret($passphrase)['secret'];
	$convertedPrivateKey = sodium_crypto_sign_ed25519_sk_to_curve25519($senderPrivateKey);
	$convertedPublicKey = sodium_crypto_sign_ed25519_pk_to_curve25519(hex2bin($recipientPublicKey));
	$nonce = random_bytes(SODIUM_CRYPTO_BOX_NONCEBYTES);
	$skpk = sodium_crypto_box_keypair_from_secretkey_and_publickey($convertedPrivateKey,$convertedPublicKey);
	$encryptedMessage = sodium_crypto_box(hex2bin(strToHex($message)), $nonce, $skpk);
	return array(
		"nonce" => bin2hex($nonce),
		"message" => bin2hex($encryptedMessage),
	);
}


function DecryptMessage($message, $nonce, $passphrase, $senderPublicKey) {
	$recipientPrivateKey = getKeysFromSecret($passphrase)['secret'];
	$convertedPrivateKey = sodium_crypto_sign_ed25519_sk_to_curve25519($recipientPrivateKey);
	$convertedPublicKey = sodium_crypto_sign_ed25519_pk_to_curve25519(hex2bin($senderPublicKey));
	$skpk = sodium_crypto_box_keypair_from_secretkey_and_publickey($convertedPrivateKey,$convertedPublicKey);
	$output = sodium_crypto_box_open(hex2bin($message),hex2bin($nonce),$skpk);
	return $output;
}


function encryptPassphrase($passphrase, $password) {
  $key = hash('sha256', $password, true);
  $iv = bin2hex(random_bytes(16));
  $padding = 16 - (strlen($passphrase) % 16);
  $passphrase .= str_repeat(chr($padding), $padding);
  return array('iv' => $iv, 'encrypted' => bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $passphrase, MCRYPT_MODE_CBC, hex2bin($iv))));
}


function decryptPassphrase($encrypted, $iv, $password) {
  $key = hash('sha256', $password, true);
  $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, hex2bin($encrypted), MCRYPT_MODE_CBC, hex2bin($iv));
  $padding = ord($data[strlen($data) - 1]);
  return substr($data, 0, -$padding);
}


function VerifyMessage($signedMessage, $publicKey1, $publicKey2=false) {
	if (strlen($publicKey1) != 64) {
		return "Invalid publicKey lenght.";
	}
	if ($publicKey2 && strlen($publicKey2) != 64) {
		return "Invalid publicKey2 lenght.";
	}
	if ($publicKey2) {
		$tmp = $publicKey1;
		$publicKey1 = $publicKey2;
		$publicKey2 = $tmp;
	}
	$openSignature = sodium_crypto_sign_open(hex2bin($signedMessage), hex2bin($publicKey1));
	if ($openSignature) {
		if ($publicKey2) {
			$openSignature = sodium_crypto_sign_open($openSignature, hex2bin($publicKey2));
			if ($openSignature) {
				return hextostr(bin2hex($openSignature));
			} else {
				return  "Invalid signature publicKey2 combination, cannot verify message";
			}
		} else {
			return hextostr(bin2hex($openSignature));
		}
	} else {
		return  "Invalid signature publicKey combination, cannot verify message";
	}
}


function signMessageWithSecret($message, $passphrase1, $passphrase2=false) {
	$signedMessage = sodium_crypto_sign(hex2bin(strToHex($message)), getKeysFromSecret($passphrase1)['secret']);
	if ($passphrase2) {
		$signedMessage = sodium_crypto_sign($signedMessage, getKeysFromSecret($passphrase2)['secret']);
	}
	return bin2hex($signedMessage);
}


function signMessage($message, $passphrase1, $passphrase2=false) {
	$signedMessageHeader = '-----BEGIN LISK SIGNED MESSAGE-----';
	$messageHeader = '-----MESSAGE-----';
	$plainMessage = $message;
	$pubklicKeyHeader = '-----PUBLIC KEY-----';
	$publicKey = getKeysFromSecret($passphrase1,true)['public'];
	if ($passphrase2) {
		$publicKey .= PHP_EOL.getKeysFromSecret($passphrase2,true)['public'];
	}
	$signatureHeader = '-----SIGNATURE-----';
	$signedMessage = signMessageWithSecret($message, $passphrase1, $passphrase2);
	$signatureFooter = '-----END LISK SIGNED MESSAGE-----';
 	$outputArray = array(
		$signedMessageHeader,
		$messageHeader,
		$plainMessage,
		$pubklicKeyHeader,
		$publicKey,
		$signatureHeader,
		$signedMessage,
		$signatureFooter
	);
	return implode(PHP_EOL, $outputArray);
}


function getKeysFromSecret($secret,$string=false) {
	$hash = hex2bin(hash('sha256', $secret));
	$kp = sodium_crypto_sign_seed_keypair($hash);
	$secret = sodium_crypto_sign_secretkey($kp);
	$public = sodium_crypto_sign_publickey($kp);
	if ($string) {
		$keys = array('public' => bin2hex($public),'secret' => bin2hex($secret));
	} else {
		$keys = array('public' => $public,'secret' => $secret);
	}
	return $keys;
}


function signTx($transaction, $keys) {
	$hash = hex2bin(getSignedTxBody($transaction));
	$signature = sodium_crypto_sign_detached($hash, $keys['secret']);
	return $signature;
}


function getSignedTxBody($transaction){
	$bytes = getTxAssetBytes($transaction);
	$assetSize = $bytes['assetSize'];
	$assetBytes = $bytes['assetBytes'];
	$body = assignTransactionBuffer($transaction, $assetSize, $assetBytes,'');
	return hash('sha256', $body);
}


function getAddressFromPublicKey($pubKey){
	$buffer = BBStream::factory('');
	$buffer->isLittleEndian = false;
	assignHexToBuffer($buffer, $pubKey);
	$buffer->rewind();
	$size = $buffer->size();
	$bytes = $buffer->readBytes($size);
	$pubKey = call_user_func_array("pack", array_merge(array("C*"), $bytes));
	$pubKeyHash = hash('sha256', $pubKey);
	$byte_array = str_split($pubKeyHash,2);
	$tmp = array();
	for ($i = 0; $i < 8; $i++) {
		$tmp[$i] = $byte_array[7 - $i];
	}
	$tmp = implode("",$tmp);
	$tmp = bchexdec($tmp);
	return $tmp.'L';
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


function assignHexToBuffer($transactionBuffer, $hexValue) {
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
		$transactionBuffer = assignHexToBuffer($transactionBuffer, $transaction['senderPublicKey']);
		if (array_key_exists('requesterPublicKey', $transaction)) {//32
			assignHexToBuffer($transactionBuffer, $transaction['requesterPublicKey']);
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
			$transactionBuffer = assignHexToBuffer($transactionBuffer, $transaction['data']);//64
		}
		if ($assetSize > 0) {
			for ($i = 0; $i < $assetSize; $i++) {
				$transactionBuffer->writeBytes([$assetBytes[$i]]);
			}
		}
		if($options != 'multisignature') {
			if (array_key_exists('signature', $transaction)) {
				$transactionBuffer = assignHexToBuffer($transactionBuffer, $transaction['signature']);//64
			}
			if (array_key_exists('signSignature', $transaction)) {
				$transactionBuffer = assignHexToBuffer($transactionBuffer, $transaction['signSignature']);//64
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
	if ($transaction['type'] == SEND_TRANSACTION_FLAG) {
		$tmp = array('assetBytes' => null,
					 'assetSize' => 0);
	} else if ($transaction['type'] == SECOND_SIG_TRANSACTION_FLAG) {
		$hash = $transaction['asset']['signature']['publicKey'];
		$hexBuffer = str_split($hash,2);
		$byteBuffer = array();
		foreach ($hexBuffer as $key => $value) {
			$byte = bchexdec($value);
			$byteBuffer[] = $byte;
		}
		$tmp = array('assetBytes' => $byteBuffer,
					 'assetSize' => 32);
	} else if ($transaction['type'] == DELEGATE_TRANSACTION_FLAG) {
		$username = strtohex($transaction['asset']['delegate']['username']);
		$hexBuffer = str_split($username,2);
		$byteBuffer = array();
		foreach ($hexBuffer as $key => $value) {
			$byte = bchexdec($value);
			$byteBuffer[] = $byte;
		}
		$tmp = array('assetBytes' => $byteBuffer,
					 'assetSize' => count($byteBuffer));
	} else if ($transaction['type'] == VOTE_TRANSACTION_FLAG) {
		$votes = $transaction['asset']['votes'];
		$votes = implode('',$votes);
		//$votes = str_replace('+', '2B', $votes);
		//$votes = str_replace('-', '2D', $votes);
		$votes = strTohex($votes);
		$hexBuffer = str_split($votes,2);
		$byteBuffer = array();
		foreach ($hexBuffer as $key => $value) {
			$byte = bchexdec($value);
			$byteBuffer[] = $byte;
		}
		$tmp = array('assetBytes' => $byteBuffer,
					 'assetSize' => count($byteBuffer));
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


function strtohex($string){
    $hex = '';
    for ($i=0; $i<strlen($string); $i++){
        $ord = ord($string[$i]);
        $hexCode = dechex($ord);
        $hex .= substr('0'.$hexCode, -2);
    }
    return strToUpper($hex);
}


function hextostr($hex){
    $string='';
    for ($i=0; $i < strlen($hex)-1; $i+=2){
        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
    return $string;
}


?>