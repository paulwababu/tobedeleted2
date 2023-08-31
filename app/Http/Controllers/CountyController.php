<?php

namespace App\Http\Controllers;

use App\Models\County;
use Illuminate\Http\Request;

class CountyController extends Controller
{
    // load
    public function load(Request $request)
    {
        return County::filter($request)->paginate(50);
    }
}
