<?php

namespace App\Http\Controllers;

use App\Http\Requests\Brands\Create;
use App\Http\Requests\Brands\Update;
use App\Models\Brand;
use App\Models\User;
use App\Traits\ApiTrait;
use App\Traits\Model;
use App\Traits\UpdateDataWhenDelete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandsController extends Controller
{
    use ApiTrait;
    use Model;
    use UpdateDataWhenDelete;

    public function read(){
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        $brands = $this->getDataPagination($user,'brands',Brand::class);
        $total = $brands->lastPage();
        $this->returnPhotoToClient($brands,'images/brands/');
        return $this->data(compact('brands','total'));
    }
    private function InsertDataRequest($request, $user_id, $photoName){
        return [
            'name' => $request->name,
            'status' => $request->status ?? '1',
            'code' => $request->code,
            'image' => $photoName,
            'user_id' => $user_id
        ];
    }
    public function create(Create $request){
        $user_id = Auth::user()->id;
        $brand = Brand::where('user_id', $user_id)
                        ->where('code', $request->code)->first();
        if($brand){
            return $this->errorsMessage(['error' => 'Code Must Be Unique']);
        }
        $photoName = $this->uploadPhoto($request,'brands');
        $data = $this->InsertDataRequest($request, $user_id, $photoName);
        $brand = Brand::create($data);
        return $this->data(compact('brand'));
    }   

    public function show($id){
        $user_id = Auth::user()->id;
        $brand = User::find($user_id)->brands()->where('id', $id)->first();
        if(!$brand){
            return $this->errorsMessage(['error' => 'Id Is Not Valid']);
        }
        $brand->image_url = asset('/images/brands', $brand->image);
        return $this->data(compact('brand'));
    }

    public function update(Update $request, $id){
        $data = $request->except('image');

        $user_id = Auth::user()->id;
        $brand = User::find($user_id)->brands()->where('id', $id)->first();

        if($brand){
            if($request->hasFile('image')){
                // old Photo and deleted it
                $oldPhotoName = $brand->image;
                $oldPathName = public_path('/images/brands/') . $oldPhotoName;
                $this->deletePhoto($oldPathName);

                // update image 
                $photoName = $this->uploadPhoto($request,'brands');
                $data['image'] = $photoName;
            }
            if($data['status'] === null){
                $data['status'] = '1';
            }
            $brand->update($data);
            $brand->image_url = asset('images/brands/' . $brand->image);
            return $this->data(compact('brand'), 'Updated Successfully');
        } else {
            return $this->errorsMessage(['error' => 'Id Is Not Valid']);
        }

    }

    public function delete($id)
    {
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        $brand = $this->getDataOfUserOrAdmin($user,'brands',$id,Brand::class);
    
        if ($brand) {
            $oldPhotoName = $brand->image;
            $oldPathName = public_path('/images/brands/') . $oldPhotoName;
            $this->deletePhoto($oldPathName);
    
            $brand->delete();
    
            $brands = $this->getDataPagination($user,'brands',Brand::class);
            $total = $brands->lastPage();
            $this->returnPhotoToClient($brands, 'images/brands/');
    
            return $this->data(compact('brands', 'total'), 'Brand Deleted Successfully');
        } else {
            return $this->errorsMessage(['error' => 'Id Is Not Valid']);
        }
    }
    
}
