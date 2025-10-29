<?php

namespace App\Http\Controllers;

use App\Services\CCAvenuePayment;
use Illuminate\Http\Request;
use App\Helpers\CryptoHelper;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
     private $access_code = 'AVDR35LK53CC78RDCC'; // Replace with your access code
     private $working_key = 'CBC353AEF85432D18CDA03FCF48B3D5B'; // Replace with your working key
     private $merchant_id = 3147015;
    //
     public function initiatePayment_test(Request $request)
    {
        dd('qqqqq');
        $data = [
            'tid' => time(),
            'order_id' => $request->order_id,
            'amount' => $request->amount,
            'currency' => 'INR',
            'redirect_url' => config('ccavenue.redirect_url'),
            'cancel_url' => config('ccavenue.cancel_url'),
            'billing_name' => $request->name,
            'billing_email' => $request->email,
            'billing_tel' => $request->phone,
        ];

        $encryptedData = CCAvenuePayment::encrypt(http_build_query($data), config('ccavenue.working_key'));

        return response()->json([
            'encryptedRequest' => $encryptedData,
            'accessCode' => config('ccavenue.access_code'),
        ]);
    }
    
    
    public function getPayForm(Request $request)
    {
               
         return view('ccavenue.payform');
        
    }
    public function getStatusForm(Request $request)
    {
               
         return view('ccavenue.status');
        
    }
    public function initiatePayment2(Request $request)
    {
        dd('xsxx');

    }
    // Check status for 
    public function checkStatus1(Request $request)
    {
       // dd('hello');
        $merchant_id = 3147015;
        $tid=$request['tid'];
        $order_id=$request['order_id'] ;
        $merchant_id=$request['merchant_id'] ;

      	$order_id='113789907789';
       
    }

//use Illuminate\Support\Facades\Http;

public function checkStatus()
{
       // Load credentials from .env
    $workingKey = env('CCAVENUE_WORKING_KEY');
    $accessCode = env('CCAVENUE_ACCESS_CODE');
    $isSandbox  = env('CCAVENUE_SANDBOX', true); // true or false in .env

    // Choose API URL based on environment
    $apiUrl = $isSandbox 
        ? 'https://test.ccavenue.com/apis/servlet/DoWebTrans'
        : 'https://api.ccavenue.com/apis/servlet/DoWebTrans';

    // Step 1: Prepare the JSON request payload
    $requestData = [
        "order_no" => "113789907789", // Replace with dynamic ID if needed
    ];

    // Step 2: Encrypt the payload using your CryptoHelper
    $jsonPayload = json_encode($requestData);
    $encryptedRequest = CryptoHelper::encrypt($jsonPayload, $workingKey);

    // Step 3: Formulate the POST body
    $postData = [
        'enc_request'   => $encryptedRequest,
        'access_code'   => trim($accessCode),
        'request_type'  => 'JSON',
        'response_type' => 'JSON',
        'command'       => 'orderStatusTracker',
    ];

    // Step 4: Make the HTTP request
    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS     => http_build_query($postData),
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded']
    ]);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    // Step 5: Basic validation
    if ($curlError) {
        return response()->json(['error' => "cURL Error: $curlError"], 500);
    }

    // Check if response is like `status=1&enc_response=...`
    parse_str($response, $parsedResponse);

    if (!isset($parsedResponse['enc_response'])) {
        return response()->json(['error' => 'Invalid API response', 'raw' => $response], 500);
    }

    // Step 6: Decrypt the response
    $decrypted = CryptoHelper::decrypt($parsedResponse['enc_response'], $workingKey);
    $decodedJson = json_decode($decrypted, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return response()->json([
            'error' => 'Decryption or JSON decode failed',
            'decrypted' => $decrypted,
            'raw_response' => $response
        ], 500);
    }

    return response()->json($decodedJson);
}







    public function checkStatus3333()
{
    // Example order details
    $data = [
      
        "order_no"     => "17489429528261"
    ];

    // Convert array to JSON
    $jsonRequest = json_encode($data);

    // Encrypt the request JSON using your working key
    $encrypted_data = CryptoHelper::encrypt($jsonRequest, $this->working_key);

    // Prepare the POST fields for CCAvenue
    $post_data = [
        'enc_request'   => $encrypted_data,
        'access_code'   => $this->access_code,
        'request_type'  => 'JSON',
        'response_type' => 'JSON'
       
    ];

    // Send request using cURL
    $ch = curl_init('https://secure.ccavenue.com/transaction/transaction.do?command=orderStatusTracker');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    $response = curl_exec($ch);
    curl_close($ch);

    // Decrypt response
    $decryptedResponse = CryptoHelper::decrypt($response, $this->working_key);
dd($decryptedResponse);
    // Return or process the response
    return response()->json([
        'raw'       => $response,
        'decrypted' => json_decode($decryptedResponse, true)
    ]);
}
public function checkStatus111(Request $request)
{ 
   
$order_id = '17489429528261'; // Use only one order ID

// Create request string
$request_data = "order_no=" . $order_id;

// Encrypt the request using CCAvenue encryption logic
$encrypted_data = CryptoHelper::encrypt($request_data, $this->working_key); // Replace 'encrypt' with the correct encryption function

// Prepare POST data
$post_data = [
    'enc_request'   => $encrypted_data,
    'access_code'   => $this->access_code,
    'command'       => 'orderStatusTracker',
    'request_type'  => 'JSON',
    'response_type' => 'JSON'
];

// Send request to CCAvenue endpoint
$ch = curl_init('https://secure.ccavenue.com/transaction/getOrderStatus');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
$response = curl_exec($ch);
curl_close($ch);

// Decrypt the response
$decrypted_response = CryptoHelper::decrypt($response, $this->working_key); // Replace 'decrypt' with actual CCAvenue decrypt logic

// Show the decrypted response
dd($decrypted_response);

// Optionally convert to array if needed (JSON case)
$response_array = json_decode($decrypted_response, true);




}

    public function initiatePayment(Request $request)
    {
        

        if (isset($request['sub_account_id'])) {
                $sub_account_id = $request['sub_account_id'];
              //  return $sub_account_id;

            }
            else
            {
                return ' Sub Account ID not found !!!!! ';
            }

     
     //   dd($request);
        
    $tid = time().random_int(1000, 9999);   
    $order_id =time(). random_int(1000, 9999);
    $merchant_id = 3147015;
    $cancel_url = 'https://payment.ssus.ac.in/payment/cancel';
    $redirect_url='https://payment.ssus.ac.in/payment/redirect';    
          unset($request['_token']);
          $request['tid'] =  $tid;
          $request['order_id'] = $order_id; 
          $request['merchant_id'] =  $merchant_id;
          $fee_splitup= $request['fee_splitup'];
          $amount=$request['amount'];          
          $req_cancel_url= $request['cancel_url'];
          $req_redirect_url=$request['redirect_url'];  
          $billing_name=$request['billing_name'];
          $billing_email=$request['billing_email'];
          $billing_tel=$request['billing_tel'];
          $billing_zip=$request['billing_zip'];   
          $billing_city=$request['billing_city'];
          $merchant_param1=$request['merchant_param1'];           
          $merchant_param2=$request['merchant_param2'];    // university service        
         
          
          if (!isset($merchant_param2)) {
                  
                $merchant_param2='TEST FEE';

            }
          $merchant_param3=$request['merchant_param3'];   
          if (!isset($merchant_param3)) {

                 $merchant_param3='TEST REG NO';                 
           }          
          $merchant_param3=$request['merchant_param3'];   
          $request['cancel_url']=$cancel_url;
          $request['redirect_url']=$redirect_url;
          $merchant_data = '';   
    

    // Prepare the data to insert
    $data = [
        'tid' => $tid,
        'order_id' => $order_id,
        'merchant_id' => $merchant_id,
        'merchantid'=> $merchant_id,
        'trans_amt' => $amount,
        'amount' => $amount,        
        'merchant_param1_app_id' => $merchant_param1,
        'ucity_service' => $merchant_param2,   
        'merchant_param2_ucity_service' => $merchant_param2,
        'merchant_param3_regno' => $merchant_param3,
        'client_code' => $merchant_param3,
        'billing_name'=> $billing_name,
        'billing_email'=> $billing_email,
        'billing_tel'=> $billing_tel,        
        'cancel_url' => $req_cancel_url,
        'redirect_url' => $req_redirect_url,        
        'created_at' => now(), // Optional: Include timestamps if the table has them
        'updated_at' => now(), // Optional
        'billing_zip' => $billing_zip, // mandatory
        'billing_city' => $billing_city, // mandatory 
        'fee_splitup' =>   $fee_splitup,
        'sub_account_id' => $sub_account_id,
                
    ];

  
        // Insert data into the database

        $fees = json_decode($fee_splitup, true);
       // dd($fees);
        DB::table('tbz_ccavenue_txns')->insert($data);           
        foreach ($fees as $feeType => $amount) {
            DB::table('fee.ccavenue_txn_details')->insert([
                'det_fee_desc' => $feeType,
                'det_fee_amount' => $amount,
                'det_order_id' => $order_id,
                
            ]);
        }
            
        foreach ($request->all() as $key => $value) {
            
            $merchant_data .= $key . '=' . $value . '&';
        }
        
      //   dd($merchant_data);
        
     
          
        $encrypted_data = CryptoHelper::encrypt($merchant_data, $this->working_key);    
        //    dd($encrypted_data);
        // CCAvenue production URL
        
            $production_url = 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction&encRequest='
          . $encrypted_data . '&access_code=' . $this->access_code;
            $access_code=$this->access_code;
            $purl='https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
            
          // dd($production_url);
   //     $production_url = 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction&encRequest='
        //                          .$encrypted_data.'&access_code='.$this->access_code;
        // Return the view with the production URL for the iframe
           
            
// Auto-submit the form
$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting...</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }
        .spinner-container {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="spinner-container">
        
        <div class="spinner-grow text-secondary" role="status" style="width: 1.0rem; height: 1.0rem;"> 
  <span class="visually-hidden">Loading...</span>
</div>
<div class="spinner-grow text-success" role="status" style="width: 1.0rem; height: 1.0rem;">
  <span class="visually-hidden">Loading...</span>
</div>
<div class="spinner-grow text-danger" role="status" style="width: 1.0rem; height: 1.0rem;">
  <span class="visually-hidden">Loading...</span>
</div>
<div class="spinner-grow text-warning" role="status" style="width: 1.0rem; height: 1.0rem;">
  <span class="visually-hidden">Loading...</span>
</div>
<div class="spinner-grow text-info" role="status" style="width: 1.0rem; height: 1.0rem;">
  <span class="visually-hidden">Loading...</span>
</div>

<div class="spinner-grow text-dark" role="status" style="width: 1.0rem; height: 1.0rem;">
  <span class="visually-hidden">Loading...</span>
</div>
        <p class="mt-3">Redirecting, please wait...</p>
    </div>

    <form id="ccavenueForm" method="post" action="{$production_url}" style="display: none;">
        <input type="hidden" name="encRequest" value="{$encrypted_data}">
        <input type="hidden" name="access_code" value="{$access_code}">
    </form>

    <script>
        // Automatically submit the form after a delay
        document.addEventListener('DOMContentLoaded', function () {
            const delay = 2000; // Delay in milliseconds (5000ms = 5 seconds)
            setTimeout(function () {
                document.getElementById('ccavenueForm').submit();
            }, delay);
        });
    </script>
    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
return response($html);
          
            
       // return view('ccavenue.payment', compact('production_url','encrypted_data','access_code','purl'));
    }


    public function res(Request $request)
    {  
        dd($request->encResp);
        $decryptedData=CryptoHelper::decrypt($request->encResp, $this->working_key);    
       // $decryptedData = decrypt::decrypt($request->encResp, $this->working_key);
        
        parse_str($decryptedData, $response);

        dd(response()->json($response));
        return response()->json($response);
    }
    
public function cancelResponse(Request $request)
{
    // Log all request data for debugging
      \Log::info('Cancel URL Response:', $request->all());
   
    // Check if encResp exists
    if ($request->has('encResp')) {
        $encResp = $request->input('encResp');
       
        $decryptedResponse = CryptoHelper::decrypt($request->encResp, $this->working_key);    
        //dd($decryptedResponse);
        parse_str($decryptedResponse, $responseArray);
        
        $order_id=$responseArray['order_id'];
        $data = [
        'tracking_id' =>  $responseArray['tracking_id'],
        'order_status' => $responseArray['order_status'],
        'tdate' =>  $responseArray['trans_date'],
        'updated_at' => now(), ];
         DB::table('tbz_ccavenue_txns')->where('order_id', $order_id)->update($data);
          $transactions = DB::table('tbz_ccavenue_txns')
                  ->where('order_id', $order_id) // Add WHERE condition
                  ->get();
                  $cancel_url='';
                  $redirect_url='';
                  foreach ($transactions as $transaction){

                     $redirect_url =$transaction->redirect_url;
                     $cancel_url =$transaction->cancel_url;
                                       
                  }      
                 
            $targetUrl = $redirect_url; // Replace with actual URL
            $respArray = [
                            'order_status' => $responseArray['order_status'],
                            'order_id' => $responseArray['order_id'],
                            'merchant_param1' => $responseArray['merchant_param1'],
                            'merchant_param2' => $responseArray['merchant_param2'],
                            'merchant_param3' => $responseArray['merchant_param3'],
                            'tracking_id' =>  $responseArray['tracking_id'],                               
                        ];           
                        
                        // dd( $respArray);
            // Append response details as query parameters
            $redirectUrl = $targetUrl . '?' . http_build_query($respArray);
            // dd($redirectUrl);
            // Redirect to the target project
            return redirect()->away($redirectUrl);
           
         
         // Process the decrypted response
//         return response()->json([
//                 'status' => 'cancelled',
//                 'message' => 'Transaction cancelled by user.',
//                 'response' => $decryptedResponse,
//        ]);
            
    } else {
      //  dd($request);
        return response()->json([
            'status' => 'error',
            'message' => 'No encResp received on cancel.',
        ]);
    }
}


    
public function redirectResponse(Request $request)
{
    // Log all request data
    \Log::info('Redirect URL Response:', $request->all());

    if ($request->has('encResp')) {
        try {
            // Decrypt the response
            $encResp = $request->input('encResp');
            $decryptedResponse = CryptoHelper::decrypt($encResp, $this->working_key);

            // Convert decrypted response into an array
            parse_str($decryptedResponse, $responseArray);
            // Log decrypted response
            \Log::info('Decrypted Response:', $responseArray);
             $order_id=$responseArray['order_id'];
                $data = [
                'tracking_id' =>  $responseArray['tracking_id'],
                'order_status' => $responseArray['order_status'],
                'tdate' =>  $responseArray['trans_date'],
                'bank_ref_no' =>  $responseArray['bank_ref_no'],
                'payment_mode' =>  $responseArray['payment_mode'],
                'card_name' =>  $responseArray['card_name'],
                'failure_message' =>  $responseArray['failure_message'],
                'status_message' =>  $responseArray['status_message'],
                'updated_at' => now(), ];      
                 DB::table('tbz_ccavenue_txns')->where('order_id', $order_id)->update($data);   
                 
                  $transactions = DB::table('tbz_ccavenue_txns')
                  ->where('order_id', $order_id) // Add WHERE condition
                  ->get();
                  $cancel_url='';
                  $redirect_url='';
                  foreach ($transactions as $transaction){
                      
                     $redirect_url =$transaction->redirect_url;
                     $cancel_url =$transaction->cancel_url;
                                       
                  }
                 
              //  dd($responseArray);         
                        
            $status = $responseArray['order_status'] ?? 'unknown';

            // Take action based on the status
//            switch ($status) {
//                case 'Success':
//                    // Handle success response (e.g., update DB, show success message)
//                    return redirect()->route('payment.success')->with('message', 'Payment successful!')->with('details', $responseArray);
//                case 'Aborted':
//                    // Handle aborted response
//                    return redirect()->route('payment.aborted')->with('message', 'Payment was aborted by the user.')->with('details', $responseArray);
//                case 'Failure':
//                    // Handle failure response
//                    return redirect()->route('payment.failed')->with('message', 'Payment failed. Please try again.')->with('details', $responseArray);
//                default:
//                    // Handle unknown status
//                    return redirect()->route('payment.unknown')->with('message', 'Transaction status unknown.')->with('details', $responseArray);
//            }
                                    
            // Prepare the redirect URL for the target project
            $targetUrl = $redirect_url; // Replace with actual URL
            $respArray = [
                            'order_status' => $responseArray['order_status'],
                            'order_id' => $responseArray['order_id'],
                            'merchant_param1' => $responseArray['merchant_param1'],
                            'merchant_param2' => $responseArray['merchant_param2'],
                            'merchant_param3' => $responseArray['merchant_param3'],
                            'tracking_id' =>  $responseArray['tracking_id'],                               
                        ];           
            
            // Append response details as query parameters
            $redirectUrl = $targetUrl . '?' . http_build_query($respArray);
            // Redirect to the target project
            return redirect()->away($redirectUrl);
        } catch (\Exception $e) {
            \Log::error('Error decrypting response: ' . $e->getMessage());
            return redirect()->route('payment.error')->with('message', 'An error occurred while processing the payment.');
        }
    } else {
        \Log::warning('No encResp received in redirect URL.');
        return redirect()->route('payment.error')->with('message', 'Invalid payment response received.');
    }
}


public function redirectResponse_Same(Request $request)
{
    // Log all request data for debugging purposes
    \Log::info('Redirect URL Response:', $request->all());

    // Check if encResp exists
    if ($request->has('encResp')) {
        try {
            // Extract the encrypted response
            $encResp = $request->input('encResp');

            // Decrypt the response using CCAvenue's encryption method
            $decryptedResponse = CryptoHelper::decrypt($encResp, $this->working_key); // Ensure you have set $this->working_key

            // Convert decrypted response into an array
            parse_str($decryptedResponse, $responseArray);

            // Log the decrypted response for debugging
            \Log::info('Decrypted Response:', $responseArray);

            // Process the transaction status
            $status = $responseArray['order_status'] ?? 'unknown';

            // Take action based on the status
            switch ($status) {
                case 'Success':
                    // Handle success response (e.g., update DB, show success message)
                    return redirect()->route('payment.success')->with('message', 'Payment successful!')->with('details', $responseArray);
                case 'Aborted':
                    // Handle aborted response
                    return redirect()->route('payment.aborted')->with('message', 'Payment was aborted by the user.')->with('details', $responseArray);
                case 'Failure':
                    // Handle failure response
                    return redirect()->route('payment.failed')->with('message', 'Payment failed. Please try again.')->with('details', $responseArray);
                default:
                    // Handle unknown status
                    return redirect()->route('payment.unknown')->with('message', 'Transaction status unknown.')->with('details', $responseArray);
            }
        } catch (\Exception $e) {
            // Log exception
            \Log::error('Error decrypting response: ' . $e->getMessage());

            // Return error response
            return redirect()->route('payment.error')->with('message', 'An error occurred while processing the payment.');
        }
    } else {
        // No encResp found in the request
        \Log::warning('No encResp received in redirect URL.');
        return redirect()->route('payment.error')->with('message', 'Invalid payment response received.');
    }
}
   
   public function paymentResponse(Request $request)
    {
          
        return response()->json($request);
    }  
    
    
    public function paymentResponse1(Request $request)
    {
        dd($request->encResp);
        $decryptedData=CryptoHelper::decrypt($request->encResp, $this->working_key);    
       // $decryptedData = decrypt::decrypt($request->encResp, $this->working_key);
        
        parse_str($decryptedData, $response);

        dd(response()->json($response));
        return response()->json($response);
    }
    // Payment Interface
    public function paymentInitiate()
    {
        dd('nnn');
        // Make the API request to the payment gateway
        $response = Http::post('https://payment.ssus.ac.in/api/payment/initiate', [
            'order_id' => '12345',
            'amount' => 1000,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            // Return the response data as JSON or handle the success accordingly
            $data = $response->json();
            return response()->json($data);
        } else {
            // Handle errors (e.g., log or display an error message)
            return response()->json(['error' => 'Payment API request failed'], 500);
        }
    }
    
}
