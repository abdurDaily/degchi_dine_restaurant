<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Services\UploadService;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    private $uploadService;
    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;

        $this->middleware('permission:users-show')->only('index');
        $this->middleware('permission:users-create')->only(['create', 'store']);
        $this->middleware('permission:users-edit')->only(['edit', 'update']);
        $this->middleware('permission:users-delete')->only('destroy');
    }

    /**
     * Resolve the branch for the user being saved.
     *
     * Super Admin users are not restricted to a branch (branch_id = null => all branches).
     * Every other role MUST select a branch or "All Branch".
     */
    private function resolveBranchId(Request $request, array $roleNames): ?int
    {
        if (in_array('Super Admin', $roleNames))
        {
            return null;
        }

        $branchId = trim((string) $request->input('branch_id'));

        if ($branchId === '')
        {
            throw ValidationException::withMessages([
                'branch_id' => ['Must select a branch.'],
            ]);
        }

        if ($branchId === 'all')
        {
            return null;
        }

        if (!Branch::where('id', $branchId)->exists())
        {
            throw ValidationException::withMessages([
                'branch_id' => ['The selected branch does not exist.'],
            ]);
        }

        return (int) $branchId;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax())
        {
            $query = User::query()->with('branch')->latest();

            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('image', function ($row)
                {
                    if (isset($row->profile_image))
                    {
                        return '<img src="' . $row->profile_image . '" class="img-fluid" alt="image" style="height:90px; width:90px;">';
                    }
                    else
                    {
                        return '<img src="' . asset('assets/images/no-image.svg') . '" class="img-fluid" alt="image" style="height:90px; width:90px;">';
                    }
                })
                ->addColumn('branch', function ($row)
                {
                    if ($row->branch_id === null)
                    {
                        return '<span class="badge bg-info">All Branch</span>';
                    }

                    return e($row->branch->name ?? 'N/A');
                })
                ->addColumn('status', function ($row)
                {
                    if ($row->status == true)
                    {
                        return '<span class="badge bg-success">Active</span>';
                    }
                    else
                    {
                        return '<span class="badge bg-danger">Pending approval</span>';
                    }
                })
                ->addColumn('actions', function ($item)
                {
                    $html = '';
                    if (auth()->user()->can('users-edit') || auth()->user()->can('users-delete'))
                    {
                    $html .= '<div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        ' . __('Options') . '
                    </button>
                    <ul class="dropdown-menu">';

                    if (auth()->user()->can('users-edit'))
                    {
                    $html .= ' <li><a class="dropdown-item" href="' . route('users.edit', $item->id) . '" >' . __('Edit') . '</a></li>';
                    }

                    if (auth()->user()->can('users-delete'))
                    {
                    $html .= ' <li><button class="dropdown-item text-danger"  onclick="deleteUser(' . $item->id . ')">' . __('Delete') . '</button></li>';
                    }
                    '</ul>
                </div>';
                    }
                    return $html;
                })
                ->rawColumns(['image', 'branch', 'status', 'actions'])
                ->make(true);
        }

        return view('users.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $branches = Branch::orderBy('name')->get();
        return view('users.create', compact('roles', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $validatedData = $request->validated();

        $roleNames = [];
        if (auth()->user()->hasRole('Super Admin') && isset($validatedData['roles']))
        {
            $roleNames = $validatedData['roles'];
        }

        $branchId = $this->resolveBranchId($request, $roleNames);

        DB::beginTransaction();
        try
        {
            $path = 'images/profile/';

            $user = new User();
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            # make unique user number
            $user->user_number = mt_rand(10000000, 99999999);
            $user->branch_id = $branchId;
            $user->password = Hash::make($validatedData['password']);
            $user->status = true;

            # make profile image
            if (isset($validatedData['image']))
            {
                $profileImage = [
                    'image' => $validatedData['image'],
                ];

                $images = $this->uploadService->upload($profileImage, $path);
                $user->image = $images['image'];
            }



            $user->save();

            # only admin can assign roles
            if (auth()->user()->hasRole('Super Admin'))
            {
                # assign roles if roles exists
                if (isset($validatedData['roles']))
                {
                    $user->assignRole($validatedData['roles']);
                }
            }



            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
            ]);
        }
        catch (\Throwable $th)
        {
            DB::rollBack();
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load('roles');
        $roleNames = $user->roles->pluck('name');

        $roles = Role::all();
        $branches = Branch::orderBy('name')->get();
        return view('users.edit', compact('user', 'roles', 'roleNames', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        $validatedData = $request->validated();

        $roleNames = $user->roles->pluck('name')->all();
        if (auth()->user()->hasRole('Super Admin') && isset($validatedData['roles']))
        {
            $roleNames = $validatedData['roles'];
        }

        $branchId = $this->resolveBranchId($request, $roleNames);

        DB::beginTransaction();
        try
        {
            $path = 'images/profile/';
            $oldImage = $user->image;

            $user->name = $validatedData['name'];
            $user->branch_id = $branchId;

            if (isset($validatedData['email']))
            {
                $user->email = $validatedData['email'];
                $user->email_verified_at = null;
            }


            if (isset($validatedData['password']))
            {
                $user->password = Hash::make($validatedData['password']);
            }

            if (auth()->user()->can('users-edit'))
            {
                $user->status = $request->boolean('status');
            }

            # make profile image
            if (isset($validatedData['image']))
            {
                $profileImage = [
                    'image' => $validatedData['image'],
                ];

                $images = $this->uploadService->upload($profileImage, $path);
                $user->image = $images['image'];

                # remove old image if exists
                if ($oldImage != null)
                {
                    $path = $path . $oldImage;
                    if (Storage::disk('public')->exists($path))
                    {
                        Storage::disk('public')->delete($path);
                    }
                }
            }



            $user->save();

            # only admin can assign roles
            if (auth()->user()->hasRole('Super Admin'))
            {
                # assign roles if roles exists
                if (isset($validatedData['roles']))
                {
                    $user->syncRoles($validatedData['roles']);
                }
            }



            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
            ]);
        }
        catch (\Throwable $th)
        {
            DB::rollBack();
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (auth()->user()->id == $user->id)
        {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete yourself',
            ], 400);
        }

        DB::beginTransaction();
        try
        {
            $path = 'images/profile/';
            $oldImage = $user->image;

            $user->delete();

            # remove old image if exists
            if ($oldImage != null)
            {
                $path = $path . $oldImage;
                if (Storage::disk('public')->exists($path))
                {
                    Storage::disk('public')->delete($path);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);
        }
        catch (\Throwable $th)
        {
            DB::rollBack();
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 400);
        }
    }
}
