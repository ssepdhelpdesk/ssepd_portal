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
        'applied_at',
        'status',
        'financial_year',
    ];

    protected $casts = [
        'address_type'      => 'integer',
        'state_id'          => 'integer',
        'district_id'       => 'integer',
        'block_id'          => 'integer',
        'gp_id'             => 'integer',
        'village_id'        => 'integer',
        'municipality_id'   => 'integer',
        'number_of_escorts' => 'integer',
        'status'            => 'integer',
        'applied_at'        => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
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
}
