<?php

namespace App\Models\PensionVerificationAppModels;

use Illuminate\Database\Eloquent\Model;

class PensionVerificationAppWard extends Model
{
    protected $connection = 'pension_verification_app';
    protected $table = 'wards';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'district_id', 'block_id', 'name', 'ward_code',
        'rural_urban_area', 'zone_id', 'status'
    ];

    public function district()
    {
        return $this->belongsTo(PensionVerificationAppDistrict::class, 'district_id');
    }

    public function block()
    {
        return $this->belongsTo(PensionVerificationAppBlock::class, 'block_id');
    }

    public function beneficiaries()
    {
        return $this->hasMany(PensionVerificationAppBeneficiary::class, 'ward_id');
    }
}
