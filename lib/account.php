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


function Vanitygen($lenght=12,$prefix,$t=1,$special=true){
  $start = new DateTime();
  //Function designed only for CLI use
  $i = 0;
  $prefix_lenght = strlen($prefix);
  echo "\nVanitgen started...";
  echo "\nLooking for account with prefix:".$prefix."\n";
  if ($special) {
    echo "Special Mode, printing special accounts until desired account is found.\n";
  }
  $thread_file = "php ".realpath(dirname(__FILE__))."/vanitygen/vanity_thread.php";
  $threads = ((int)shell_exec("cat /proc/cpuinfo | grep processor | wc -l")*8*$t)-1;
  while(1){
    $start_time = microtime(true);
    for ($j=0; $j<$threads; $j++) {
      $pipe[$j] = popen($thread_file." ".$lenght, 'r');
    }
    for ($j=0; $j<$threads; ++$j) {
      $i++;
      $response = json_decode(stream_get_contents($pipe[$j]),true);
      pclose($pipe[$j]);
      $address = $response['address'];
      if (strlen($address) < 17) {
        PrintAccount($response,"Short address, less than 17 characters (".strlen($address).")");
      }
      if (IsSpecial($address)) {
        PrintAccount($response,"Multiple similar occurrences");
      }
      $curr_prefix = substr($address,0,$prefix_lenght);
      if ($curr_prefix == $prefix) {
        return $response;
      }
    }
    $diff = microtime(true)-$start_time;
    $acc_per_sec = $j/$diff;
    $current = new DateTime();
    $interval = $start->diff($current);
    $elapsed_time = $interval->format('%d')."d ".$interval->format('%h')."h ".$interval->format('%i')."min ".$interval->format('%s')."s";
    echo "\033[K\r[Done:".$i."] [Accounts per sec: ".number_format((float)$acc_per_sec, 3, '.', '')."] [Elapsed time: ".$elapsed_time."]";
  }
}


function PrintAccount($account,$reason){
  echo "\n################ ".$reason." ################";
  echo "\nPassphrase:".$account['passphrase'];
  echo "\nAddress:".$account['address']."\n\n";
}


function IsSpecial($address){
  $speciality = 0;
  foreach (count_chars($str, 1) as $chr => $value) {
    if ($value >= 4) {
      $speciality += $value;
    }
  }
  if ($speciality >= 8) {
    return true;
  } else {
    return false;
  }
}


function GenerateAccount($lenght=12,$custom_entropy=false){
  $size = 128;
  /*
  CS = ENT / 32
  MS = (ENT + CS) / 11
  |  ENT  | CS | ENT+CS |  MS  |
  +-------+----+--------+------+
  |  128  |  4 |   132  |  12  |
  |  160  |  5 |   165  |  15  |
  |  192  |  6 |   198  |  18  |
  |  224  |  7 |   231  |  21  |
  |  256  |  8 |   264  |  24  |
  */
  if ($lenght == 12) {
    $size = 128;
  } else if ($lenght == 15) {
    $size = 160;
  } else if ($lenght == 18) {
    $size = 192;
  } else if ($lenght == 21) {
    $size = 224;
  } else if ($lenght == 24) {
    $size = 256;
  } else {
    return false;
  }
  if ($custom_entropy) {
    $entropy = substr($custom_entropy,0,$size/4);
  } else {
    $entropy = bin2hex(mcrypt_create_iv($size/8, \MCRYPT_DEV_URANDOM));
  }
  return GetMnemonicSeedFromEntropy($entropy);
}


function GetMnemonicSeedFromEntropy($hex_entropy){
  $ent = (strlen($hex_entropy)/2) * 8;
  $cs = $ent/32;
  $hash = hash("sha256", hex2bin($hex_entropy));
  $bhash = gmp_strval(gmp_init($hash, 16), 2);
  $bhash = str_pad($bhash, 256, "0", STR_PAD_LEFT);
  $hash = substr($bhash, 0, $cs);
  $bits = str_pad(gmp_strval(gmp_init($hex_entropy, 16), 2) . $hash, $ent + $cs, "0", STR_PAD_LEFT);
  $words = B39WordsArray();
  $output = array();
  foreach (str_split($bits, 11) as $bit) {
    $i = gmp_strval(gmp_init($bit, 2), 10);
    $output[] = $words[$i];
  }
  $output = implode(" ", $output);
  return $output;
}


?>
