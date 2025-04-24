<?php

namespace App\Http\Controllers;

use App\Http\Requests\Categories\Create;
use App\Http\Requests\Categories\Update;
use App\Models\Category;
use App\Models\User;
use App\Traits\ApiTrait;
use App\Traits\Model;
use App\Traits\UpdateDataWhenDelete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoriesController extends Controller
{
    use ApiTrait;
    use Model;
    use UpdateDataWhenDelete;

    public function read(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        $categories = $this->getDataPagination($user, 'categories', Category::class);
        $total = $categories->lastPage();
        return $this->data(compact('categories', 'total'));
    }

    public function show($id)
    {
        $user_id = Auth::user()->id;
        $category = User::find($user_id)->categories()->where('id', $id)->first();
        if (!$category) {
            return $this->errorsMessage(['error' => 'Id Is Not Valid']);
        }
        return $this->data(compact('category'));
    }

    private function InsertDataRequest($request, $user_id)
    {
        return [
            'name' => $request->name,
            'code' => $request->code,
            'status' => $request->status ?? '1',
            'user_id' => $user_id
        ];
    }

    public function create(Create $request)
    {
        $user_id = Auth::user()->id;
        $category = Category::where('user_id', $user_id)
                    ->where('code', $request->code)->first();
        if($category){
            return $this->errorsMessage(['error' => 'Code Must Be Unique']);
        }
        $data = $this->InsertDataRequest($request, $user_id);
        $category = Category::create($data);
        return $this->data(compact('category'), 'Created Successfully', 201);
    }

    public function update(Update $request, $id)
    {

        $user_id = Auth::user()->id;
        $category = User::find($user_id)->categories()->where('id', $id)->first();

        if ($category) {
            $data = $this->InsertDataRequest($request, $user_id);
            if (is_null($data['status'])) {
                $data['status'] = '1';
            }
            $category->update($data);
            return $this->data(compact('category'));
        } else {
            return $this->errorsMessage(['error' => 'Id Is Not Valid']);
        }
    }

    public function delete($id)
    {
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        $category = $this->getDataOfUserOrAdmin($user, 'categories', $id, Category::class);
        if ($category) {
            $category->delete();
            $categories = $this->getDataPagination($user, 'categories', Category::class);
            $total = $categories->lastPage();
            return $this->data(compact('categories', 'total'));
        } else {
            return $this->errorsMessage(['error' => 'Id Is Not Valid']);
        }
    }
}
