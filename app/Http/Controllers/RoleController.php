<?php

namespace App\Http\Controllers;

class RoleController extends Controller
{
    public function index()
    {
        return view('Role.index');
    }

    public function create()
    {
        return view('Role.create');
    }
}
