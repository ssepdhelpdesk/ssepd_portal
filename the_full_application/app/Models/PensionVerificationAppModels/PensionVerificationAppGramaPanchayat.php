<?php

namespace App\Models\PensionVerificationAppModels;

use Illuminate\Database\Eloquent\Model;

class PensionVerificationAppGramaPanchayat extends Model
{
    protected $connection = 'pension_verification_app';
    protected $table = 'grama_panchayats';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'district_id', 'block_id', 'name', 'gp_code', 'status'
    ];

    public function district()
    {
        return $this->belongsTo(PensionVerificationAppDistrict::class, 'district_id');
    }

    public function block()
    {
        return $this->belongsTo(PensionVerificationAppBlock::class, 'block_id');
    }

    public function villages()
    {
        return $this->hasMany(PensionVerificationAppVillage::class, 'gp_id');
    }

    public function beneficiaries()
    {
        return $this->hasMany(PensionVerificationAppBeneficiary::class, 'gp_id');
    }
}
