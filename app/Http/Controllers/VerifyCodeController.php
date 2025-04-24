<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\VerifyCode;
use App\Mail\SendCode;
use App\Mail\SendCodeMail;
use App\Models\User;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class VerifyCodeController extends Controller
{
    use ApiTrait;
    
    public function sendCode()
    {
        $userAuthanticated = Auth::guard('sanctum')->user();
        // generate code 
        $code = rand(10000, 99999);
        $code_expired_at = date('Y-m-d H:i:s', strtotime('+3 minutes'));
        // get user in db and update all new values
        $userInDb = User::find($userAuthanticated->id);
        if (!$userInDb) {
            return $this->errorsMessage(['error' => 'User Not Found'], 404);
        }
        $userInDb = $this->UpdateUserData($userInDb, $code, $code_expired_at);
        // send mail 
        $stringCode = (string) $code;
        Mail::to($userAuthanticated->email)->send(new SendCode($stringCode));
        return $this->data(['code' => $userInDb->code, 'email' => $userInDb->email], 'Send Code Successfully');
    }

    public function checkCode(VerifyCode $request)
    {
        $token = $request->header('Authorization');
        $userAuthanticated = Auth::guard('sanctum')->user();
        if (!$userAuthanticated) {
            return $this->errorsMessage(['error' => 'Unauthorized'], '', 401);
        }
        $userInDb = User::find($userAuthanticated->id);
        if (!$userInDb) {
            return $this->errorsMessage(['error' => 'User Not Found'], '', 404);
        }
        if (!$userInDb->code_expired_at > date('Y-m-d H:i:s')) {
            return $this->errorsMessage(['error' => 'Code Is Expired']);
        } else {
            if ($request->code != $userInDb->code) {
                return $this->errorsMessage(['error' => 'Code Not Matched']);
            }
            // update email verified at 
            $userInDb->email_verified_at = date('Y-m-d H:i:s');
            $userInDb->status = 'Active';
            $userInDb->save();
            $userInDb->image_url = asset('images/users/' . $userInDb->image);
            $userInDb->token = $token;
            return $this->data(compact('userInDb'), 'Verified Succcess');
        }
    }

    private function UpdateUserData($userInDb, $code, $code_expired_at)
    {
        $userInDb->code = $code;
        $userInDb->code_expired_at = $code_expired_at;
        if ($userInDb->save()) {
            return $userInDb;
        }
        throw new \Exception('Faild To Update Values');
    }

}
