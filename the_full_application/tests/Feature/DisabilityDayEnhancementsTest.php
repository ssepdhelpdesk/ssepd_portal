<?php

namespace Tests\Feature;

use App\Mail\StallAllotmentConfirmationMail;
use App\Models\DisabilityDayRegistration;
use App\Models\DisabilityDayStallAllotment;
use App\Models\District;
use App\Models\State;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DisabilityDayEnhancementsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Clean any test records
        DisabilityDayRegistration::where('registration_no', 'like', '%DISABILITYDAY/PHPUNIT%')->forceDelete();
        DisabilityDayRegistration::where('email', 'like', 'phpunit.%')->forceDelete();
    }

    protected function tearDown(): void
    {
        DisabilityDayRegistration::where('registration_no', 'like', '%DISABILITYDAY/PHPUNIT%')->forceDelete();
        DisabilityDayRegistration::where('email', 'like', 'phpunit.%')->forceDelete();
        parent::tearDown();
    }

    public function test_financial_year_is_automatically_calculated_correctly()
    {
        $dateNov2026 = Carbon::create(2026, 11, 15, 12, 0, 0, 'Asia/Kolkata');
        $this->assertEquals('2026-27', DisabilityDayRegistration::calculateFinancialYear($dateNov2026));

        $dateJan2027 = Carbon::create(2027, 1, 10, 12, 0, 0, 'Asia/Kolkata');
        $this->assertEquals('2026-27', DisabilityDayRegistration::calculateFinancialYear($dateJan2027));
    }

    public function test_new_registration_with_unique_email_succeeds_and_assigns_defaults()
    {
        Mail::fake();
        $uniqueEmail = 'phpunit.unique.' . time() . '@example.com';
        $state = State::first();
        $district = District::first();

        $response = $this->post('/disability-day-2026/stall-registration', [
            'organization_name' => 'PHPUnit Test Org',
            'contact_person'    => 'Tester Person',
            'email'             => $uniqueEmail,
            'phone'             => '9437199999',
            'purpose'           => 'PHPUnit testing stall application flow.',
            'at'                => 'Unit Test Street',
            'po'                => 'Unit Test PO',
            'ps'                => 'Unit Test PS',
            'district_id'       => $district->id,
            'state_id'          => $state->id,
            'pin'               => '751001',
        ]);

        $response->assertRedirect('/disability-day-2026/stall-registration');
        $this->assertDatabaseHas('disability_day_registrations', [
            'email'          => $uniqueEmail,
            'status'         => 1,
            'financial_year' => '2026-27',
        ]);
    }

    public function test_duplicate_email_registration_is_rejected_case_insensitively()
    {
        Mail::fake();
        $email = 'phpunit.dup.' . time() . '@example.com';
        $state = State::first();
        $district = District::first();

        // First registration
        DisabilityDayRegistration::create([
            'registration_no'   => 'SSEPD/DISABILITYDAY/PHPUNIT/DUP1',
            'organization_name' => 'First Reg Org',
            'contact_person'    => 'First Person',
            'email'             => $email,
            'phone'             => '9437199991',
            'purpose'           => 'Testing duplicate validation',
            'at'                => 'Street',
            'po'                => 'PO',
            'ps'                => 'PS',
            'district_id'       => $district->id,
            'state_id'          => $state->id,
            'pin'               => '751001',
            'status'            => 1,
            'financial_year'    => '2026-27',
        ]);

        // Attempt second registration with uppercase email
        $response = $this->from('/disability-day-2026/stall-registration')
            ->post('/disability-day-2026/stall-registration', [
                'organization_name' => 'Second Reg Org',
                'contact_person'    => 'Second Person',
                'email'             => strtoupper($email),
                'phone'             => '9437199992',
                'purpose'           => 'Testing duplicate uppercase validation',
                'at'                => 'Street',
                'po'                => 'PO',
                'ps'                => 'PS',
                'district_id'       => $district->id,
                'state_id'          => $state->id,
                'pin'               => '751001',
            ]);

        $response->assertRedirect('/disability-day-2026/stall-registration');
        $response->assertSessionHasErrors(['email']);
        $this->assertEquals(
            'This email ID has already been registered. One organization can submit only one registration.',
            session('errors')->first('email')
        );
    }

    public function test_dashboard_filters_out_inactive_registrations()
    {
        $admin = User::first();
        $state = State::first();
        $district = District::first();

        $active = DisabilityDayRegistration::create([
            'registration_no'   => 'SSEPD/DISABILITYDAY/PHPUNIT/ACT1',
            'organization_name' => 'Active Org',
            'contact_person'    => 'Active Person',
            'email'             => 'phpunit.act.' . time() . '@example.com',
            'phone'             => '9437199993',
            'purpose'           => 'Active Org Purpose',
            'at'                => 'Street',
            'po'                => 'PO',
            'ps'                => 'PS',
            'district_id'       => $district->id,
            'state_id'          => $state->id,
            'pin'               => '751001',
            'status'            => 1,
            'financial_year'    => '2026-27',
        ]);

        $inactive = DisabilityDayRegistration::create([
            'registration_no'   => 'SSEPD/DISABILITYDAY/PHPUNIT/INACT1',
            'organization_name' => 'Inactive Org',
            'contact_person'    => 'Inactive Person',
            'email'             => 'phpunit.inact.' . time() . '@example.com',
            'phone'             => '9437199994',
            'purpose'           => 'Inactive Org Purpose',
            'at'                => 'Street',
            'po'                => 'PO',
            'ps'                => 'PS',
            'district_id'       => $district->id,
            'state_id'          => $state->id,
            'pin'               => '751001',
            'status'            => 0, // INACTIVE
            'financial_year'    => '2026-27',
        ]);

        $response = $this->actingAs($admin)
            ->getJson('/dashboard/disability-day');

        $response->assertStatus(200);
        $data = $response->json('data') ?? [];
        $regNos = array_map('strip_tags', array_column($data, 'registration_no'));

        $this->assertContains($active->registration_no, $regNos);
        $this->assertNotContains($inactive->registration_no, $regNos);
    }

    public function test_multiple_stall_allotment_and_conflict_prevention()
    {
        $admin = User::first();
        $state = State::first();
        $district = District::first();

        $reg1 = DisabilityDayRegistration::create([
            'registration_no'   => 'SSEPD/DISABILITYDAY/PHPUNIT/MULTI1',
            'organization_name' => 'Multi Stall Org 1',
            'contact_person'    => 'Multi Person 1',
            'email'             => 'phpunit.multi1.' . time() . '@example.com',
            'phone'             => '9437199995',
            'purpose'           => 'Testing multi stall allotment',
            'at'                => 'Street',
            'po'                => 'PO',
            'ps'                => 'PS',
            'district_id'       => $district->id,
            'state_id'          => $state->id,
            'pin'               => '751001',
            'status'            => 1,
            'financial_year'    => '2026-27',
        ]);

        $reg2 = DisabilityDayRegistration::create([
            'registration_no'   => 'SSEPD/DISABILITYDAY/PHPUNIT/MULTI2',
            'organization_name' => 'Multi Stall Org 2',
            'contact_person'    => 'Multi Person 2',
            'email'             => 'phpunit.multi2.' . time() . '@example.com',
            'phone'             => '9437199996',
            'purpose'           => 'Testing multi stall allotment 2',
            'at'                => 'Street',
            'po'                => 'PO',
            'ps'                => 'PS',
            'district_id'       => $district->id,
            'state_id'          => $state->id,
            'pin'               => '751001',
            'status'            => 1,
            'financial_year'    => '2026-27',
        ]);

        // Allot stalls 30 and 31 to Reg 1
        $resAllot = $this->actingAs($admin)
            ->postJson("/dashboard/disability-day/{$reg1->id}/allot-stall", [
                'stalls' => [30, 31],
            ]);

        $resAllot->assertStatus(200);
        $reg1->refresh();
        $this->assertEquals([30, 31], $reg1->getAllottedStallNumbers());
        $this->assertEquals('Stall 30, Stall 31', $reg1->allotted_stall);

        // Attempt to allot stall 30 to Reg 2 (conflict)
        $resConflict = $this->actingAs($admin)
            ->postJson("/dashboard/disability-day/{$reg2->id}/allot-stall", [
                'stalls' => [30, 40],
            ]);

        $resConflict->assertStatus(422);
        $this->assertStringContainsString('Stall 30 is already assigned', $resConflict->json('message'));

        // Allot additional stall 32 to Reg 1 using mode=add
        $resAdd = $this->actingAs($admin)
            ->postJson("/dashboard/disability-day/{$reg1->id}/allot-stall", [
                'mode'   => 'add',
                'stalls' => [32],
            ]);

        $resAdd->assertStatus(200);
        $reg1->refresh();
        $this->assertEquals([30, 31, 32], $reg1->getAllottedStallNumbers());
    }

    public function test_send_allotment_email_with_and_without_stalls()
    {
        Mail::fake();
        $admin = User::first();
        $state = State::first();
        $district = District::first();

        $reg = DisabilityDayRegistration::create([
            'registration_no'   => 'SSEPD/DISABILITYDAY/PHPUNIT/EMAIL1',
            'organization_name' => 'Email Test Org',
            'contact_person'    => 'Email Person',
            'email'             => 'phpunit.email.' . time() . '@example.com',
            'phone'             => '9437199997',
            'purpose'           => 'Testing email sending',
            'at'                => 'Street',
            'po'                => 'PO',
            'ps'                => 'PS',
            'district_id'       => $district->id,
            'state_id'          => $state->id,
            'pin'               => '751001',
            'status'            => 1,
            'financial_year'    => '2026-27',
        ]);

        // Attempt without stall -> rejected
        $resNoStall = $this->actingAs($admin)
            ->postJson("/dashboard/disability-day/{$reg->id}/send-allotment-email");

        $resNoStall->assertStatus(422);
        $this->assertStringContainsString('No stall has been allotted', $resNoStall->json('message'));
        Mail::assertNothingSent();

        // Assign stall 45
        $this->actingAs($admin)
            ->postJson("/dashboard/disability-day/{$reg->id}/allot-stall", [
                'stalls' => [45],
            ]);

        // Attempt with stall -> succeeds
        $resWithStall = $this->actingAs($admin)
            ->postJson("/dashboard/disability-day/{$reg->id}/send-allotment-email");

        $resWithStall->assertStatus(200);
        Mail::assertSent(StallAllotmentConfirmationMail::class, function ($mail) use ($reg) {
            return $mail->hasTo($reg->email) && $mail->allottedStalls === [45];
        });
    }
}
