<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isNull;

trait Model
{
    use ApiTrait;
    //  315151.jpg
    public function uploadPhoto($request, $folderPath){
        $photoName = time() . "." . $request->image->extension();
        $request->image->move(public_path('images/'.$folderPath),$photoName);
        return $photoName;
    }

    public function insertProduct($request, $user_id, $photoName){
        return [
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'code' => $request->code,
            'status' => $request->status,
            'desc' => $request->desc,
            'brand_id' => $request->brand_id,
            'subcategory_id' => $request->subcategory_id,
            'image' => $photoName,
            'user_id' => $user_id
        ];
    }

    public function deletePhoto($photoPath){
        if(file_exists($photoPath) && $photoPath != public_path('/images/users/default.jpg')){
            unlink($photoPath);
        }
    }
    
    public function returnPhotoToClient($collection, $folderPath){
        foreach($collection as $item){
            $item->image_url = asset($folderPath . $item->image);
        }
    }

    public function searchItem($queryValue, $queryName, $folderPath){
        $user_id = Auth::user()->id;
        $result = User::find($user_id)->$queryName()->where('name', 'like', "%$queryValue%")->get();
        if($result->isEmpty()){
            return $this->successMessage("No $queryName Found...!");
        }
        if($queryName === 'products' || $queryName === 'brands'){
            $this->returnPhotoToClient($result,$folderPath);
        }
        return $this->data(["$queryName" => $result], 'Success Search');
    }
}
