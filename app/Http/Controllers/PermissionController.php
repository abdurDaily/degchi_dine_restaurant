<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    function index()
    {
        $permissions = Permission::get()->collect()->groupBy('group');
        return view('permission.index', compact('permissions'));
    }
}
