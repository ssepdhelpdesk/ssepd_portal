<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

class DisabilityDayOtp extends Model
{
    use HasFactory;

    protected $table = 'disability_day_otps';

    protected $fillable = [
        'registration_id',
        'email',
        'otp_hash',
        'expires_at',
        'attempts',
        'verified_at',
        'ip_address',
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'verified_at' => 'datetime',
        'attempts'    => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(DisabilityDayRegistration::class, 'registration_id');
    }

    /**
     * Check if OTP has expired.
     */
    public function isExpired(): bool
    {
        return Carbon::now()->isAfter($this->expires_at);
    }

    /**
     * Check if maximum attempts have been exceeded.
     */
    public function isMaxAttemptsExceeded(int $max = 5): bool
    {
        return $this->attempts >= $max;
    }

    /**
     * Verify plain OTP against stored hash.
     */
    public function verify(string $plainOtp): bool
    {
        return Hash::check($plainOtp, $this->otp_hash);
    }
}

