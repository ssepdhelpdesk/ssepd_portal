<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class DisabilityPensionerConsent extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $guarded = [];

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'district_id');
    }

    public function blocks()
    {
        return $this->hasMany(Block::class, 'district_id', 'district_id');
    }

    public function municipalities()
    {
        return $this->hasMany(Municipality::class, 'district_id', 'district_id');
    }

    public function grampanchayats()
    {
        return $this->hasMany(Grampanchayat::class, 'district_id', 'district_id');
    }

    public function villages()
    {
        return $this->hasMany(Village::class, 'gp_id', 'gp_id');
    }

    public function wards()
    {
        return $this->hasMany(WardMaster::class, 'ward_id', 'ward_code');
    }
}
