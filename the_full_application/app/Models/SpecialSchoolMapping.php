<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class SpecialSchoolMapping extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $guarded = [''];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_table_id');
    }

    public function district() {
        return $this->belongsTo(District::class, 'district_id', 'district_id');
    }

    public function staff()
    {
        return $this->hasMany(\App\Models\SpecialSchoolStaff::class, 'special_school_id', 'special_school_id');
    }
}
