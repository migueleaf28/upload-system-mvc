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
                return response()->json([
                    'success' => false,
                    'message' => "Por favor selecciona un archivo"
                ], 400);
            }

            $uploadedFile = $request->file('file');
            $fileSize = $uploadedFile->getSize();

            $quotaCheck = $this->uploadService->canUserUploadFile($user, $fileSize);
            
            if (!$quotaCheck['allowed']) {
                $maxSize = $this->uploadService->getUserMaxFileSizeReadable($user);
                $available = $this->uploadService->formatBytes($quotaCheck['available']);
                
                if ($quotaCheck['exceeds_quota']) {
                    $message = "Error: No tienes espacio suficiente. Disponible: {$available}, Archivo: {$this->uploadService->formatBytes($fileSize)}";
                } else {
                    $message = "Error: El archivo es demasiado grande. Máximo permitido: {$maxSize}";
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }

            if ($this->uploadService->exceedsMaxSize($fileSize)) {
                $maxSize = $this->uploadService->getGlobalMaxFileSizeReadable();
                return response()->json([
                    'success' => false,
                    'message' => "Error: El archivo excede el límite global del sistema: {$maxSize}"
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
                'size' => $fileSize,
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Archivo subido con éxito.',
                'file' => [
                    'id' => $file->id,
                    'original_name' => $file->original_name,
                    'mime_type' => $file->mime_type,
                    'formatted_size' => $file->formatted_size
                ]
            ]);

        } catch (\Illuminate\Http\Exceptions\PostTooLargeException $e) {
            $maxSize = $this->uploadService->getGlobalMaxFileSizeReadable();
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
        
        $totalUsed = $user->files()->sum('size');
        $storageLimit = $user->getEffectiveStorageLimit();
        $formattedUsed = number_format($totalUsed / 1048576, 2) . ' MB';
        $formattedLimit = number_format($storageLimit / 1048576, 2) . ' MB';
        $percentage = $storageLimit > 0 ? min(100, ($totalUsed / $storageLimit) * 100) : 0;
        
        $maxFileSize = $this->uploadService->getUserMaxFileSizeReadable($user);
        $globalMaxSize = $this->uploadService->getGlobalMaxFileSizeReadable();
        
        return view('dashboard.user', compact(
            'files', 
            'maxFileSize',
            'globalMaxSize',
            'totalUsed',
            'formattedUsed',
            'formattedLimit', 
            'percentage'
        ));
    }

    public function destroy(File $file)
    {
        try {
            if ($file->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para eliminar este archivo'
                ], 403);
            }

            Storage::delete($file->path);

            $file->delete();

            return response()->json([
                'success' => true,
                'message' => 'Archivo eliminado correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el archivo: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getStorageInfo()
    {
        $user = Auth::user();
        
        $totalUsed = $user->files()->sum('size');
        $storageLimit = $user->getEffectiveStorageLimit();
        $formattedUsed = number_format($totalUsed / 1048576, 2) . ' MB';
        $formattedLimit = number_format($storageLimit / 1048576, 2) . ' MB';
        $percentage = $storageLimit > 0 ? min(100, ($totalUsed / $storageLimit) * 100) : 0;
        $maxFileSize = $this->uploadService->getUserMaxFileSizeReadable($user);
        
        return response()->json([
            'success' => true,
            'storage_info' => [
                'used' => $formattedUsed,
                'limit' => $formattedLimit,
                'percentage' => $percentage,
                'max_file_size' => $maxFileSize,
                'used_bytes' => $totalUsed,
                'limit_bytes' => $storageLimit
            ]
        ]);
    }
}