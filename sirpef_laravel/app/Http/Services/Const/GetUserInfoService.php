<?php

namespace App\Http\Services\Const;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Evento;


class GetUserInfoService
{
    static public function getUserInfo()
    {
        $user = Auth::user();

        $evento_db = $user->configUser->evento_activo;
        $Evento_id = Evento::where("id", $evento_db)->get();

        $evento_array = $Evento_id->map(function ($item) {
          return [
            'id'=>$item->id,
            'title' => $item->titulo,
            'subtitle' => $item->descripcion,
            'cortesia' => !$item->cortesia == 0,
          ];
        });

        return $evento_array;
    }
}