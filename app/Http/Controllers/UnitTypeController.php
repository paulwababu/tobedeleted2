<?php

namespace App\Http\Controllers;

use App\Models\UnitType;
use Illuminate\Http\Request;

class UnitTypeController extends Controller
{
    // load
    public function load(Request $request)
    {
        return UnitType::filter($request)->paginate(50);
    }
}
