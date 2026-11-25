<?php

namespace App\Http\Controllers\Frontend_Controller;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\DisabilityDayEscort;
use App\Models\DisabilityDayParticipantRegistration;
use App\Models\District;
use App\Models\Grampanchayat;
use App\Models\Municipality;
use App\Models\State;
use App\Models\Village;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DisabilityDayParticipantController extends Controller
{
    /**
     * Show the public participant registration form.
     */
    public function create()
    {
        // 1. Fetch school/institute list from field_visit_institutes
        $institutes = DB::table('field_visit_institutes')
            ->select('id', 'institute_name')
            ->whereNotNull('institute_name')
            ->where('institute_name', '!=', '')
            ->orderBy('institute_name', 'asc')
            ->get();

        return view('main_website.disability_day_participant_registration', compact('institutes'));
    }

    /**
     * Handle participant registration form submission.
     */
    public function store(Request $request)
    {
        $appliedAt = Carbon::now('Asia/Kolkata');
        $financialYear = DisabilityDayParticipantRegistration::calculateFinancialYear($appliedAt);

        // Sanitize string inputs
        $participantName = DisabilityDayParticipantRegistration::normalizeParticipantName($request->input('participant_name'));
        $instituteId = $request->input('institute_id');
        $gender = trim((string) $request->input('gender'));
        $tshirtSize = strtoupper(trim((string) $request->input('tshirt_size')));
        $addressType = (int) $request->input('address_type');
        $numEscorts = (int) $request->input('number_of_escorts', 0);
        $participationIn = trim((string) $request->input('participation_in'));
        $participationOther = trim((string) $request->input('participation_other'));
        $attendingDates = DisabilityDayParticipantRegistration::normalizeAttendingDates($request->input('attending_dates', []));
        $transportMode = trim((string) $request->input('transport_mode'));
        $transportModeOther = trim((string) $request->input('transport_mode_other'));
        $expectedArrivalDate = trim((string) $request->input('expected_arrival_date'));
        $expectedArrivalTime = trim((string) $request->input('expected_arrival_time'));

        // Base Validation Rules
        $rules = [
            'participant_name'    => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    DisabilityDayParticipantRegistration::validateSingleParticipantName($value, $fail);
                },
            ],
            'institute_id'        => ['required', 'integer', 'exists:field_visit_institutes,id'],
            'gender'              => ['required', 'string', 'in:Male,Female,Other'],
            'tshirt_size'         => ['required', 'string', 'in:S,M,L,XL,XXL,XXXL'],
            'address_type'        => ['required', 'in:1,2'],
            'state'               => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = DB::table('states')->where('state_id', $value)->orWhere('id', $value)->exists();
                    if (!$exists) {
                        $fail('The selected State is invalid.');
                    }
                }
            ],
            'district'            => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $stateVal = $request->input('state');
                    $stateRow = DB::table('states')->where('state_id', $stateVal)->orWhere('id', $stateVal)->first();
                    $districtRow = DB::table('districts')->where('district_id', $value)->orWhere('id', $value)->first();
                    if (!$districtRow) {
                        $fail('The selected District is invalid.');
                        return;
                    }
                    if ($stateRow && $districtRow->state_id != $stateRow->state_id && $districtRow->state_id != $stateRow->id) {
                        $fail('The selected District does not belong to the chosen State.');
                    }
                }
            ],
            'pin'                 => ['required', 'regex:/^[1-9][0-9]{5}$/'],
            'number_of_escorts'   => ['required', 'integer', 'min:0', 'max:5'],
            'participation_in'    => ['required', 'string', 'in:Cultural,Sports,Others'],
            'participation_other' => ['required_if:participation_in,Others', 'nullable', 'string', 'max:255'],
            'attending_dates'     => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    DisabilityDayParticipantRegistration::validateAttendingDates($value, $fail);
                },
            ],
            'transport_mode'        => ['required', 'string', 'in:' . implode(',', DisabilityDayParticipantRegistration::TRANSPORT_MODES)],
            'transport_mode_other'  => ['required_if:transport_mode,Other', 'nullable', 'string', 'max:255'],
            'expected_arrival_date' => ['required', 'date'],
            'expected_arrival_time' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
        ];

        // Conditional Address Validation
        if ($addressType === 1) {
            // Village (Block)
            $rules['block'] = [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $distVal = $request->input('district');
                    $distRow = DB::table('districts')->where('district_id', $distVal)->orWhere('id', $distVal)->first();
                    $blockRow = DB::table('blocks')->where('block_id', $value)->orWhere('id', $value)->first();
                    if (!$blockRow) {
                        $fail('The selected Block is invalid.');
                        return;
                    }
                    if ($distRow && $blockRow->district_id != $distRow->district_id && $blockRow->district_id != $distRow->id) {
                        $fail('The selected Block does not belong to the chosen District.');
                    }
                }
            ];
            $rules['grampanchayat'] = [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $blockVal = $request->input('block');
                    $blockRow = DB::table('blocks')->where('block_id', $blockVal)->orWhere('id', $blockVal)->first();
                    $gpRow = DB::table('grampanchayats')->where('gp_id', $value)->orWhere('id', $value)->first();
                    if (!$gpRow) {
                        $fail('The selected Grampanchayat is invalid.');
                        return;
                    }
                    if ($blockRow && $gpRow->block_id != $blockRow->block_id && $gpRow->block_id != $blockRow->id) {
                        $fail('The selected Grampanchayat does not belong to the chosen Block.');
                    }
                }
            ];
            $rules['village'] = [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $gpVal = $request->input('grampanchayat');
                    $gpRow = DB::table('grampanchayats')->where('gp_id', $gpVal)->orWhere('id', $gpVal)->first();
                    $villageRow = DB::table('villages')->where('village_id', $value)->orWhere('id', $value)->first();
                    if (!$villageRow) {
                        $fail('The selected Village is invalid.');
                        return;
                    }
                    if ($gpRow && $villageRow->gp_id != $gpRow->gp_id && $villageRow->gp_id != $gpRow->id) {
                        $fail('The selected Village does not belong to the chosen Grampanchayat.');
                    }
                }
            ];
        } elseif ($addressType === 2) {
            // Municipality (ULB)
            $rules['municipality'] = [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $distVal = $request->input('district');
                    $distRow = DB::table('districts')->where('district_id', $distVal)->orWhere('id', $distVal)->first();
                    $muniRow = DB::table('municipalities')->where('municipality_id', $value)->orWhere('id', $value)->first();
                    if (!$muniRow) {
                        $fail('The selected Municipality is invalid.');
                        return;
                    }
                    if ($distRow && $muniRow->district_id != $distRow->district_id && $muniRow->district_id != $distRow->id) {
                        $fail('The selected Municipality does not belong to the chosen District.');
                    }
                }
            ];
        }

        // Escort Validation (Exact Match)
        if ($numEscorts === 0) {
            // Must not contain extra escort records when 0 escorts declared
            $rules['escorts'] = ['nullable', 'array', 'max:0'];
        } else {
            $rules['escorts'] = ['required', 'array', 'size:' . $numEscorts];
            $rules['escorts.*.name'] = ['required', 'string', 'min:2', 'max:150', 'regex:/^[\p{L}\p{M}\s\.\'-]+$/u'];
            $rules['escorts.*.mobile'] = ['required', 'regex:/^[6-9][0-9]{9}$/'];
            $rules['escorts.*.email'] = ['nullable', 'email', 'max:150'];
            $rules['escorts.*.relationship'] = ['required', 'string', 'min:2', 'max:100'];
        }

        // Custom Validation Messages
        $messages = [
            'participant_name.required' => 'Please enter the participant full name.',
            'participant_name.min'      => 'Participant name must be at least 2 characters.',
            'participant_name.max'      => 'Participant name may not exceed 150 characters.',
            'participant_name.regex'    => 'Participant name may only contain letters, spaces, dots, hyphens, and apostrophes.',
            
            'institute_id.required'     => 'Please select a school / institute from the list.',
            'institute_id.exists'       => 'The selected school / institute is invalid or does not exist.',
            
            'gender.required'           => 'Please select a gender.',
            'gender.in'                 => 'Please select a valid gender option (Male, Female, or Other).',
            
            'tshirt_size.required'      => 'Please select a T-Shirt size.',
            'tshirt_size.in'            => 'Please select a valid T-Shirt size (S, M, L, XL, XXL, XXXL).',
            
            'address_type.required'     => 'Please choose an Address Type (Village or Municipality).',
            'address_type.in'           => 'Invalid address type selected.',
            
            'state.required'            => 'Please select a State.',
            'district.required'         => 'Please select a District.',
            'block.required'            => 'Please select a Block.',
            'grampanchayat.required'    => 'Please select a Grampanchayat.',
            'village.required'          => 'Please select a Village.',
            'municipality.required'     => 'Please select a Municipality / ULB.',
            
            'pin.required'              => 'Please enter the 6-digit PIN code.',
            'pin.regex'                 => 'Please enter a valid 6-digit PIN code (e.g. 751001).',
            
            'number_of_escorts.required' => 'Please specify the number of escorts (0 or more).',
            'number_of_escorts.integer'  => 'The number of escorts must be an integer.',
            'number_of_escorts.min'      => 'Number of escorts cannot be negative.',
            'number_of_escorts.max'      => 'Maximum of 5 escorts can accompany a participant.',
            
            'escorts.max'                => 'No escort details should be provided when the number of escorts is 0.',
            'escorts.required'           => 'Please provide details for the specified number of escorts.',
            'escorts.size'               => 'The number of submitted escort details must match the declared count of :size.',
            'escorts.*.name.required'    => 'Escort name is required for all escorts.',
            'escorts.*.name.regex'       => 'Escort name may only contain letters, spaces, dots, hyphens, and apostrophes.',
            'escorts.*.mobile.required'  => 'A valid 10-digit mobile number is required for every escort.',
            'escorts.*.mobile.regex'     => 'Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9 for each escort.',
            'escorts.*.email.email'      => 'Please enter a valid email address for the escort, or leave it blank.',
            'escorts.*.relationship.required' => 'Please specify the relationship of the escort with the participant.',
            
            'participation_in.required'  => 'Please select a participation category.',
            'participation_in.in'        => 'Please select a valid participation category (Cultural, Sports, or Others).',
            'participation_other.required_if' => 'Please specify your other participation activity.',
            'attending_dates.required'   => 'Please select at least one date of participation.',
            'attending_dates.min'        => 'Please select at least one date of participation.',

            'transport_mode.required'        => 'Please select a mode of transportation.',
            'transport_mode.in'              => 'Please select a valid mode of transportation.',
            'transport_mode_other.required_if' => 'Please specify the other mode of transportation.',
            'expected_arrival_date.required' => 'Please select the expected date of arrival at the venue.',
            'expected_arrival_date.date'     => 'Please enter a valid expected date of arrival.',
            'expected_arrival_time.required' => 'Please enter the expected time of arrival at the venue.',
            'expected_arrival_time.regex'    => 'Please enter a valid time of arrival (HH:MM).',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The given data was invalid.',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            return redirect()
                ->route('disability_day.participant_registration')
                ->withErrors($validator)
                ->withInput();
        }

        // 2. Resolve School Name
        $institute = DB::table('field_visit_institutes')->where('id', $instituteId)->first();
        $schoolName = $institute ? $institute->institute_name : 'Unknown Institute';

        // 3. Resolve Location Names for Auditing and Display
        $stateId = $request->input('state');
        $districtId = $request->input('district');
        $blockId = $request->input('block');
        $gpId = $request->input('grampanchayat');
        $villageId = $request->input('village');
        $municipalityId = $request->input('municipality');

        $stateName = State::where('state_id', $stateId)->orWhere('id', $stateId)->value('state_name');
        $districtName = District::where('district_id', $districtId)->orWhere('id', $districtId)->value('district_name');
        $blockName = $blockId ? Block::where('block_id', $blockId)->orWhere('id', $blockId)->value('block_name') : null;
        $gpName = $gpId ? Grampanchayat::where('gp_id', $gpId)->orWhere('id', $gpId)->value('gp_name') : null;
        $villageName = $villageId ? Village::where('village_id', $villageId)->orWhere('id', $villageId)->value('village_name') : null;
        $municipalityName = $municipalityId ? Municipality::where('municipality_id', $municipalityId)->orWhere('id', $municipalityId)->value('municipality_name') : null;

        // 4. Save Participant & Escort Details within Database Transaction (with Concurrency Collision Retry)
        try {
            $registration = null;
            $maxRetries = 3;

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    $registration = DB::transaction(function () use (
                        $participantName,
                        $instituteId,
                        $schoolName,
                        $gender,
                        $tshirtSize,
                        $addressType,
                        $stateId,
                        $stateName,
                        $districtId,
                        $districtName,
                        $blockId,
                        $blockName,
                        $gpId,
                        $gpName,
                        $villageId,
                        $villageName,
                        $municipalityId,
                        $municipalityName,
                        $request,
                        $numEscorts,
                        $participationIn,
                        $participationOther,
                        $attendingDates,
                        $transportMode,
                        $transportModeOther,
                        $expectedArrivalDate,
                        $expectedArrivalTime,
                        $appliedAt,
                        $financialYear
                    ) {
                        // Generate Registration Number
                        $regNo = DisabilityDayParticipantRegistration::generateUniqueRegistrationNo($appliedAt);

                        $participant = DisabilityDayParticipantRegistration::create([
                            'registration_no'       => $regNo,
                            'participant_name'      => $participantName,
                            'institute_id'          => $instituteId,
                            'school_name'           => $schoolName,
                            'gender'                => $gender,
                            'tshirt_size'           => $tshirtSize,
                            'address_type'          => $addressType,
                            'state_id'              => $stateId ? (int)$stateId : null,
                            'state_name'            => $stateName,
                            'district_id'           => $districtId ? (int)$districtId : null,
                            'district_name'         => $districtName,
                            'block_id'              => $blockId ? (int)$blockId : null,
                            'block_name'            => $blockName,
                            'gp_id'                 => $gpId ? (int)$gpId : null,
                            'gp_name'               => $gpName,
                            'village_id'            => $villageId ? (int)$villageId : null,
                            'village_name'          => $villageName,
                            'municipality_id'       => $municipalityId ? (int)$municipalityId : null,
                            'municipality_name'     => $municipalityName,
                            'pin'                   => trim((string)$request->input('pin')),
                            'number_of_escorts'     => $numEscorts,
                            'participation_in'      => $participationIn,
                            'participation_other'   => $participationIn === 'Others' ? $participationOther : null,
                            'attending_dates'       => $attendingDates,
                            'transport_mode'        => $transportMode,
                            'transport_mode_other'  => $transportMode === 'Other' ? $transportModeOther : null,
                            'expected_arrival_date' => $expectedArrivalDate,
                            'expected_arrival_time' => $expectedArrivalTime,
                            'applied_at'            => $appliedAt,
                            'status'                => 1,
                            'financial_year'        => $financialYear,
                        ]);

                        // Insert Escort Records
                        if ($numEscorts > 0 && $request->has('escorts') && is_array($request->input('escorts'))) {
                            $escortList = $request->input('escorts');
                            $index = 1;
                            foreach ($escortList as $escortData) {
                                if ($index > $numEscorts) {
                                    break;
                                }

                                $escortEmail = isset($escortData['email']) && trim((string)$escortData['email']) !== '' 
                                    ? strtolower(trim((string)$escortData['email'])) 
                                    : null;

                                DisabilityDayEscort::create([
                                    'participant_registration_id' => $participant->id,
                                    'escort_index'                => $index,
                                    'name'                        => trim((string)($escortData['name'] ?? '')),
                                    'mobile'                      => trim((string)($escortData['mobile'] ?? '')),
                                    'email'                       => $escortEmail,
                                    'relationship'                => trim((string)($escortData['relationship'] ?? '')),
                                ]);

                                $index++;
                            }
                        }

                        return $participant;
                    });

                    // Transaction committed successfully
                    break;

                } catch (\Illuminate\Database\QueryException $qe) {
                    if ($qe->getCode() == 23000 && str_contains($qe->getMessage(), 'registration_no') && $attempt < $maxRetries) {
                        Log::warning("Participant registration number collision on attempt {$attempt}, retrying...");
                        continue;
                    }
                    throw $qe;
                }
            }

            Log::info("Disability Day 2026: Participant registered successfully", [
                'registration_no'  => $registration->registration_no,
                'participant_name' => $registration->participant_name,
                'school_name'      => $registration->school_name,
                'escort_count'     => $registration->number_of_escorts,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success'         => true,
                    'message'         => 'Participant registration submitted successfully.',
                    'registration_no' => $registration->registration_no,
                    'data'            => $registration->load('escorts'),
                ], 201);
            }

            return redirect()
                ->route('disability_day.participant_registration')
                ->with([
                    'registration_success'  => true,
                    'registration_no'       => $registration->registration_no,
                    'participant_name'      => $registration->participant_name,
                    'school_name'           => $registration->school_name,
                    'gender'                => $registration->gender,
                    'tshirt_size'           => $registration->tshirt_size,
                    'number_of_escorts'     => $registration->number_of_escorts,
                    'participation_in'      => $registration->participation_in,
                    'attending_dates'       => DisabilityDayParticipantRegistration::formatAttendingDatesForDisplay($registration->attending_dates),
                    'transport_mode'        => $registration->transport_mode === 'Other' && $registration->transport_mode_other
                        ? 'Other (' . $registration->transport_mode_other . ')'
                        : $registration->transport_mode,
                    'expected_arrival_date' => $registration->expected_arrival_date ? $registration->expected_arrival_date->format('d M Y') : '',
                    'expected_arrival_time' => $registration->expected_arrival_time,
                    'applied_at'            => $appliedAt->format('d M Y, h:i A'),
                ]);

        } catch (\Throwable $e) {
            Log::error("Disability Day 2026: Failed to save participant registration", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to complete your registration due to a server error. Please try again or contact IT support.',
                ], 500);
            }

            return redirect()
                ->route('disability_day.participant_registration')
                ->with('error', 'Unable to complete your registration due to a server error. Please try again or contact IT support.')
                ->withInput();
        }
    }
}
