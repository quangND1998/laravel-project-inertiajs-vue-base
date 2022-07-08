<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class MapBoxController extends Controller
{
    public function index()
    {
        // return Inertia::render("MapBox");
        return view('map2');
    }
}
