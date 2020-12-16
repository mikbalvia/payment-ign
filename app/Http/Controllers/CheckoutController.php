<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests\StoreCustomerInfo;
use Illuminate\Support\Facades\Validator;
use App\User;
use Midtrans\Config as PaymentConfig;
use Midtrans\CoreApi as SendPaymentResponse;

class CheckoutController extends Controller
{
    public function index()
    {
        return view('checkout.index');
    }

    /**
     * Store customer info
     *
     * @param StoreCustomerInfo $request
     * @return view 
     */
    public function paymentMethod(StoreCustomerInfo $request)
    {
        $countryCode = explode("|", $request->prefixNumber);
        $completeNumber = preg_replace('/^0?/', '+' . $countryCode[0], $request->phone);

        // User::create([
        //     'firstname' => $request->firstname,
        //     'lastname' => $request->lastname,
        //     'email' => $request->email,
        //     'phone' => $completeNumber,
        //     'country' => $countryCode[1],
        //     'user_type' => 2
        // ]);

        return view('checkout.payment-method');
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

        if ($payment['payment-type'] === "credit-card") {

            $paymentResponse = $this->getPaymentResponse($payment);

            return response()->json($paymentResponse);
        }
    }

    /**
     * Get Payment response from midtrans
     *
     * @param array $data
     * @return json 
     */
    public function getPaymentResponse($data)
    {
        PaymentConfig::$serverKey = env('MIDTRANS_SERVER_KEY');

        $params = array(
            'transaction_details' => array(
                'order_id' => rand(),
                'gross_amount' => 400000,
            ),

            'payment_type' => 'credit_card',
            'credit_card'  => array(
                'token_id'      => $data['mTokenId'],
                'authentication' => true,
            ),
            'customer_details' => array(
                'first_name' => 'Budi',
                'last_name' => 'Pratama',
                'email' => 'budi.pra@example.com',
                'phone' => '08111222333',
            ),
        );

        return SendPaymentResponse::charge($params);
    }

    public function finish()
    {
        return view('checkout.thankyou');
    }
}
