<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class FieldVisitEvidence extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'field_visit_evidences';

    protected $guarded = ['id'];

    protected $casts = [
        'latitude'              => 'decimal:7',
        'longitude'             => 'decimal:7',
        'gps_accuracy'          => 'decimal:2',
        'location_reading_time' => 'datetime',
        'client_capture_time'   => 'datetime',
        'file_size'             => 'integer',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
    ];

    /**
     * Relationship to parent FieldVisit.
     */
    public function visit(): BelongsTo
    {
        return $this->belongsTo(FieldVisit::class, 'field_visit_id', 'id');
    }

    /**
     * Relationship to Creator resolved strictly via user_table_id.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_table_id');
    }

    /**
     * Relationship to Updater resolved strictly via user_table_id.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_table_id');
    }

    /**
     * Secure image stream URL.
     */
    public function getUrlAttribute(): string
    {
        return route('admin.field_visits.view_evidence', $this->id);
    }

    /**
     * Human-readable formatted file size.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Helper to return map link for photo coordinates.
     */
    public function getMapUrlAttribute(): ?string
    {
        if (!empty($this->latitude) && !empty($this->longitude)) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }
        return null;
    }
}
