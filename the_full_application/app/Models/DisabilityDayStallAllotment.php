<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class DisabilityDayStallAllotment extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'disability_day_stall_allotments';

    protected $fillable = [
        'registration_id',
        'stall_number',
        'stall_name',
        'created_by',
    ];

    protected $casts = [
        'registration_id' => 'integer',
        'stall_number'    => 'integer',
        'created_by'      => 'integer',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    /**
     * Parent registration.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(DisabilityDayRegistration::class, 'registration_id', 'id');
    }

    /**
     * Creator user.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
