<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests\StoreCustomerInfo;
use Illuminate\Support\Facades\Validator;
use App\User;
use Midtrans\Config as PaymentConfig;
use Midtrans\CoreApi as SendPaymentResponse;
use App\Models\Payment;

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
    public function storeCustomerInfo(StoreCustomerInfo $request)
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

        return redirect('payment')->withCookie($cookie);
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

        if ($user) {
            if ($payment['payment-type'] === "credit-card") {

                $paymentResponse = $this->getPaymentResponse($payment, $user);

                return response()->json($paymentResponse);
            } else {
                $payment = Payment::create([
                    'order_id' => rand(),
                    'user_id' => $user->id,
                    'amount' => 400000,
                    'status' => 1,
                    'payment_type' => 2
                ]);

                $data['status_code'] = 200;
                $data['status_message'] = "Success, Direct Transfer transaction is successful";

                return response()->json($data);
            }
        } else {
            $data['status_code'] = 300;
            $data['status_message'] = "User data not found, Please register your data again!";

            return response()->json($data);
        }
    }

    /**
     * Get Payment response from midtrans
     *
     * @param array $data
     * @return json 
     */
    public function getPaymentResponse($data, $user)
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
                'first_name' => $user->firstname,
                'last_name' => $user->lastname,
                'email' => $user->email,
                'phone' => $user->phone,
            ),
        );

        return SendPaymentResponse::charge($params);
    }

    public function finish()
    {
        return view('checkout.thankyou');
    }
}
