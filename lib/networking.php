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


function GetDelegateInfo($pk,$server){
	$url = $server.DELEGATE_ENDPOINT.'?publicKey='.$pk;
	return MainFunction("GET",$url,false,false,true,4);
}


function GetPendingTx($server,$offset=0){
  $url = $server.PENDING_TX_ENDPOINT."?limit=100&offset=".$offset;
  return MainFunction("GET",$url,false,false,true,5);
}


function GetDelegateList($limit,$offset,$server){
  $url = $server.DELEGATE_ENDPOINT."?limit=".$limit."&offset=".$offset;
  return MainFunction("GET",$url,false,false,true,4);
}


function ToggleForging($password,$pk,$server){
  $url = $server.FORGING_ENDPOINT;
  return MainFunction("PUT",$url,'{"decryptionKey": "'.$password.'","publicKey": "'.$pk.'"}',true,true,5);
}


function ForgingStatus($pk,$server){
  $url = $server.FORGING_ENDPOINT."?publicKey=".$pk;
  return MainFunction("GET",$url,false,true,true,5);
}


function GetVotersFor($pk,$server,$offset=0){
	$url = $server.VOTERS_ENDPOINT.$pk."&limit=100&offset=".$offset;
	return MainFunction("GET",$url,false,false,true,5);
}


function GetDelegatesList($server, $limit=101, $orderBy="rate", $offset=0, $orderType="asc"){
    $url = $server.DELEGATE_ENDPOINT.'?limit='.$limit.'&offset='.$offset.'&orderBy='.$orderBy.':'.$orderType;
    return MainFunction("GET",$url,false,false,true,7);
}


function GetVotes($address,$server){
    $url = $server.VOTES_ENDPOINT.$address;
    return MainFunction("GET",$url,false,false,true,5);
}


function GetBlocksBy($pk,$server,$offset=0,$orderBy='height',$orderType='desc'){
	$url = $server.BLOCKS_ENDPOINT.'?generatorPublicKey='.$pk.'&limit=100&offset='.$offset.'&orderBy='.$orderBy.':'.$orderType;
	return MainFunction("GET",$url,false,false,true,7);
}


function GetBlock($id,$server){
    $url = $server.BLOCKS_ENDPOINT.'?blockId='.$id;
    return MainFunction("GET",$url,false,false,true,5);
}


function GetBlockchainInfo($server){
    $url = $server.BCONST;
    return MainFunction("GET",$url,false,false,true,5);
}


function AccountForAddress($address,$server){
	$url = $server.ACCOUNTS.'?address='.$address;
	return MainFunction("GET",$url,false,false,true,3);
}


function NodeStatus($server){
  $url = $server.NODE_STATUS;
	return MainFunction("GET",$url,false,false,true,3);
}


function SendTransaction($transaction_string,$server){
	$url = $server.SEND_TRANSACTION_ENDPOINT;
	return MainFunction("POST",$url,$transaction_string,true,true,6);
}


function MainFunction($method,$url,$body=false,$jsonBody=true,$jsonResponse=true,$timeout=3){
  $ch = curl_init($url);                                                                      
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);                                                                                      
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,$timeout);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
  $headers =  array();
  if ($body) {  
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);                                                             
	  if ($jsonBody) {
		  $headers = array('Content-Type: application/json','Content-Length: ' . strlen($body)); 
    }
  }
  $port = parse_url($url)['port'];
  if (!$port) {
  	if (parse_url($url)['scheme']=='https') {
		  $port="443";
  	} else {
  		$port="80";
  	}
  }
  array_push($headers, "minVersion: ".MINVERSION);
  array_push($headers, "os: ".OS);
  array_push($headers, "version: ".API_VERSION);
  array_push($headers, "port: ".$port);
  array_push($headers, "Accept-Language: en-GB");
  array_push($headers, "nethash: ".NETWORK_HASH);
  array_push($headers, "broadhash: ".NETWORK_HASH);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  $result = curl_exec($ch);
  if ($jsonResponse) {
  	$result = json_decode($result, true); 
  }
  return $result;
}


?>