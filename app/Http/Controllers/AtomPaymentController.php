<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AtomPaymentController extends Controller
{
    //

    public function createPayment()
    {
      //  dd('1111111111111');
        $merchantId = "71480";
        $password = "SREE@123";
        $productId = "UNIVERSITY";
        $transactionId = uniqid(); // Unique Transaction ID
        $amount = "10.00"; // Transaction Amount
        $responseUrl = route('atompayment.response'); // Response URL
        $requestUrl = "https://payment.atomtech.in/payments/"; // Atom Payment Gateway URL
        $clentcode= 'sp@ssus.ac.in';
        $merchanttxnid = time().random_int(1000, 9999);

        // Payment Request Parameters
        $params = [
            'merchantid' => $merchantId,
            'txnscamt' => "0", // Convenience fee
            'txnamount' => $amount,
            'currency' => "INR",
            'clientcode' => base64_encode($clentcode),
            'custacc' => "1234567890",
            'txnscid' => $productId,
            'responseurl' => $responseUrl,
            'udf1' => "",
            'udf2' => "",
            'udf3' => "",
            'mode' => "LIVE" // "TEST" for sandbox
        ];

        // Generate Form and Auto-Submit to Gateway
        $formHtml = "<form id='atom-payment-form' method='POST' action='{$requestUrl}'>";
        foreach ($params as $key => $value) {
            $formHtml .= "<input type='hidden' name='{$key}' value='{$value}'>";
        }
        $formHtml .= "<button type='submit'>Pay Now</button></form>";
        $formHtml .= "<script>document.getElementById('atom-payment-form').submit();</script>";
        return response($formHtml);

    }

    // Step 2: Handle Atom Payment Response
    public function handleResponse(Request $request)
    {
        // Retrieve Response Data
        $status = $request->input('f_code'); // Response status (e.g., 'Ok' for success)
        $transactionId = $request->input('tpsl_txn_id'); // Transaction ID
        $amount = $request->input('txn_amt'); // Amount
        $responseMessage = $request->input('desc'); // Description

        if ($status === "Ok") {
            // Payment Successful
            return response()->json([
                'status' => 'success',
                'message' => 'Payment successful!',
                'transaction_id' => $transactionId,
                'amount' => $amount,
            ]);
        } else {
            // Payment Failed
            return response()->json([
                'status' => 'failed',
                'message' => 'Payment failed!',
                'error' => $responseMessage,
            ]);
        }
    }

    public function atomPayForm(Request $request)
    {
               
         return view('atom.payform');
        
    }


}
