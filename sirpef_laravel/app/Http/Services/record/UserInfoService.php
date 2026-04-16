<?php

namespace App\Http\Services\record;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Persona;

use ElephantIO\Client;

class UserInfoService{
    static public function getUserInfo($message)
    {
        $url = env("WEBSKOCKET_BACKEND", 'http://10.5.10.131:9000');
        $options = [
            'client' => Client::CLIENT_4X,
            'auth' => ['id' => 'Laravel44461']
         ];
    
        $client = Client::create($url, $options);
        $client->connect();
        $client->of('/');
    
        $data = ['data' => $message];
        $client->emit('notificacion', $data);
    
    
        $client->disconnect();
    
    }
}