<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Dashboard_Controllers\{
    DashboardController,
    HomeController,
    RoleController,
    PermissionController,
    UserController,
    ProductController,
    MyProfileController,
    LocationController,
    NgoRegdController,
    SpecialSchoolController,
    SpecialSchoolConstructionController,
    PensionFundsRequirementsController,
    DdrcController,
    PensionMonthlyDisbursementController,
    DailyPensionDisbursementController,
    SsepdNotificationController,
    PensionController
};

use App\Http\Controllers\Dashboard_Controllers\Files3500Controllers\{
    OldAge3500Controller,
    Disability3500Controller,
    ReportOf3500Controller,
    SchemeMigrationEpController
};

use App\Http\Controllers\Frontend_Controller\{
    NgoRegdFrontendController,
    LocationFrontendController,
    DisabilityDayStallController
};

use App\Http\Controllers\Api\{
    EpPensionersController
};


Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return view('auth.login');
});
Route::prefix('apis')->name('apis.')->controller(EpPensionersController::class)->group(function () {
    Route::get('/disability-pensioner-datas/{aadhar_no}', 'getDisabilityPensionerByAadharRequiredDatas');
    Route::get('/oldage-pensioner-datas1/{aadhar_no}', 'getOldAgePensionerByAadharRequiredDatas');
});

Route::prefix('frontend')->name('frontend.')->group(function () {

    Route::prefix('ngo')->name('ngo.')->controller(NgoRegdFrontendController::class)->group(function () {
        Route::post('initial_part_one_store', 'initial_part_one_store')->name('ngo_initial_part_one_store');
        Route::post('part_one_store', 'part_one_store')->name('ngo_part_one_store');
        Route::get('check-pan-no', 'check_pan_no')->name('check_pan_no');
        Route::get('check-email-id', 'check_ngo_org_email')->name('check_ngo_org_email');
    });

    Route::prefix('locations')->name('locations.')->controller(LocationController::class)->group(function () {
        Route::get('block/index', 'blockIndex')->name('blockwise.index');
        Route::get('municipality/index', 'municipalityIndex')->name('municipalitywise.index');
        Route::post('fetch-districts', 'fetchDistrict')->name('fetchDistrict');
        Route::post('fetch-municipality', 'fetchMunicipality')->name('fetchMunicipality');
        Route::post('fetch-block', 'fetchBlock')->name('fetchBlock');
        Route::post('fetch-grampanchayat', 'fetchGrampanchayat')->name('fetchGrampanchayat');
        Route::post('fetch-village', 'fetchVillage')->name('fetchVillage');
        Route::post('fetch-ward', 'fetchWard')->name('fetchWard');
    });

    Route::get('get-address-type-content-data/{type}', function ($type) {
        $content = '';
        if ($type === '1') {
            $content = view('frontend.locations.villages.villagecontent')->render();
        } elseif ($type === '2') {
            $content = view('frontend.locations.municipalities.municipalitycontent')->render();
        }
        $buttons = '<button type="submit" id="submitButton" name="register" class="btn btn-primary text-white from-prevent-multiple-submits">
        <i class="spinner fa fa-spinner fa-spin"></i> Submit
        </button>
        <button type="button" class="btn btn-warning">Cancel</button>';

        return response()->json([
            'content' => $content,
            'buttons' => $buttons,
        ]);
    })->name('get_address_type_content_data');


    Route::prefix('disabilitydaystallregistration')->name('disabilitydaystallregistration.')->controller(DisabilityDayStallController::class)->group(function () {
        Route::get('index', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::get('list', 'list')->name('list');
    });

});

Auth::routes(['verify' => true]);

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/refresh_captcha', [HomeController::class, 'refreshCaptcha'])->name('refreshCaptcha');

Route::group(['middleware' => ['auth', 'prevent-back-history', 'track.session', 'verified'], 'prefix' => 'dashboard', 'as' => 'admin.'], function () {

    //Route::view('/', 'dashboard.layouts.index')->name('dashboard');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('roles')->name('roles.')->controller(RoleController::class)->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('{id}/edit', 'edit')->name('edit');
        Route::post('{id}/update', 'update')->name('update');
        Route::get('{id}/view', 'show')->name('show');
        Route::get('{id}/delete', 'destroy')->name('destroy');
    });

    Route::prefix('permissions')->name('permissions.')->controller(PermissionController::class)->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('{id}/edit', 'edit')->name('edit');
        Route::post('{id}/update', 'update')->name('update');
        Route::get('{id}/view', 'show')->name('show');
        Route::get('{id}/delete', 'destroy')->name('destroy');
    });

    Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('{id}/edit', 'edit')->name('edit');
        Route::post('{id}/update', 'update')->name('update');
        Route::get('{id}/view', 'show')->name('show');
        Route::get('{id}/delete', 'destroy')->name('destroy');
        Route::get('{id}/reset_password', 'reset_password')->name('reset_password');
    });

    Route::prefix('my-profile')->name('myprofile.')->controller(MyProfileController::class)->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('{id}/edit', 'edit')->name('edit');
        Route::post('{id}/update', 'update')->name('update');
        Route::get('{id}/view', 'show')->name('show');
        Route::get('{id}/delete', 'destroy')->name('destroy');
        Route::get('change-password', 'changePassword')->name('changePassword');
        Route::post('change-password', 'changePasswordStore')->name('changePasswordStore');
    });

    Route::prefix('locations')->name('locations.')->controller(LocationController::class)->group(function () {
        Route::get('block/index', 'blockIndex')->name('blockwise.index');
        Route::get('municipality/index', 'municipalityIndex')->name('municipalitywise.index');
        Route::post('fetch-districts', 'fetchDistrict')->name('fetchDistrict');
        Route::post('fetch-municipality', 'fetchMunicipality')->name('fetchMunicipality');
        Route::post('fetch-block', 'fetchBlock')->name('fetchBlock');
        Route::post('fetch-grampanchayat', 'fetchGrampanchayat')->name('fetchGrampanchayat');
        Route::post('fetch-village', 'fetchVillage')->name('fetchVillage');
        Route::post('fetch-ward', 'fetchWard')->name('fetchWard');
    });

    Route::prefix('ngo')->name('ngo.')->controller(NgoRegdController::class)->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('part_one_store', 'part_one_store')->name('part_one_store');
        Route::get('{id}/{no_of_form_completed}/continue-application', 'continue_application')->name('continue_application');
        Route::post('{id}/part_one_after_initial_store', 'part_one_after_initial_store')->name('part_one_after_initial_store');
        Route::post('{id}/part_two_store', 'part_two_store')->name('part_two_store');
        Route::post('{id}/part_three_store', 'part_three_store')->name('part_three_store');
        Route::post('{id}/part_four_store', 'part_four_store')->name('part_four_store');
        Route::post('{id}/part_five_store', 'part_five_store')->name('part_five_store');
        Route::post('{id}/part_six_store', 'part_six_store')->name('part_six_store');
        Route::get('{id}/view_ngo_application', 'view_ngo_application')->name('view_ngo_application');
        Route::get('check-pan-no', 'check_pan_no')->name('check_pan_no');
        Route::get('check-email-id', 'check_ngo_org_email')->name('check_ngo_org_email');
        Route::get('check-trained-staff-aadhar-no', 'check_trained_staff_aadhar_no')->name('check_trained_staff_aadhar_no');
        Route::get('check-pan-no-of-office-bearer', 'check_pan_no_of_office_bearer')->name('check_pan_no_of_office_bearer');
        Route::get('check-aadhar-no-of-office-bearer', 'check_aadhar_no_of_office_bearer')->name('check_aadhar_no_of_office_bearer');
        Route::post('{id}/executive_remarks', 'executive_remarks')->name('executive_remarks');
        Route::get('{id}/edit_ngo_application', 'edit_ngo_application')->name('edit_ngo_application');
        Route::post('{id}/update_ngo_application_part_one', 'update_ngo_application_part_one')->name('update_ngo_application_part_one');

        Route::get('/update_ngo_application_part_two_get_office_bearer', 'update_ngo_application_part_two_get_office_bearer')->name('update_ngo_application_part_two_get_office_bearer');
        Route::post('/update_ngo_application_part_two_update_office_bearer', 'update_ngo_application_part_two_update_office_bearer')->name('update_ngo_application_part_two_update_office_bearer');
        Route::get('{id}/update_ngo_application_part_two_add_another_office_bearer', 'update_ngo_application_part_two_add_another_office_bearer')->name('update_ngo_application_part_two_add_another_office_bearer');
        Route::post('{id}/update_ngo_application_part_two_store_another_office_bearer', 'update_ngo_application_part_two_store_another_office_bearer')->name('update_ngo_application_part_two_store_another_office_bearer');
    });

    Route::prefix('specialschool')->name('specialschool.')->controller(SpecialSchoolController::class)->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('{id}/delete', 'delete')->name('delete');
        Route::post('store_school_basic_details', 'store_school_basic_details')->name('store_school_basic_details');
        Route::post('store_school_staff_details', 'store_school_staff_details')->name('store_school_staff_details');
        Route::get('view_staff_details', 'view_staff_details')->name('view_staff_details');
        Route::get('{id}/view_staff_details_by_state_office', 'view_staff_details_by_state_office')->name('view_staff_details_by_state_office');
        Route::get('cumulative_report', 'cumulative_report')->name('cumulative_report');
        Route::get('school_wise_staff_count_report', 'school_wise_staff_count_report')->name('school_wise_staff_count_report');

        Route::get('check-staff-aadhar', 'check_staff_aadhar')->name('check_staff_aadhar');
        Route::get('check-staff-udidno', 'check_staff_udidno')->name('check_staff_udidno');
    });
    
    Route::prefix('specialschoolconstructions')->name('specialschoolconstructions.')->controller(SpecialSchoolConstructionController::class)->group(function () {
        Route::get('{id}/index', 'index')->name('index');
        Route::get('construction_timeline', 'construction_timeline')->name('construction_timeline');
        Route::post('construction_timeline_store', 'construction_timeline_store')->name('construction_timeline_store');
        Route::get('{id}/approve_construction_status', 'approve_construction_status')->name('approve_construction_status');
        Route::post('{id}/approve_construction_status_store', 'approve_construction_status_store')->name('approve_construction_status_store');
        Route::get('all_in_one_approval', 'all_in_one_approval')->name('all_in_one_approval');

        Route::get('school_wise_toilet_construction_report', 'school_wise_toilet_construction_report')->name('school_wise_toilet_construction_report');
    });

    Route::prefix('pension')->name('pension.')->controller(PensionFundsRequirementsController::class)->group(function () {
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('{id}/edit', 'edit')->name('edit');
        Route::post('{id}/update', 'update')->name('update');
        Route::get('{id}/delete', 'delete')->name('delete');
        Route::get('report', 'report')->name('report');
        Route::get('report_without_ajax', 'report_without_ajax')->name('report_without_ajax');

        Route::get('pension_authority_index', 'pension_authority_index')->name('pension_authority_index');
        Route::post('pension_authority_store', 'pension_authority_store')->name('pension_authority_store');
        Route::get('pension_authority_report', 'pension_authority_report')->name('pension_authority_report');
        Route::get('{id}/pension_authority_edit', 'pension_authority_edit')->name('pension_authority_edit');
        Route::post('{id}/pension_authority_update', 'pension_authority_update')->name('pension_authority_update');
        Route::get('{id}/pension_authority_delete', 'pension_authority_delete')->name('pension_authority_delete');

        Route::get('district_wise_monthly_fund_requirement_report', 'district_wise_monthly_fund_requirement_report')->name('district_wise_monthly_fund_requirement_report');
    });

    Route::prefix('pensionforbeneficiaries')->name('pensionforbeneficiaries.')->controller(PensionController::class)->group(function () {
        Route::get('oldage_pensioner_consents_create', 'oldage_pensioner_consents_create')->name('oldage_pensioner_consents_create');
        Route::post('oldage_pensioner_consents_store', 'oldage_pensioner_consents_store')->name('oldage_pensioner_consents_store');

        Route::get('disability_pensioner_consents_create', 'disability_pensioner_consents_create')->name('disability_pensioner_consents_create');
        Route::post('disability_pensioner_consents_store', 'disability_pensioner_consents_store')->name('disability_pensioner_consents_store');
        
        Route::get('check-benf-aadhar', 'check_benf_aadhar')->name('check_benf_aadhar');
        Route::get('check-benf-nsap-sanction-or-no', 'check_benf_nsap_sanction_or_no')->name('check_benf_nsap_sanction_or_no');
        Route::get('check-benf-udidno', 'check_benf_udidno')->name('check_benf_udidno');        
    });

    Route::prefix('oldage3500data')->name('oldage3500data.')->controller(OldAge3500Controller::class)->group(function () {
        Route::get('index', 'index')->name('index');
        Route::post('update_status', 'update_status')->name('update_status');
        Route::get('index_district', 'index_district')->name('index_district');
        Route::get('{id}/edit', 'edit')->name('edit');
        Route::post('{id}/update', 'update')->name('update');
        Route::get('{id}/delete', 'delete')->name('delete');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');

        Route::get('check-benf-aadhar', 'check_benf_aadhar')->name('check_benf_aadhar');
        Route::get('check-benf-nsap-sanction-or-no', 'check_benf_nsap_sanction_or_no')->name('check_benf_nsap_sanction_or_no');

        Route::get('oldage_index_district_block_ulb', 'oldage_index_district_block_ulb')->name('oldage_index_district_block_ulb');
        Route::get('oldage_index_district_block_ulb_gp_update', 'oldage_index_district_block_ulb_gp_update')->name('oldage_index_district_block_ulb_gp_update');
        Route::get('oldage_index_district_block_ulb_ward_update', 'oldage_index_district_block_ulb_ward_update')->name('oldage_index_district_block_ulb_ward_update');

        Route::get('oldage_duplicate_sanction_order_no', 'oldage_duplicate_sanction_order_no')->name('oldage_duplicate_sanction_order_no');
        Route::get('oldage_wrong_sanction_order_no', 'oldage_wrong_sanction_order_no')->name('oldage_wrong_sanction_order_no');
        Route::post('{id}/oldage_duplicate_sanction_order_no_update', 'oldage_duplicate_sanction_order_no_update')->name('oldage_duplicate_sanction_order_no_update');        
    });

    Route::prefix('disability3500data')->name('disability3500data.')->controller(Disability3500Controller::class)->group(function () {
        Route::get('index', 'index')->name('index');
        Route::post('update_status', 'update_status')->name('update_status');
        Route::get('index', 'index')->name('index');
        Route::get('index_district', 'index_district')->name('index_district');
        Route::get('{id}/edit', 'edit')->name('edit');
        Route::post('{id}/update', 'update')->name('update');
        Route::get('{id}/delete', 'delete')->name('delete');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');

        Route::get('check-benf-aadhar', 'check_benf_aadhar')->name('check_benf_aadhar');
        Route::get('check-benf-nsap-sanction-or-no', 'check_benf_nsap_sanction_or_no')->name('check_benf_nsap_sanction_or_no');
        Route::get('check-benf-udidno', 'check_benf_udidno')->name('check_benf_udidno');

        Route::get('disability_index_district_block_ulb', 'disability_index_district_block_ulb')->name('disability_index_district_block_ulb');
        Route::get('disability_index_district_block_ulb_gp_update', 'disability_index_district_block_ulb_gp_update')->name('disability_index_district_block_ulb_gp_update');
        Route::get('disability_index_district_block_ulb_ward_update', 'disability_index_district_block_ulb_ward_update')->name('disability_index_district_block_ulb_ward_update');

        Route::get('disability_duplicate_sanction_order_no', 'disability_duplicate_sanction_order_no')->name('disability_duplicate_sanction_order_no');
        Route::get('disability_wrong_sanction_order_no', 'disability_wrong_sanction_order_no')->name('disability_wrong_sanction_order_no');
        Route::post('{id}/disability_duplicate_sanction_order_no_update', 'disability_duplicate_sanction_order_no_update')->name('disability_duplicate_sanction_order_no_update');

        Route::get('disability_aadhar_verification', 'disability_aadhar_verification')->name('disability_aadhar_verification');
        Route::post('disability_aadhar_verification_process', 'disability_aadhar_verification_process')->name('disability_aadhar_verification_process');

        Route::get('disability_bulk_aadhar_verification', 'disability_bulk_aadhar_verification')->name('disability_bulk_aadhar_verification');
        Route::post('disability_bulk_aadhar_verification_process', 'disability_bulk_aadhar_verification_process')->name('disability_bulk_aadhar_verification_process');
    });

    Route::prefix('reportof3500data')->name('reportof3500data.')->controller(ReportOf3500Controller::class)->group(function () {
        Route::get('active_ineligible', 'active_ineligible')->name('active_ineligible');        
        Route::get('sanction_report', 'sanction_report')->name('sanction_report');
        Route::get('duplicate_sanction_order_no', 'duplicate_sanction_order_no')->name('duplicate_sanction_order_no');
        Route::get('active_ineligible_with_scheme', 'active_ineligible_with_scheme')->name('active_ineligible_with_scheme');
    });

    Route::prefix('schememigrationep')->name('schememigrationep.')->controller(SchemeMigrationEpController::class)->group(function () {
        Route::get('nsap_sanction_order_no_check', 'nsap_sanction_order_no_check')->name('nsap_sanction_order_no_check');
        Route::post('nsap_sanction_order_no_check_list', 'nsap_sanction_order_no_check_list')->name('nsap_sanction_order_no_check_list');
        Route::get('check-benf-nsap-sanction-or-no', 'check_benf_nsap_sanction_or_no')->name('check_benf_nsap_sanction_or_no');
        Route::get('{id}/oap_to_dp_migration', 'oap_to_dp_migration')->name('oap_to_dp_migration');
        Route::get('{id}/dp_to_oap_migration', 'dp_to_oap_migration')->name('dp_to_oap_migration');
        Route::post('{id}/oap_to_dp_migration_update', 'oap_to_dp_migration_update')->name('oap_to_dp_migration_update');
        Route::post('{id}/dp_to_oap_migration_update', 'dp_to_oap_migration_update')->name('dp_to_oap_migration_update');
    });

    Route::prefix('ddrc')->name('ddrc.')->controller(DdrcController::class)->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::post('staff_store', 'staff_store')->name('staff_store');
        Route::get('{id}/view_staff_details', 'view_staff_details')->name('view_staff_details');

        Route::get('ddrc-check-staff-aadhar', 'ddrc_check_staff_aadhar')->name('ddrc_check_staff_aadhar');
        Route::get('ddrc-check-staff-udidno', 'ddrc_check_staff_udidno')->name('ddrc_check_staff_udidno');
    });

    Route::prefix('monthlyPensionDisbursement')->name('monthlypensiondisbursement.')->controller(PensionMonthlyDisbursementController::class)->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('{id}/edit', 'edit')->name('edit');
        Route::post('{id}/update', 'update')->name('update');
        Route::get('{id}/delete', 'delete')->name('delete');
        Route::get('monthly_pension_disbursement_report', 'monthly_pension_disbursement_report')->name('monthly_pension_disbursement_report');
        Route::get('pension_disbursement_daily_submission', 'pension_disbursement_daily_submission')->name('pension_disbursement_daily_submission');
        Route::get('pension_disbursement_daily_not_submission', 'pension_disbursement_daily_not_submission')->name('pension_disbursement_daily_not_submission');
        Route::get('monthly_pension_disbursement_report_abstract', 'monthly_pension_disbursement_report_abstract')->name('monthly_pension_disbursement_report_abstract');
        Route::get('daily_pension_disbursement_submission', 'daily_pension_disbursement_submission')->name('daily_pension_disbursement_submission');
    });

    Route::prefix('DailyPensionDisbursement')->name('dailypensiondisbursement.')->controller(DailyPensionDisbursementController::class)->group(function () {
        Route::get('index', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::get('{id}/edit', 'edit')->name('edit');
        Route::post('{id}/update', 'update')->name('update');
        Route::get('listing_report', 'listing_report')->name('listing_report');
        Route::get('combined_report', 'combined_report')->name('combined_report');
        Route::get('pension_disbursement_daily_not_submission', 'pension_disbursement_daily_not_submission')->name('pension_disbursement_daily_not_submission');
        Route::post('update_record', 'update_record')->name('update_record');

        Route::get('daily_pension_disbursement_vs_funds_requirements', 'daily_pension_disbursement_vs_funds_requirements')->name('daily_pension_disbursement_vs_funds_requirements');
        Route::get('daily_pension_disbursement_vs_funds_requirements_beneficiaries', 'daily_pension_disbursement_vs_funds_requirements_beneficiaries')->name('daily_pension_disbursement_vs_funds_requirements_beneficiaries');
        Route::get('daily_pension_disbursement_fund_vs_funds_requirements', 'daily_pension_disbursement_fund_vs_funds_requirements')->name('daily_pension_disbursement_fund_vs_funds_requirements');
        Route::get('daily_pension_disbursement_vs_funds_requirements_beneficiaries_and_funds', 'daily_pension_disbursement_vs_funds_requirements_beneficiaries_and_funds')->name('daily_pension_disbursement_vs_funds_requirements_beneficiaries_and_funds');
        Route::get('month_wise_fund_requirement_comparison_for_district', 'month_wise_fund_requirement_comparison_for_district')->name('month_wise_fund_requirement_comparison_for_district');
        Route::get('month_wise_fund_requirement_comparison_for_block_ulb', 'month_wise_fund_requirement_comparison_for_block_ulb')->name('month_wise_fund_requirement_comparison_for_block_ulb');

        Route::get('block_ulb_wise_daily_pension_disbursement_report', 'block_ulb_wise_daily_pension_disbursement_report')->name('block_ulb_wise_daily_pension_disbursement_report');
        Route::get('block_ulb_wise_monthly_report', 'block_ulb_wise_monthly_report')->name('block_ulb_wise_monthly_report');
        Route::get('block_ulb_wise_monthly_report_ajax', 'block_ulb_wise_monthly_report_ajax')->name('block_ulb_wise_monthly_report_ajax');
        Route::get('district_wise_monthly_pension_disbursement_report', 'district_wise_monthly_pension_disbursement_report')->name('district_wise_monthly_pension_disbursement_report');
    });

    Route::prefix('SsepdNotification')->name('ssepdnotification.')->controller(SsepdNotificationController::class)->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('{id}/edit', 'edit')->name('edit');
        Route::post('{id}/update', 'update')->name('update');
    });

    Route::get('/get-address-type-content/{type}', function ($type) {
        $content = '';    
        if ($type === '1') {
            $content = view('dashboard.locations.villages.villagecontent')->render();
        } elseif ($type === '2') {
            $content = view('dashboard.locations.municipalities.municipalitycontent')->render();
        }
        $buttons = '<button type="submit" id="submitButton" name="register" class="btn btn-primary text-white from-prevent-multiple-submits">
        <i class="spinner fa fa-spinner fa-spin"></i> Submit
        </button>
        <button type="button" class="btn btn-warning">Cancel</button>';
        return response()->json([
            'content' => $content,
            'buttons' => $buttons,
        ]);
    });

    Route::resource('products', ProductController::class)->names('products');
});