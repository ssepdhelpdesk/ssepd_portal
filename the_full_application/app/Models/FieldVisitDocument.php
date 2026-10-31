<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class FieldVisitDocument extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'field_visit_documents';

    protected $guarded = ['id'];

    protected $casts = [
        'file_size'  => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
     * Secure document download URL.
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('admin.field_visits.download_document', $this->id);
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
     * Get an appropriate FontAwesome icon based on file mime type.
     */
    public function getFileIconAttribute(): string
    {
        if (str_contains($this->mime_type, 'pdf')) {
            return 'fas fa-file-pdf text-danger';
        } elseif (str_contains($this->mime_type, 'word') || str_contains($this->mime_type, 'document')) {
            return 'fas fa-file-word text-primary';
        } elseif (str_contains($this->mime_type, 'image')) {
            return 'fas fa-file-image text-success';
        }
        return 'fas fa-file-alt text-secondary';
    }
}
