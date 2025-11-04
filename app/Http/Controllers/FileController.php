<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use ZipArchive;

class FileController extends Controller
{
    protected $uploadService;

    public function __construct(FileUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        try {
            if (!$request->hasFile('file')) {
                $maxSize = $this->uploadService->getMaxUploadSizeReadable();
                return response()->json([
                    'success' => false,
                    'message' => "Error: El archivo es demasiado grande. Límite máximo: {$maxSize}"
                ], 400);
            }

            $uploadedFile = $request->file('file');

            if ($this->uploadService->exceedsMaxSize($uploadedFile->getSize())) {
                $maxSize = $this->uploadService->getMaxUploadSizeReadable();
                return response()->json([
                    'success' => false,
                    'message' => "Error: El archivo es demasiado grande. Límite máximo: {$maxSize}"
                ], 400);
            }

            $maxSizeKb = $this->uploadService->getMaxUploadSize() / 1024;
            $request->validate([
                'file' => "required|file|max:{$maxSizeKb}",
            ]);

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

        } catch (\Illuminate\Http\Exceptions\PostTooLargeException $e) {
            $maxSize = $this->uploadService->getMaxUploadSizeReadable();
            return response()->json([
                'success' => false,
                'message' => "Error: El archivo excede el límite permitido: {$maxSize}"
            ], 400);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir el archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    private function isExtensionAllowed(string $extension): bool
    {
        $blockedExtensions = $this->uploadService->getBlockedExtensions();
        return !in_array(strtolower($extension), $blockedExtensions);
    }

    private function validateZipContents($zipFile): array
    {
        $zip = new ZipArchive();
        $tempPath = $zipFile->getPathname();
        
        if ($zip->open($tempPath) === TRUE) {
            $blockedExtensions = $this->uploadService->getBlockedExtensions();
            
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

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus archivos.');
        }

        $user = Auth::user();
        $files = $user->files()->orderBy('created_at', 'desc')->get();
        
        $maxSize = $this->uploadService->getMaxUploadSizeReadable();
        
        return view('dashboard.user', compact('files', 'maxSize'));
    }
}