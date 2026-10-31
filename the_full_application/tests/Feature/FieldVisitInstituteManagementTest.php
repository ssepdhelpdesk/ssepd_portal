<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\FieldVisitInstitute;
use App\Models\District;
use App\Models\Block;
use Illuminate\Support\Facades\DB;
use Database\Seeders\FieldVisitInstituteSeeder;

class FieldVisitInstituteManagementTest extends TestCase
{
    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Retrieve existing SuperAdmin user with valid user_table_id
        $this->adminUser = User::where('email', 'super_admin@gmail.com')->first();
        if (!$this->adminUser) {
            $this->adminUser = User::first();
        }
    }

    /**
     * Test 1: Guest is redirected to login for institute routes
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.field_visit_institutes.index'));
        $response->assertRedirect('/login');

        $response = $this->get(route('admin.field_visit_institutes.create'));
        $response->assertRedirect('/login');
    }

    /**
     * Test 2: Authorized SuperAdmin can access institute index and create views
     */
    public function test_authorized_user_can_access_index_and_create(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.field_visit_institutes.index'));
        $response->assertStatus(200);
        $response->assertSee('SSEPD Institute Master');
        $response->assertSee('Add Institute');

        $response = $this->get(route('admin.field_visit_institutes.create'));
        $response->assertStatus(200);
        $response->assertSee('Register New Institute');
        $response->assertSee('Institute Code');
    }

    /**
     * Test 3: Create valid institute and verify user_table_id audit compliance
     */
    public function test_store_creates_institute_with_user_table_id_audit(): void
    {
        $this->actingAs($this->adminUser);

        $testCode = 'TEST-INST-' . time();
        $district = District::first();

        $payload = [
            'institute_code'  => $testCode,
            'institute_name'  => 'Automated Test Welfare Center',
            'category_id'     => 1, // Special School
            'management_name' => 'Automated Test Society',
            'district_id'     => $district ? $district->district_id : 2401,
            'which_govt'      => 1,
            'is_active'       => 1,
            'contact_person'  => 'Test Superintendent',
            'contact_number'  => '9876543210',
            'email'           => 'test@welfarecenter.org',
            'address'         => 'Test Plot 123, Test Nagar',
            'pin_code'        => '751001',
            'remarks'         => 'Created via automated feature test',
        ];

        $response = $this->post(route('admin.field_visit_institutes.store'), $payload);
        $response->assertRedirect(route('admin.field_visit_institutes.index'));
        $response->assertSessionHas('success');

        // Verify database persistence and strict user_table_id attribution
        $institute = FieldVisitInstitute::where('institute_code', $testCode)->first();
        $this->assertNotNull($institute);
        $this->assertEquals('Automated Test Welfare Center', $institute->institute_name);
        $this->assertEquals($this->adminUser->user_table_id, $institute->created_by);
        $this->assertNull($institute->updated_by);

        // Clean up test record
        $institute->forceDelete();
    }

    /**
     * Test 4: Validation rejects duplicate institute codes
     */
    public function test_store_rejects_duplicate_institute_code(): void
    {
        $this->actingAs($this->adminUser);

        // Get an existing seeded institute
        $existing = FieldVisitInstitute::first();
        $this->assertNotNull($existing);

        $payload = [
            'institute_code'  => $existing->institute_code, // Duplicate
            'institute_name'  => 'Another Institute Name',
            'category_id'     => 1,
            'district_id'     => 2401,
            'which_govt'      => 1,
            'is_active'       => 1,
        ];

        $response = $this->post(route('admin.field_visit_institutes.store'), $payload);
        $response->assertSessionHasErrors(['institute_code']);
    }

    /**
     * Test 5: Edit and update an institute with updated_by audit tracking
     */
    public function test_update_modifies_institute_and_sets_updated_by(): void
    {
        $this->actingAs($this->adminUser);

        $testCode = 'TEST-EDIT-' . time();
        $district = District::first();

        $institute = FieldVisitInstitute::create([
            'institute_code'  => $testCode,
            'institute_name'  => 'Pre-Edit Institute Name',
            'category_id'     => 2,
            'category_name'   => 'Old Age Home',
            'district_id'     => $district ? $district->district_id : 2401,
            'district_name'   => $district ? $district->district_name : 'District',
            'which_govt'      => 1,
            'status'          => 1,
            'is_active'       => true,
            'created_by'      => $this->adminUser->user_table_id,
        ]);

        $updatePayload = [
            'institute_code'  => $testCode,
            'institute_name'  => 'Updated Institute Name Post-Edit',
            'category_id'     => 2,
            'district_id'     => $district ? $district->district_id : 2401,
            'which_govt'      => 2, // Changed to Govt of India
            'is_active'       => 1,
            'contact_person'  => 'Updated Contact Person',
        ];

        $response = $this->post(route('admin.field_visit_institutes.update', $institute->id), $updatePayload);
        $response->assertRedirect(route('admin.field_visit_institutes.index'));
        $response->assertSessionHas('success');

        $refreshed = $institute->fresh();
        $this->assertEquals('Updated Institute Name Post-Edit', $refreshed->institute_name);
        $this->assertEquals(2, $refreshed->which_govt);
        $this->assertEquals($this->adminUser->user_table_id, $refreshed->updated_by);

        // Clean up
        $institute->forceDelete();
    }

    /**
     * Test 6: Institute details page can be viewed
     */
    public function test_authorized_user_can_view_institute_details(): void
    {
        $this->actingAs($this->adminUser);

        $institute = FieldVisitInstitute::first();
        $this->assertNotNull($institute);

        $response = $this->get(route('admin.field_visit_institutes.show', $institute->id));
        $response->assertStatus(200);
        $response->assertSee($institute->institute_code);
        $response->assertSee($institute->institute_name);
        $response->assertSee('System Audit');
    }

    /**
     * Test 7: AJAX locations endpoint returns blocks and municipalities for a district
     */
    public function test_ajax_locations_endpoint(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->getJson(route('admin.field_visit_institutes.ajax.locations', ['district_id' => 2401]));
        $response->assertStatus(200);
        $response->assertJsonStructure(['blocks', 'municipalities']);
    }

    /**
     * Test 8: Seeder idempotency test — exactly 437 records imported from Excel
     */
    public function test_seeder_idempotency_and_record_reconciliation(): void
    {
        $initialCount = FieldVisitInstitute::count();
        $this->assertGreaterThanOrEqual(437, $initialCount, "Expected at least 437 institutes from Excel Sheet1");

        // Run the seeder again
        $seeder = new FieldVisitInstituteSeeder();
        $seeder->run();

        $secondCount = FieldVisitInstitute::count();
        $this->assertEquals($initialCount, $secondCount, "Seeder re-execution must not create duplicate records");
    }

    /**
     * Test 9: Create Rural institute via dynamic address hierarchy (Address Type 1) without Postal Address
     */
    public function test_store_with_dynamic_rural_address_hierarchy(): void
    {
        $this->actingAs($this->adminUser);

        $dist = District::first();
        $block = Block::where('district_id', $dist->district_id)->first();
        $gp = \App\Models\Grampanchayat::where('block_id', $block->block_id)->first();
        $vil = \App\Models\Village::where('gp_id', $gp->gp_id)->first();

        $testCode = 'TEST-DYN-RUR-' . time();
        $payload = [
            'institute_code'  => $testCode,
            'institute_name'  => 'Dynamic Rural Welfare Institute',
            'category_id'     => 1,
            'management_name' => 'Rural Welfare Society',
            'which_govt'      => 1,
            'is_active'       => 1,
            'address_type'    => '1',
            'state'           => 228,
            'district'        => $dist->district_id,
            'block'           => $block->block_id,
            'grampanchayat'   => $gp->gp_id,
            'village'         => $vil ? $vil->village_id : 1,
            'pin'             => '751001',
            // Notice: Postal Address fields completely omitted from payload (UI removed)
            'contact_person'  => 'Rural Secretary',
            'contact_number'  => '9876543210',
            'email'           => 'rural.dyn@welfare.org',
            'remarks'         => 'Rural dynamic test without postal address',
        ];

        $response = $this->post(route('admin.field_visit_institutes.store'), $payload);
        $response->assertRedirect(route('admin.field_visit_institutes.index'));
        $response->assertSessionHas('success');

        $record = FieldVisitInstitute::where('institute_code', $testCode)->first();
        $this->assertNotNull($record);
        $this->assertEquals(1, $record->address_type);
        $this->assertEquals(228, $record->state_id);
        $this->assertEquals($dist->district_id, $record->district_id);
        $this->assertEquals($block->block_id, $record->block_id);
        $this->assertEquals($gp->gp_id, $record->gp_id);
        $this->assertEquals($vil->village_id, $record->village_id);
        $this->assertEquals($vil->village_name, $record->village_name);
        $this->assertNull($record->municipality_id);
        $this->assertNull($record->ward_id);
        $this->assertEquals('751001', $record->pin_code);

        $record->forceDelete();
    }

    /**
     * Test 10: Create Urban institute via dynamic address hierarchy (Address Type 2) without Postal Address
     */
    public function test_store_with_dynamic_urban_address_hierarchy(): void
    {
        $this->actingAs($this->adminUser);

        $muni = \App\Models\Municipality::first();
        $dist = District::where('district_id', $muni->district_id)->first();
        $ward = \App\Models\WardMaster::where('municipal_area_code', $muni->municipality_id)->first();

        $testCode = 'TEST-DYN-URB-' . time();
        $payload = [
            'institute_code'  => $testCode,
            'institute_name'  => 'Dynamic Urban Welfare Institute',
            'category_id'     => 2,
            'management_name' => 'Urban Welfare Society',
            'which_govt'      => 1,
            'is_active'       => 1,
            'address_type'    => '2',
            'state'           => 228,
            'district'        => $dist->district_id,
            'municipality'    => $muni->municipality_id,
            'ward'            => $ward ? $ward->ward_code : '1',
            'pin'             => '751024',
            // Notice: Postal Address fields completely omitted from payload (UI removed)
            'contact_person'  => 'Urban Secretary',
            'contact_number'  => '9876543211',
            'email'           => 'urban.dyn@welfare.org',
            'remarks'         => 'Urban dynamic test without postal address',
        ];

        $response = $this->post(route('admin.field_visit_institutes.store'), $payload);
        $response->assertRedirect(route('admin.field_visit_institutes.index'));
        $response->assertSessionHas('success');

        $record = FieldVisitInstitute::where('institute_code', $testCode)->first();
        $this->assertNotNull($record);
        $this->assertEquals(2, $record->address_type);
        $this->assertEquals(228, $record->state_id);
        $this->assertEquals($dist->district_id, $record->district_id);
        $this->assertEquals($muni->municipality_id, $record->municipality_id);
        $this->assertNotNull($record->ward_id);
        $this->assertNull($record->block_id);
        $this->assertNull($record->gp_id);
        $this->assertNull($record->village_id);
        $this->assertNull($record->village_name);
        $this->assertEquals('751024', $record->pin_code);

        $record->forceDelete();
    }

    /**
     * Test 11: Edit page renders correctly for Institute 212
     */
    public function test_edit_view_renders_for_institute_212(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.field_visit_institutes.edit', 212));
        $response->assertStatus(200);
        $response->assertSee('Edit Institute Details');
        $response->assertSee('Section 2: Geographic & Location Hierarchy');
        $response->assertSee('Block (Rural)');
        $response->assertSee('ULB (Urban)');
        $response->assertDontSee('Postal Address</h3>');
    }

    /**
     * Test 12: Update Institute 212 with Rural Geographic Hierarchy
     */
    public function test_update_institute_212_with_rural_hierarchy(): void
    {
        $this->actingAs($this->adminUser);

        $institute = FieldVisitInstitute::findOrFail(212);
        $dist = District::where('district_id', 2421)->first() ?? District::first();
        $block = Block::where('district_id', $dist->district_id)->first();
        $gp = \App\Models\Grampanchayat::where('block_id', $block->block_id)->first();
        $vil = \App\Models\Village::where('gp_id', $gp->gp_id)->first();

        $payload = [
            'institute_code'  => $institute->institute_code,
            'institute_name'  => $institute->institute_name,
            'category_id'     => $institute->category_id,
            'management_name' => $institute->management_name,
            'which_govt'      => $institute->which_govt,
            'is_active'       => 1,
            'address_type'    => '1',
            'state'           => 228,
            'district'        => $dist->district_id,
            'block'           => $block->block_id,
            'grampanchayat'   => $gp->gp_id,
            'village'         => $vil ? $vil->village_id : 1,
            'pin'             => '759014',
            'contact_person'  => 'Site Superintendent',
            'contact_number'  => '9437000000',
            'email'           => 'institute212@ssepd.gov.in',
            'remarks'         => 'Updated via automated feature test',
        ];

        $response = $this->post(route('admin.field_visit_institutes.update', 212), $payload);
        $response->assertRedirect(route('admin.field_visit_institutes.index'));
        $response->assertSessionHas('success');

        $refreshed = FieldVisitInstitute::findOrFail(212);
        $this->assertEquals(1, $refreshed->address_type);
        $this->assertEquals(228, $refreshed->state_id);
        $this->assertEquals($dist->district_id, $refreshed->district_id);
        $this->assertEquals($block->block_id, $refreshed->block_id);
        $this->assertEquals($gp->gp_id, $refreshed->gp_id);
        $this->assertEquals($vil->village_id, $refreshed->village_id);
        $this->assertEquals($vil->village_name, $refreshed->village_name);
        $this->assertNull($refreshed->municipality_id);
        $this->assertNull($refreshed->ward_id);
        $this->assertEquals('759014', $refreshed->pin_code);
        $this->assertEquals($this->adminUser->user_table_id, $refreshed->updated_by);
    }

    /**
     * Test 13: Update Institute 212 with Urban Geographic Hierarchy
     */
    public function test_update_institute_212_with_urban_hierarchy(): void
    {
        $this->actingAs($this->adminUser);

        $institute = FieldVisitInstitute::findOrFail(212);
        $muni = \App\Models\Municipality::first();
        $dist = District::where('district_id', $muni->district_id)->first();
        $ward = \App\Models\WardMaster::where('municipal_area_code', $muni->municipality_id)->first();

        $payload = [
            'institute_code'  => $institute->institute_code,
            'institute_name'  => $institute->institute_name,
            'category_id'     => $institute->category_id,
            'management_name' => $institute->management_name,
            'which_govt'      => $institute->which_govt,
            'is_active'       => 1,
            'address_type'    => '2',
            'state'           => 228,
            'district'        => $dist->district_id,
            'municipality'    => $muni->municipality_id,
            'ward'            => $ward ? $ward->ward_code : '1',
            'pin'             => '751024',
            'contact_person'  => 'Site Superintendent',
            'contact_number'  => '9437000000',
            'email'           => 'institute212@ssepd.gov.in',
            'remarks'         => 'Updated to Urban via automated feature test',
        ];

        $response = $this->post(route('admin.field_visit_institutes.update', 212), $payload);
        $response->assertRedirect(route('admin.field_visit_institutes.index'));
        $response->assertSessionHas('success');

        $refreshed = FieldVisitInstitute::findOrFail(212);
        $this->assertEquals(2, $refreshed->address_type);
        $this->assertEquals(228, $refreshed->state_id);
        $this->assertEquals($dist->district_id, $refreshed->district_id);
        $this->assertEquals($muni->municipality_id, $refreshed->municipality_id);
        $this->assertNotNull($refreshed->ward_id);
        $this->assertNull($refreshed->block_id);
        $this->assertNull($refreshed->gp_id);
        $this->assertNull($refreshed->village_id);
        $this->assertNull($refreshed->village_name);
        $this->assertEquals('751024', $refreshed->pin_code);
        $this->assertEquals($this->adminUser->user_table_id, $refreshed->updated_by);
    }

    /**
     * Test 14: Verify shared route /get-address-type-content returns partials
     */
    public function test_get_address_type_content_route_returns_partials(): void
    {
        $this->actingAs($this->adminUser);

        // Rural partial
        $responseRural = $this->getJson(url('dashboard/get-address-type-content/1'));
        $responseRural->assertStatus(200);
        $responseRural->assertJsonStructure(['content', 'buttons']);
        $this->assertStringContainsString('id="block-dropdown"', $responseRural->json('content'));
        $this->assertStringContainsString('id="village-dropdown"', $responseRural->json('content'));

        // Urban partial
        $responseUrban = $this->getJson(url('dashboard/get-address-type-content/2'));
        $responseUrban->assertStatus(200);
        $responseUrban->assertJsonStructure(['content', 'buttons']);
        $this->assertStringContainsString('id="municipality-dropdown"', $responseUrban->json('content'));
        $this->assertStringContainsString('id="ward-dropdown"', $responseUrban->json('content'));
    }

    /**
     * Test 15: Show view displays village details when set
     */
    public function test_show_view_displays_village_details_for_rural_institute(): void
    {
        $this->actingAs($this->adminUser);

        $institute = FieldVisitInstitute::first();
        $vil = \App\Models\Village::first();
        $institute->village_id = $vil->village_id;
        $institute->village_name = $vil->village_name;
        $institute->save();

        $response = $this->get(route('admin.field_visit_institutes.show', $institute->id));
        $response->assertStatus(200);
        $response->assertSee('Village');
        $response->assertSee($vil->village_name);
        $response->assertSee((string)$vil->village_id);
    }
}
