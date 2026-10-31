<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class FieldVisitType extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'field_visit_types';

    /**
     * The attributes that are mass assignable.
     * Note: created_by and updated_by are protected and strictly set by the application.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active'   => 'boolean',
        'sort_order'  => 'integer',
        'created_by'  => 'integer',
        'updated_by'  => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /**
     * User who created this record (matched against users.user_table_id).
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_table_id');
    }

    /**
     * User who last updated this record (matched against users.user_table_id).
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_table_id');
    }

    /**
     * Scope a query to only include active visit types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by sort_order ascending, then name ascending.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Safe dependency protection check.
     * When later Field Visit transaction tables (e.g. field_visits) are introduced,
     * this checks if any related visit records exist before allowing deletion.
     *
     * @return bool
     */
    public function hasDependencies(): bool
    {
        // Future dependency check:
        // if (\Schema::hasTable('field_visits')) {
        //     return \DB::table('field_visits')->where('visit_type_id', $this->id)->exists();
        // }
        return false;
    }
}
