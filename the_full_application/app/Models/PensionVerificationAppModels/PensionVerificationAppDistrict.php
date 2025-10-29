<?php

namespace App\Models\PensionVerificationAppModels;

use Illuminate\Database\Eloquent\Model;

class PensionVerificationAppDistrict extends Model
{
    protected $connection = 'pension_verification_app';
    protected $table = 'districts';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'state_id', 'name', 'status'
    ];

    public function blocks()
    {
        return $this->hasMany(PensionVerificationAppBlock::class, 'district_id');
    }

    public function gramaPanchayats()
    {
        return $this->hasMany(PensionVerificationAppGramaPanchayat::class, 'district_id');
    }

    public function villages()
    {
        return $this->hasMany(PensionVerificationAppVillage::class, 'district_id');
    }

    public function wards()
    {
        return $this->hasMany(PensionVerificationAppWard::class, 'district_id');
    }

    public function beneficiaries()
    {
        return $this->hasMany(PensionVerificationAppBeneficiary::class, 'district_id');
    }
}
