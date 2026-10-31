<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use OwenIt\Auditing\Contracts\Auditable;

class FieldVisitInstitute extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'field_visit_institutes';

    /**
     * The attributes that are mass assignable.
     * Note: created_by and updated_by are strictly set by the application.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'institute_code',
        'institute_name',
        'category_id',
        'category_name',
        'management_id',
        'management_name',
        'source_institute_id',
        'legacy_sub_id',
        'address_type',
        'state_id',
        'district_id',
        'district_name',
        'block_id',
        'block_name',
        'municipality_id',
        'municipality_name',
        'gp_id',
        'gp_name',
        'village_id',
        'village_name',
        'ward_id',
        'ward_name',
        'address',
        'pin_code',
        'contact_person',
        'contact_number',
        'email',
        'status',
        'is_active',
        'which_govt',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'category_id'         => 'integer',
        'management_id'       => 'integer',
        'source_institute_id' => 'integer',
        'legacy_sub_id'       => 'integer',
        'address_type'        => 'integer',
        'state_id'            => 'integer',
        'district_id'         => 'integer',
        'block_id'            => 'integer',
        'municipality_id'     => 'integer',
        'gp_id'               => 'integer',
        'village_id'          => 'integer',
        'ward_id'             => 'integer',
        'status'              => 'integer',
        'is_active'           => 'boolean',
        'which_govt'          => 'integer',
        'created_by'          => 'integer',
        'updated_by'          => 'integer',
        'deleted_by'          => 'integer',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
        'deleted_at'          => 'datetime',
    ];

    /**
     * Relationship to State master (matched on domain key state_id).
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
    }

    /**
     * Relationship to District master (matched on domain key district_id).
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'district_id');
    }

    /**
     * Relationship to Block master (matched on domain key block_id).
     */
    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id', 'block_id');
    }

    /**
     * Relationship to Gram Panchayat master (matched on domain key gp_id).
     */
    public function grampanchayat(): BelongsTo
    {
        return $this->belongsTo(Grampanchayat::class, 'gp_id', 'gp_id');
    }

    /**
     * Relationship to Village master (matched on domain key village_id).
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id', 'village_id');
    }

    /**
     * Relationship to Municipality master (matched on domain key municipality_id).
     */
    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class, 'municipality_id', 'municipality_id');
    }

    /**
     * Relationship to Field Visits recorded for this institute.
     */
    public function fieldVisits(): HasMany
    {
        return $this->hasMany(FieldVisit::class, 'institution_id', 'id');
    }

    /**
     * User who created this record (matched strictly against users.user_table_id).
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_table_id');
    }

    /**
     * User who last updated this record (matched strictly against users.user_table_id).
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_table_id');
    }

    /**
     * User who deleted this record (matched strictly against users.user_table_id).
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'user_table_id');
    }

    /**
     * Human-readable Government funding scope.
     */
    public function getGovtTypeAttribute(): string
    {
        return match ($this->which_govt) {
            1 => 'Govt. of Odisha',
            2 => 'Govt. of India',
            default => 'Other / Private',
        };
    }

    /**
     * Scope a query to only include active institutes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by district domain code.
     */
    public function scopeFilterByDistrict($query, $districtId)
    {
        if (!empty($districtId)) {
            return $query->where('district_id', $districtId);
        }
        return $query;
    }

    /**
     * Scope to filter by institutional category ID.
     */
    public function scopeFilterByCategory($query, $categoryId)
    {
        if (!empty($categoryId)) {
            return $query->where('category_id', $categoryId);
        }
        return $query;
    }

    /**
     * Scope to filter by which_govt (1 = State, 2 = Central).
     */
    public function scopeFilterByGovt($query, $whichGovt)
    {
        if (!empty($whichGovt)) {
            return $query->where('which_govt', $whichGovt);
        }
        return $query;
    }

    /**
     * Scope for multi-field search.
     */
    public function scopeSearch($query, ?string $keyword)
    {
        if (!empty($keyword)) {
            $keyword = trim($keyword);
            return $query->where(function ($q) use ($keyword) {
                $q->where('institute_name', 'LIKE', "%{$keyword}%")
                  ->orWhere('institute_code', 'LIKE', "%{$keyword}%")
                  ->orWhere('management_name', 'LIKE', "%{$keyword}%")
                  ->orWhere('contact_person', 'LIKE', "%{$keyword}%")
                  ->orWhere('email', 'LIKE', "%{$keyword}%");
            });
        }
        return $query;
    }

    /**
     * Safe dependency protection check.
     * Prevents physical deletion if related historical field visits exist.
     */
    public function hasDependencies(): bool
    {
        if (Schema::hasTable('field_visits')) {
            return DB::table('field_visits')
                ->where('institution_id', $this->id)
                ->whereNull('deleted_at')
                ->exists();
        }
        return false;
    }
}
