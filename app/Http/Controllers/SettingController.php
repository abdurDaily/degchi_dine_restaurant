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
use App\Support\SeoSettings;

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

    public function seoSetting()
    {
        $settings = Setting::where('setting_group', 'seo_setting')
            ->pluck('value', 'key');

        $ogImageUrl = $settings->get('seo_og_image')
            ? asset('storage/seo/' . $settings->get('seo_og_image'))
            : null;

        return view('settings.seo-setting', compact('settings', 'ogImageUrl'));
    }

    public function seoSettingStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'seo_site_name' => 'required|string|max:120',
            'seo_default_title' => 'required|string|max:160',
            'seo_default_description' => 'required|string|max:320',
            'seo_default_keywords' => 'nullable|string|max:500',
            'seo_robots_default' => 'nullable|string|max:80',
            'seo_og_type' => 'nullable|string|max:40',
            'seo_twitter_card' => 'nullable|string|max:40',
            'seo_twitter_handle' => 'nullable|string|max:80',
            'seo_canonical_url' => 'nullable|url|max:255',
            'seo_google_analytics_id' => 'nullable|string|max:40',
            'seo_google_tag_manager_id' => 'nullable|string|max:40',
            'seo_facebook_pixel_id' => 'nullable|string|max:40',
            'seo_head_scripts' => 'nullable|string|max:5000',
            'seo_robots_txt' => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $fields = [
                'seo_site_name',
                'seo_default_title',
                'seo_default_description',
                'seo_default_keywords',
                'seo_robots_default',
                'seo_og_type',
                'seo_twitter_card',
                'seo_twitter_handle',
                'seo_canonical_url',
                'seo_google_analytics_id',
                'seo_google_tag_manager_id',
                'seo_facebook_pixel_id',
                'seo_head_scripts',
                'seo_robots_txt',
            ];

            foreach ($fields as $key) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'setting_group' => 'seo_setting',
                        'value' => $request->input($key, ''),
                        'user_id' => Auth::id(),
                    ]
                );
            }

            DB::commit();
            SeoSettings::clearCache();

            return response()->json([
                'status' => 'success',
                'message' => 'SEO settings updated successfully.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ], 400);
        }
    }

    public function seoOgImageUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'seo_og_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:1024|dimensions:min_width=600,min_height=315',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        $existing = Setting::where('key', 'seo_og_image')->first();
        $path = 'seo/';
        $images = $this->uploadService->upload($request->only('seo_og_image'), $path);

        if ($existing && $existing->value) {
            $oldPath = $path . $existing->value;
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            $existing->value = $images['seo_og_image'];
            $existing->save();
        } else {
            Setting::create([
                'key' => 'seo_og_image',
                'setting_group' => 'seo_setting',
                'value' => $images['seo_og_image'],
                'user_id' => Auth::id(),
            ]);
        }

        SeoSettings::clearCache();

        return response()->json([
            'status' => 'success',
            'message' => 'Open Graph image uploaded successfully.',
            'url' => asset('storage/seo/' . $images['seo_og_image']),
        ]);
    }
}
