<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedExtension extends Model
{
    use HasFactory;

    protected $fillable = ['extension', 'name', 'reason', 'is_active'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function isBlocked($extension)
    {
        return static::active()->where('extension', strtolower($extension))->exists();
    }
}