<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\Login;
use App\Http\Requests\User\Register;
use App\Http\Requests\User\Update;
use App\Models\User;
use App\Traits\ApiTrait;
use App\Traits\InsertDataIntoUser;
use App\Traits\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiTrait;
    use InsertDataIntoUser;

    public function register(Register $request)
    {
        // insert data except password and password confirmation
        $data = $this->Insert($request);
        // create user and  generate token 
        $user = User::create($data);
        $token = $user->createToken('token')->plainTextToken;
        return $this->data(['email' => $user->email, 'token' => $token], 'created successfully', 201);
    }
    
    public function login(Login $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!Hash::check($request->password, $user->password)) {
            return $this->errorsMessage(['error' => 'Email Or Password Or Been Invalid']);
        }
        if (is_null($user->email_verified_at)) {
            $token = $user->createToken('token')->plainTextToken;
            return $this->data(compact('user', 'token'), 'Email Not Verified');
        }
        $user->status = 'Active';
        $user->save();
        $user->token = $user->createToken('token')->plainTextToken;
        $user->image_url = asset('images/users/' . $user->image);
        return $this->data(compact('user'), 'Login Successfully');
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successMessage('Logout Successfully');
    }
}
