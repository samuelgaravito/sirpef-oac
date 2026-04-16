<?php

namespace App\Http\Controllers;

//use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Menu\RecursiveMenuRepository;

class AuthMenuController extends Controller
{
    public function __invoke()
    {
        if (!Auth::user())  //auth()->check()
            return  response()->json(["message" => "Forbidden"], 403);          
    
        $user = Auth::user();
        
        $menuIds = json_decode($user->configUser->menu_ids, true); // Convertir la cadena JSON en un array

      /*   if($user->is_admin == true){
            $role = \App\Models\Role::select('menu_ids')->find($user->role_id);           
            $menus = RecursiveMenuRepository::recursive($role->menu_ids);
        }
            else */ 
        $menus = [];
        if(count($menuIds) > 0) $menus = RecursiveMenuRepository::recursive($menuIds);

        return response()->json($menus);       
    }
}
