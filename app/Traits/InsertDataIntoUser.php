<?php

namespace App\Traits;

use Illuminate\Support\Facades\Hash;

trait InsertDataIntoUser
{
    public function Insert(object $request){
        return [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'gender' => $request->gender
        ];
    }
}
