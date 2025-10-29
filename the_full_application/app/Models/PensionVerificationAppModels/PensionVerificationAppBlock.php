<?php

namespace App\Models\PensionVerificationAppModels;

use Illuminate\Database\Eloquent\Model;

class PensionVerificationAppBlock extends Model
{
    protected $connection = 'pension_verification_app';
    protected $table = 'blocks';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'district_id', 'name', 'type', 'block_code', 'municipality_code', 'status'
    ];

    public function district()
    {
        return $this->belongsTo(PensionVerificationAppDistrict::class, 'district_id');
    }

    public function gramaPanchayats()
    {
        return $this->hasMany(PensionVerificationAppGramaPanchayat::class, 'block_id');
    }

    public function villages()
    {
        return $this->hasMany(PensionVerificationAppVillage::class, 'block_id');
    }

    public function wards()
    {
        return $this->hasMany(PensionVerificationAppWard::class, 'block_id');
    }

    public function beneficiaries()
    {
        return $this->hasMany(PensionVerificationAppBeneficiary::class, 'block_id');
    }
}
