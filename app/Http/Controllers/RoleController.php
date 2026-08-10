<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Api\ApiUserService;
use App\Support\PermissionGroups;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use stdClass;

class RoleController extends Controller
{

    function index(Request $request)
    {
        if ($request->ajax())
        {
            $roles = Role::query()->with('permissions');

            return datatables()->of($roles)
                ->addIndexColumn()
                ->addColumn('permissions', function ($row)
                {
                    $permissions = $row->permissions;
                    if ($permissions->isEmpty())
                    {
                        return '<span class="text-muted">No Permissions</span>';
                    }

                    return $permissions->take(8)->map(function ($permission)
                    {
                        $action = \App\Support\PermissionGroups::actionFromName($permission->name);
                        $class = \App\Support\PermissionGroups::actionBadgeClass($action);

                        return '<span class="mb-1 me-1 badge '.$class.'" title="'.e($permission->name).'">'
                            .e(\App\Support\PermissionGroups::groupLabel($permission->group)).': '.e($action)
                            .'</span>';
                    })->implode(' ')
                    .($permissions->count() > 8
                        ? '<span class="badge bg-light text-body">+'.($permissions->count() - 8).' more</span>'
                        : '');
                })
                ->addColumn('action', function ($row)
                {
                    $btn = '';
                    $btn .= '<button class="btn btn-primary edit-role me-3" data-id="' . $row->id . '" data-name="' . $row->name . '" type="button"><i class="ri-edit-line"></i></button>';
                    $btn .= '<a href="' . route('roles.users', $row->id) . '" class="btn btn-dark me-3 position-relative" data-id="' . $row->id . '"><i class="ri-group-line"></i> <span class="top-0 position-absolute start-100 translate-middle badge rounded-pill bg-success">' . $row->users->count() . '</span></a>';
                    $btn .= '<a href="' . route('roles.permissions', $row->id) . '" class="btn btn-success"><i class="ri-key-line"></i></a>';
                    return $btn;
                })
                ->rawColumns(['permissions', 'action'])
                ->make(true);
        }
        return view('roles.index');
    }

    /**
     * Store new role
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:roles',
        ]);

        try
        {
            $role = Role::create($validated);
            return response()->json([
                'status' => 'success',
                'message' => 'Role added successfully',
            ], 201);
        }
        catch (\Throwable $th)
        {
            //throw $th;
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ], 400);
        }
    }

    /**
     * Update role
     */
    public function update(Role $role, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
        ]);

        try
        {
            $role = $role->update($validated);
            return response()->json([
                'status' => 'success',
                'message' => 'Role updated successfully',
            ], 201);
        }
        catch (\Throwable $th)
        {
            //throw $th;
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ], 400);
        }
    }


    /**
     * Geting users of a role
     */
    public function users(Role $role, Request $request)
    {

        if ($request->ajax())
        {
            $role->load('users');
            return datatables()->of($role->users)
                ->addIndexColumn()
                ->addColumn('name', function ($row)
                {
                    return $row->name;
                })
                ->addColumn('email', function ($row)
                {
                    return $row->email;
                })
                ->addColumn('action', function ($row)
                {
                    $btn = '';
                    $btn .= '<button class="btn btn-danger remove-role me-3" . onclick="removeRole(' . $row->id . ')" . " type="button"><i class="ri-delete-bin-line"></i></button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $users = User::active()->select(['id', 'name', 'user_number','status'])->orderBy('id', 'desc')->get();
        return view('roles.users', compact('role', 'users'));
    }

    public function userAdd($id, Request $request)
    {
        $validated = $request->validate([
            'users' => 'required',
        ]);

        $role = Role::find($id);
        $role->users()->syncWithoutDetaching($request->users);
        return response()->json([
            'status' => 'success',
            'message' => 'User added successfully',
        ], 201);
    }

    /**
     * Remove user from a role
     */
    public function userRemove(Request $request)
    {
        // dd($request->all());
        $role = Role::find($request->role_id);
        $role->users()->detach($request->user_id);
        return response()->json([
            'success' => true,
            'message' => 'User removed successfully',
        ], 201);
    }

    /**
     * Assign permission to role
     */
    public function rolePermission(Request $request, $id)
    {
        $role = Role::with('permissions')->where('id', $id)->firstOrFail();
        $permissions = Permission::query()
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group');
        $sections = PermissionGroups::organize($permissions);

        return view('roles.assign-permissions', compact('role', 'sections'));
    }

    /**
     * assign permissions to role
     */
    public function assignPermissions(Request $request, $id)
    {
        // dd($request->all());
        try
        {
            $role = Role::find($id);
            $role->permissions()->sync($request->input('permissions', []));

            Artisan::call('cache:forget spatie.permission.cache');

            return response()->json([
                'status' => 'success',
                'message' => 'Permissions assigned successfully',
            ], 200);
        }
        catch (\Throwable $th)
        {
            //throw $th;
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => $th->getMessage(),
            ], 400);
        }
    }
    function storeOrUpdateRole(Request $request, $id = null)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
        ]);

        $role = Role::updateOrCreate([
            'id' => $id,
        ], [
            'name' => $request->name,
        ]);

        return response()->json([
            'data'  => $role,
            'msg' => $id ? 'Role updated successfully' : 'Role added successfully'
        ]);
    }



    function deleteRole($id)
    {

        $role = Role::findOrFail($id)->delete();
        return response($role ? 'Deleted successfully' : false);
    }


    function assignPermission(User $users, $roleId)
    {

        $role = Role::with('permissions:id')->find($roleId);


        $selectedPermissions = $role->permissions->pluck('id')->toArray();
        $permissions = Permission::get()->collect()->groupBy('group');

        return view('Backend.users.role-assign', compact('role', 'permissions', 'selectedPermissions'));
    }

    function assignPermissionUpdate(Request $request, $id)
    {

        $this->assignRoleByUserIds($id, $request->users);
        $roles = Role::findOrFail($id);
        $roles->permissions()->sync($request->permissions);
        return to_route('roles.index');
    }

    function assignRoleToUser(Request $request, $userId)
    {
        $this->assignRoleByRoleIds($userId, $request->ids);
        return response(true, 200);
    }


    private function assignRoleByUserIds($roleId, $users): void
    {
        if ($users)
        {
            DB::table('model_has_roles')->where('role_id', $roleId)->delete();
            foreach ($users as $user)
            {

                $query = DB::table('model_has_roles')->insert([
                    'role_id' => $roleId,
                    'model_type' => 'App\Models\User',
                    'model_id' => $user
                ]);
            }
        }
    }


    private function assignRoleByRoleIds($userId, $roleIds): void
    {
        if ($roleIds)
        {
            DB::table('model_has_roles')->where('model_id', $userId)->delete();
            foreach ($roleIds as $roleId)
            {
                $query = DB::table('model_has_roles')->insert([
                    'role_id' => $roleId,
                    'model_type' => 'App\Models\User',
                    'model_id' => $userId
                ]);
            }
        }
    }



    function getApiRoles(Request $request, $params = null)
    {
        $query = Role::query();
        if ($request->search)
        {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }
        if ($request->ids)
        {
            $query->whereIn('id', explode(',', $request->ids));
        }
        $roles = $query->get();

        $roles = collect($roles)->map(function ($role)
        {
            return [
                'id' => $role->id,
                'text' => $role->name
            ];
        });
        return response()->json([
            'results' => $roles
        ]);
    }
}
