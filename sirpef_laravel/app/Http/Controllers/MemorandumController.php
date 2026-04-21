<?php

namespace App\Http\Controllers;

use App\Http\Services\Memos\StoreMemorandumService;
use Illuminate\Http\Request;

class MemorandumController extends Controller
{
    public function buscarPuntoCuenta(string $numero)
    {
        return StoreMemorandumService::buscarPuntoCuenta($numero);
    }

    public function store(Request $request)
    {
        return StoreMemorandumService::store($request);
    }
}
