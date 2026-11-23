<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisabilityDayEscort extends Model
{
    use HasFactory;

    protected $table = 'disability_day_escorts';

    protected $fillable = [
        'participant_registration_id',
        'escort_index',
        'name',
        'mobile',
        'email',
        'relationship',
    ];

    protected $casts = [
        'participant_registration_id' => 'integer',
        'escort_index'                => 'integer',
        'created_at'                  => 'datetime',
        'updated_at'                  => 'datetime',
    ];

    /**
     * Parent participant registration.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(DisabilityDayParticipantRegistration::class, 'participant_registration_id', 'id');
    }
}
