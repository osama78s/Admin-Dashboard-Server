<?php

namespace App\Http\Requests\User;

use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // $id = Auth::user()->id;
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            // 'email' => "required|email|unique:users,email,$id,id",
            'gender' => ['required' , new Enum(Gender::class)],
            'image' => 'nullable|file|mimes:jpg,jpeg,gif,png',
            'city' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string|max:255'
        ]; 
    }
}
