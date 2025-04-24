<?php

namespace App\Http\Controllers;

use App\Http\Requests\Security\UpdateRole;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use App\Traits\ApiTrait;
use App\Traits\Model;
use App\Traits\UpdateDataWhenDelete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    use ApiTrait;
    use Model;
    use UpdateDataWhenDelete;

    public function getAllUsers(){
        $users = User::where('email', '!=', 'osamasaif242@gmail.com')->paginate(6);
        $total = $users->lastPage();
        $this->returnPhotoToClient($users,'/images/users/');
        return $this->data(compact('users','total'));
    }

    public function getAllUsersWithoutPagination(){
        $users = User::where('email', '!=', 'osamasaif242@gmail.com')->count();
        return $this->data(compact('users'));
    }

    // public function getUser($id){
    //     $user = User::find($id);
    //     if(!$user){
    //         return $this->errorsMessage(['error' => 'Id Is Invalid']);
    //     }
    //     $user->image_url = asset('images/users/' . $user->image);
    //     return $this->data(compact('user'));
    // }

    public function updateUserRole(UpdateRole $request, $id){
        $user = User::find($id);
        if(!$user){
            return $this->errorsMessage(['error' => 'Id Is Invalid']);
        }
        $user->role = $request->role;
        $user->save();
        return $this->data(compact('user'), 'Updated Success');
    }

    public function deleteUser($id){
        $user = User::find($id);
        $user->delete();
        $users = User::paginate(6);
        $total = $users->lastPage();
        return $this->data(compact('users','total'), "Deleted User Successfully");
    }

    // public function getAllSubcategories(){
    //     $subcategories = Subcategory::all();
    //     return $this->data(compact('subcategories'));
    // }

    // public function deleteSubcategory($id){
    //     $subcategory = Subcategory::find($id);        
    //     if(!$subcategory){
    //         return $this->errorsMessage(['error' => 'Id Is Invalid']);
    //     }
    //     $subcategory->delete();
    //     return $this->successMessage('Deleted Successfully');
    // }

    // public function getAllBrands(){
    //     $brands = Brand::all();
    //     $this->returnPhotoToClient($brands,'/images/brands/');
    //     return $this->data(compact('brands'));
    // }

    // public function deleteBrand($id){
    //     $brand = Brand::find($id);        
    //     if(!$brand){
    //         return $this->errorsMessage(['error' => 'Id Is Invalid']);
    //     }
    //     $brand->delete();
    //     return $this->successMessage('Deleted Successfully');
    // }

    // public function getAllCategories(){
    //     $category = Category::all();
    //     return $this->data(compact('category'));
    // }

    // public function deleteCategory($id){
    //     $category = Category::find($id);        
    //     if(!$category){
    //         return $this->errorsMessage(['error' => 'Id Is Invalid']);
    //     }
    //     $category->delete();
    //     return $this->successMessage('Deleted Successfully');
    // }

    // public function GetAllProducts(){
    //     $products = Product::all();
    //     $this->returnPhotoToClient($products,'/images/products/');
    //     return $this->data(compact('products'));
    // }

    // public function deleteProduct($id){
    //     $product = Product::find($id);        
    //     if(!$product){
    //         return $this->errorsMessage(['error' => 'Id Is Invalid']);
    //     }
    //     $product->delete();
    //     return $this->successMessage('Deleted Successfully');
    // }
}
