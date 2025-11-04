<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'storage_limit'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_group');
    }

    public function getStorageQuotaInMbAttribute()
    {
        return $this->storage_limit ? round($this->storage_limit / 1048576) : null;
    }
}