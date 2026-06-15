<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function index()
    {
        $settings = Setting::where('setting_group', 'contact_section')
            ->get()
            ->keyBy('key');

        return view('backend.contact.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'contact_section_title'    => 'nullable|string|max:100',
            'contact_section_subtitle' => 'nullable|string|max:255',
            'contact_restaurant_name'  => 'nullable|string|max:255',
            'contact_address'          => 'nullable|string|max:500',
            'contact_hours'            => 'nullable|string|max:255',
            'contact_phone'            => 'nullable|string|max:50',
            'contact_email'            => 'nullable|email|max:100',
            'contact_map_embed'        => 'nullable|string',
            'contact_map_link'         => 'nullable|url|max:500',
            'contact_facebook_url'     => 'nullable|url|max:500',
            'contact_instagram_url'    => 'nullable|url|max:500',
        ]);

        DB::beginTransaction();
        try {
            $fields = [
                'contact_section_title', 'contact_section_subtitle',
                'contact_restaurant_name', 'contact_address', 'contact_hours',
                'contact_phone', 'contact_email', 'contact_map_embed',
                'contact_map_link', 'contact_facebook_url', 'contact_instagram_url',
            ];

            foreach ($fields as $key) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'setting_group' => 'contact_section',
                        'value'         => $request->input($key, ''),
                        'user_id'       => Auth::id(),
                    ]
                );
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Contact section updated successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
