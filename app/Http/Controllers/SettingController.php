<?php

namespace App\Http\Controllers;

use Session;
use App\Models\Setting;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Services\UploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Settings\EmailSettingRequest;
use App\Http\Requests\Settings\PusherSettingRequest;
use App\Http\Requests\Settings\GeneralSettingRequest;
use App\Http\Requests\Settings\OffDayMinManpowerRequest;
use App\Http\Requests\Settings\ThemeCustomizationRequest;

class SettingController extends Controller
{
    private $uploadService;
    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }
    /**
     * Display a listing of the resource.
     */
    public function settings()
    {
        return view('settings.system-setting');
    }

    /**
     * Theme customization
     */
    public function customize(ThemeCustomizationRequest $request)
    {
        DB::beginTransaction();
        try
        {
            foreach ($request->all() as $key => $value)
            {
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'setting_group' => 'theme_customization',
                        'value' => $value,
                        'user_id' => Auth::user()->id
                    ]
                );
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Customization save sucessfully',
            ], 200);
        }
        catch (\Throwable $th)
        {
            //throw $th;
            dd($th);
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ], 400);
        }
    }

    /**
     * General setting view generate.
     */
    public function generalSetting(Request $request)
    {
        $currency_list = Currency::where('is_active', true)->get();
        return view('settings.general-setting', compact('currency_list'));
    }

    /**
     * General setting store
     */
    public function generalSettingStore(GeneralSettingRequest $request)
    {
        DB::beginTransaction();
        try
        {
            foreach ($request->all() as $key => $value)
            {
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'setting_group' => 'general_setting',
                        'value' => $value,
                        'user_id' => Auth::user()->id
                    ]
                );
            }
            DB::commit();

            $generalSettings = Setting::whereIn('key', array_keys($request->all()))->get();
            foreach ($generalSettings as $key => $value)
            {
                session()->put($value->key, $value->value);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Setting updated..',
            ], 200);
        }
        catch (\Throwable $th)
        {
            //throw $th;
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ], 400);
        }
    }

    /**
     * Upload logo for the softwae
     */
    public function logoUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:200',
        ]);

        if ($validator->fails())
        {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        $logo = Setting::where('key', 'logo')->first();
        $path = 'logo/';
        $images = $this->uploadService->upload($request->only('logo'), $path);

        if ($logo != NULL)
        {

            $path = $path . $logo->value;
            if (Storage::disk('public')->exists($path))
            {
                Storage::disk('public')->delete($path . $logo->value);
            }

            $logo->value = $images['logo'];
            $logo->save();
        }
        else
        {
            $logo = Setting::create([
                'key' => 'logo',
                'setting_group' => 'general_setting',
                'value' => $images['logo'],
                'user_id' => Auth::user()->id
            ]);
        }

        $request->session()->put('logo', asset('storage/logo/' . $logo->value));

        return response()->json([
            'status' => 'success',
            'message' => 'Logo uploaded successfully',
        ]);
    }

    public function faviconUpload(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'favicon' => 'required|image|mimes:png,jpg,jpeg,ico|dimensions:min_width=16,min_height=16,max_width=48,max_height=48',
        ]);

        if ($validator->fails())
        {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        $favicon = Setting::where('key', 'favicon')->first();
        $path = 'favicon/';
        $images = $this->uploadService->upload($request->only('favicon'), $path);

        if ($favicon != NULL)
        {

            $path = $path . $favicon->value;
            if (Storage::disk('public')->exists($path))
            {
                Storage::disk('public')->delete($path . $favicon->value);
            }

            $favicon->value = $images['favicon'];
            $favicon->save();
        }
        else
        {
            $favicon = Setting::create([
                'key' => 'favicon',
                'setting_group' => 'general_setting',
                'value' => $images['favicon'],
                'user_id' => Auth::user()->id
            ]);
        }

        $request->session()->put('favicon', asset('storage/favicon/' . $favicon->value));

        return response()->json([
            'status' => 'success',
            'message' => 'Favicon uploaded successfully',
        ]);
    }

    /**
     * Email setting view generate
     */
    public function emailSetting(Request $request)
    {
        if ($request->ajax())
        {
            $email_setting = $request->emailType;
            $settings = Setting::select('key', 'value')->where('setting_group', 'email_setting')->get()->toArray();
            $keys = array_column($settings, 'key');
            $values = array_column($settings, 'value');
            $settings = array_combine($keys, $values);
            return view('settings.email-fields', compact('email_setting', 'settings'));
        }
        $emailType = Setting::select('key', 'value')->where('key', 'email')->first();

        return view('settings.email-setting', compact('emailType'));
    }

    /**
     * Email fields view generate
     */
    public function emailFields(Request $request)
    {
        dd($request->all());
    }

    /**
     * Email setting store
     */
    public function emailSettingUpdate(EmailSettingRequest $request)
    {
        DB::beginTransaction();
        try
        {
            foreach ($request->all() as $key => $value)
            {
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'setting_group' => 'email_setting',
                        'value' => $value,
                        'user_id' => Auth::user()->id
                    ]
                );
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Email setting updated..',
            ], 200);
        }
        catch (\Throwable $th)
        {
            //throw $th;
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ], 400);
        }
    }

    /**
     * Pusher setting
     */
    public function pusherSetting()
    {
        $data['settings'] = Setting::select('key', 'value')->where('setting_group', 'pusher_setting')->get();
        return view('settings.pusher-setting', $data);
    }

    /**
     * Pusher setting store
     */
    public function pusherSettingStore(PusherSettingRequest $request)
    {
        DB::beginTransaction();
        try
        {
            foreach ($request->all() as $key => $value)
            {
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'setting_group' => 'pusher_setting',
                        'value' => $value,
                        'user_id' => Auth::user()->id
                    ]
                );
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Pusher setting updated..',
            ], 200);
        }
        catch (\Throwable $th)
        {
            //throw $th;
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ], 400);
        }
    }

    public function minimumBusDinningManpower()
    {
        $data['settings'] = Setting::select('key', 'value')->where('setting_group', 'offday_minimum_manpower')->get();
        return view('settings.minimum-bus-dinning-manpower')->with($data);
    }

    public function minimumBusDinningManpowerStore(OffDayMinManpowerRequest $request)
    {
        DB::beginTransaction();
        try
        {
            foreach ($request->all() as $key => $value)
            {
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'setting_group' => 'offday_minimum_manpower',
                        'value' => $value,
                        'user_id' => Auth::user()->id
                    ]
                );
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Off Day minimum manpower setting updated.',
            ], 200);
        }
        catch (\Throwable $th)
        {
            //throw $th;
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ], 400);
        }
    }

    public function sslcommerzSetting()
    {
        $settings = Setting::select('key', 'value')
            ->where('setting_group', 'sslcommerz_setting')
            ->get()
            ->pluck('value', 'key');

        return view('settings.sslcommerz-setting', [
            'storeId' => $settings->get('store_id', config('sslcommerz.store_id')),
            'storePassword' => $settings->get('store_password', config('sslcommerz.store_password')),
            'sandbox' => filter_var(
                $settings->get('sandbox', config('sslcommerz.sandbox') ? '1' : '0'),
                FILTER_VALIDATE_BOOLEAN
            ),
        ]);
    }

    public function sslcommerzSettingStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|string|max:255',
            'store_password' => 'required|string|max:255',
            'sandbox' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $payload = [
                'store_id' => $request->store_id,
                'store_password' => $request->store_password,
                'sandbox' => $request->input('sandbox', '0'),
            ];

            foreach ($payload as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'setting_group' => 'sslcommerz_setting',
                        'value' => $value,
                        'user_id' => Auth::id(),
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'SSLCommerz settings updated successfully.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ], 400);
        }
    }
}
