<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    private $uploadService;
    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function profile()
    {
        return view('auth.profile');
    }

    public function profileUpdate(ProfileRequest $request)
    {
        $validatedData = $request->validated();

        DB::beginTransaction();
        try
        {
            $user = auth()->user();
            $oldImage = $user->image;
            $path = 'images/profile/';
            $user->name = $validatedData['name'];

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

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
            ]);
        }
        catch (\Throwable $th)
        {
            DB::rollBack();
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function passwordUpdate(PasswordRequest $request)
    {
        $validatedData = $request->validated();

        $user = auth()->user();

        DB::beginTransaction();
        try
        {
            // check password
            if (Hash::check($validatedData['current_password'], $user->password))
            {
                $user->password = Hash::make($validatedData['password']);
                $user->save();
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Password updated successfully',
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password not matched',
                ], 400);
            }
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

    public function liveSearch(Request $request)
    {
        $searchParams = [
            'per_page'  => 30,
            'select'    => 'id,name,user_number',
        ];

        $users = User::select($searchParams['select'])->orderBy('id', 'desc')->get();

        foreach ($users as $user)
        {
            $response[] = array(
                "id"    => $user['id'],
                "text"  => $user['name'] . '-' . $user['user_number'],
            );
        }

        return response()->json($response);
    }
}
