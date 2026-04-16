<?php

namespace App\Http\Services\User;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class IndexUserService
{
    static public function execute(Request $request): JsonResponse
    {
        // Initialize query 
        $query = User::withTrashed()
          ->select('id', 'name', 'email', 'cedula', 'email_verified_at', 'password', 'config_user_id', 'estatus', 'deleted_at') // Añadimos config_user_id
        ;
    
        // search 
        $search = $request->input("search");
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query
                    ->where("name", "like", "%$search%")
                    ->orWhere("cedula", "like", "%$search%")
                    ->orWhere("email", "like", "%$search%");
            });
        }
    
        // sort 
        $sort = $request->input("sort");
        $direction = $request->input("direction") == "desc" ? "desc" : "asc";
        
        if ($sort) {
            $query->orderBy($sort, $direction);
        }
    
        // get paginated results 
        //$users = User::withTrashed()
      
    
        return response()->json([
            "rows" => $query->paginate(5),
            "sort" => $request->query("sort"),
            "direction" => $request->query("direction"),
            "search" => $request->query("search")
        ]);
    }
}