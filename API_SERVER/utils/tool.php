
<?php 

// use Spatie\Crypto\Rsa\KeyPair;
// use Spatie\Crypto\src\Rsa\PrivateKey;
// use phpseclib\phpseclib\Crypt\RSA;
//require __DIR__ . '/vendor/autoload.php';
// require_once 'phpseclib/Crypt/RSA.php';
// include("../vendor/phpseclib/phpseclib/phpseclib/Crypt/RSA.php");

require_once('vendor/autoload.php');
 
use phpseclib\Crypt\RSA;



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
    echo "\n TEST RES: \n";

    $privateKey_pem = "MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC/ZcoOng1sJZ4CegopQVCw3HYqqVRLEudgT+dDpS8fRVy7zBgqZunju2VRCQuHeWs7yWgc9QGd4/8kRSLY+jlvKNeZ60yWcqEY+eKyQMmcjOz2Sn41fcVNgF+HV3DGiV4b23B6BCMjnpEFIb9d99/TsjsFSc7gCPgfl2yWDxE/Y1B2tVE6op2qd63YsMVFQGdre/CQYvFJENpQaBLMq4hHyBDgluUXlF0uA1X7UM0ZjbFC6ZIB/Hn1+pl5Ua8dKYrkVaecolmJT/s7c/+/1JeN+ja8luBoONsoODt2mTeVJHLF9Y3oh5rI+IY8HukIZJ1U6O7/JcjH3aRJTZagXUS9AgMBAAECggEBALBIBx8JcWFfEDZFwuAWeUQ7+VX3mVx/770kOuNx24HYt718D/HV0avfKETHqOfA7AQnz42EF1Yd7Rux1ZO0e3unSVRJhMO4linT1XjJ9ScMISAColWQHk3wY4va/FLPqG7N4L1w3BBtdjIc0A2zRGLNcFDBlxl/CVDHfcqD3CXdLukm/friX6TvnrbTyfAFicYgu0+UtDvfxTL3pRL3u3WTkDvnFK5YXhoazLctNOFrNiiIpCW6dJ7WRYRXuXhz7C0rENHyBtJ0zura1WD5oDbRZ8ON4v1KV4QofWiTFXJpbDgZdEeJJmFmt5HIi+Ny3P5n31WwZpRMHGeHrV23//0CgYEA+2/gYjYWOW3JgMDLX7r8fGPTo1ljkOUHuH98H/a/lE3wnnKKx+2ngRNZX4RfvNG4LLeWTz9plxR2RAqqOTbX8fj/NA/sS4mru9zvzMY1925FcX3WsWKBgKlLryl0vPScq4ejMLSCmypGz4VgLMYZqT4NYIkU2Lo1G1MiDoLy0CcCgYEAwt77exynUhM7AlyjhAA2wSINXLKsdFFF1u976x9kVhOfmbAutfMJPEQWb2WXaOJQMvMpgg2rU5aVsyEcuHsRH/2zatrxrGqLqgxaiqPz4ELINIh1iYK/hdRpr1vATHoebOv1wt8/9qxITNKtQTgQbqYci3KV1lPsOrBAB5S57nsCgYAvw+cagS/jpQmcngOEoh8I+mXgKEET64517DIGWHe4kr3dO+FFbc5eZPCbhqgxVJ3qUM4LK/7BJq/46RXBXLvVSfohR80Z5INtYuFjQ1xJLveeQcuhUxdK+95W3kdBBi8lHtVPkVsmYvekwK+ukcuaLSGZbzE4otcn47kajKHYDQKBgDbQyIbJ+ZsRw8CXVHu2H7DWJlIUBIS3s+CQ/xeVfgDkhjmSIKGX2to0AOeW+S9MseiTE/L8a1wY+MUppE2UeK26DLUbH24zjlPoI7PqCJjl0DFOzVlACSXZKV1lfsNEeriC61/EstZtgezyOkAlSCIH4fGr6tAeTU349Bnt0RtvAoGBAObgxjeH6JGpdLz1BbMj8xUHuYQkbxNeIPhH29CySn0vfhwg9VxAtIoOhvZeCfnsCRTj9OZjepCeUqDiDSoFznglrKhfeKUndHjvg+9kiae92iI6qJudPCHMNwP8wMSphkxUqnXFR3lr9A765GA980818UWZdrhrjLKtIIZdh+X1";

   // $private_key = openssl_pkey_get_private(file_get_contents('./config/private_key.pem'));

    $rsa = new Crypt_RSA();

    if($rsa->loadKey($privateKey_pem) != TRUE){
        echo "Erro loading privateKey";
        return;
    };

    $rsa->setHash("sha256");
    $rsa->setMGFHash("sha256");

    // $rsa->signatureMode(Crypt_RSA::$signatureMode);
    $signtureByte = $rsa->sign($data);

    echo "\n NEW__SIGN__DATA \n";

    // print_r(base64_encode($signtureByte));


    // echo "START SORTED__";
    // var_dump($data);
    // echo "__END SORTED";


//     $public_key = file_get_contents('./config/public_key.pem');

//     $binary_signature = "";

//     openssl_sign($data, $binary_signature, $private_key, OPENSSL_ALGO_SHA256);
//    // openssl_sign($data, $binary_signature, $private_key, RSAwithSHA256/PSS);

//     return base64_encode($binary_signature);
      return base64_encode($signtureByte);

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


