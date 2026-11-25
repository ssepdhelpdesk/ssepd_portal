<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisabilityDayParticipantRegistration extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'disability_day_participant_registrations';

    /**
     * Approved modes of transportation.
     */
    public const TRANSPORT_MODES = [
        'Government Vehicle',
        'School/Institution Vehicle',
        'Bus',
        'Train',
        'Private Vehicle',
        'Other',
    ];

    /**
     * Canonical Disability Day 2026 event attendance dates.
     */
    public const EVENT_DATES = [
        '2026-12-01' => 'December 1, 2026',
        '2026-12-02' => 'December 2, 2026',
        '2026-12-03' => 'December 3, 2026',
    ];

    public const ALL_DAYS_VALUE = 'ALL_DAYS';
    public const WALKATHON_DATE = '2026-12-01';

    /**
     * Approved participation categories.
     */
    public const PARTICIPATION_CATEGORIES = [
        'Walkathon',
        'Cultural',
        'Sports',
        'Others',
    ];

    /**
     * Maximum space-separated words allowed in a single participant's full name
     * to prevent multi-participant pasted lists while accommodating long Indian names.
     */
    public const MAX_PARTICIPANT_NAME_WORDS = 6;

    protected $fillable = [
        'registration_no',
        'participant_name',
        'institute_id',
        'school_name',
        'gender',
        'tshirt_size',
        'address_type',
        'state_id',
        'state_name',
        'district_id',
        'district_name',
        'block_id',
        'block_name',
        'gp_id',
        'gp_name',
        'village_id',
        'village_name',
        'municipality_id',
        'municipality_name',
        'pin',
        'at',
        'post',
        'ps',
        'postal_pin',
        'number_of_escorts',
        'participation_in',
        'participation_other',
        'attending_dates',
        'transport_mode',
        'transport_mode_other',
        'expected_arrival_date',
        'expected_arrival_time',
        'applied_at',
        'status',
        'financial_year',
    ];

    protected $casts = [
        'address_type'          => 'integer',
        'state_id'              => 'integer',
        'district_id'           => 'integer',
        'block_id'              => 'integer',
        'gp_id'                 => 'integer',
        'village_id'            => 'integer',
        'municipality_id'       => 'integer',
        'number_of_escorts'     => 'integer',
        'attending_dates'       => 'array',
        'expected_arrival_date' => 'date',
        'status'                => 'integer',
        'applied_at'            => 'datetime',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
        'deleted_at'            => 'datetime',
    ];

    /**
     * Model lifecycle boot.
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
     * Associated escorts.
     */
    public function escorts(): HasMany
    {
        return $this->hasMany(DisabilityDayEscort::class, 'participant_registration_id', 'id')->orderBy('escort_index');
    }

    /**
     * District relationship.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'district_id');
    }

    /**
     * State relationship.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
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
     * Generates a collision-resistant unique registration number for participants.
     * Format: SSEPD/DD2026/PART/DD/MM/YYYY/XXXXXX
     */
    public static function generateUniqueRegistrationNo(Carbon $appliedAt): string
    {
        $dateComponent = $appliedAt->format('d/m/Y');
        $prefix = "SSEPD/DD2026/PART/{$dateComponent}/";

        $maxRetries = 25;
        for ($i = 0; $i < $maxRetries; $i++) {
            $randomDigits = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $candidate = $prefix . $randomDigits;

            if (!static::where('registration_no', $candidate)->exists()) {
                return $candidate;
            }
        }

        throw new \RuntimeException('Failed to generate a unique participant registration number after multiple attempts.');
    }

    /**
     * Normalize whitespace in a participant's full name.
     */
    public static function normalizeParticipantName(?string $name): string
    {
        return preg_replace('/\s+/u', ' ', trim((string) $name)) ?? '';
    }

    /**
     * Authoritative validation for single-participant full name.
     * Rejects comma/semicolon/newline lists and suspicious multi-participant strings
     * while preserving legitimate multiword Indian & Unicode names.
     */
    public static function validateSingleParticipantName($rawValue, \Closure $fail): void
    {
        $raw = (string) $rawValue;

        if (preg_match('/[,;\r\n\t|\/&]/u', $raw)) {
            $fail('Please enter only one participant\'s full name. Multiple names separated by commas, semicolons, or new lines are not allowed.');
            return;
        }

        $normalized = self::normalizeParticipantName($raw);
        $len = mb_strlen($normalized, 'UTF-8');

        if ($len < 2) {
            $fail('Participant name must be at least 2 characters.');
            return;
        }

        if ($len > 150) {
            $fail('Participant name may not exceed 150 characters.');
            return;
        }

        if (!preg_match('/^[\p{L}\p{M}\s\.\'-]+$/u', $normalized)) {
            $fail('Participant name may only contain letters, spaces, dots, hyphens, and apostrophes.');
            return;
        }

        $words = preg_split('/\s+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY);
        if (is_array($words) && count($words) > self::MAX_PARTICIPANT_NAME_WORDS) {
            $fail('Please enter only one participant\'s full name. Multiple participants\' names cannot be entered in a single registration.');
        }
    }

    /**
     * Authoritative validation for participant attendance dates.
     * Enforces canonical dates, rejects duplicates/invalid dates, enforces All Days exclusivity,
     * and enforces Walkathon-only December 1, 2026 restriction.
     */
    public static function validateAttendingDates($value, \Closure $fail, ?string $participationIn = null): void
    {
        if (!is_array($value) || count($value) === 0) {
            $fail('Please select at least one date of participation.');
            return;
        }

        $allowed = array_merge(array_keys(self::EVENT_DATES), [self::ALL_DAYS_VALUE]);
        $cleaned = array_map(fn($v) => trim((string) $v), $value);

        if (count($cleaned) !== count(array_unique($cleaned))) {
            $fail('Duplicate attendance dates are not allowed.');
            return;
        }

        foreach ($cleaned as $item) {
            if (!in_array($item, $allowed, true)) {
                $fail('One or more selected participation dates are invalid.');
                return;
            }
        }

        if (in_array(self::ALL_DAYS_VALUE, $cleaned, true) && count($cleaned) > 1) {
            $fail('All Days cannot be combined with individual date selections.');
            return;
        }

        if ($participationIn === 'Walkathon') {
            if (in_array(self::ALL_DAYS_VALUE, $cleaned, true) || $cleaned !== [self::WALKATHON_DATE]) {
                $fail('For Walkathon participation, only December 1, 2026 can be selected.');
            }
        }
    }

    /**
     * Normalize selected attendance dates to sorted canonical ISO dates.
     * If All Days or all three dates are selected, stores all three canonical dates.
     */
    public static function normalizeAttendingDates(?array $dates): array
    {
        if (empty($dates)) {
            return [];
        }

        $canonical = array_keys(self::EVENT_DATES);
        $cleaned = array_map(fn($v) => trim((string) $v), $dates);

        if (in_array(self::ALL_DAYS_VALUE, $cleaned, true)) {
            return $canonical;
        }

        $filtered = array_values(array_intersect($canonical, array_unique($cleaned)));
        sort($filtered);
        return $filtered;
    }

    /**
     * Format stored canonical attendance dates for Admin/Receipt display.
     */
    public static function formatAttendingDatesForDisplay(?array $dates): string
    {
        if (empty($dates)) {
            return 'Not Specified';
        }

        $canonical = array_keys(self::EVENT_DATES);
        $sorted = array_values(array_intersect($canonical, $dates));
        sort($sorted);

        if ($sorted === $canonical) {
            return 'All Days (December 1–3, 2026)';
        }

        $labels = array_map(fn($d) => self::EVENT_DATES[$d] ?? $d, $sorted);
        return implode(', ', $labels);
    }
}
