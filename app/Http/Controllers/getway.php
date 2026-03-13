<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use App\Models\Payment;
class getway extends Controller
{
    
    public function createPayment(Request $request)
    {
        $baseURL = 'https://hazrat.paymently.io/';
        $apiKEY = '55GpsCHEKwjtezrJi9tO9mhSnD8GIKB45WmMc2t9';

        
        $fields = [
            'full_name'    => $request->input('full_name'),
            'email'        => $request->input('email'),
            'amount'       => $request->input('amount'),
            'metadata'     => [
                'user_id'  => $request->input('user_id'),
                'order_id' => $request->input('order_id'),
            ],
            'redirect_url' => url('/success'),
            'return_type'  => 'GET',
            'cancel_url'   => url('/cancel'),
            'webhook_url'  => url('/webhook'),
        ];
        try {
            $response = Http::withHeaders([
                'RT-UDDOKTAPAY-API-KEY' => $apiKEY,
                'Accept'                => 'application/json',
                'Content-Type'          => 'application/json',
            ])->post($baseURL . 'api/checkout-v2', $fields);

            if ($response->successful()) {

                if ($response->successful()) {
                    $data = $response->json();

                    // Assuming the response looks like: ['payment_url' => 'https://...']
                    if (isset($data['payment_url'])) {
                        return redirect()->to($data['payment_url']);
                    } else {
                        return response()->json(['error' => 'payment_url not found in response'], 400);
                    }
                }
            } else {
                return response()->json(['error' => $response->body()], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Request failed: ' . $e->getMessage()], 500);
        }
    }


    public function paymentSuccess(Request $request)
{
    $invoiceId = $request->query('invoice_id');
    $status = $request->query('status');



   return response()->json([
            'invoice_id' => $invoiceId,
        'status' => $status,
        'all_data' => $request->all(), // includes everything from query string

        ]);

}
    public function paymentCancel(Request $request)
{



   
   return response()->json([
        'status' =>'paymentCancel',

        ]);
}

public function handleWebhook(Request $request)
{
    

   $data = $request->all();

  
    $status = $data['status'] ?? 'pending';
    $invoiceId = $data['invoice_id'] ?? null;
    $metadata = $data['metadata'] ?? [];

    if ($status === 'completed' && $invoiceId && isset($metadata['user_id'])) {

        // Prevent duplicate insert
        $existing = Payment::where('invoice_id', $invoiceId)->first();
        if ($existing) {
            return response()->json(['message' => 'Already saved'], 200);
        }

        Payment::create([
            'full_name'  => $data['full_name'] ?? 'N/A',
            'email'      => $data['email'] ?? 'N/A',
            'amount'     => $data['amount'] ?? 0,
            'user_id'    => $metadata['user_id'],
            'order_id'   => $metadata['order_id'] ?? null,
            'invoice_id' => $invoiceId,
            'status'     => 'completed',
        ]);

        // Unlock user package or access here if needed
    }

    return response()->json(['message' => 'Webhook processed'], 200);


}
}
