<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleEditManagementTest extends TestCase
{
    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::where('email', 'super_admin@gmail.com')->first();
        if (!$this->adminUser) {
            $this->adminUser = User::first();
        }
    }

    /**
     * Test 1: Guest is redirected to login for role routes
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.roles.create'))->assertRedirect('/login');
        $this->get(route('admin.roles.show', 9))->assertRedirect('/login');
        $this->get(route('admin.roles.edit', 9))->assertRedirect('/login');
    }

    /**
     * Test 2: Authorized user can access edit page for Role 9
     */
    public function test_authorized_user_can_access_role_9_edit_page(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.roles.edit', 9));
        $response->assertStatus(200);

        // Verify role details
        $response->assertSee('Edit Role: DSSO');
        $response->assertSee('value="DSSO"', false);
        $response->assertSee('Role ID: #9');

        // Verify key modules are present
        $response->assertSee('User Management');
        $response->assertSee('Role &amp; Permission Management', false);
        $response->assertSee('Location Management');
        $response->assertSee('Pension Management');
        $response->assertSee('Enhanced Pension');
        $response->assertSee('Special School');
        $response->assertSee('DDRC');
        $response->assertSee('ARC');
        $response->assertSee('PIA/NGO Institutes');
        $response->assertSee('NGO Management');
        $response->assertSee('Field Visit Management');
        $response->assertSee('My Profile');
        $response->assertSee('Product Management');

        // Verify search and toolbar elements
        $response->assertSee('id="permission_search"', false);
        $response->assertSee('id="total_available_count"', false);
        $response->assertSee('id="total_selected_count"', false);
        $response->assertSee('id="active_modules_count"', false);
        $response->assertSee('id="btn_select_all_global"', false);
        $response->assertSee('id="btn_clear_all_global"', false);

        // Verify form action and method
        $response->assertSee(route('admin.roles.update', 9));
    }

    /**
     * Test 3: Verify all database permissions exist in Edit HTML and assigned ones are checked
     */
    public function test_all_permissions_rendered_and_assigned_checked(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.roles.edit', 9));
        $content = $response->getContent();

        $allPermissions = Permission::all();
        $dbTotal = $allPermissions->count();

        // Verify each permission name is present in the rendered HTML
        foreach ($allPermissions as $perm) {
            $this->assertStringContainsString('name="permission[' . $perm->id . ']"', $content);
            $this->assertStringContainsString($perm->name, $content);
        }

        // Verify assigned permissions for Role 9
        $role9 = Role::find(9);
        $assignedPermIds = $role9->permissions->pluck('id')->toArray();
        $assignedCount = count($assignedPermIds);

        // Verify exactly the assigned number of checkboxes are checked
        preg_match_all('/<input[^>]+class="[^"]*permission-checkbox[^"]*"[^>]+checked[^>]*>/', $content, $checkedMatches);
        $this->assertCount($assignedCount, $checkedMatches[0]);

        // Verify an unassigned permission is NOT checked (e.g. user-access)
        $userAccess = Permission::where('name', 'user-access')->first();
        $this->assertFalse(in_array($userAccess->id, $assignedPermIds));
        $this->assertMatchesRegularExpression('/id="perm_' . $userAccess->id . '"(?![^>]*checked)/', $content);

        // Verify an assigned permission IS checked (e.g. pension-access)
        $pensionAccess = Permission::where('name', 'pension-access')->first();
        $this->assertTrue(in_array($pensionAccess->id, $assignedPermIds));
        $this->assertMatchesRegularExpression('/id="perm_' . $pensionAccess->id . '"[^>]*checked/', $content);
    }

    /**
     * Test 4: Validation requires name and at least one permission on update
     */
    public function test_validation_fails_on_empty_input(): void
    {
        $this->actingAs($this->adminUser);

        // Empty name
        $response = $this->post(route('admin.roles.update', 9), [
            'name' => '',
            'permission' => [1, 2],
        ]);
        $response->assertSessionHasErrors('name');

        // Missing permissions
        $response = $this->post(route('admin.roles.update', 9), [
            'name' => 'DSSO',
            'permission' => [],
        ]);
        $response->assertSessionHasErrors('permission');
    }

    /**
     * Test 5: Role update successfully synchronizes permissions and preserves data integrity
     */
    public function test_role_update_synchronizes_permissions_safely(): void
    {
        $this->actingAs($this->adminUser);

        $role9 = Role::find(9);
        $originalName = $role9->name;
        $originalPermIds = $role9->permissions->pluck('id')->toArray();

        try {
            // Test updating with a known subset of permissions (e.g. first 5 assigned)
            $newPermissions = array_slice($originalPermIds, 0, 5);

            $payload = [
                'name' => $originalName,
                'permission' => array_combine($newPermissions, $newPermissions),
            ];

            $response = $this->post(route('admin.roles.update', 9), $payload);
            $response->assertRedirect(route('admin.roles.index'));

            // Check permissions were synced
            $role9->refresh();
            $this->assertEquals(count($newPermissions), $role9->permissions->count());
            $this->assertEquals($newPermissions, $role9->permissions->pluck('id')->toArray());

        } finally {
            // Restore original permissions
            $role9->syncPermissions($originalPermIds);
            $role9->refresh();
            $this->assertEquals(count($originalPermIds), $role9->permissions->count());
        }
    }

    /**
     * Test 6: Role Create page renders with all database permissions and matching module layout
     */
    public function test_role_create_page_renders_with_all_permissions(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.roles.create'));
        $response->assertStatus(200);

        // Title and header
        $response->assertSee('Create New Role');
        $response->assertSee('id="role_create_form"', false);
        $response->assertSee(route('admin.roles.store'));

        // Verify key modules are present
        $response->assertSee('User Management');
        $response->assertSee('Role &amp; Permission Management', false);
        $response->assertSee('Location Management');
        $response->assertSee('Pension Management');
        $response->assertSee('Enhanced Pension');
        $response->assertSee('Special School');
        $response->assertSee('DDRC');
        $response->assertSee('ARC');
        $response->assertSee('PIA/NGO Institutes');
        $response->assertSee('NGO Management');
        $response->assertSee('Field Visit Management');
        $response->assertSee('My Profile');
        $response->assertSee('Product Management');

        // All database permission checkboxes rendered
        $dbTotal = Permission::count();
        $content = $response->getContent();
        preg_match_all('/<input[^>]+class="[^"]*permission-checkbox[^"]*"[^>]*>/', $content, $matches);
        $this->assertCount($dbTotal, $matches[0]);

        // In Create page initially 0 checkboxes are checked
        preg_match_all('/<input[^>]+class="[^"]*permission-checkbox[^"]*"[^>]+checked[^>]*>/', $content, $checkedMatches);
        $this->assertCount(0, $checkedMatches[0]);
    }

    /**
     * Test 7: Role Create stores new role and synchronizes permissions
     */
    public function test_role_create_stores_new_role_and_permissions(): void
    {
        $this->actingAs($this->adminUser);

        $testRoleName = 'Test Automated Quality Role';

        // Clean up any leftover from previous runs
        Role::where('name', $testRoleName)->delete();

        try {
            // Select 3 test permissions: user-access, role-access, location-access
            $selectedPerms = Permission::whereIn('name', ['user-access', 'role-access', 'location-access'])->get();
            $this->assertCount(3, $selectedPerms);

            $permIds = $selectedPerms->pluck('id')->toArray();
            $payload = [
                'name' => $testRoleName,
                'permission' => array_combine($permIds, $permIds),
            ];

            $response = $this->post(route('admin.roles.store'), $payload);
            $response->assertRedirect(route('admin.roles.index'));

            // Verify role created in database
            $createdRole = Role::where('name', $testRoleName)->first();
            $this->assertNotNull($createdRole);
            $this->assertEquals(3, $createdRole->permissions->count());

            // Test Show page for this newly created role
            $showResponse = $this->get(route('admin.roles.show', $createdRole->id));
            $showResponse->assertStatus(200);
            $showResponse->assertSee($testRoleName);
            $showResponse->assertSee('Role ID: #' . $createdRole->id);

        } finally {
            // Cleanup
            Role::where('name', $testRoleName)->delete();
        }
    }

    /**
     * Test 8: Role Show page is read-only with 0 checkboxes, correct metadata and permissions
     */
    public function test_role_show_page_is_readonly_with_correct_metadata(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.roles.show', 9));
        $response->assertStatus(200);

        // Verify metadata
        $role9 = Role::find(9);
        $assignedCount = $role9->permissions->count();
        $totalCount = Permission::count();
        $unassignedCount = $totalCount - $assignedCount;

        $response->assertSee('Role Details: DSSO');
        $response->assertSee('Role ID: #9');
        $response->assertSee('Guard: web');
        $response->assertSee($assignedCount . ' Assigned');

        // CRITICAL: Strictly read-only, NO checkboxes
        $content = $response->getContent();
        $this->assertStringNotContainsString('<input type="checkbox"', $content);
        $this->assertStringNotContainsString('permission-checkbox', $content);

        // Verify action button to Edit Role
        $response->assertSee(route('admin.roles.edit', 9));

        // Count assigned vs unassigned indicators
        preg_match_all('/class="[^"]*permission-box\s+is-assigned[^"]*"/', $content, $assignedBoxes);
        $this->assertCount($assignedCount, $assignedBoxes[0]);

        preg_match_all('/class="[^"]*permission-box\s+is-unassigned[^"]*"/', $content, $unassignedBoxes);
        $this->assertCount($unassignedCount, $unassignedBoxes[0]);
    }

    /**
     * Test 9: Newly added permissions in database automatically appear on Create, Edit, and Show pages
     */
    public function test_newly_added_permission_in_db_automatically_appears(): void
    {
        $this->actingAs($this->adminUser);

        $testPermName = 'grievance-redressal-create';

        // Ensure clean state
        Permission::where('name', $testPermName)->delete();

        try {
            // Add new permission to DB
            $newPerm = Permission::create([
               'name' => $testPermName,
               'guard_name' => 'web',
            ]);

            // 1. Verify it automatically appears on Create page
            $createRes = $this->get(route('admin.roles.create'));
            $createRes->assertStatus(200);
            $createRes->assertSee($testPermName);
            $createRes->assertSee('Grievance Redressal');

            // 2. Verify it automatically appears on Edit page
            $editRes = $this->get(route('admin.roles.edit', 9));
            $editRes->assertStatus(200);
            $editRes->assertSee($testPermName);
            $editRes->assertSee('Grievance Redressal');

            // 3. Verify it automatically appears on Show page as unassigned
            $showRes = $this->get(route('admin.roles.show', 9));
            $showRes->assertStatus(200);
            $showRes->assertSee($testPermName);
            $showRes->assertSee('Grievance Redressal');

        } finally {
            // Clean up test permission
            Permission::where('name', $testPermName)->delete();
        }
    }
}
