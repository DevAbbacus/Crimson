<?php

namespace App\Models;

use App\Enums\CRMSSyncStatus;
use App\Enums\CurrentRmsSyncProcess;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'api_token',
        'sub_domain'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    public function syncStatus()
    {
        return $this->hasOne(SyncStatus::class);
    }

    public function getLastSyncDate()
    {
        if ($this->syncStatus()->exists())
            return $this->syncStatus()->first()->last_sync;
        return null;
    }

    public function getCurrentSyncStatus()
    {
        if ($this->syncStatus()->exists()) {
            $status = $this->syncStatus()->first()->status ?: 0;
            return CRMSSyncStatus::NAMES[$status];
        }
        return CRMSSyncStatus::NAMES[-1];
    }

    public function getCRMSStatus()
    {
        if (isset($this->sub_domain) && !empty($this->sub_domain)) {
            if ($this->syncStatus()->exists()) {
                return CurrentRmsSyncProcess::READY;
            } else {
                return CurrentRmsSyncProcess::SYNC;
            }
        }
        return CurrentRmsSyncProcess::SETUP;
    }
}
