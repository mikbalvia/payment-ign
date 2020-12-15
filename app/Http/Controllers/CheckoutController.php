<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests\StoreCustomerInfo;
use Illuminate\Support\Facades\Validator;
use App\User;

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

        User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $completeNumber,
            'country' => $countryCode[1],
            'user_type' => 2
        ]);

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

    public function finish(Request $request)
    {
        $payment = $request->all();
        if ($payment['payment-type'] === "credit-card") {
            $validator = Validator::make($request->all(), [
                'card-number' => ['required', 'max:19', 'numeric'],
                'ccv'  => ['required', 'numeric', 'min:3',]
            ]);

            if ($validator->fails()) {
                return redirect($request->server('HTTP_REFERER'))
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        return view('checkout.thankyou');
    }
}
