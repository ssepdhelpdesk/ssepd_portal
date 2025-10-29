<?php

namespace App\Models\PensionVerificationAppModels;

use Illuminate\Database\Eloquent\Model;

class PensionVerificationAppBeneficiary extends Model
{
    protected $connection = 'pension_verification_app';
    protected $table = 'beneficiaries';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'sanction_number', 'name', 'gender', 'dob', 'dob_string', 'caste', 'father_name',
        'photo', 'area', 'address', 'state_id', 'district_id', 'district_name', 'block_id',
        'block_name', 'gp_id', 'gp_name', 'village_id', 'village_name', 'scheme_id', 'scheme',
        'scheme_type', 'age', 'pincode', 'bpl_id', 'bpl_member_id', 'ration_card_no', 'epic_no',
        'sanction_date', 'sanction_date_excel', 'bank_account_no', 'ifsc_code', 'disbursement_mode',
        'beneficiary_no', 'aadhar_verified', 'aadhar_no', 'is_pv', 'verification_status', 'status',
        'current_verification', 'verified_by', 'verification_date', 'month', 'is_matched', 'remarks',
        'user_level', 'ward_id', 'ward_name', 'disability_percentage', 'disability_category',
        'import_type', 'is_active', 'modified_by', 'last_modify_date', 'udid_no',
        'updated_scheme_name', 'excel_data_type', 'ssepd_id', 'is_new'
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

    public function village()
    {
        return $this->belongsTo(PensionVerificationAppVillage::class, 'village_id');
    }

    public function ward()
    {
        return $this->belongsTo(PensionVerificationAppWard::class, 'ward_id');
    }
}
