<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AboutController extends Controller
{
    public function index()
    {
        $settings = Setting::whereIn('setting_group', ['about_section', 'about_page'])
            ->get()
            ->keyBy('key');

        return view('backend.about.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'about_kicker'      => 'nullable|string|max:100',
            'about_title'       => 'required|string|max:255',
            'about_lead'        => 'nullable|string|max:500',
            'about_paragraph'   => 'nullable|string',
            'about_feature_1_icon'  => 'nullable|string|max:100',
            'about_feature_1_text'  => 'nullable|string|max:100',
            'about_feature_2_icon'  => 'nullable|string|max:100',
            'about_feature_2_text'  => 'nullable|string|max:100',
            'about_exp_number'  => 'nullable|string|max:20',
            'about_exp_text'    => 'nullable|string|max:100',
            'about_cta_url'     => 'nullable|url|max:500',
            'about_image'       => 'nullable|image|mimes:webp,png,jpg,jpeg|max:3072',
        ]);

        DB::beginTransaction();
        try {
            $fields = [
                'about_kicker', 'about_title', 'about_lead', 'about_paragraph',
                'about_feature_1_icon', 'about_feature_1_text',
                'about_feature_2_icon', 'about_feature_2_text',
                'about_exp_number', 'about_exp_text', 'about_cta_url',
            ];

            foreach ($fields as $key) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'setting_group' => 'about_section',
                        'value'         => $request->input($key, ''),
                        'user_id'       => Auth::id(),
                    ]
                );
            }

            // Handle image upload separately
            if ($request->hasFile('about_image')) {
                $file      = $request->file('about_image');
                $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/about'), $imageName);

                // Delete old image if it exists
                $old = Setting::where('key', 'about_image')->first();
                if ($old && $old->value && file_exists(public_path('uploads/about/' . $old->value))) {
                    unlink(public_path('uploads/about/' . $old->value));
                }

                Setting::updateOrCreate(
                    ['key' => 'about_image'],
                    [
                        'setting_group' => 'about_section',
                        'value'         => $imageName,
                        'user_id'       => Auth::id(),
                    ]
                );
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'About section updated successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
