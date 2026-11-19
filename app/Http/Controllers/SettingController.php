<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'store_name'     => 'required|string|max:255',
            'store_address'  => 'required|string',
            'store_phone'    => 'required|string|max:20',
            'receipt_footer' => 'nullable|string',
            'store_logo'     => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
        ]);

        // Simpan pengaturan teks
        $keys = ['store_name', 'store_address', 'store_phone', 'receipt_footer'];
        foreach ($keys as $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($key)]
            );
        }

        // Simpan upload Logo Toko jika ada file baru
        if ($request->hasFile('store_logo')) {
            $oldLogo = Setting::where('key', 'store_logo')->value('value');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $logoPath = $request->file('store_logo')->store('logos', 'public');
            Setting::updateOrCreate(
                ['key' => 'store_logo'],
                ['value' => $logoPath]
            );
        }

        if (class_exists('App\Helpers\LogHelper')) {
            LogHelper::log('Pengaturan', 'Memperbarui pengaturan informasi & logo toko.');
        }

        return back()->with('success', 'Pengaturan toko & logo berhasil diperbarui!');
    }
}