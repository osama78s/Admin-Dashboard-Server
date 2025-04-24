<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\ForgetPassword;
use App\Http\Requests\User\Update;
use App\Models\Brand;
use App\Models\User;
use App\Traits\ApiTrait;
use App\Traits\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiTrait;
    use Model;
    
    public function getSubcategoriesAndBrands(){
        $user_id = Auth::user()->id;
        $brands = User::find($user_id)->brands;
        $subcategories = User::find($user_id)->subcategories;
        return $this->data(compact('brands','subcategories'));
    }
    
    public function searchProduct(Request $request){
        $productName = 'products';
        $productValue = $request->query($productName);
        return $this->searchItem($productValue, $productName, '/images/products/');
    }

    public function searchBrand(Request $request){
        $brandName = 'brands';
        $brandValue = $request->query($brandName);
        return $this->searchItem($brandValue, $brandName, '/images/brands/');
    }

    public function searchCategory(Request $request){
        $brandName = 'categories';
        $brandValue = $request->query($brandName);
        return $this->searchItem($brandValue, $brandName, '');
    }

    public function searchSubcategory(Request $request){
        $brandName = 'subcategories';
        $brandValue = $request->query($brandName);
        return $this->searchItem($brandValue, $brandName, '');
    }


    public function searchUser(Request $request)
    {
        $queryValue = $request->query('users'); 
        $users = User::where(function ($query) use ($queryValue) {
            $query->where('first_name', 'LIKE', "%{$queryValue}%")
                  ->orWhere('last_name', 'LIKE', "%{$queryValue}%");
         })
         ->where('email', '!=', 'osamasaif242@gmail.com')
         ->get();

        return response()->json(['users' => $users], 200);
    }

    public function update(Update $request){
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        $data = $request->except('image');
        if($request->hasFile('image')){
            // delete old photo 
            $oldPotoName = $user->image;
            $oldPathName = public_path('/images/users/') . $oldPotoName;
            $this->deletePhoto($oldPathName);
            // insert new image 
            $photoName = $this->uploadPhoto($request, 'users');
            $data['image'] = $photoName;
        }
        $user->update($data);
        $user->image_url = asset('/images/users/' . $user->image);
        $user->token = 'Bearer ' . $user->createToken('token')->plainTextToken;
        return $this->data(compact('user'), 'Updated Successfully');
    }

    public function delete(){
        $user = Auth::user()->delete();
        return $this->successMessage('Deleted Success');
    }

    public function ForgetPassword(ForgetPassword $request){
        $userInDb = User::where('email', $request->email)->first();
        $userInDb->password = Hash::make($request->password);
        $userInDb->save();
        return $this->successMessage('Updated Successfully');
    }
}
