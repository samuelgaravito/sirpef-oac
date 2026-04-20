<?php

namespace App\Http\Controllers;

use App\Http\Services\Memorandum\StoreMemorandumService;
use Illuminate\Http\Request;

class MemorandumController extends Controller
{
    public function store(Request $request)
    {
        return StoreMemorandumService::store($request);
    }
}
