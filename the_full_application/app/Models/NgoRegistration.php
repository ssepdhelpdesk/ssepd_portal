<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class NgoRegistration extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $guarded = [''];

    public function applicationStage()
    {
        return $this->belongsTo(ApplicationStage::class, 'application_stage_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_table_id');
    }

    public function officeBearers()
    {
        return $this->hasMany(NgoOfficeBearer::class, 'ngo_tbl_id');
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
}
