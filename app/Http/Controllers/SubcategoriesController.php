<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subcategories\Create;
use App\Http\Requests\Subcategories\Update;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use App\Traits\ApiTrait;
use App\Traits\Model;
use App\Traits\UpdateDataWhenDelete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubcategoriesController extends Controller
{
    use ApiTrait;
    use Model;
    use UpdateDataWhenDelete;

    public function read(){
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        $subcategories = $this->getDataPagination($user,'subcategories',Subcategory::class);
        $total = $subcategories->lastPage();
        return $this->data(compact('subcategories'));
    }

    public function show($id){
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        if($user->role === 'user'){
            $categories = $user->categories;
            $subcategory = $user->subcategories()->where('id', $id)->first();
        }else {
            $categories = Category::all();
            $subcategory = Subcategory::find($id);
        }
        return $this->data(compact('subcategory','categories'));
    }

    private function InsertDataRequest($request, $user_id){
        return[
            'name' => $request->name,
            'category_id' => $request->category_id,
            'status' => $request->status ?? '1',
            'code' => $request->code,
            'user_id' => $user_id
        ];
    }

    public function create(Create $request){
        $user_id = Auth::user()->id;
        $subcategory = Subcategory::where('user_id', $user_id)
                       ->where('code', $request->code)->first();
        if($subcategory){
            return $this->errorsMessage(['error' => 'Code Must Be Unique']);
        }
        $data = $this->InsertDataRequest($request, $user_id);
        $subcategory = Subcategory::create($data);
        return $this->data(compact('subcategory'));
    }

    public function update(Update $request, $id){
        $user_id = Auth::user()->id;
        $subcategory = Subcategory::find($id);
        if($subcategory){
            $data = $this->InsertDataRequest($request,$user_id);
            $subcategory->update($data);
            return $this->data(compact('subcategory'));
        }else {
            return $this->errorsMessage(['error' => 'Id Is Not Valid']);
        }
    }

    public function delete($id){
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        $subcategory = $this->getDataOfUserOrAdmin($user,'subcategories',$id,Subcategory::class);

        if($subcategory){
            $subcategory->delete();
            $subcategories = $this->getDataPagination($user,'subcategories',Subcategory::class);
            $total = $subcategories->lastPage();

            return $this->data(compact('subcategories','total'));
        }else{
            return $this->errorsMessage(['error' => 'Id Is Not Valid']);
        }
    }
}
