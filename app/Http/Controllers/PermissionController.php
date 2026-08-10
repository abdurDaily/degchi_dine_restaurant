<?php

namespace App\Http\Controllers;

use App\Support\PermissionGroups;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::query()
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group');

        $sections = PermissionGroups::organize($permissions);

        return view('permission.index', compact('sections'));
    }
}
