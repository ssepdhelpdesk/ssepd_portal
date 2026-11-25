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
        'status',
        'financial_year',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'applied_at'     => 'datetime',
        'district_id'    => 'integer',
        'state_id'       => 'integer',
        'status'         => 'integer',
        'financial_year' => 'string',
        'created_by'     => 'integer',
        'updated_by'     => 'integer',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'deleted_at'     => 'datetime',
    ];

    /**
     * Model lifecycle events.
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->financial_year)) {
                $model->financial_year = static::calculateFinancialYear($model->applied_at ?: Carbon::now('Asia/Kolkata'));
            }
            if (!isset($model->status)) {
                $model->status = 1;
            }
            if (empty($model->applied_at)) {
                $model->applied_at = Carbon::now('Asia/Kolkata');
            }
        });
    }

    /**
     * Stalls allotted to this organization.
     */
    public function stallAllotments(): HasMany
    {
        return $this->hasMany(DisabilityDayStallAllotment::class, 'registration_id', 'id')->orderBy('stall_number');
    }

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
     * Format the complete registered address from non-empty address fields.
     */
    public function getCompleteAddressAttribute(): string
    {
        $parts = [];
        if (!empty(trim((string) $this->at))) {
            $parts[] = 'At: ' . trim((string) $this->at);
        }
        if (!empty(trim((string) $this->po))) {
            $parts[] = 'Post: ' . trim((string) $this->po);
        }
        if (!empty(trim((string) $this->ps))) {
            $parts[] = 'PS: ' . trim((string) $this->ps);
        }
        if (!empty(trim((string) $this->pin))) {
            $parts[] = 'PIN: ' . trim((string) $this->pin);
        }

        return !empty($parts) ? implode(', ', $parts) : '—';
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
     * Scope for active registrations only (status = 1).
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope for filtering by financial year.
     */
    public function scopeFinancialYear($query, ?string $fy)
    {
        if (!empty($fy)) {
            return $query->where('financial_year', $fy);
        }
        return $query;
    }

    /**
     * Determine the financial year (convention: April 1 to March 31).
     * Example: November 2026 => "2026-27"
     */
    public static function calculateFinancialYear(?Carbon $date = null): string
    {
        $date = $date ? $date->copy()->setTimezone('Asia/Kolkata') : Carbon::now('Asia/Kolkata');
        $year = (int) $date->year;
        if ($date->month >= 4) {
            $startYear = $year;
            $endYear = substr((string) ($year + 1), -2);
        } else {
            $startYear = $year - 1;
            $endYear = substr((string) $year, -2);
        }
        return "{$startYear}-{$endYear}";
    }

    /**
     * Helper to check if any stall has been allotted.
     */
    public function isAllotted(): bool
    {
        return count($this->getAllottedStallNumbers()) > 0 || !empty($this->allotted_stall);
    }

    /**
     * Get array of all allotted stall numbers (e.g. [5, 12]).
     * Checks stallAllotments relation first, falls back to parsing allotted_stall string.
     *
     * @return array<int>
     */
    public function getAllottedStallNumbers(): array
    {
        $numbers = [];
        if ($this->relationLoaded('stallAllotments')) {
            $numbers = $this->stallAllotments->pluck('stall_number')->map(fn($v) => (int)$v)->all();
        } else {
            $numbers = $this->stallAllotments()->pluck('stall_number')->map(fn($v) => (int)$v)->all();
        }

        if (empty($numbers) && !empty($this->allotted_stall)) {
            preg_match_all('/\d+/', (string) $this->allotted_stall, $matches);
            if (!empty($matches[0])) {
                $numbers = array_map('intval', $matches[0]);
            }
        }

        $numbers = array_values(array_unique(array_filter($numbers, fn($n) => $n >= 1 && $n <= 100)));
        sort($numbers);
        return $numbers;
    }

    /**
     * Get primary or first allotted stall number (for backwards compatibility).
     */
    public function getCurrentStallNumber(): ?int
    {
        $numbers = $this->getAllottedStallNumbers();
        return $numbers[0] ?? null;
    }

    /**
     * Get formatted string of all allotted stalls (e.g. "Stall 5, Stall 12").
     */
    public function getAllottedStallsFormattedAttribute(): string
    {
        $numbers = $this->getAllottedStallNumbers();
        if (empty($numbers)) {
            return '';
        }
        return implode(', ', array_map(fn($n) => "Stall {$n}", $numbers));
    }

    /**
     * Get array of available stall numbers between 1 and 100.
     * Excludes stalls assigned to ANY other organization/registration.
     *
     * @param int|null $excludeRegistrationId
     * @return array<int>
     */
    public static function getAvailableStallNumbers(?int $excludeRegistrationId = null): array
    {
        // 1. From disability_day_stall_allotments table
        $allotmentQuery = DisabilityDayStallAllotment::query();
        if ($excludeRegistrationId) {
            $allotmentQuery->where('registration_id', '!=', $excludeRegistrationId);
        }
        $assignedFromAllotments = $allotmentQuery->pluck('stall_number')
            ->map(fn($v) => (int)$v)
            ->all();

        // 2. From disability_day_registrations.allotted_stall column
        $regQuery = static::whereNotNull('allotted_stall')
            ->where('allotted_stall', '!=', '');
        if ($excludeRegistrationId) {
            $regQuery->where('id', '!=', $excludeRegistrationId);
        }
        $assignedFromRegs = [];
        foreach ($regQuery->pluck('allotted_stall') as $val) {
            preg_match_all('/\d+/', (string) $val, $matches);
            if (!empty($matches[0])) {
                foreach ($matches[0] as $m) {
                    $assignedFromRegs[] = (int) $m;
                }
            }
        }

        $allAssigned = array_unique(array_merge($assignedFromAllotments, $assignedFromRegs));

        $available = [];
        for ($i = 1; $i <= 100; $i++) {
            if (!in_array($i, $allAssigned, true)) {
                $available[] = $i;
            }
        }

        return $available;
    }
}

