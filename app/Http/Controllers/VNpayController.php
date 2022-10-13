<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VNpayController extends Controller
{
    // public function vnpay()
    // {

    //     return view('vnpay.index');
    // }

    public function vnpayment(Request $request)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $startTime = date("YmdHis");
        // $expire = date('YmdHis', strtotime('+1 minutes', strtotime($startTime)));
        $vnp_TxnRef = $request->order_id;
        $vnp_OrderInfo = $request->order_desc;
        $vnp_OrderType = $request->order_type;
        $vnp_Amount = $request->amount * 100;
        $vnp_Locale = $request->language;
        $vnp_BankCode = $request->bank_code;
        // $vnp_ExpireDate = $expire;
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        $inputData = array(
            "vnp_Version" => "2.0.0",
            "vnp_TmnCode" => env('VNP_TMNCODE'),
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => route('vnpay.return'),
            "vnp_TxnRef" => $vnp_TxnRef,
            // "vnp_ExpireDate" => $vnp_ExpireDate,
        );
        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . $key . "=" . $value;
            } else {
                $hashdata .= $key . "=" . $value;
                $i = 1;
            }
            $query .= urldecode($key) . "=" . urldecode($value) . '&';
        }

        $vnp_Url = env('VNP_URL') . "?" . $query;
        if (env('VNP_HASH_SECRET')) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, env('VNP_HASH_SECRET')); //  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        // dd($vnp_Url);
        return redirect($vnp_Url);
    }

    public function vnpayReturn(Request $request)
    {
        // dd($request->all());
        if ($request->vnp_ResponseCode == "00") {
            $vnpay = new Payment();
            $vnpay->order_id = $request->vnp_TxnRef;
            $vnpay->money = $request->vnp_Amount;
            $vnpay->vnp_response_code = $request->vnp_ResponseCode;
            $vnpay->code_vnpay = $request->vnp_CardType;
            $vnpay->code_bank = $request->vnp_BankCode;
            $vnpay->time = $request->vnp_PayDate;
            $vnpay->save();
            return to_route('thankyou');
        }
        return dd('Lỗi');
    }
}
