
<?php 

function applySHA256Encription($req){
    $app_key="9e0ff359-582c-4677-b07d-dbe3a4dc24ea";
    return sign($req, $app_key);
}

function sign($ussd, $app_key) {   
    $data = $ussd;               
    ksort($data);
    $stringApplet = '';         
    foreach ($data as $key => $values) {        
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
   return encrypt_RSA($sortedString); 
}

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

    print_r($stringExplode);
       
    return $stringExplode;
}

function encrypt_RSA($data){
    $private_key = file_get_contents('./config/private_key.pem');
    $public_key = file_get_contents('./config/public_key.pem');

    $binary_signature = '';

    $algo = "sha256WithRSAEncryption";

    openssl_sign($data, $signature, $private_key, $algo);

    //openssl_sign($data, $binary_signature, $private_key, OPENSSL_ALGO_SHA256);
    
    $signature_ = base64_encode($signature);

    // $r = openssl_verify($data, $binary_signature, $public_key, "sha256WithRSAEncryption");
    // echo "VERIFY";
    var_dump($signature_);


    return $signature_;
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


