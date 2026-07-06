<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SettingsController extends Controller
{
    private const GROUPS = [
        'general' => [
            'property_name' => ['label' => 'Nama Penginapan', 'type' => 'string', 'public' => true],
            'short_description' => ['label' => 'Deskripsi Singkat', 'type' => 'string', 'public' => true],
            'about_content' => ['label' => 'Konten Tentang', 'type' => 'string', 'public' => true],
        ],
        'contact' => [
            'whatsapp' => ['label' => 'Nomor WhatsApp', 'type' => 'string', 'public' => true],
            'email' => ['label' => 'Email', 'type' => 'string', 'public' => true],
            'address' => ['label' => 'Alamat', 'type' => 'string', 'public' => true],
            'map_url' => ['label' => 'URL Peta (Google Maps)', 'type' => 'url', 'public' => true],
        ],
        'booking' => [
            'check_in_time' => ['label' => 'Jam Check-in', 'type' => 'time', 'public' => true],
            'check_out_time' => ['label' => 'Jam Check-out', 'type' => 'time', 'public' => true],
        ],
        'seo' => [
            'meta_title' => ['label' => 'Meta Title', 'type' => 'string', 'public' => true],
            'meta_description' => ['label' => 'Meta Description', 'type' => 'string', 'public' => true],
        ],
    ];

    public function edit(string $group): View
    {
        if (!isset(self::GROUPS[$group])) {
            abort(404);
        }

        $fields = self::GROUPS[$group];
        $values = [];
        foreach ($fields as $key => $config) {
            $values[$key] = Setting::get($group, $key, '');
        }

        return view('admin.settings.edit', compact('group', 'fields', 'values'));
    }

    public function update(Request $request, string $group): RedirectResponse
    {
        if (!isset(self::GROUPS[$group])) {
            abort(404);
        }

        $fields = self::GROUPS[$group];
        $adminId = Auth::guard('admin')->id();

        foreach ($fields as $key => $config) {
            if ($request->has($key)) {
                Setting::set(
                    $group,
                    $key,
                    $request->input($key),
                    $adminId,
                    $config['type'],
                    $config['public'] ?? false
                );
            }
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
