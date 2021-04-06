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
use Illuminate\Support\Facades\Http;
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
use URL;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function index($id, Request $request)
    {
        $productId = $id;
        $product = ($productId) ? Product::where('code', $productId)->get()->toArray() : "";
        if (!count($product)) {
            abort(404);
        }
        if (isset($_GET['data'])) {
            $cart=(json_decode(base64_decode($_GET['data'])));
            $request->session()->put('cart',$cart);
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
        //$countryCode = explode("|", $request->prefixNumber);
        //$completeNumber = preg_replace('/^0?/', '+' . $countryCode[0], $request->phone);

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'address' => $request->address,
            'phone' => $request->phone,
            'post_code' => $request->post_code,
            //'country' => $countryCode[1],
            'country' => 100,
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
        $request->session()->forget('cart');
        $payment = $request->all();
        $userId = $request->cookie('piu');
        $user = ($userId) ? User::find($userId) : "";
        //$user = User::find(1);
        $product = ($payment['productId']) ? Product::find($payment['productId']) : "";
        $orderId = "ign-" . time() . mt_rand() . "-" . $user->id . "-" . $product->id;
        $bank = [1 => 'Mandiri', 2 => 'CIMB', 3 => 'BCA'];
        
        if ($user && $product) {
            $amount=$product->price;
            if (isset($payment['addItem'])) {
                foreach ($payment['addItem'] as $key => $value) {
                    $item=AdditionalProduct::find($value);
                    $amount=$amount+($item->price * (int)$request['qty'][$key]);
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
                                'quantity' => (int)$request['qty'][$key]
                            ]);
                            
                        }
                    }

                } else {
                    $data['status_code'] = 300;
                    $data['status_message'] = "Error processing payment";

                    return response()->json($data);
                }

                return response()->json($paymentResponse);
            } elseif ($payment['payment-type'] === "wallet" || $payment['payment-type'] === "qris") {
                $paymentResponse = $this->getPaymentResponseNicepay($payment, $user, $product, $orderId,$amount);
                Log::channel('daily')->info("[RESPONSE]" . json_encode($paymentResponse));
                
                if (isset($paymentResponse->resultCd) && $paymentResponse->resultCd=="0000") {
                    $payment = Payment::create([
                        'order_id' => $orderId,
                        'user_id' => $user->id,
                        'amount' => $amount,
                        'payment_type' => $payment['payment-type'] === "wallet" ? $payment['walletToSelect'] : $payment['payment-type'],
                        'product_id' => $payment['productId'],
                        'transaction_id' => $paymentResponse->tXid,
                        'status_message' => $paymentResponse->resultMsg,
                        'status_code' => $paymentResponse->resultCd,
                        'transaction_status' => 'pending',
                        'transaction_time' => $paymentResponse->transDt.$paymentResponse->transTm,
                        'currency' => $paymentResponse->currency
                    ]);
                    if (isset($request['addItem'])) {
                        foreach ($request['addItem'] as $key => $value) {
                            $paymentAdditionalProduct = PaymentAdditionalProduct::create([
                                'payment_id' => $payment->id,
                                'additional_product_id' => $value,
                                'quantity' => (int)$request['qty'][$key]
                            ]);
                            
                        }
                    }
                    $time=date('Ymd').date('His');
                    $callBackUrl=URL::to('/').'/checkout/nc-finish';
                    $merchantToken=$this->merchantToken($time,$orderId,$amount);
                    if ($request['payment-type'] === "wallet") {
                        $link = "https://www.nicepay.co.id/nicepay/direct/v2/payment"."?tXid=".$paymentResponse->tXid."&timeStamp=".$time."&callBackUrl=".$callBackUrl."&merchantToken=".$merchantToken;
                    }else{
                        $link = URL::to('/').'/checkout/qrcode?paymentExpDt='.$paymentResponse->paymentExpDt.'&paymentExpTm='.$paymentResponse->paymentExpTm.'&qrUrl='.$paymentResponse->qrUrl.'&amt='.$paymentResponse->amt.'&referenceNo='.$paymentResponse->referenceNo.'&tXid='.$paymentResponse->tXid;
                    }

                    $data['status_code'] = 200;
                    $data['status_message'] = "Silahkan Selesaikan Pembayaran Anda";
                    $data['link'] = $link;
                    return response()->json($data);
                    
                    
                    
                    
                }else{
                    $data['status_code'] = 300;
                    $data['status_message'] = "Error processing payment";

                    return response()->json($data);
                }
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
                            'quantity' => (int)$request['qty'][$key]
                        ]);
                        
                    }
                }
                
                $data['status_code'] = 200;
                $data['status_message'] = "Silahkan Selesaikan Pembayaran Anda";
                $data['bank'] = $payment['bankToSelect'];

                //dispatch(new SendEmailJob($user, $product, $payment));

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
     * Get Payment response from nicepay
     *
     * @param array $data
     * @return json 
     */

    public function getPaymentResponseNicepay($data, $user, $product, $orderId, $amount)
    {
        $time=date('Ymd').date('His');
        $merchantToken=$this->merchantToken($time,$orderId,$amount);
        if ($data['payment-type']=='qris') {
            $cart="{}";
            $payMethod='08';
            $mitraCd="QSHP";
        }else{
            $payMethod='05';
            $mitraCd=$data['walletToSelect'];
            if($data['walletToSelect'] == "OVOE"){

                $cart="{\"count\": \"1\",\"item\": [{\"img_url\": \"http://img.aaa.com/ima1.jpg\",\"goods_name\": \" $product->name\",\"goods_detail\": \"\",\"goods_amt\":" . "\"" . $amount . "\"}]}";
            } else{
                $cart="{\"count\": \"1\",\"item\": [{\"img_url\": \"http://img.aaa.com/ima1.jpg\",\"goods_name\": \" $product->name\",\"goods_quantity\": \"1\",\"goods_detail\": \".\",\"goods_amt\":" . "\"" . $amount . "\"}]}";
            }
        }
        
        $params=array (
            'timeStamp' => $time,
            'iMid' => env('NICEPAY_IMID'),
            'payMethod' => $payMethod,
            'currency' => 'IDR',
            'amt' => $amount,
            'referenceNo' => $orderId,
            'merchantToken' => $merchantToken,
            'goodsNm' => $product->name,
            'billingNm' => $user->firstname.' '.$user->lastname,
            'billingPhone' => $data['payment-type']=='qris' ? $user->phone : $data['wallet-number'],
            'billingEmail' => $user->email,
            'billingAddr' => 'Jl. Tole Iskandar No.19 A, Mekar Jaya, Kec. Sukmajaya',
            'billingCity' => 'Depok',
            'billingState' => 'Jawa Barat',
            'billingPostCd' => '16412',
            'billingCountry' => 'ID',
            'dbProcessUrl' => URL::to('/').'/api/checkout/notificationNicepay',
            'description' => '',
            'userIP' => '0:0:0:0:0:0:0:1',
            'cartData' => $cart,
            'mitraCd' => $mitraCd,
        );
        if ($data['payment-type']=='qris') {
            $params=array_merge($params,['shopId' => env('NICEPAY_SHOP_ID')]);
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post('https://www.nicepay.co.id/nicepay/direct/v2/registration', $params);
        $response=json_decode($response);
        return $response;
        

    }

    /**
     * Create merchant token for nicepay
     *
     * @param  $time,$amt,$referenceNo
     * @return hash
     */
    public function merchantToken($time,$referenceNo,$amt) {
        return hash('sha256', $time.env('NICEPAY_IMID').$referenceNo.$amt.env('NICEPAY_MERCHANT_KEY'));
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
     * Receive notification from nicepay
     *
     * @param Request $request
     * @return string 
     */
    public function notificationNicepay(Request $request)
    {
        Log::channel('daily')->info("[REQUEST]" . json_encode($_REQUEST));

        $payment = DB::table('payments')->where('order_id', '=', $_REQUEST['referenceNo'])->get();

        
        if ($payment) {
            $payment = Payment::find($payment[0]->id);
            $status='';
            if ($_REQUEST['status']==0) {
                $status='Paid';
            }elseif ($_REQUEST['status']==1) {
                $status='Void';
            }
            elseif ($_REQUEST['status']==8) {
                $status='Fail';
            }
            elseif ($_REQUEST['status']==9) {
                $status='Init';
            }elseif ($_REQUEST['status']==2) {
                $status='Refund';
            }elseif ($_REQUEST['status']==3) {
                $status='Unpaid';
            }
            elseif ($_REQUEST['status']==4) {
                $status='Processing';
            }
            elseif ($_REQUEST['status']==5) {
                $status='Expired';
            }

            if ($status) {
                $payment->transaction_status = $status;
                $payment->save();
            }
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

    /**
     * Update payment status by nicepay callback
     *
     * @param $request, $response
     * @return void 
     */

    public function callbackNicepay(Request $request)
    {
        $requestData = array();
        $requestData['iMid'] = env('NICEPAY_IMID');
        $requestData['merchantKey'] = env('NICEPAY_MERCHANT_KEY');
        $requestData['amt'] = $_GET['amt'];
        $requestData['referenceNo'] = $_GET['referenceNo'];
        $requestData['merchantToken'] = hash('sha256', $requestData['iMid'].$requestData['referenceNo'].$requestData['amt'].$requestData['merchantKey']);
        $requestData['tXid'] = $_GET['tXid'];

        $postData = '';
        foreach ($requestData as $key => $value) {
        $postData .= urlencode($key) . '='.urlencode($value).'&';
        }
        $postData = rtrim($postData, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.nicepay.co.id/nicepay/api/onePassStatus.do');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $curl_result = curl_exec($ch);
        $result = json_decode($curl_result);
        
        $payment = DB::table('payments')->where('order_id', '=', $result->referenceNo)->get();

        //Process Response Nicepay
        if(isset($result->resultCd) && $result->resultCd == '0000'){
            if ($payment) {
                $payment = Payment::find($payment[0]->id);
                $payment->transaction_status = $result->resultMsg;
                $payment->transaction_time = $result->reqDt.$result->reqTm;
                $payment->save();
                if ($payment) {
                    return view('checkout.thankyou-nicepay',compact('payment','result'));
                }
            }

            
        }
        elseif (isset($result->resultCd)) {
            echo "<pre>";
            echo "result code       :".$result->resultCd."\n";
            echo "result message    :".$result->resultMsg."\n";
            echo "</pre>";
        }
        else {
            //return view('checkout.thankyou-nicepay',compact('payment'));
            echo "<pre>";
            echo "Timeout When Checking Payment Status";
            echo "</pre>";
        }
    }
}
