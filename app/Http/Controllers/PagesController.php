<?php

namespace App\Http\Controllers;


use App\Http\Request;
use App\Http\Response;
use App\Http\View;
use App\Todo;

class PagesController extends Controller
{
    public function index(Request $request)
    {
        return new View('home');
    }

}