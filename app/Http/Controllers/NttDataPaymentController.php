<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymentService;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class NttDataPaymentController extends Controller
{
    //
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
       // $this->paymentService = $paymentService;
    }


    public function ninitiate1(Request $request)
    {
        dd('ssssssssssssssssssssssssssssssssssss252');
    }

    public function ninitiate(Request $request)
    {
        dd('ssssssssssssssssssssssssssssssssssss252');



        $validated = $request->validate([
            'amount' => 'required|numeric',
            'currency' => 'required|string',
            'order_id' => 'required|string',
            'callback_url' => 'required|url',
            'customer_details' => 'required|array',
        ]);

        
        $response = $this->paymentService->initiatePayment($validated);

        return response()->json($response);
    }

    public function ncallback(Request $request)
    {
        $transactionId = $request->get('transaction_id');

        $response = $this->paymentService->verifyPayment($transactionId);

        if ($response['status'] === 'success') {
            // Handle successful payment
        } else {
            // Handle failed payment
        }

        return response()->json($response);
    }





    public function processTransactions()
    {
        // Base URL
        $url = "https://payment.atomtech.in/paynetz/vfts?";

        // Fetch rows from the database
        $transactions = DB::table('tbz_atom_transactions')
            ->select('merchantid', 'trans_amt', DB::raw("TO_CHAR(trans_timestamp, 'YYYY-MM-DD') as tdate"), 'merchanttxnid')
            ->whereNull('res_verified')
            ->orWhereIn('res_verified', ['NODATA', 'INITIATED', 'Pending From Bank', 'INITIALIZED', 'FAILED'])
            ->whereIn('ucity_service', [
                'TEACHING POST APPLICATION FEE-2023',
                'TEACHING POST(DMM) APPLICATION FEE-2023'
            ])
            ->orderByDesc('trans_id')
            ->limit(500)
            ->get();

        foreach ($transactions as $transaction) {
            $params = [
                'merchantid' => $transaction->merchantid,
                'merchanttxnid' => $transaction->merchanttxnid,
                'amt' => $transaction->trans_amt,
                'tdate' => $transaction->tdate,
            ];

            // Generate the request URL
            $requestUrl = $url . http_build_query($params);

            try {
                // Make HTTP GET request
                $response = Http::get($requestUrl);

                if ($response->ok()) {
                    $xml = simplexml_load_string($response->body());

                    foreach ($xml->children() as $child) {
                        $attributes = $child->attributes();

                        // Extract data from XML attributes
                        $res_verified = (string) $attributes['VERIFIED'];
                        $res_bid = (string) $attributes['BID'];
                        $res_reconstatus = (string) $attributes['reconstatus'];
                        $res_bankname = (string) $attributes['bankname'];
                        $res_atomtxn_id = (string) $attributes['atomtxnId'];
                        $res_discriminator = (string) $attributes['discriminator'];
                        $res_card_number = (string) $attributes['CardNumber'];
                        $res_surcharge = (string) $attributes['surcharge'];
                        $res_sdt = (string) $attributes['sdt'];

                        // Update the database
                        DB::table('tbz_atom_transactions')
                            ->where('merchanttxnid', $transaction->merchanttxnid)
                            ->update([
                                'res_verified' => $res_verified,
                                'res_bid' => $res_bid,
                                'res_reconstatus' => $res_reconstatus,
                            ]);
                    }
                }
            } catch (\Exception $e) {
                // Log the error
                logger()->error("Error processing transaction: " . $transaction->merchanttxnid, [
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return "Transactions processed successfully!";
    }

}
