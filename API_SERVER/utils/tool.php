
<?php 

// use Spatie\Crypto\Rsa\KeyPair;
// use Spatie\Crypto\src\Rsa\PrivateKey;
// use phpseclib\phpseclib\Crypt\RSA;
//require __DIR__ . '/vendor/autoload.php';
// require_once 'phpseclib/Crypt/RSA.php';
// include("../vendor/phpseclib/phpseclib/phpseclib/Crypt/RSA.php");

require_once('vendor/autoload.php');
 
use phpseclib3\Crypt\RSA;



// use Spatie\Crypto\Rsa\KeyPair;
// use Spatie\Crypto;
// use Spatie\Crypto\Rsa\PublicKey;

function applySHA256Encription($req){
    $app_key="9e0ff359-582c-4677-b07d-dbe3a4dc24ea";
    return sign($req, $app_key);
}

/**
* @Purpose: Extract the attributes to be signed into a map
*
* @Param: array $arr_req - the request message in array format
* @Return: A array object includes attributes to be signed
*/
function sign($ussd, $app_key) {  

    $result = array();
    $exclude_fields = array("sign", "sign_type", "header", "refund_info", "openType", "raw_request");

    $data = $ussd;               
    ksort($data);
    $stringApplet = '';         
    foreach ($data as $key => $values) {   
        if (in_array($key, $exclude_fields)) {
            continue;
        }

        if($key !== "biz_content"){
            if ($stringApplet == '') {       
                $stringApplet = $key . '=' . $values; 
            } else {                              
                $stringApplet = $stringApplet . '&' . $key . '=' . $values;            
            } 
        }

        else if($key == "biz_content"){
            foreach($values as $value => $single_value){
                if ($stringApplet == '') {     
                    $stringApplet = $value . '=' . $single_value;           
                } else {                
                    $stringApplet = $stringApplet . '&' . $value . '=' . $single_value;            
                }  
            }
        } 
    }
   
   $sortedString = sortedString($stringApplet);
   return SignWithRSA($sortedString); 
}

 /**
   * @Purpose: Generate signature original string
   *
   * @Param: array $arr_req - the sign message in array format
   * @Return: the sign source string in 'key1=value1&key2=value2' format
   */

function sortedString($stringApplet){
    $stringExplode = '';
    $sortedArray = explode("&",$stringApplet);
    sort($sortedArray);
    foreach($sortedArray as $x => $x_value) {
        if ($stringExplode == '') {     
            $stringExplode = $x_value;           
        } else {                
            $stringExplode = $stringExplode . '&' . $x_value;            
        }  
    }

    echo 'START_';
    print_r($stringExplode);
    echo 'END_';

    return $stringExplode;

   //return "appid=930231098009602&business_type=BuyGoods&merch_code=101011&merch_order_id=1472575010&method=payment.preorder&nonce_str=A2Y0OMG8E9H8TM5F45WR1V8VY78G6O5U&notify_url=https://www.google.com&payee_identifier=220311&payee_identifier_type=04&payee_type=5000&timeout_express=120m&timestamp=1672575010&title=diamond_20&total_amount=20&trade_type=InApp&trans_currency=ETB&version=1.0";
}

function SignWithRSA($data){

    $rsa = new RSA();

    echo "START SORTED__";
    var_dump($data);
    echo "__END SORTED";

    $private_key = openssl_pkey_get_private(file_get_contents('./config/private_key.pem'));

    $public_key = file_get_contents('./config/public_key.pem');

    $binary_signature = "";

    openssl_sign($data, $binary_signature, $private_key, OPENSSL_ALGO_SHA256);
   // openssl_sign($data, $binary_signature, $private_key, RSAwithSHA256/PSS);

    return base64_encode($binary_signature);

    // Spatie\Crypto\Rsa\PrivateKey::fromFile($pathToPrivateKey);
    // Spatie\Crypto\src\Rsa\PrivateKey::fromFile($pathToPrivateKey);
    
    //  $private_key = file_get_contents('./config/private_key.pem');
    // Spatie\Crypto\Rsa\PrivateKey::fromFile($pathToPrivateKey);
    // $privateKey = Spatie\Crypto\Rsa\PrivateKey::fromFile('./config/private_key.pem');
    // $privateKey = PrivateKey::fromFile('./config/private_key.pem');
    // $encryptedData = $privateKey->encrypt($data); // returns something unreadable



    // $binary_signature = '';

    // $algo = "sha256WithRSAEncryption";

    //openssl_sign($data, $signature, $private_key, $algo);

    // openssl_sign($data, $binary_signature, $private_key, OPENSSL_ALGO_SHA256);
    //openssl_private_encrypt($data, $binary_signature, $private_key, OPENSSL_PKCS1_PADDING);

    // openssl_sign($data, $signature, $private_key, "RSA-SHA256");
    
    // openssl_free_key($private_key);
    // $signature = $privateKey->sign($data); // returns a string


    // $r = openssl_verify($data, $binary_signature, $public_key, "sha256WithRSAEncryption");
    // echo "VERIFY";

    // return $signature_;
}

function createMerchantOrderId() {
    return (string)time();
}

function createTimeStamp() {
    return (string)time();
    // //   return (string)round(time());
    // return (string)strtotime(date('Y-m-d H:i:s'));
}
// create a 32 length random string
function createNonceStr() {
  $chars = [
    "0",
    "1",
    "2",
    "3",
    "4",
    "5",
    "6",
    "7",
    "8",
    "9",
    "A",
    "B",
    "C",
    "D",
    "E",
    "F",
    "G",
    "H",
    "I",
    "J",
    "K",
    "L",
    "M",
    "N",
    "O",
    "P",
    "Q",
    "R",
    "S",
    "T",
    "U",
    "V",
    "W",
    "X",
    "Y",
    "Z",
  ];
  $str = "";
  for ($i = 0; $i < 32; $i++) {
    // $index = parseInt(Math.random() * 35);
    $index = intval(rand() * 35);
    // print_r($index);
    $str .= $chars[$i];
  }
    //   return "5K8264pLTKCH16CQ2502nI8zNMTM6790";
    return uniqid();
}


