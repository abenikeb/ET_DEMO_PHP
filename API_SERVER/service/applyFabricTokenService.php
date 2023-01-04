 <?php 
  class ApplyFabricToken {

  public $BASE_URL;
  public $fabricAppId;
  public $appSecret;
  public $merchantAppId;

  function __construct($BASE_URL, $fabricAppId, $appSecret,$merchantAppId) {
    $this->BASE_URL = $BASE_URL;
    $this->fabricAppId = $fabricAppId;
    $this->appSecret = $appSecret;
    $this->merchantAppId = $merchantAppId;
   }

   Public function applyFabricToken(){
     $ch = curl_init();

     $headers = array(
          "Content-Type: application/json",
          "X-APP-Key: ".$this->fabricAppId
          );

     curl_setopt($ch, CURLOPT_URL, $this->BASE_URL."/payment/v1/token");  
     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
     curl_setopt($ch, CURLOPT_HEADER, 0);

     $payload =  array( 
                "appSecret"=>$this->appSecret          
                 );
 
     //print_r(json_encode($payload));exit;
     $data = json_encode($payload); 
 
    curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
  
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // for dev envirnoment only

 
     // Timeout in seconds
     curl_setopt($ch, CURLOPT_TIMEOUT, 30);
 
     $authToken = curl_exec($ch);
     
     return $authToken;
   }

 }
    
