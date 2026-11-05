<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\CryptoHelper;

class CheckCcavenueStatus extends Command
{
    protected $signature = 'ccavenue:check-status';
    protected $description = 'Check CCAvenue order status';

   public function handle()
{
    $workingKey = config('services.ccavenue.working_key');
    $accessCode = config('services.ccavenue.access_code');

    $this->info('--- CCAvenue cron started ---');

    // Get max 1 pending order

    // $orders = DB::table('tbz_ccavenue_txns')
    //     ->where(function ($q) {
    //         $q->whereNull('order_status')
    //           ->orWhere('order_status', 'Invalid');
    //     })
    //     ->where('retry_count', '<', 5)
    //     ->orderBy('updated_at', 'asc')
    //     ->limit(1)
    //     ->get();


   // $statuses = ['Pending', 'Invalid', 'Failure', 'Aborted'];

  
// $statuses = ['Invalid','Initiated','Pending','Awaited'];
// //  ->whereNotNull('tdate')
// $orders = DB::table('tbz_ccavenue_txns')
//     ->where(function ($q) use ($statuses) {
//         $q->whereNull('order_status')
//           ->orWhereIn('order_status', $statuses);
//     })
//     ->where('retry_count', '<', 5)   
//     ->where('trans_amt', '>', 0)
//     ->orderBy('trans_id', 'desc')
//     ->limit(100)
//     ->get();

// Dynamic Query chacking

// $orders = DB::table('tbz_ccavenue_txns')
//     ->where(function ($q) use ($statuses) {
//         $q->whereNull('order_status')
//           ->orWhereIn('order_status', $statuses);
//     })
//     ->where('retry_count', '<', 5)
//     ->where('trans_amt', '>', 0)
//     ->whereRaw("
//         updated_at IS NULL
//         OR NOW() >= updated_at + (INTERVAL '1 hour' * POWER(2, retry_count))
//     ")
//     ->orderBy('trans_id', 'desc')
//     ->limit(100)
//     ->get();

$statuses = ['Invalid','Initiated','Pending','Awaited'];

$orders = DB::table('tbz_ccavenue_txns')
    ->where(function ($q) use ($statuses) {
        $q->whereNull('order_status')
          ->orWhereIn('order_status', $statuses);
    })
    ->where('retry_count', '<', 5)
    ->where('trans_amt', '>', 0)
    ->whereRaw("
         NOW() >= updated_at + (INTERVAL '1 hour' * POWER(2, retry_count))
    ")
    ->where('created_at', '>=', '2026-01-01')
    ->orderBy('trans_id', 'desc')
    ->limit(100)
    ->get();
  


    $this->info('Orders found: ' . $orders->count());

    foreach ($orders as $order) {

        $this->line('--------------------------------');
        $this->info('Processing Order: ' . $order->order_id);

        try {

            $o='512721732881283';
                            // 'order_no' => $order->order_id,
            // Step 1: Prepare request
            $this->line('Step 1: Preparing request');
            $requestPayload = [
                'order_no' =>  $order->order_id,
            ];

            $requestData = json_encode($requestPayload);
            $encRequest = CryptoHelper::encrypt($requestData, $workingKey);

            // Step 2: Send request
            $this->line('Step 2: Sending request to CCAvenue');

            $url = 'https://api.ccavenue.com/apis/servlet/DoWebTrans';
            $params = http_build_query([
                'enc_request'   => $encRequest,
                'access_code'   => $accessCode,
                'request_type'  => 'JSON',
                'response_type' => 'JSON',
                'command'       => 'orderStatusTracker',
                'version'       => '1.2',
            ]);

            $fullUrl = $url . '?' . $params;

            $response = Http::asForm()->post($fullUrl);
            $body = $response->body();

            $this->line('Step 3: Response received');

            parse_str($body, $parsed);

            if (isset($parsed['enc_response'])) {

                $this->line('Step 4: Decrypting response');

                $encResponse = $parsed['enc_response'];
                $drs = CryptoHelper::decrypt($encResponse, $workingKey);
                $data = json_decode($drs, true);

                $this->info('Order Status from CCAvenue: ' . ($data['order_status'] ?? 'NULL'));

                if (!empty($data['order_no']) && isset($data['order_status'])) {

                    $this->line('Step 5: Updating database');
                    
                    

                    // $updated = DB::table('tbz_ccavenue_txns')
                    //     ->where('order_id', $data['order_no'])
                    //     ->update([
                    //         'order_status'   => $data['order_status'],
                    //         'bank_ref_no'    => $data['order_bank_ref_no'] ?? null,
                    //         'status_message' => $data['order_bank_response'] ?? null,
                    //         'retry_count'    => 0,
                    //         'updated_at'     => Carbon::now(),
                    //     ]);

                    $updated = DB::table('tbz_ccavenue_txns')
                    ->where('order_id', $data['order_no'])
                    ->update([
                    'order_status'   => $data['order_status'],
                    'bank_ref_no'    => $data['order_bank_ref_no'] ?? null,
                    'status_message' => $data['order_bank_response'] ?? null,
                    'retry_count'    => DB::raw('retry_count + 1'),
                    'updated_at'     => Carbon::now(),
                    ]);

                    $this->info('Database updated rows: ' . $updated);

                    Log::info('Order updated', [
                        'order_id' => $data['order_no'],
                        'status'   => $data['order_status'],
                    ]);
                } else {
                    $this->error('Invalid response data');
                }

            } else {

                $this->error('No enc_response received');
                DB::table('tbz_ccavenue_txns')
                    ->where('order_id', $order->order_id)
                    ->increment('retry_count');

                Log::warning('No enc_response', [
                    'order_id' => $order->order_id
                ]);
            }

        } catch (\Exception $e) {

            $this->error('Error: ' . $e->getMessage());

            DB::table('tbz_ccavenue_txns')
                ->where('order_id', $order->order_id)
                ->increment('retry_count');
            Log::error('CCAvenue error', [
                'order_id' => $order->order_id,
                'error'    => $e->getMessage()
            ]);
        }
    }

    DB::statement("
    UPDATE public.tb_studexam s
    SET
        exstd_merchanttxnid = t.order_id::bigint,
        bank_ref_no         = t.bank_ref_no,
        payment_status      = 'Success'
    FROM (
        SELECT DISTINCT ON (client_code)
               client_code,
               order_id,
               bank_ref_no,
               order_status 
        FROM tbz_ccavenue_txns where order_status='Shipped'
        ORDER BY client_code, trans_id DESC
    ) t
    WHERE (s.exstd_registerno || '-' || s.exstd_exam_sl) = t.client_code
      AND t.order_status = 'Shipped'
      AND COALESCE(s.payment_status, '') <> 'Success'
    ");
     DB::statement("
        UPDATE cert.student_requests sr
SET
    payment_status = 'success',
    paid_at        = t.created_at,
    order_no       = t.order_id,
    bank_ref_no    = t.bank_ref_no,
    paid_amount    = t.amount,
    file_status    = 'new'
FROM (
    SELECT DISTINCT ON (merchant_param1_app_id)
           merchant_param1_app_id,
           created_at,
           order_id,
           bank_ref_no,
           amount,
           order_status
    FROM tbz_ccavenue_txns where order_status='Shipped'
    ORDER BY merchant_param1_app_id, trans_id DESC
) t
WHERE sr.stud_req_id = t.merchant_param1_app_id
  AND t.order_status IN ('Success', 'Shipped')
  AND COALESCE(sr.payment_status, '') NOT IN
      ('success','shipped','Shipped','Success');
      
    ");
    $this->info('--- CCAvenue cron finished ---');

    
}

}
