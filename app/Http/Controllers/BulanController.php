<?php

namespace App\Http\Controllers;

use App\Models\Bulan;


use Illuminate\Http\Request;

class BulanController extends Controller
{
    public function index()
    {
        return response()->json(Bulan::all());
    }
}
