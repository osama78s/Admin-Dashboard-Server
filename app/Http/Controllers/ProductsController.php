<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\Create;
use App\Http\Requests\Products\Update;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use App\Traits\ApiTrait;
use App\Traits\Model;
use App\Traits\UpdateDataWhenDelete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    use ApiTrait;
    use Model;
    use UpdateDataWhenDelete;

    public function read(Request $request)
    {
   
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        $products = $this->getDataPagination($user,'products',Product::class);
        $total = $products->lastPage();
        $this->returnPhotoToClient($products,'images/products/');
        return $this->data(compact('products','total'));
    }

    public function getProduct($id)
    {
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        if($user->role === 'user'){
            $subcategories = $user->subcategories;
            $brands = $user->brands;
            $product = $user->products()->where('id', $id)->first();
        }else {
            $subcategories = Subcategory::all();
            $brands = Brand::all();
            $product = Product::find($id);
        }
        if ($product) {
            $product->image_url = asset('/images/products/' . $product->image);
            return $this->data(compact('product', 'brands', 'subcategories'));
        } else {
            return $this->errorsMessage(['error' => 'Id Is Not Valid']);
        }
        
    }

    public function create(Create $request)
    {
        $user_id = Auth::user()->id;
        $product = Product::where('user_id', $user_id)
                   ->where('code', $request->code)->first();
        if($product){
            return $this->errorsMessage(['error' => 'Code Must Be Unique']);
        }
        $photoName = $this->uploadPhoto($request, 'products');
        $data = $this->insertProduct($request, $user_id, $photoName);
        $product = Product::create($data);
        if ($product) {
            return $this->data(compact('product'), 'Created Successfully', 201);
        } 
    }

    public function update(Update $request, $id)
    {
        $user_id = Auth::user()->id;
        $data = $request->except('image');

        $user = User::find($user_id);
        $product = $user->products()->where('id', $id)->first();
        if (!$product) {
            return $this->errorsMessage(['error' => 'Id Is Invalid']);
        }
        
        if ($request->hasFile('image')) {
            // delete old photo 
            $oldPhotoName = $product->image;
            $oldPathName = public_path('images/products/') . $oldPhotoName;
            $this->deletePhoto($oldPathName);
            
            // add new photo in storage and data base array 
            $photoName = $this->uploadPhoto($request, 'products');
            $data['image'] = $photoName;
        }
        
        $product->update($data);
        $product->image_url = asset('/images/products/' . $product->image);
        return $this->data(compact('product'), 'Product Updated Successfully');
    }


    public function delete($id)
    {
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        $product = $this->getDataOfUserOrAdmin($user,'products',$id,Product::class);

        if ($product) {
            $photoName = $product->image;
            $photoPath = public_path('images/products/') . $photoName;
            $this->deletePhoto($photoPath);

            $product->delete();
            $products = $this->getDataPagination($user,'products',Product::class);
            $total = $products->lastPage();
            return $this->data(compact('products','total'));
        } else {
            return $this->errorsMessage(['error' => 'Id Is Not Valid']);
        }
    }
}
