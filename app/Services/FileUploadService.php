<?php

namespace App\Services;

use App\Models\Setting;

class FileUploadService
{
    public function getMaxUploadSize(): int
    {
        $customLimit = $this->getCustomMaxSize();
        if ($customLimit > 0) {
            return min($customLimit, $this->getSystemMaxSize());
        }

        return $this->getSystemMaxSize();
    }

    public function getSystemMaxSize(): int
    {
        return min(
            $this->sizeToBytes(ini_get('upload_max_filesize')),
            $this->sizeToBytes(ini_get('post_max_size')),
            $this->sizeToBytes(ini_get('memory_limit')) / 4
        );
    }

    public function getCustomMaxSize(): int
    {
        $customSize = Setting::getValue('max_upload_size', '0');
        return $this->sizeToBytes($customSize);
    }

    public function setCustomMaxSize(string $size): bool
    {
        return Setting::setValue('max_upload_size', $size);
    }

    public function getMaxUploadSizeReadable(): string
    {
        $bytes = $this->getMaxUploadSize();
        return $this->bytesToSize($bytes);
    }

    public function exceedsMaxSize(int $fileSize): bool
    {
        return $fileSize > $this->getMaxUploadSize();
    }

    public function getBlockedExtensions(): array
    {
        return Setting::getArrayValue('blocked_extensions', []);
    }

    public function updateBlockedExtensions(array $extensions): bool
    {
        return Setting::setArrayValue('blocked_extensions', $extensions);
    }

    public function sizeToBytes(string $size): int
    {
        if (empty($size) || $size === '0') return 0;

        $unit = strtolower(preg_replace('/[^a-z]/i', '', $size));
        $size = (float) preg_replace('/[^0-9\.]/', '', $size);
        
        $units = [
            'b' => 1,
            'k' => 1024,
            'm' => 1024 * 1024,
            'g' => 1024 * 1024 * 1024,
        ];

        $unit = $unit ?: 'm';
        $multiplier = $units[$unit] ?? $units['m'];

        return (int) ($size * $multiplier);
    }


    public function bytesToSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getUploadLimits(): array
    {
        return [
            'system_limits' => [
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'memory_limit' => ini_get('memory_limit'),
            ],
            'custom_max_size' => $this->getCustomMaxSize() > 0 ? $this->bytesToSize($this->getCustomMaxSize()) : 'No establecido',
            'effective_max_size' => $this->getMaxUploadSizeReadable(),
            'blocked_extensions_count' => count($this->getBlockedExtensions()),
        ];
    }
}