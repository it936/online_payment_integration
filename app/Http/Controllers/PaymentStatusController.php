<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Helpers\CryptoHelper;
use Illuminate\Support\Facades\DB;
class PaymentStatusController extends Controller
{
    private $workingKey;
    private $accessCode;

     private $access_code = 'AVDR35LK53CC78RDCC'; // Replace with your access code
     private $working_key = 'CBC353AEF85432D18CDA03FCF48B3D5B'; // Replace with your working key
     private $merchant_id = 3147015;

    public function __construct()
    {
        $this->workingKey = 'CBC353AEF85432D18CDA03FCF48B3D5B';
        $this->accessCode = 'AVDR35LK53CC78RDCC';
       
    }
    public function check(Request $request)


{
//dd($request);

$this->workingKey = '615717775ADFA47A79451673555A2642';
$this->accessCode = 'AVDY79MF57BN32YDNB';  
$orderId = $request->get('order_id') ?? '512721732881283';
$requestPayload = [   
'order_no' =>  $orderId,   
];
$update_data='';
$requestData = json_encode($requestPayload);
$encRequest =CryptoHelper::encrypt($requestData, $this->workingKey);
$url = 'https://api.ccavenue.com/apis/servlet/DoWebTrans';
$params = http_build_query([
    'enc_request'   => $encRequest,
    'access_code'   => $this->accessCode,
    'request_type'  => 'JSON',
    'response_type' => 'JSON',
    'command'       => 'orderStatusTracker',
    'version'       => '1.2',
]);
$fullUrl = $url . '?' . $params;
$response = Http::asForm()->post($fullUrl); 
$body = $response->body();
parse_str($body, $parsed);
// Now you can access enc_response
if (isset($parsed['enc_response'])) {
    $encResponse = $parsed['enc_response'];
    $drs = CryptoHelper::decrypt($encResponse, $this->workingKey);
    $data = json_decode($drs, true);  
    $order_status = $data['order_status'] ?? null;
    $order_id=$data['order_no'];
    $update_data = [
    'order_status'   => $data['order_status'],
    'bank_ref_no'    => $data['order_bank_ref_no'],
    'status_message' => $data['order_bank_response'],
    'updated_at'     => now(),
                   ];
                   
if (!empty($order_id) && isset($data['order_status'])) 
{
    DB::table('tbz_ccavenue_txns')
        ->where('order_id', $order_id)
        ->update($update_data);
}
$update_data['order_no'] = $data['order_no'];
return ($update_data);


}
else
{
    return 'Error !!!!';
} 

}

public function check_firstcopy(Request $request)
{
    $this->workingKey = '615717775ADFA47A79451673555A2642';
    $this->accessCode = 'AVDY79MF57BN32YDNB';   

    $orderId = $request->get('order_id') ?? '512721732881283';
    $orderId='17507392535183'; $refno='113819890836';

$requestPayload = [
    'reference_no' => $refno,
    'order_no' => $orderId,   
];

$requestData = json_encode($requestPayload);

//dd($requestData);

//$encRequest = $this->encrypt($requestData, $this->workingKey);
$encRequest =CryptoHelper::encrypt($requestData, $this->workingKey);

$url = 'https://api.ccavenue.com/apis/servlet/DoWebTrans';


$postData = "enc_request=$encRequest&access_code={$this->accessCode}&request_type=JSON&response_type=JSON&command=orderStatusTracker&version=1.2";

$response = Http::withHeaders([
    'Content-Type' => 'application/x-www-form-urlencoded'
])->post('https://api.ccavenue.com/apis/servlet/DoWebTrans', $postData);

dd($response);



//$url = 'https://apitest.ccavenue.com/apis/servlet/DoWebTrans';
$params = http_build_query([
    'enc_request'   => $encRequest,
    'access_code'   => $this->accessCode,
    'request_type'  => 'JSON',
    'response_type' => 'JSON',
    'command'       => 'orderStatusTracker',
    'version'       => '1.2',
]);

$fullUrl = $url . '?' . $params;
// dd($fullUrl);
$response = Http::post($fullUrl);
//dd($response);

    // Send HTTP POST request to CCAvenue
// $response = Http::asForm()->post('https://api.ccavenue.com/apis/servlet/DoWebTrans', [
//         'enc_request'   => $encRequest,
//         'access_code'   => $this->accessCode,        
//         'request_type'  => 'JSON',
//         'response_type' => 'JSON',
//         'command' => 'orderStatusTracker',
//         'version'=>'1.2',
       
// ]);

$statusCode = $response->status(); // 405
$reasonPhrase = $response->toPsrResponse()->getReasonPhrase(); // "Method Not Allowed"


return response()->json([
    'status' => 'error',
    'message' => $reasonPhrase,
    'code' => $statusCode
]);
   
}



   private function encrypt($plainText, $key)
{
    $blockSize = 16;
    $pad = $blockSize - (strlen($plainText) % $blockSize);
    $plainText .= str_repeat(chr($pad), $pad);

    $iv = substr($key, 0, 16); // Fix: use only 16-byte IV
    $encrypted = openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($encrypted);
}

private function decrypt($encryptedText, $key)
{
    $iv = substr($key, 0, 16); // Fix: use only 16-byte IV
    $encryptedText = base64_decode($encryptedText);
    return openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
}


public function check18(Request $request)
{
    //  private $access_code = 'AVDR35LK53CC78RDCC'; // Replace with your access code
    //  private $working_key = 'CBC353AEF85432D18CDA03FCF48B3D5B'; // Replace with your working key
    //  private $merchant_id = 3147015;

    // Load credentials from environment or config
    $workingKey = $this->working_key;   // or config('ccavenue.working_key')
    $accessCode = $this->access_code;

    // Step 1: Prepare JSON request payload
    $requestData = [
        "order_no" => "113789907789",  // You can pass this dynamically if needed
    ];

    // Step 2: Encrypt the request
    $jsonRequest = json_encode($requestData);
    $encRequest = CryptoHelper::encrypt($jsonRequest, $workingKey); // You must define encrypt() using AES
    dd($encRequest);
    // Step 3: Prepare POST fields
    $postData = [
        'enc_request'   => $encRequest,
        'access_code'   => $accessCode,
        'request_type'  => 'JSON',              // REQUIRED
        'response_type' => 'JSON',              // Optional, but recommended
        'command'       => 'orderLookup' // REQUIRED
    ];

    // Step 4: Send request using cURL
    $ch = curl_init('https://api.ccavenue.com/apis/servlet/DoWebTrans');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);  // Add this line
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        dd("Curl Error: " . curl_error($ch));
    }
    curl_close($ch);

    // Step 5: Decrypt the response
    $responseData = json_decode($response, true);

    if (isset($responseData['enc_response'])) {
        $decryptedResponse = CryptoHelper::decrypt($responseData['enc_response'], $workingKey);
        dd(json_decode($decryptedResponse, true)); // Final decoded array
    }

    // If response is not encrypted or malformed
    dd($response);
}



public function check155555(Request $request)
{

 // https://api.ccavenue.com/apis/servlet/DoWebTrans?enc_request=&access_code=AVDR35LK53CC78RDCC&command=orderStatusTracker&request_type=JSON&response_type=JSON&version=1.2


 


 // Prepare the data to insert
    $data = [
       
        'order_no' => '17491331022411',
        'merchant_id' => '3147015',
        'access_code'=> 'AVDR35LK53CC78RDCC',
        'request_type'=>'JSON',
        'response_type' => 'JSON',
        'command'=>'orderStatusTracker',
        'from_date'=>'05-06-2025',
        'page_number'=>'1',    
                
    ];

  $merchant_data = '';

foreach ($data as $key => $value) {
    $merchant_data .= $key . '=' . $value . '&';
}


// Optionally remove the last `&`
$merchant_data = rtrim($merchant_data, '&');

    //   dd($merchant_data);

       
            
       
        
      //   dd($merchant_data);
        
     
          
        $encrypted_data = CryptoHelper::encrypt($merchant_data, $this->working_key);    
        //    dd($encrypted_data);
        // CCAvenue production URL
        
            $production_url = 'https://secure.ccavenue.com/transaction/transaction.do?command=orderLookup&encRequest='
          . $encrypted_data . '&access_code=' . $this->access_code;
            $access_code=$this->access_code;
            $purl='https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';

$production_url = 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction'
    . '&encRequest=' . urlencode($encrypted_data)
    . '&access_code=' . urlencode($this->access_code);
$response = Http::asForm()->post($production_url);




$production_url = 'https://api.ccavenue.com/apis/servlet/DoWebTrans?command=orderStatusTracker&request_type=JSON&response_type=JSON'
    . '&encRequest=' . urlencode($encrypted_data)
    . '&access_code=' . urlencode($this->access_code);
    dd($production_url);
$response = Http::asForm()->post($production_url);
     dd($production_url); 
$decrypted = CryptoHelper::decrypt($response->body(), $this->workingKey);

dd(parse_str($decrypted, $parsed));


    }





}
