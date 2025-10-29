<?php

namespace App\Models\PensionVerificationAppModels;

use Illuminate\Database\Eloquent\Model;

class PensionVerificationAppVillage extends Model
{
    protected $connection = 'pension_verification_app';
    protected $table = 'villages';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'district_id', 'block_id', 'gp_id', 'name', 'status', 'village_code'
    ];

    public function district()
    {
        return $this->belongsTo(PensionVerificationAppDistrict::class, 'district_id');
    }

    public function block()
    {
        return $this->belongsTo(PensionVerificationAppBlock::class, 'block_id');
    }

    public function gramaPanchayat()
    {
        return $this->belongsTo(PensionVerificationAppGramaPanchayat::class, 'gp_id');
    }

    public function beneficiaries()
    {
        return $this->hasMany(PensionVerificationAppBeneficiary::class, 'village_id');
    }
}
