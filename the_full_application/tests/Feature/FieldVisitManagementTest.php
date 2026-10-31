<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\FieldVisitType;
use App\Models\FieldVisit;
use App\Models\FieldVisitEvidence;
use App\Models\FieldVisitDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FieldVisitManagementTest extends TestCase
{
    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Retrieve existing SuperAdmin user
        $this->adminUser = User::where('email', 'super_admin@gmail.com')->first();
        if (!$this->adminUser) {
            $this->adminUser = User::first();
        }
    }

    /**
     * Test 1: Guest is redirected to login for field visit routes
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.field_visits.index'));
        $response->assertRedirect('/login');

        $response = $this->get(route('admin.field_visits.create'));
        $response->assertRedirect('/login');
    }

    /**
     * Test 2: SuperAdmin can access visit list and create pages
     */
    public function test_authorized_user_can_access_index_and_create(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.field_visits.index'));
        $response->assertStatus(200);
        $response->assertSee('Field Visit Management');
        $response->assertSee('Record Field Visit');

        $response = $this->get(route('admin.field_visits.create'));
        $response->assertStatus(200);
        $response->assertSee('Create Field Visit');
        $response->assertSee('Capture Geo-Tagged Photo');
        $response->assertSee('2. Office / Institution & Location', false);
        $response->assertSee('category_id');
        $response->assertSee('institution_name');
        $response->assertSee('address_type');
        $response->assertSee('postal_address_container');
        $response->assertSee('dynamic-address-content');
        $response->assertDontSee('isLimited');
    }

    /**
     * Test 3: Model CRUD lifecycle with user_table_id audit compliance
     */
    public function test_field_visit_crud_and_audit_attribution(): void
    {
        $this->actingAs($this->adminUser);

        $type = FieldVisitType::first();
        if (!$type) {
            $type = FieldVisitType::create([
                'type_name' => 'Test Unit Inspection',
                'type_code' => 'TUI',
                'description' => 'Created during automated testing',
                'is_active' => true,
                'created_by' => $this->adminUser->user_table_id,
            ]);
        }

        // Generate visit reference
        $dateStr = now()->format('Ymd');
        $refNo = 'FV-' . $dateStr . '-TEST-' . rand(1000, 9999);

        // CREATE
        $visit = FieldVisit::create([
            'visit_reference_no' => $refNo,
            'visit_date' => now()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '11:30:00',
            'field_visit_type_id' => $type->id,
            'visiting_officer_id' => $this->adminUser->user_table_id,
            'status' => 'submitted',
            'institution_type' => 'SPECIAL_SCHOOL',
            'institution_name' => 'Unit Test Academy',
            'district_id' => 1,
            'visit_purpose' => 'Automated test of field visit management',
            'areas_inspected' => 'Classrooms, sanitation facilities, library',
            'key_observations' => 'Facility is well maintained and active',
            'findings' => 'All attendance logs verified',
            'is_follow_up_required' => false,
            'overall_remarks' => 'Test visit created successfully',
            'created_by' => $this->adminUser->user_table_id,
        ]);

        $this->assertNotNull($visit->id);
        $this->assertEquals($this->adminUser->user_table_id, $visit->created_by);
        $this->assertEquals($this->adminUser->user_table_id, $visit->visiting_officer_id);

        // Ensure clean slate for this visit id
        FieldVisitEvidence::where('field_visit_id', $visit->id)->forceDelete();
        FieldVisitDocument::where('field_visit_id', $visit->id)->forceDelete();

        // CREATE EVIDENCE
        $evidence = FieldVisitEvidence::create([
            'field_visit_id' => $visit->id,
            'photo_path' => 'field_visits/test_photo.jpg',
            'original_filename' => 'capture_test.jpg',
            'file_size' => 10240,
            'mime_type' => 'image/jpeg',
            'file_checksum' => hash('sha256', 'dummy_content'),
            'latitude' => 20.2961,
            'longitude' => 85.8245,
            'gps_accuracy' => 5.2,
            'client_capture_time' => now(),
            'location_reading_time' => now(),
            'capture_source' => 'DEVICE_CAMERA',
            'created_by' => $this->adminUser->user_table_id,
        ]);

        $this->assertNotNull($evidence->id);
        $this->assertEquals($visit->id, $evidence->field_visit_id);
        $this->assertEquals($this->adminUser->user_table_id, $evidence->created_by);

        // CREATE DOCUMENT
        $doc = FieldVisitDocument::create([
            'field_visit_id' => $visit->id,
            'document_name' => 'Test Report',
            'file_path' => 'field_visits/docs/test_doc.pdf',
            'original_filename' => 'inspection_report.pdf',
            'file_size' => 20480,
            'mime_type' => 'application/pdf',
            'file_checksum' => hash('sha256', 'dummy_pdf'),
            'created_by' => $this->adminUser->user_table_id,
        ]);

        $this->assertNotNull($doc->id);
        $this->assertEquals($this->adminUser->user_table_id, $doc->created_by);

        // READ & RELATIONSHIPS
        $loadedVisit = FieldVisit::with(['visitType', 'visitingOfficer', 'evidences', 'documents'])->find($visit->id);
        $this->assertCount(1, $loadedVisit->evidences);
        $this->assertCount(1, $loadedVisit->documents);
        $this->assertEquals($type->id, $loadedVisit->visitType->id);
        $this->assertEquals($this->adminUser->name, $loadedVisit->visitingOfficer->name);

        // TEST SHOW PAGE
        $showResponse = $this->get(route('admin.field_visits.show', $visit->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee($refNo);
        $showResponse->assertSee('Unit Test Academy');
        $showResponse->assertSee('20.296100');

        // TEST EDIT PAGE
        $editResponse = $this->get(route('admin.field_visits.edit', $visit->id));
        $editResponse->assertStatus(200);
        $editResponse->assertSee($refNo);

        $institute = \App\Models\FieldVisitInstitute::where('category_name', 'Special School')->first()
            ?? \App\Models\FieldVisitInstitute::where('category_id', '>', 3)->first();

        // UPDATE
        $updateResponse = $this->post(route('admin.field_visits.update', $visit->id), [
            'visit_date' => now()->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'field_visit_type_id' => $type->id,
            'status' => 'reviewed',
            'institution_type' => 'Government Office',
            'category_id' => $institute->category_id,
            'institution_id' => $institute->id,
            'institution_name' => $institute->institute_name,
            'contact_number' => '9876543210',
            'address_type' => 1,
            'district_id' => $institute->district_id ?: 2421,
            'block' => $institute->block_id ?: 1,
            'grampanchayat' => $institute->gp_id ?: 2,
            'village_id' => $institute->village_id ?: 379717,
            'visit_purpose' => 'Updated automated test purpose',
            'key_observations' => 'Updated observations',
            'is_follow_up_required' => '0',
            'overall_remarks' => 'Updated remarks',
        ]);

        $updateResponse->assertRedirect(route('admin.field_visits.show', $visit->id));

        $visit->refresh();
        $this->assertEquals($institute->institute_name, $visit->institution_name);
        $this->assertEquals('reviewed', $visit->status);
        $this->assertEquals($this->adminUser->user_table_id, $visit->updated_by);

        // SOFT DELETE
        $deleteResponse = $this->delete(route('admin.field_visits.destroy', $visit->id));
        $deleteResponse->assertRedirect(route('admin.field_visits.index'));

        $this->assertSoftDeleted('field_visits', ['id' => $visit->id]);
        $visit->refresh();
        $this->assertEquals($this->adminUser->user_table_id, $visit->deleted_by);

        // Clean up test records completely
        FieldVisitEvidence::where('field_visit_id', $visit->id)->forceDelete();
        FieldVisitDocument::where('field_visit_id', $visit->id)->forceDelete();
        $visit->forceDelete();
    }

    /**
     * Test 4: Sidebar menu rendering contains all 4 required Field Visit items
     */
    public function test_sidebar_menu_contains_all_field_visit_items(): void
    {
        $this->actingAs($this->adminUser);

        $html = view('dashboard.layouts.leftbar')->render();

        $this->assertStringContainsString('Field Visit Management', $html);
        $this->assertStringContainsString(route('admin.field_visits.index'), $html);
        $this->assertStringContainsString(route('admin.field_visits.create'), $html);
        $this->assertStringContainsString(route('admin.field_visit_types.index'), $html);
        $this->assertStringContainsString(route('admin.field_visit_types.create'), $html);
    }

    /**
     * Test 5: CSV Export works and returns correct headers
     */
    public function test_field_visit_export_csv(): void
    {
        $this->withoutExceptionHandling();
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.field_visits.export'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('text/csv', (string)$response->headers->get('Content-Type'));
        $this->assertStringContainsString('field_visits_', (string)$response->headers->get('Content-Disposition'));
    }

    /**
     * Test 6: AJAX Institution loader returns correct JSON payload
     */
    public function test_ajax_institutions_loader(): void
    {
        $this->actingAs($this->adminUser);

        // Test Special School list
        $response = $this->get(route('admin.field_visits.ajax.institutions', ['type' => 'SPECIAL_SCHOOL']));
        $response->assertStatus(200);
        $this->assertIsArray($response->json());

        // Test DDRC list
        $response = $this->get(route('admin.field_visits.ajax.institutions', ['type' => 'DDRC']));
        $response->assertStatus(200);
        $this->assertIsArray($response->json());

        // Test PIA Institute list
        $response = $this->get(route('admin.field_visits.ajax.institutions', ['type' => 'PIA_INSTITUTE']));
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    /**
     * Test 7: Audit rule strictly enforces user_table_id and rejects when missing
     */
    public function test_audit_rule_enforces_user_table_id_and_rejects_missing(): void
    {
        // Create a real user without user_table_id
        $userWithoutTableId = User::create([
            'user_id' => 'no_table_' . uniqid(),
            'name' => 'No Table ID Officer',
            'email' => 'notableid_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'user_table_id' => null,
            'is_full_access_to_portal' => 0,
        ]);
        $userWithoutTableId->email_verified_at = now();
        $userWithoutTableId->save();

        // Give permission to pass the Spatie route middleware
        $userWithoutTableId->givePermissionTo('field-visit-create');
        $userWithoutTableId->givePermissionTo('field-visit-access');

        $this->actingAs($userWithoutTableId);

        $type = FieldVisitType::first();

        // Attempting to store a visit should be rejected at controller level
        $response = $this->from(route('admin.field_visits.create'))->post(route('admin.field_visits.store'), [
            'visit_date' => now()->format('Y-m-d'),
            'field_visit_type_id' => $type ? $type->id : 1,
            'status' => 'submitted',
            'visit_purpose' => 'Testing audit rejection',
            'institution_type' => 'OTHER',
            'institution_name' => 'Test Location',
        ]);

        $response->assertSessionHas('error');

        // Cleanup
        $userWithoutTableId->forceDelete();
    }

    /**
     * Test 8: User without permission is blocked / redirected
     */
    public function test_unauthorized_user_is_forbidden(): void
    {
        // Create an unprivileged user without any field-visit permissions
        $unprivilegedUser = User::create([
            'user_id' => 'unprivileged_' . uniqid(),
            'name' => 'Unprivileged Officer',
            'email' => 'unprivileged_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'user_table_id' => 777777,
            'is_full_access_to_portal' => 0,
        ]);

        $this->actingAs($unprivilegedUser);

        // Accessing visit list should be blocked (302 redirect or 403)
        $response = $this->get(route('admin.field_visits.index'));
        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));

        // Accessing visit create should be blocked
        $response = $this->get(route('admin.field_visits.create'));
        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));

        // Cleanup
        $unprivilegedUser->forceDelete();
    }

    /**
     * Test 9: Contact number validation rules reject non-digits, too few, too many, or empty
     */
    public function test_contact_number_validation_rules(): void
    {
        $this->actingAs($this->adminUser);

        $type = FieldVisitType::first();
        $institute = \App\Models\FieldVisitInstitute::where('category_name', 'Special School')->first()
            ?? \App\Models\FieldVisitInstitute::where('category_id', '>', 3)->first();

        $basePayload = [
            'visit_date' => now()->format('Y-m-d'),
            'field_visit_type_id' => $type->id,
            'status' => 'submitted',
            'visit_purpose' => 'Contact validation test',
            'institution_type' => 'Government Office',
            'category_id' => $institute->category_id,
            'institution_id' => $institute->id,
            'address_type' => 1,
            'district' => $institute->district_id,
        ];

        // 1. Rejects alphabetic characters
        $res = $this->from(route('admin.field_visits.create'))->post(route('admin.field_visits.store'), array_merge($basePayload, [
            'contact_number' => '987654321A',
        ]));
        $res->assertSessionHasErrors('contact_number');

        // 2. Rejects special characters
        $res = $this->from(route('admin.field_visits.create'))->post(route('admin.field_visits.store'), array_merge($basePayload, [
            'contact_number' => '98765-4321',
        ]));
        $res->assertSessionHasErrors('contact_number');

        // 3. Rejects less than 10 digits
        $res = $this->from(route('admin.field_visits.create'))->post(route('admin.field_visits.store'), array_merge($basePayload, [
            'contact_number' => '987654321',
        ]));
        $res->assertSessionHasErrors('contact_number');

        // 4. Rejects more than 10 digits
        $res = $this->from(route('admin.field_visits.create'))->post(route('admin.field_visits.store'), array_merge($basePayload, [
            'contact_number' => '98765432101',
        ]));
        $res->assertSessionHasErrors('contact_number');

        // 5. Rejects empty
        $res = $this->from(route('admin.field_visits.create'))->post(route('admin.field_visits.store'), array_merge($basePayload, [
            'contact_number' => '',
        ]));
        $res->assertSessionHasErrors('contact_number');

        // 6. Rejects institute that does not belong to selected category
        $res = $this->from(route('admin.field_visits.create'))->post(route('admin.field_visits.store'), array_merge($basePayload, [
            'contact_number' => '9876543210',
            'category_id' => 999999, // Mismatched category
        ]));
        $res->assertSessionHasErrors('institution_id');
    }

    /**
     * Test 10: Category-filtered institutions AJAX endpoint returns distinct matching institutes
     */
    public function test_ajax_category_filtered_institutions(): void
    {
        $this->actingAs($this->adminUser);

        $institute = \App\Models\FieldVisitInstitute::where('category_name', 'Special School')->first()
            ?? \App\Models\FieldVisitInstitute::where('category_id', '>', 3)->first();

        $response = $this->get(route('admin.field_visits.ajax.institutions', [
            'category_id' => $institute->category_id,
        ]));

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals('success', $data['status']);
        $this->assertNotEmpty($data['data']);

        foreach ($data['data'] as $item) {
            $this->assertEquals($institute->category_id, $item['category_id']);
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('name', $item);
            $this->assertArrayHasKey('code', $item);
            $this->assertArrayHasKey('address_type', $item);
        }
    }

    /**
     * Test 11: Store and Edit field visit with Rural address hierarchy
     */
    public function test_store_and_edit_field_visit_rural_hierarchy(): void
    {
        $this->actingAs($this->adminUser);

        $type = FieldVisitType::first();
        $institute = \App\Models\FieldVisitInstitute::where('category_name', 'Special School')->first()
            ?? \App\Models\FieldVisitInstitute::where('category_id', '>', 3)->first();

        // STORE Rural visit
        $postData = [
            'visit_date' => now()->format('Y-m-d'),
            'start_time' => '09:30',
            'end_time' => '11:00',
            'field_visit_type_id' => $type->id,
            'status' => 'submitted',
            'visit_purpose' => 'Rural hierarchy verification visit',
            'institution_type' => 'Government Office',
            'category_id' => $institute->category_id,
            'institution_id' => $institute->id,
            'organization_code' => $institute->institute_code,
            'contact_person' => 'Shri Ramesh Kumar',
            'contact_number' => '9437123456',
            'address_type' => 1,
            'state' => 228,
            'district' => $institute->district_id ?: 2421,
            'block' => 1,
            'grampanchayat' => 2,
            'village_id' => 379717,
            'institution_address' => 'At/PO Tentol, Via Anugul',
            'is_follow_up_required' => '0',
            'overall_remarks' => 'Rural visit completed successfully',
        ];

        $response = $this->post(route('admin.field_visits.store'), $postData);
        $response->assertSessionHasNoErrors();

        $visit = FieldVisit::where('contact_number', '9437123456')
            ->orderByDesc('id')
            ->first();

        $this->assertNotNull($visit);
        $this->assertEquals(1, $visit->address_type);
        $this->assertEquals(379717, $visit->village_id);
        $this->assertEquals(1, $visit->block_id);
        $this->assertEquals(2, $visit->gp_id);
        $this->assertNull($visit->municipality_id);
        $this->assertNull($visit->ward_id);
        $this->assertEquals('9437123456', $visit->contact_number);
        $this->assertEquals('Government Office', $visit->institution_type);
        $this->assertEquals($institute->id, $visit->institution_id);

        // TEST EDIT PAGE RESTORATION
        $editRes = $this->get(route('admin.field_visits.edit', $visit->id));
        $editRes->assertStatus(200);
        $editRes->assertSee($institute->category_name);
        $editRes->assertSee('9437123456');
        $editRes->assertSee('Shri Ramesh Kumar');

        // UPDATE visit
        $updateData = array_merge($postData, [
            'contact_person' => 'Shri Ramesh Kumar (Updated)',
            'contact_number' => '9437999888',
            'overall_remarks' => 'Rural visit updated successfully',
        ]);

        $updateRes = $this->post(route('admin.field_visits.update', $visit->id), $updateData);
        $updateRes->assertRedirect(route('admin.field_visits.show', $visit->id));

        $visit->refresh();
        $this->assertEquals('Shri Ramesh Kumar (Updated)', $visit->contact_person);
        $this->assertEquals('9437999888', $visit->contact_number);
        $this->assertEquals(379717, $visit->village_id);
        $this->assertEquals($this->adminUser->user_table_id, $visit->updated_by);

        // Clean up
        $visit->forceDelete();
    }

    /**
     * Test 12: Store and Edit field visit with Urban address hierarchy
     */
    public function test_store_and_edit_field_visit_urban_hierarchy(): void
    {
        $this->actingAs($this->adminUser);

        $type = FieldVisitType::first();
        $institute = \App\Models\FieldVisitInstitute::where('category_name', 'Special School')->first()
            ?? \App\Models\FieldVisitInstitute::where('category_id', '>', 3)->first();

        // STORE Urban visit
        $postData = [
            'visit_date' => now()->format('Y-m-d'),
            'start_time' => '14:00',
            'end_time' => '16:00',
            'field_visit_type_id' => $type->id,
            'status' => 'submitted',
            'visit_purpose' => 'Urban hierarchy verification visit',
            'institution_type' => 'Other Welfare Facility',
            'category_id' => $institute->category_id,
            'institution_id' => $institute->id,
            'organization_code' => $institute->institute_code,
            'contact_person' => 'Smt. Anjali Sahoo',
            'contact_number' => '9861001122',
            'address_type' => 2,
            'state' => 228,
            'district' => $institute->district_id ?: 2421,
            'municipality' => 15,
            'ward' => 7,
            'institution_address' => 'Ward 7, Main Road, Municipality Area',
            'is_follow_up_required' => '0',
            'overall_remarks' => 'Urban visit completed successfully',
        ];

        $response = $this->post(route('admin.field_visits.store'), $postData);
        $response->assertSessionHasNoErrors();

        $visit = FieldVisit::where('contact_number', '9861001122')
            ->orderByDesc('id')
            ->first();

        $this->assertNotNull($visit);
        $this->assertEquals(2, $visit->address_type);
        $this->assertEquals(15, $visit->municipality_id);
        $this->assertEquals(7, $visit->ward_id);
        $this->assertNull($visit->block_id);
        $this->assertNull($visit->village_id);
        $this->assertEquals('9861001122', $visit->contact_number);
        $this->assertEquals('Other Welfare Facility', $visit->institution_type);

        // UPDATE visit to switch contact and verify
        $updateData = array_merge($postData, [
            'contact_number' => '9861998877',
            'overall_remarks' => 'Urban visit updated successfully',
        ]);

        $updateRes = $this->post(route('admin.field_visits.update', $visit->id), $updateData);
        $updateRes->assertRedirect(route('admin.field_visits.show', $visit->id));

        $visit->refresh();
        $this->assertEquals('9861998877', $visit->contact_number);
        $this->assertEquals(15, $visit->municipality_id);
        $this->assertEquals(7, $visit->ward_id);
        $this->assertEquals($this->adminUser->user_table_id, $visit->updated_by);

        // Clean up
        $visit->forceDelete();
    }

    /**
     * Test 13: Category dropdown ordering rule (category_id = 3 'Other' must always be last).
     */
    public function test_category_dropdown_ordering_with_other_always_last(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.field_visits.create'));
        $response->assertStatus(200);

        $categories = $response->viewData('categories');
        $this->assertNotEmpty($categories);

        $lastCat = $categories->last();
        $this->assertEquals(3, (int)$lastCat->category_id);
        $this->assertEquals('Other', $lastCat->category_name);
    }

    /**
     * Test 14: Case A - SSEPD State Office (Dropdown mode + Complete Rural Address).
     */
    public function test_store_and_edit_ssepd_state_office_dropdown_mode_with_complete_address(): void
    {
        $this->actingAs($this->adminUser);

        $type = FieldVisitType::first();
        $stateOfficeInst = \App\Models\FieldVisitInstitute::where('category_id', 1)->first();
        $this->assertNotNull($stateOfficeInst, 'SSEPD State Office institute record must exist');

        $postData = [
            'visit_date' => now()->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'field_visit_type_id' => $type->id,
            'status' => 'submitted',
            'visit_purpose' => 'SSEPD State Office verification visit',
            'category_id' => 1,
            'institution_id' => $stateOfficeInst->id,
            'contact_person' => 'Director SSEPD',
            'contact_number' => '9437112233',
            'address_type' => 1, // Rural
            'state' => 228,
            'district' => $stateOfficeInst->district_id ?: 2421,
            'block' => 1,
            'grampanchayat' => 2,
            'village_id' => 379717,
            'institution_address' => 'State Office Premises, Block 1',
            'is_follow_up_required' => '0',
            'overall_remarks' => 'State Office visit recorded',
        ];

        $response = $this->post(route('admin.field_visits.store'), $postData);
        $response->assertSessionHasNoErrors();

        $visit = FieldVisit::where('contact_number', '9437112233')->orderByDesc('id')->first();
        $this->assertNotNull($visit);
        $this->assertEquals('SSEPD State Office', $visit->institution_type);
        $this->assertEquals($stateOfficeInst->id, $visit->institution_id);
        $this->assertEquals(1, $visit->address_type);
        $this->assertEquals(2, $visit->gp_id);
        $this->assertEquals(379717, $visit->village_id);
        $this->assertEquals('State Office Premises, Block 1', $visit->institution_address);

        // Edit page verification
        $editRes = $this->get(route('admin.field_visits.edit', $visit->id));
        $editRes->assertStatus(200);
        $editRes->assertSee('SSEPD State Office');
        $editRes->assertSee('9437112233');

        $visit->forceDelete();
    }

    /**
     * Test 15: Case B - SSEPD Field Office (Manual text input mode + Complete Urban Address).
     */
    public function test_store_and_edit_ssepd_field_office_manual_mode_with_complete_address(): void
    {
        $this->actingAs($this->adminUser);

        $type = FieldVisitType::first();

        $postData = [
            'visit_date' => now()->format('Y-m-d'),
            'start_time' => '11:00',
            'end_time' => '13:00',
            'field_visit_type_id' => $type->id,
            'status' => 'submitted',
            'visit_purpose' => 'SSEPD Field Office inspection',
            'category_id' => 2,
            'institution_name' => 'DSSO Office, Cuttack Collectorate',
            'contact_person' => 'District Social Security Officer',
            'contact_number' => '9861223344',
            'address_type' => 2, // Urban
            'state' => 228,
            'district' => 2421,
            'municipality' => 15,
            'ward' => 7,
            'institution_address' => 'DSSO Office, Collectorate Compound',
            'is_follow_up_required' => '0',
            'overall_remarks' => 'Field Office visit recorded',
        ];

        $response = $this->post(route('admin.field_visits.store'), $postData);
        $response->assertSessionHasNoErrors();

        $visit = FieldVisit::where('contact_number', '9861223344')->orderByDesc('id')->first();
        $this->assertNotNull($visit);
        $this->assertEquals('SSEPD Field Office', $visit->institution_type);
        $this->assertEquals('DSSO Office, Cuttack Collectorate', $visit->institution_name);
        $this->assertNull($visit->institution_id);
        $this->assertEquals(2, $visit->address_type);
        $this->assertEquals(15, $visit->municipality_id);
        $this->assertEquals(7, $visit->ward_id);
        $this->assertEquals('DSSO Office, Collectorate Compound', $visit->institution_address);

        // Edit page verification
        $editRes = $this->get(route('admin.field_visits.edit', $visit->id));
        $editRes->assertStatus(200);
        $editRes->assertSee('DSSO Office, Cuttack Collectorate');
        $editRes->assertSee('9861223344');

        $visit->forceDelete();
    }

    /**
     * Test 16: Case C - Other Category (Manual text input mode + Complete Rural Address).
     */
    public function test_store_and_edit_other_category_manual_mode_with_complete_address(): void
    {
        $this->actingAs($this->adminUser);

        $type = FieldVisitType::first();

        $postData = [
            'visit_date' => now()->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '12:30',
            'field_visit_type_id' => $type->id,
            'status' => 'submitted',
            'visit_purpose' => 'NGO Outreach and Screening Camp',
            'category_id' => 3, // Other
            'institution_name' => 'Rural Screening & Rehabilitation Camp',
            'contact_person' => 'Camp Coordinator',
            'contact_number' => '9438001122',
            'address_type' => 1, // Rural
            'state' => 228,
            'district' => 2421,
            'block' => 1,
            'grampanchayat' => 2,
            'village_id' => 379717,
            'institution_address' => 'Community Hall, Tentol Village, Pincode 759122',
            'is_follow_up_required' => '0',
            'overall_remarks' => 'Complete hierarchy verification for Other category',
        ];

        $response = $this->post(route('admin.field_visits.store'), $postData);
        $response->assertSessionHasNoErrors();

        $visit = FieldVisit::where('contact_number', '9438001122')->orderByDesc('id')->first();
        $this->assertNotNull($visit);
        $this->assertEquals('Other', $visit->institution_type);
        $this->assertEquals('Rural Screening & Rehabilitation Camp', $visit->institution_name);
        $this->assertNull($visit->institution_id);
        $this->assertEquals(1, $visit->address_type);
        $this->assertEquals(1, $visit->block_id);
        $this->assertEquals(2, $visit->gp_id);
        $this->assertEquals(379717, $visit->village_id);
        $this->assertEquals('Community Hall, Tentol Village, Pincode 759122', $visit->institution_address);

        // Edit page verification
        $editRes = $this->get(route('admin.field_visits.edit', $visit->id));
        $editRes->assertStatus(200);
        $editRes->assertSee('Rural Screening & Rehabilitation Camp');
        $editRes->assertSee('9438001122');
        $editRes->assertSee('Community Hall, Tentol Village, Pincode 759122');

        $visit->forceDelete();
    }

    /**
     * Test 17: Edit page for Visit 2 restores saved location hierarchy and preserves Select2.
     */
    public function test_visit_2_edit_page_restores_saved_location_hierarchy(): void
    {
        $this->actingAs($this->adminUser);

        $type = FieldVisitType::first();
        $visit = FieldVisit::firstOrNew(['id' => 2]);
        $visit->id = 2;
        $visit->visit_reference_no = $visit->visit_reference_no ?: 'FV-20261028-TEST-5741';
        $visit->field_visit_type_id = $visit->field_visit_type_id ?: ($type ? $type->id : 1);
        $visit->visit_date = $visit->visit_date ?: '2026-10-28';
        $visit->status = 'submitted';
        $visit->visit_purpose = $visit->visit_purpose ?: 'Automated test of field visit management';
        $visit->institution_type = 'SSEPD State Office';
        $visit->category_id = 1;
        $visit->address_type = 1;
        $visit->district_id = 2430;
        $visit->block_id = 2430009;
        $visit->visiting_officer_id = $this->adminUser->user_table_id;
        $visit->created_by = $this->adminUser->user_table_id;
        $visit->save();

        $response = $this->get(route('admin.field_visits.edit', 2));
        $response->assertStatus(200);

        // Verify view data
        $this->assertEquals(1, $response->viewData('savedAddressType'));
        $this->assertEquals(2430, $response->viewData('savedDistrictId'));
        $this->assertEquals(2430009, $response->viewData('savedBlockId'));

        // Verify that the view uses namespaced event handlers preventing Select2 destruction
        $content = $response->getContent();
        $this->assertStringContainsString('change.fvLocation', $content);
        $this->assertStringNotContainsString("$('#state-dropdown').off('change').on('change'", $content);
        $this->assertStringNotContainsString("$('#district-dropdown').off('change').on('change'", $content);
        $this->assertStringNotContainsString("$('#block-dropdown').off('change').on('change'", $content);
    }

    /**
     * Test 18: Location dropdown AJAX flow and options integrity.
     */
    public function test_location_dropdown_ajax_flow_and_options_integrity(): void
    {
        $this->actingAs($this->adminUser);

        // 1. Fetch districts for Odisha (state 228)
        $districtRes = $this->post(url('dashboard/locations/fetch-districts'), [
            'state_id' => 228,
        ]);
        $districtRes->assertStatus(200);
        $districts = $districtRes->json('districts');
        $this->assertNotEmpty($districts);
        $khordha = collect($districts)->firstWhere('district_id', 2430);
        $this->assertNotNull($khordha, 'District Khordha (2430) must exist in state 228');

        // 2. Fetch blocks for Khordha (2430)
        $blockRes = $this->post(url('dashboard/locations/fetch-block'), [
            'district_id' => 2430,
        ]);
        $blockRes->assertStatus(200);
        $blocks = $blockRes->json('blocks');
        $this->assertNotEmpty($blocks);
        $bbsr = collect($blocks)->firstWhere('block_id', 2430009);
        $this->assertNotNull($bbsr, 'Block 2430009 must exist in district 2430');

        // 3. Fetch municipalities for Khordha (2430)
        $muniRes = $this->post(url('dashboard/locations/fetch-municipality'), [
            'district_id' => 2430,
        ]);
        $muniRes->assertStatus(200);
        $municipalities = $muniRes->json('municipalities');
        $this->assertIsArray($municipalities);

        // 4. Create view contains change.fvLocation
        $createRes = $this->get(route('admin.field_visits.create'));
        $createRes->assertStatus(200);
        $createContent = $createRes->getContent();
        $this->assertStringContainsString('change.fvLocation', $createContent);
        $this->assertStringNotContainsString("$('#state-dropdown').off('change').on('change'", $createContent);
    }
}
