<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class PiaInstituteMaster extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $guarded = [''];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_table_id');
    }

    public function state() {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
    }

    public function district() {
        return $this->belongsTo(District::class, 'district_id', 'district_id');
    }

    public function block() {
        return $this->belongsTo(Block::class, 'block_id', 'block_id');
    }

    public function grampanchayat() {
        return $this->belongsTo(Grampanchayat::class, 'gp_id', 'gp_id');
    }

    public function village() {
        return $this->belongsTo(Village::class, 'village_id', 'village_id');
    }

    public function municipality() {
        return $this->belongsTo(Municipality::class, 'municipality_id', 'municipality_id');
    }

    public function ward() {
        return $this->belongsTo(WardMaster::class, 'ward_id', 'ward_code');
    }

    public function beneficiaries()
    {
        return $this->hasMany(PiaInstituteBenfDetails::class, 'pia_institute_master_institute_id', 'institute_master_id');
    }
}
