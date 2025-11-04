<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;

class FileUploadService
{
    public function getUserMaxFileSize(User $user): int
    {
        $userLimit = $user->getEffectiveStorageLimit();
        $usedStorage = $user->files()->sum('size');
        $available = $userLimit - $usedStorage;
        
        $available = max(0, $available);
        
        $globalLimit = $this->getGlobalMaxFileSize();
        
        return min($available, $globalLimit);
    }

    public function getGlobalMaxFileSize(): int
    {
        return Setting::getValue('max_file_size', 26214400);
    }

    public function getUserMaxFileSizeReadable(User $user): string
    {
        $bytes = $this->getUserMaxFileSize($user);
        return $this->formatBytes($bytes);
    }

    public function getGlobalMaxFileSizeReadable(): string
    {
        $bytes = $this->getGlobalMaxFileSize();
        return $this->formatBytes($bytes);
    }

    public function canUserUploadFile(User $user, int $fileSize): array
    {
        $userLimit = $user->getEffectiveStorageLimit();
        $usedStorage = $user->files()->sum('size');
        $available = max(0, $userLimit - $usedStorage);
        $maxFileSize = $this->getUserMaxFileSize($user);

        return [
            'allowed' => $fileSize <= $available && $fileSize <= $maxFileSize,
            'user_limit' => $userLimit,
            'used_storage' => $usedStorage,
            'available' => $available,
            'max_file_size' => $maxFileSize,
            'file_size' => $fileSize,
            'exceeds_quota' => $fileSize > $available,
            'exceeds_global' => $fileSize > $this->getGlobalMaxFileSize()
        ];
    }

    public function exceedsMaxSize(int $fileSize): bool
    {
        return $fileSize > $this->getGlobalMaxFileSize();
    }

    public function getBlockedExtensions(): array
    {
        $extensions = Setting::getValue('blocked_extensions', '');
        return $extensions ? explode(',', $extensions) : [];
    }

    public function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}