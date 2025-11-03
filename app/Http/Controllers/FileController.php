<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use ZipArchive;

class FileController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $uploadedFile = $request->file('file');

        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        try {
            $currentUsage = $user->storageUsed();
            $newFileSize = $uploadedFile->getSize();
            $quota = $user->storageQuota();

            if ($currentUsage + $newFileSize > $quota) {
                return response()->json([
                    'success' => false,
                    'message' => "Error: Cuota de almacenamiento (" . number_format($quota / 1024 / 1024, 2) . " MB) excedida."
                ], 400);
            }

            $extension = strtolower($uploadedFile->getClientOriginalExtension());
            if (!$this->isExtensionAllowed($extension)) {
                return response()->json([
                    'success' => false,
                    'message' => "Error: El tipo de archivo '.{$extension}' no está permitido"
                ], 400);
            }

            if ($extension === 'zip') {
                $zipValidation = $this->validateZipContents($uploadedFile);
                if (!$zipValidation['allowed']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Error: El archivo '{$zipValidation['blocked_file']}' dentro del ZIP no está permitido"
                    ], 400);
                }
            }

            $path = $uploadedFile->store('uploads');
            $filename = basename($path);

            $file = $user->files()->create([
                'filename' => $filename,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'mime_type' => $uploadedFile->getMimeType(),
                'path' => $path,
                'size' => $newFileSize,
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Archivo subido con éxito.',
                'file' => [
                    'original_name' => $file->original_name,
                    'mime_type' => $file->mime_type,
                    'formatted_size' => $file->formatted_size
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function isExtensionAllowed(string $extension): bool
    {
        $blockedExtensions = $this->getBlockedExtensions();
        return !in_array(strtolower($extension), $blockedExtensions);
    }


    private function validateZipContents($zipFile): array
    {
        $zip = new ZipArchive();
        $tempPath = $zipFile->getPathname();
        
        if ($zip->open($tempPath) === TRUE) {
            $blockedExtensions = $this->getBlockedExtensions();
            
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                
                if (substr($filename, -1) === '/') {
                    continue;
                }
                
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                
                if (in_array(strtolower($extension), $blockedExtensions)) {
                    $zip->close();
                    return [
                        'allowed' => false,
                        'blocked_file' => $filename
                    ];
                }
            }
            $zip->close();
        }
        
        return ['allowed' => true];
    }

    private function getBlockedExtensions(): array
    {
        if (class_exists('App\Models\Setting')) {
            return Setting::getBlockedExtensions();
        }
        
        $setting = \Illuminate\Support\Facades\DB::table('settings')
                    ->where('key', 'blocked_extensions')
                    ->first();
        
        if ($setting && $setting->value) {
            $extensions = json_decode($setting->value, true);
            return is_array($extensions) ? $extensions : [];
        }
        
        return ['exe', 'bat', 'cmd', 'sh', 'php', 'js', 'html', 'htm', 'phtml', 'py', 'pl', 'jar', 'war', 'apk'];
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus archivos.');
        }

        $user = Auth::user();
        $files = $user->files()->orderBy('created_at', 'desc')->get();
        return view('dashboard.user', compact('files'));
    }
}