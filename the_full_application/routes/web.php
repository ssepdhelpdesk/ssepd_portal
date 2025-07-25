<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Dashboard_Controllers\{
    HomeController,
    RoleController,
    PermissionController,
    UserController,
    ProductController,
    MyProfileController,
    LocationController,
    NgoRegdController,
    SpecialSchoolController,
    SpecialSchoolConstructionController
};

use App\Http\Controllers\Frontend_Controller\{
    NgoRegdFrontendController,
    LocationFrontendController
};

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return view('auth.login');
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

});

Auth::routes(['verify' => true]);

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/refresh_captcha', [HomeController::class, 'refreshCaptcha'])->name('refreshCaptcha');

Route::group(['middleware' => ['auth', 'prevent-back-history', 'track.session', 'verified'], 'prefix' => 'dashboard', 'as' => 'admin.'], function () {

    Route::view('/', 'dashboard.layouts.index')->name('dashboard');

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
        Route::post('store_school_basic_details', 'store_school_basic_details')->name('store_school_basic_details');
        Route::post('store_school_staff_details', 'store_school_staff_details')->name('store_school_staff_details');
        Route::get('view_staff_details', 'view_staff_details')->name('view_staff_details');

        Route::get('check-staff-aadhar', 'check_staff_aadhar')->name('check_staff_aadhar');
        Route::get('check-staff-udidno', 'check_staff_udidno')->name('check_staff_udidno');
    });
    
    Route::prefix('specialschoolconstructions')->name('specialschoolconstructions.')->controller(SpecialSchoolConstructionController::class)->group(function () {
        Route::get('{id}/index', 'index')->name('index');
        Route::get('construction_timeline', 'construction_timeline')->name('construction_timeline');
        Route::post('construction_timeline_store', 'construction_timeline_store')->name('construction_timeline_store');
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
    Route::get('decompose','\Lubusin\Decomposer\Controllers\DecomposerController@index');
});
