<?php

namespace App\Traits;

use App\Models\Brand;

trait UpdateDataWhenDelete
{
    public function getDataOfUserOrAdmin($user, $relation, $id, $model){
        if($user->role === 'user'){
            $data = $user->$relation()->where('id', $id)->first();
          
        }else {
            $data = $model::find($id);
        }
        return $data;
    }

    public function getDataPagination($user,$relation,$model){
            
        if ($user->role === 'user') {
            $data = $user->$relation()->paginate(6);
        } else {
            $data = $model::paginate(6);
        }

        return $data;
    }
}
