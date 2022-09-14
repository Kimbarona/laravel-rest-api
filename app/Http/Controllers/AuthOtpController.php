<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\VerificationCode;

class AuthOtpController extends Controller
{
    public function generate (Request $request){

        $request->validate([
            'mobile_no' => 'required|exists:users,mobile_no'
        ]);

        #Generate An OTP
        $verificationCode = $this->generateOtp($request->mobile_no);

        #sms side here ................


        #return the otp
        $otpCode = $verificationCode->otp;

        return response()->json([
            'message' => 'Generated Successfully.',
            'Otp' => $otpCode
        ], 200);

    }

    public function generateOtp ($mobile_no) {

        $user = User::where('mobile_no', $mobile_no)->first();

        # User Does not Have Existing OTP
        $verificationCode = VerificationCode::where('user_id', $user->id)->latest()->first();

        $now = Carbon::now();

        if($verificationCode && $now->isBefore($verificationCode->expire_at)){
            return $verificationCode;
        }

        #Create new OTP
        return VerificationCode::create([
            'user_id' => $user->id,
            'otp' => rand(123456, 999999),
            'expire_at' => Carbon::now()->addMinutes(10)
        ]);
    }
}
