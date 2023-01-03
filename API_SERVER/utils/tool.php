
<?php 

require_once('vendor/autoload.php');
 
use phpseclib\Crypt\RSA;


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

    return $stringExplode;
}

function SignWithRSA($data){
   $rsa = new Crypt_RSA();

   $private_key_load = file_get_contents('./config/private_key.pem');

   $private_key = trimPrivateKey($private_key_load)[2];

    if($rsa->loadKey($private_key) != TRUE){
        echo "Error loading PrivateKey";
        return;
    };

    $rsa->setHash("sha256");

    $rsa->setMGFHash("sha256");

    // $rsa->signatureMode(Crypt_RSA::$signatureMode);
    $signtureByte = $rsa->sign($data);

    return base64_encode($signtureByte);
}

function trimPrivateKey($stingData){
 
  return explode("-----", (string)$stingData);

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


