<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class FieldVisit extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'field_visits';

    protected $guarded = ['id'];

    protected $casts = [
        'address_type'          => 'integer',
        'category_id'           => 'integer',
        'village_id'            => 'integer',
        'visit_date'            => 'date',
        'follow_up_date'        => 'date',
        'is_follow_up_required' => 'boolean',
        'latitude'              => 'decimal:7',
        'longitude'             => 'decimal:7',
        'gps_accuracy'          => 'decimal:2',
        'location_captured_at'  => 'datetime',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
        'deleted_at'            => 'datetime',
    ];

    /**
     * Relationship to Visit Type Master.
     */
    public function visitType(): BelongsTo
    {
        return $this->belongsTo(FieldVisitType::class, 'field_visit_type_id', 'id');
    }

    /**
     * Relationship to Centralized Institute Master.
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(FieldVisitInstitute::class, 'institution_id', 'id');
    }

    /**
     * Relationship to District master (matched on business key district_id).
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'district_id');
    }

    /**
     * Relationship to Block master (matched on business key block_id).
     */
    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id', 'block_id');
    }

    /**
     * Relationship to Municipality master (matched on business key municipality_id).
     */
    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class, 'municipality_id', 'municipality_id');
    }

    /**
     * Relationship to Grampanchayat master (matched on business key gp_id).
     */
    public function grampanchayat(): BelongsTo
    {
        return $this->belongsTo(Grampanchayat::class, 'gp_id', 'gp_id');
    }

    /**
     * Relationship to Village master (matched on business key village_id).
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id', 'village_id');
    }

    /**
     * Relationship to Ward master (matched on business key ward_code).
     */
    public function ward(): BelongsTo
    {
        return $this->belongsTo(WardMaster::class, 'ward_id', 'ward_code');
    }

    /**
     * Relationship to Visiting Officer resolved strictly via user_table_id.
     */
    public function visitingOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'visiting_officer_id', 'user_table_id');
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
     * Relationship to Deleter resolved strictly via user_table_id.
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'user_table_id');
    }

    /**
     * Relationship to captured Geo-Tagged Photos / Evidences.
     */
    public function evidences(): HasMany
    {
        return $this->hasMany(FieldVisitEvidence::class, 'field_visit_id', 'id');
    }

    /**
     * Relationship to attached Reference Documents.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(FieldVisitDocument::class, 'field_visit_id', 'id');
    }

    /**
     * Scope to search by reference number, purpose, or institution name.
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('visit_reference_no', 'like', "%{$search}%")
              ->orWhere('visit_purpose', 'like', "%{$search}%")
              ->orWhere('institution_name', 'like', "%{$search}%")
              ->orWhere('organization_code', 'like', "%{$search}%");
        });
    }

    /**
     * Format a readable status badge HTML.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft'     => '<span class="badge bg-secondary"><i class="fas fa-pencil-alt me-1"></i> Draft</span>',
            'submitted' => '<span class="badge bg-primary"><i class="fas fa-paper-plane me-1"></i> Submitted</span>',
            'reviewed'  => '<span class="badge bg-info text-dark"><i class="fas fa-check-double me-1"></i> Reviewed</span>',
            'closed'    => '<span class="badge bg-success"><i class="fas fa-archive me-1"></i> Closed</span>',
            default     => '<span class="badge bg-light text-dark">' . ucfirst($this->status) . '</span>',
        };
    }

    /**
     * Helper to return map link for coordinates.
     */
    public function getMapUrlAttribute(): ?string
    {
        if (!empty($this->latitude) && !empty($this->longitude)) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }
        return null;
    }

    /**
     * Helper to return OpenStreetMap preview URL.
     */
    public function getOsmEmbedUrlAttribute(): ?string
    {
        if (!empty($this->latitude) && !empty($this->longitude)) {
            $lat = $this->latitude;
            $lon = $this->longitude;
            $delta = 0.005;
            $minLon = $lon - $delta;
            $minLat = $lat - $delta;
            $maxLon = $lon + $delta;
            $maxLat = $lat + $delta;
            return "https://www.openstreetmap.org/export/embed.html?bbox={$minLon}%2C{$minLat}%2C{$maxLon}%2C{$maxLat}&layer=mapnik&marker={$lat}%2C{$lon}";
        }
        return null;
    }
}
