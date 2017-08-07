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
const LISK_PHP_VER = 1.0;
const NETWORK_FEE = 10000000;
const LISK_START = 1464109200;
const USER_AGENT = "LISK-PHP ".LISK_PHP_VER." via CURL (Linux, en-GB)";
const SECURE = true;
const MAINNET = true;
const MINVERSION = ">=0.5.0";
const OS = "lisk-php-api";
const API_VERSION = "1.0.0";

const SEND_TRANSACTION_ENDPOINT = "/peer/transactions";
const NODE_STATUS = "/api/loader/status/sync";
const ACCOUNT_BY_ADDR = "/api/accounts?address=";
const BLOCKS_ENDPOINT = "/api/blocks/?generatorPublicKey=";
const VOTERS_ENDPOINT = "/api/delegates/voters?publicKey=";
const DELEGATE_INFO_ENDPOINT = "/api/delegates/get/?publicKey=";
?>