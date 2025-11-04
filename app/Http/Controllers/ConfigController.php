<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class ConfigController extends Controller
{

    public function adminDashboard()
    {
        $userDefaultStorage = Setting::getValue('user_default_storage', 104857600);
        $maxFileSize = Setting::getValue('max_file_size', 26214400);
        $blockedExtensions = Setting::getBlockedExtensions();
        
        return view('dashboard.admin', compact('userDefaultStorage', 'maxFileSize', 'blockedExtensions'));
    }

    public function updateConfig(Request $request)
    {
        $request->validate([
            'max_file_size' => ['required', 'integer', 'min:1048576'],
            'user_default_storage' => ['required', 'integer', 'min:1048576'],
        ]);

        Setting::setValue('max_file_size', $request->max_file_size);
        Setting::setValue('user_default_storage', $request->user_default_storage);

        return redirect()->route('dashboard.admin')
            ->with('success', 'Configuraciones actualizadas exitosamente.');
    }

    public function addBlockedExtension(Request $request)
    {
        $request->validate([
            'extension' => [
                'required', 
                'string', 
                'max:10',
                'regex:/^[a-zA-Z0-9]+$/',
            ],
        ]);

        $extension = strtolower(trim($request->extension));
        $blockedExtensions = Setting::getBlockedExtensions();

        if (in_array($extension, $blockedExtensions)) {
            return redirect()->route('dashboard.admin')
                ->with('error', 'Esta extensión ya está bloqueada.');
        }

        $blockedExtensions[] = $extension;
        Setting::updateBlockedExtensions($blockedExtensions);

        return redirect()->route('dashboard.admin')
            ->with('success', 'Extensión bloqueada agregada.');
    }

    public function removeBlockedExtension($extension)
    {
        $blockedExtensions = Setting::getBlockedExtensions();
        
        $blockedExtensions = array_filter($blockedExtensions, function($ext) use ($extension) {
            return $ext !== $extension;
        });

        Setting::updateBlockedExtensions(array_values($blockedExtensions));

        return redirect()->route('dashboard.admin')
            ->with('success', 'Extensión eliminada.');
    }
}