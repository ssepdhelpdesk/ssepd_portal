<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\FieldVisit;
use App\Models\FieldVisitInstitute;
use App\Models\District;
use App\Models\Block;
use App\Models\Municipality;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FieldVisitReportsTest extends TestCase
{
    protected $superAdmin;
    protected $dssoUser;
    protected $bssoUser;
    protected $unauthorizedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::where('role_id', 1)->first() ?? User::where('email', 'super_admin@gmail.com')->first();

        // DSSO User (Role 9)
        $this->dssoUser = User::where('role_id', 9)->whereNotNull('posted_district')->first();

        // BSSO User (Role 4)
        $this->bssoUser = User::where('role_id', 4)->whereNotNull('posted_block')->first();

        // User without reporting permission (ensure email verified and no force password change)
        $this->unauthorizedUser = User::whereNotIn('role_id', [1, 2, 4, 5, 6, 8, 9, 10, 11, 12, 13, 14, 15, 25])
            ->where(function ($q) {
                $q->whereNull('otp_for_forgot_password')->orWhere('otp_for_forgot_password', '!=', '123456');
            })
            ->whereNotNull('email_verified_at')
            ->first();
    }

    /**
     * 1. Guest is redirected to login.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.field_visit_reports.summary'));
        $response->assertRedirect('/login');

        $response = $this->get(route('admin.field_visit_reports.detailed'));
        $response->assertRedirect('/login');
    }

    /**
     * 2. Unauthorized user without permission gets 403.
     */
    public function test_unauthorized_user_without_permission_is_forbidden(): void
    {
        if (!$this->unauthorizedUser) {
            $this->markTestSkipped('No user without reporting role found.');
        }

        $this->actingAs($this->unauthorizedUser);
        $response = $this->get(route('admin.field_visit_reports.summary'));
        $response->assertStatus(403);
    }

    /**
     * 3. SuperAdmin can access all 5 reports.
     */
    public function test_superadmin_can_access_all_five_reports(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('admin.field_visit_reports.summary'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.field_visit.reports.summary');
        $response->assertSee('Field Visit Summary');
        $response->assertSee('Total Visits');

        $response = $this->get(route('admin.field_visit_reports.geographical'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.field_visit.reports.geographical');

        $response = $this->get(route('admin.field_visit_reports.detailed'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.field_visit.reports.detailed');

        $response = $this->get(route('admin.field_visit_reports.coverage'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.field_visit.reports.coverage');

        $response = $this->get(route('admin.field_visit_reports.datewise'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.field_visit.reports.datewise');
    }

    /**
     * 4. DSSO (District user) gets district-scoped data.
     */
    public function test_dsso_user_is_scoped_to_assigned_district(): void
    {
        if (!$this->dssoUser) {
            $this->markTestSkipped('No DSSO user found.');
        }

        $this->actingAs($this->dssoUser);
        $response = $this->get(route('admin.field_visit_reports.summary'));
        $response->assertStatus(200);
        $response->assertSee('Access Level: District');
        $response->assertSee('Subdivision-wise Visit Breakdown');
    }

    /**
     * 5. BSSO (Block user) gets block-scoped data.
     */
    public function test_bsso_user_is_scoped_to_assigned_block(): void
    {
        if (!$this->bssoUser) {
            $this->markTestSkipped('No BSSO user found.');
        }

        $this->actingAs($this->bssoUser);
        $response = $this->get(route('admin.field_visit_reports.summary'));
        $response->assertStatus(200);
        $response->assertSee('Access Level: Block');
        $response->assertSee('Gram Panchayat-wise Visit Breakdown');
    }

    /**
     * 6. User tampering with district parameter cannot escape assigned jurisdiction.
     */
    public function test_user_cannot_access_unauthorized_jurisdiction_via_query_params(): void
    {
        if (!$this->bssoUser) {
            $this->markTestSkipped('No BSSO user found.');
        }

        $this->actingAs($this->bssoUser);
        // BSSO tries to query a foreign district (e.g. 9999 or 2423)
        $response = $this->get(route('admin.field_visit_reports.detailed', ['district_id' => 2423]));
        $response->assertStatus(200);

        // Should NOT see any visits from district 2423 unless it matches the user's block
        $foreignVisits = FieldVisit::where('district_id', 2423)
            ->where('block_id', '!=', $this->bssoUser->posted_block)
            ->get();

        foreach ($foreignVisits as $fv) {
            $response->assertDontSee($fv->visit_reference_no);
        }
    }

    /**
     * 7. Excel export routes work properly.
     */
    public function test_excel_export_routes_return_successful_downloads(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('admin.field_visit_reports.summary.export'));
        $response->assertStatus(200);
        $response->assertHeader('content-disposition');

        $response = $this->get(route('admin.field_visit_reports.detailed.export'));
        $response->assertStatus(200);
        $response->assertHeader('content-disposition');

        $response = $this->get(route('admin.field_visit_reports.coverage.export'));
        $response->assertStatus(200);
        $response->assertHeader('content-disposition');

        $response = $this->get(route('admin.field_visit_reports.datewise.export'));
        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
    }

    /**
     * 8. AJAX cascading filter endpoints work and return JSON.
     */
    public function test_ajax_cascading_filter_endpoints(): void
    {
        $this->actingAs($this->superAdmin);

        $district = District::first();
        if ($district) {
            $response = $this->get(route('admin.field_visit_reports.ajax.subdivisions', ['district_id' => $district->district_id]));
            $response->assertStatus(200);
            $response->assertJsonStructure([
                '*' => ['subdivision_id', 'subdivision_name']
            ]);

            $response = $this->get(route('admin.field_visit_reports.ajax.blocks', ['district_id' => $district->district_id]));
            $response->assertStatus(200);
            $response->assertJsonStructure([
                '*' => ['block_id', 'block_name']
            ]);
        }
    }

    /**
     * 9. Sidebar navigation displays Field Visit Reports menu.
     */
    public function test_sidebar_displays_field_visit_reports_for_authorized_user(): void
    {
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('admin.field_visit_reports.summary'));
        $response->assertSee('Field Visit Reports');
        $response->assertSee('Field Visit Summary');
        $response->assertSee('Geographical Analysis');
        $response->assertSee('Detailed Visit Report');
        $response->assertSee('Institution Coverage Report');
        $response->assertSee('Date-wise Visit Report');
    }
}
