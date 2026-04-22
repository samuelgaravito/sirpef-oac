<?php

namespace App\Http\Controllers;

use App\Http\Services\Memos\CreateMemoService;
use Illuminate\Http\Request;

class MemorandumController extends Controller
{
    public function buscarPuntoCuenta(Request $request)
    {
        $numero = $request->query('numero');
        return CreateMemoService::buscarPuntoCuenta($numero);
    }

    public function store(Request $request)
    {
        return CreateMemoService::store($request);
    }

    public function index()
    {
        return CreateMemoService::index();
    }
}
