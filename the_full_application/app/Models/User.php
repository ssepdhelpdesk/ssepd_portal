<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\LoginHistory;

class User extends Authenticatable implements Auditable, MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;
    use \OwenIt\Auditing\Auditable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'user_id', 'mobile_no', 'profile_photo', 'profile_photo_path', 'password',
        'address', 'entry_dt', 'is_active', 'created_by', 'posted_district', 'posted_subdiv',
        'posted_block', 'posted_municipality', 'posted_gp', 'posted_village', 'posted_address',
        'posted_tahasil', 'ngo_approve_date', 'posted_disability_department_section',
        'otp_for_forgot_password', 'account_non_locked', 'failed_login_cnt', 'additnl_para',
        'allow_multiple_sessions', 'is_logged_in', 'last_req_time', 'enabled', 'is_doctor',
        'otp_for_issuing_disability_id_card', 'is_survey_consultant', 'competent_authority_designation',
        'mci_crr_regd_no', 'doctor_qualification', 'medical_institute_id',
        'otp_for_forgot_password_duration', 'otp_for_issuing_disability_id_card_duration',
        'posted_zone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'entry_dt' => 'datetime',
            'last_req_time' => 'datetime',
            'otp_for_forgot_password_duration' => 'datetime',
            'otp_for_issuing_disability_id_card_duration' => 'datetime',
            'ngo_approve_date' => 'date',
            'account_non_locked' => 'boolean',
            'allow_multiple_sessions' => 'boolean',
            'is_logged_in' => 'boolean',
            'enabled' => 'boolean',
            'is_doctor' => 'boolean',
            'is_survey_consultant' => 'boolean',
        ];
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function currentLoginHistory()
    {
        return $this->loginHistories()->whereNull('logout_time')->latest()->first();
    }

    public function ngoRegistrations()
    {
        return $this->hasMany(NgoRegistration::class, 'user_table_id');
    }
}
