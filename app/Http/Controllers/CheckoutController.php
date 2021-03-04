<?php

/**
 * Project: Payment Point
 * File: CheckoutController.php
 * Date: 12/16/20
 * Time: 08:37 AM
 * @author: muhammadikhsan
 * @copyright: IGN &copy; 2020
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests\StoreCustomerInfo;
use App\Jobs\SendEmailJob;
use App\User;
use Midtrans\Config as PaymentConfig;
use Midtrans\CoreApi as SendPaymentResponse;
use Midtrans\Notification as PaymentNotification;
use App\Models\Payment;
use App\Models\Product;
use App\Models\AdditionalProduct;
use App\Models\PaymentAdditionalProduct;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $productId = $id;
        $product = ($productId) ? Product::where('code', $productId)->get()->toArray() : "";
        if (!count($product)) {
            abort(404);
        }

        return view('checkout.index', compact('productId', 'product'));
    }

    /**
     * Store customer info
     *
     * @param StoreCustomerInfo $request
     * @return view 
     */
    public function storeCustomerInfo(StoreCustomerInfo $request, $id)
    {
        $countryCode = explode("|", $request->prefixNumber);
        $completeNumber = preg_replace('/^0?/', '+' . $countryCode[0], $request->phone);

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $completeNumber,
            'country' => $countryCode[1],
            'user_type' => 2
        ]);

        $cookie = cookie('piu', $user->id, 30);

        return redirect('checkout/step2/' . $id)->withCookie($cookie);
    }

    /**
     * Get country id and prefix phone number
     *
     * @param Request $request
     * @return json 
     */
    public function getPrefixNumber(Request $request)
    {
        if ($request->has('q')) {
            $find = $request->q;
            $data = DB::table('countries')->select('id', 'nicename', 'phonecode')->where('nicename', 'LIKE', '%' . $find . '%')->get();
            return response()->json($data);
        } else {
            $data = DB::table('countries')->select('id', 'nicename', 'phonecode')->get();
            return response()->json($data);
        }
    }

    /**
     * Processing midtrans payment
     *
     * @param Request $request
     * @return json 
     */
    public function process(Request $request)
    {
        $payment = $request->all();
        $userId = $request->cookie('piu');
        $user = ($userId) ? User::find($userId) : "";
        $product = ($payment['productId']) ? Product::find($payment['productId']) : "";
        $orderId = "ign-" . time() . mt_rand() . "-" . $user->id . "-" . $product->id;
        $bank = [1 => 'Mandiri', 2 => 'CIMB', 3 => 'BCA'];

        if ($user && $product) {
            $amount=$product->price;
            if (isset($payment['addItem'])) {
                foreach ($payment['addItem'] as $key => $value) {
                    $item=AdditionalProduct::find($value);
                    $amount=$amount+$item->price;
                }
            }
            

            if ($payment['payment-type'] === "credit-card") {
                $paymentResponse = $this->getPaymentResponse($payment, $user, $product, $orderId,$amount);
                Log::channel('daily')->info("[RESPONSE]" . json_encode($paymentResponse));

                if ($paymentResponse) {
                    $payment = Payment::create([
                        'order_id' => $orderId,
                        'user_id' => $user->id,
                        'amount' => $amount,
                        'payment_type' => 'Credit Card',
                        'product_id' => $payment['productId'],
                        'transaction_id' => $paymentResponse->transaction_id,
                        'status_message' => $paymentResponse->status_message,
                        'status_code' => $paymentResponse->status_code,
                        'transaction_status' => $paymentResponse->transaction_status,
                        'transaction_time' => $paymentResponse->transaction_time,
                        'currency' => $paymentResponse->currency
                    ]);

                    if (isset($request['addItem'])) {
                        foreach ($request['addItem'] as $key => $value) {
                            $paymentAdditionalProduct = PaymentAdditionalProduct::create([
                                'payment_id' => $payment->id,
                                'additional_product_id' => $value,
                            ]);
                            
                        }
                    }

                } else {
                    $data['status_code'] = 300;
                    $data['status_message'] = "Error processing payment";

                    return response()->json($data);
                }

                return response()->json($paymentResponse);
            } else {
                
                $payment = Payment::create([
                    'order_id' => $orderId,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'transaction_status' => 'pending',
                    'payment_type' => 'Direct Transfer',
                    'product_id' => $payment['productId'],
                    'currency' => "IDR",
                    'bank' => $bank[$payment['bankToSelect']]
                ]);
                if (isset($request['addItem'])) {
                    foreach ($request['addItem'] as $key => $value) {
                        $paymentAdditionalProduct = PaymentAdditionalProduct::create([
                            'payment_id' => $payment->id,
                            'additional_product_id' => $value,
                        ]);
                        
                    }
                }
                $data['status_code'] = 200;
                $data['status_message'] = "Success, Please complete your payment";
                $data['bank'] = $payment['bankToSelect'];

                dispatch(new SendEmailJob($user, $product, $payment));

                return response()->json($data);
            }
        } else {
            $data['status_code'] = 300;
            $data['status_message'] = "User data or Product not found, Please register your data again!";

            return response()->json($data);
        }
    }

    /**
     * Get Payment response from midtrans
     *
     * @param array $data
     * @return json 
     */
    public function getPaymentResponse($data, $user, $product, $orderId, $amount)
    {
        PaymentConfig::$serverKey = env('MIDTRANS_SERVER_KEY');
        PaymentConfig::$isProduction = true;

        $params = array(
            'transaction_details' => array(
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ),
            'payment_type' => 'credit_card',
            'credit_card'  => array(
                'token_id'      => $data['mTokenId'],
                'authentication' => true,
            ),
            'customer_details' => array(
                'first_name' => $user->firstname,
                'last_name' => $user->lastname,
                'email' => $user->email,
                'phone' => $user->phone,
            ),
        );

        Log::channel('daily')->info("[REQUEST]" . json_encode($params));
        return SendPaymentResponse::charge($params);
    }

    /**
     * Display a thank you page after payment success.
     *
     * @param  $id
     * @return view
     */
    public function finish($id, $channel)
    {
        $paymentChannel = explode("-", $channel);
        if ($paymentChannel[0] == 1) {
            return view('checkout.thankyou');
        } else if ($channel == 2) {
            return view('checkout.thankyou-direct-transfer', compact('paymentChannel'));
        }
    }

    /**
     * Receive notification from midtrans
     *
     * @param Request $request
     * @return string 
     */
    public function notification(Request $request)
    {
        PaymentConfig::$isProduction = true;
        PaymentConfig::$serverKey = env('MIDTRANS_SERVER_KEY');
        $notif = new PaymentNotification();

        Log::channel('daily')->info("[NOTIFICATION]" . json_encode($notif->getResponse()));

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $order_id = $notif->order_id;
        $fraud = $notif->fraud_status;

        $payment = DB::table('payments')->where('order_id', '=', $order_id)->get();

        if (!count($payment)) {
            echo "Transaction not found";
            exit();
        }

        if ($transaction == 'capture') {
            // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    // TODO set payment status in merchant's database to 'Challenge by FDS'
                    // TODO merchant should decide whether this transaction is authorized or not in MAP
                    echo "Transaction order_id: " . $order_id . " is challenged by FDS";
                } else {
                    $this->updatePaymentStatus($payment[0]->id, $notif);
                    // TODO set payment status in merchant's database to 'Success'
                    echo "Transaction order_id: " . $order_id . " successfully captured using " . $type;
                }
            }
        } else if ($transaction == 'settlement') {
            $this->updatePaymentStatus($payment[0]->id, $notif);
            // TODO set payment status in merchant's database to 'Settlement'
            echo "Transaction order_id: " . $order_id . " successfully transfered using " . $type;
        } else if ($transaction == 'pending') {
            $this->updatePaymentStatus($payment[0]->id, $notif);
            // TODO set payment status in merchant's database to 'Pending'
            echo "Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;
        } else if ($transaction == 'deny') {
            $this->updatePaymentStatus($payment[0]->id, $notif);
            // TODO set payment status in merchant's database to 'Denied'
            echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";
        } else if ($transaction == 'expire') {
            $this->updatePaymentStatus($payment[0]->id, $notif);
            // TODO set payment status in merchant's database to 'expire'
            echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.";
        } else if ($transaction == 'cancel') {
            $this->updatePaymentStatus($payment[0]->id, $notif);
            // TODO set payment status in merchant's database to 'Denied'
            echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.";
        }
    }

    /**
     * Update payment status by midtrans notification
     *
     * @param $request, $response
     * @return void 
     */
    public function updatePaymentStatus($id, $response)
    {
        $payment = Payment::find($id);
        $payment->status_code = $response->status_code;
        $payment->status_message = $response->status_message;
        $payment->transaction_status = $response->transaction_status;
        $payment->transaction_time = $response->transaction_time;
        $payment->bank = $response->bank;
        $payment->save();
    }
}
