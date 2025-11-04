<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Models\File;
use App\Models\Group;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'storage_quota',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'user_group');
    }

    public function getEffectiveStorageLimit()
    {
        if ($this->storage_limit !== null) {
            return $this->storage_limit;
        }
        
        $groupLimits = $this->groups()->whereNotNull('storage_limit')->pluck('storage_limit');
        if ($groupLimits->isNotEmpty()) {
            return $groupLimits->min();
        }
        
        return Setting::getValue('user_default_storage', 104857600);
    }
    
    public function storageUsed()
    {
        return $this->files()->sum('size');
    }

    public function storageQuota()
    {
        if ($this->storage_quota) {
            return $this->storage_quota;
        }
        
        return 10 * 1024 * 1024;
    }
}