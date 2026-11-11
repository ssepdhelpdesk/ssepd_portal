<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class DisabilityDayRegistration extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'disability_day_registrations';

    protected $fillable = [
        'registration_no',
        'organization_name',
        'contact_person',
        'email',
        'phone',
        'purpose',
        'at',
        'po',
        'ps',
        'district_id',
        'district_name',
        'state_id',
        'state_name',
        'pin',
        'applied_at',
        'allotted_stall',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'applied_at'   => 'datetime',
        'district_id'  => 'integer',
        'state_id'     => 'integer',
        'created_by'   => 'integer',
        'updated_by'   => 'integer',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    /**
     * District relationship (references District master).
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    /**
     * State relationship (references State master).
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    /**
     * OTP verification requests for this registration.
     */
    public function otps(): HasMany
    {
        return $this->hasMany(DisabilityDayOtp::class, 'registration_id');
    }

    /**
     * User who created the record (if created via dashboard).
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who last updated the record.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for searching across primary text fields.
     */
    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        $term = trim($term);
        return $query->where(function ($q) use ($term) {
            $q->where('registration_no', 'like', "%{$term}%")
              ->orWhere('organization_name', 'like', "%{$term}%")
              ->orWhere('contact_person', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%")
              ->orWhere('district_name', 'like', "%{$term}%")
              ->orWhere('allotted_stall', 'like', "%{$term}%");
        });
    }

    /**
     * Scope for filtering by stall allotment state.
     */
    public function scopeFilterAllotment($query, ?string $state)
    {
        if ($state === 'allotted') {
            return $query->whereNotNull('allotted_stall')->where('allotted_stall', '!=', '');
        }

        if ($state === 'not_allotted' || $state === 'unallotted') {
            return $query->where(function ($q) {
                $q->whereNull('allotted_stall')->orWhere('allotted_stall', '');
            });
        }

        return $query;
    }

    /**
     * Generates a collision-resistant, server-side unique registration number.
     * Format: SSEPD/DISABILITYDAY/DD/MM/YYYY/XXXXXX
     */
    public static function generateUniqueRegistrationNo(Carbon $appliedAt): string
    {
        $dateComponent = $appliedAt->format('d/m/Y');
        $prefix = "SSEPD/DISABILITYDAY/{$dateComponent}/";

        $maxRetries = 20;
        for ($i = 0; $i < $maxRetries; $i++) {
            // 6 cryptographically secure random digits: 000000 - 999999
            $randomDigits = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $candidate = $prefix . $randomDigits;

            if (!static::where('registration_no', $candidate)->exists()) {
                return $candidate;
            }
        }

        throw new \RuntimeException('Failed to generate a unique registration number after multiple attempts.');
    }

    /**
     * Helper to check if a stall has been allotted.
     */
    public function isAllotted(): bool
    {
        return !empty($this->allotted_stall);
    }

    /**
     * Get normalized current stall number as integer, or null.
     */
    public function getCurrentStallNumber(): ?int
    {
        if (empty($this->allotted_stall)) {
            return null;
        }
        preg_match('/\d+/', (string) $this->allotted_stall, $matches);
        return isset($matches[0]) ? (int) $matches[0] : null;
    }

    /**
     * Get array of available stall numbers between 1 and 100.
     * Optionally excludes a specific registration ID (e.g. current registration's stall is considered available to itself).
     *
     * @param int|null $excludeRegistrationId
     * @return array<int>
     */
    public static function getAvailableStallNumbers(?int $excludeRegistrationId = null): array
    {
        $query = static::whereNotNull('allotted_stall')
            ->where('allotted_stall', '!=', '');

        if ($excludeRegistrationId) {
            $query->where('id', '!=', $excludeRegistrationId);
        }

        $assigned = $query->pluck('allotted_stall')
            ->map(function ($val) {
                preg_match('/\d+/', (string) $val, $matches);
                return isset($matches[0]) ? (int) $matches[0] : null;
            })
            ->filter()
            ->unique()
            ->toArray();

        $available = [];
        for ($i = 1; $i <= 100; $i++) {
            if (!in_array($i, $assigned, true)) {
                $available[] = $i;
            }
        }

        return $available;
    }
}

