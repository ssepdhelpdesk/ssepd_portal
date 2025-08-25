<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class WardMaster extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $guarded = [''];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipal_area_code', 'municipality_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_code', 'district_id');
    }
}
