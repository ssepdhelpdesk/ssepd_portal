@php
    $isEdit = isset($participant) && $participant->exists;
    $activeAddressType = (string) old('address_type', $isEdit ? ($participant->address_type ?? '1') : '1');
    $activeState = old('state', $isEdit ? $participant->state_id : '');
    $activeDistrict = old('district', $isEdit ? $participant->district_id : '');
    $activeBlock = old('block', $isEdit ? $participant->block_id : '');
    $activeGp = old('grampanchayat', $isEdit ? $participant->gp_id : '');
    $activeVillage = old('village', $isEdit ? $participant->village_id : '');
    $activeMuni = old('municipality', $isEdit ? $participant->municipality_id : '');
    $activeParticipation = old('participation_in', $isEdit ? $participant->participation_in : '');
    $activeAttendingDates = old('attending_dates');
    if (is_null($activeAttendingDates) && $isEdit) {
        $savedDates = is_array($participant->attending_dates) ? $participant->attending_dates : [];
        $canonicalKeys = array_keys(\App\Models\DisabilityDayParticipantRegistration::EVENT_DATES);
        $sortedSaved = array_values(array_intersect($canonicalKeys, $savedDates));
        sort($sortedSaved);
        $activeAttendingDates = ($sortedSaved === $canonicalKeys)
            ? [\App\Models\DisabilityDayParticipantRegistration::ALL_DAYS_VALUE]
            : $sortedSaved;
    }
    $activeAttendingDates = is_array($activeAttendingDates) ? $activeAttendingDates : [];
    $activeTransport = old('transport_mode', $isEdit ? $participant->transport_mode : '');
    $activeArrivalDate = old('expected_arrival_date', ($isEdit && $participant->expected_arrival_date) ? $participant->expected_arrival_date->format('Y-m-d') : '');
    $activeArrivalTime = old('expected_arrival_time', ($isEdit && $participant->expected_arrival_time) ? substr((string)$participant->expected_arrival_time, 0, 5) : '');
    $activeEscortsCount = (int) old('number_of_escorts', $isEdit ? $participant->number_of_escorts : 0);

    $existingEscorts = old('escorts');
    if (is_null($existingEscorts) && $isEdit) {
        $existingEscorts = $participant->escorts->map(function ($e) {
            return [
                'name' => $e->name,
                'mobile' => $e->mobile,
                'email' => $e->email,
                'relationship' => $e->relationship,
            ];
        })->toArray();
    }
    $existingEscorts = $existingEscorts ?: [];
@endphp

@if($isEdit)
<!-- Non-editable System Metadata -->
<div class="row g-3 p-3 bg-light rounded mb-4 border">
    <div class="col-md-4">
        <label class="form-label small text-muted mb-1">Registration Number (Immutable)</label>
        <input type="text" class="form-control font-monospace fw-bold bg-white" value="{{ $participant->registration_no }}" readonly disabled>
    </div>
    <div class="col-md-4">
        <label class="form-label small text-muted mb-1">Applied Date &amp; Time (Immutable)</label>
        <input type="text" class="form-control bg-white" value="{{ optional($participant->applied_at)->format('d-M-Y h:i:s A') ?? 'N/A' }}" readonly disabled>
    </div>
    <div class="col-md-4">
        <label class="form-label small text-muted mb-1">Financial Year</label>
        <input type="text" class="form-control font-monospace fw-semibold bg-white" value="{{ $participant->financial_year ?? '2026-27' }}" readonly disabled>
    </div>
</div>
@endif

<!-- Section A: Participant Details -->
<h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
    <i class="fas fa-user me-1"></i> Section A — Participant Details
</h6>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="participant_name" class="form-label fw-semibold">
            Participant Full Name (Single Participant Only) <span class="required-asterisk">*</span>
        </label>
        <input type="text"
               class="form-control @error('participant_name') is-invalid @enderror"
               id="participant_name"
               name="participant_name"
               value="{{ old('participant_name', $isEdit ? $participant->participant_name : '') }}"
               maxlength="150"
               required>
        <div class="form-text small text-muted">Enter one individual's full name only (do not enter comma-separated or pasted lists of multiple participants).</div>
        @error('participant_name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="institute_id" class="form-label fw-semibold">
            School / Institute <span class="required-asterisk">*</span>
        </label>
        <select class="form-control select2 @error('institute_id') is-invalid @enderror"
                id="institute_id"
                name="institute_id"
                style="width: 100%;"
                required>
            <option value="">Search and Select School Name</option>
            @foreach($institutes as $inst)
                <option value="{{ $inst->id }}" {{ (string) old('institute_id', $isEdit ? $participant->institute_id : '') === (string) $inst->id ? 'selected' : '' }}>
                    {{ $inst->institute_name }}
                </option>
            @endforeach
        </select>
        @error('institute_id')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="gender" class="form-label fw-semibold">
            Gender <span class="required-asterisk">*</span>
        </label>
        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
            <option value="">-- Select Gender --</option>
            @foreach(['Male', 'Female', 'Other'] as $g)
                <option value="{{ $g }}" {{ old('gender', $isEdit ? $participant->gender : '') === $g ? 'selected' : '' }}>{{ $g }}</option>
            @endforeach
        </select>
        @error('gender')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="tshirt_size" class="form-label fw-semibold">
            T-Shirt Size <span class="required-asterisk">*</span>
        </label>
        <select class="form-select @error('tshirt_size') is-invalid @enderror" id="tshirt_size" name="tshirt_size" required>
            <option value="">-- Select Size --</option>
            @foreach(['S', 'M', 'L', 'XL', 'XXL', 'XXXL'] as $sz)
                <option value="{{ $sz }}" {{ old('tshirt_size', $isEdit ? $participant->tshirt_size : '') === $sz ? 'selected' : '' }}>{{ $sz }}</option>
            @endforeach
        </select>
        @error('tshirt_size')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @if($isEdit)
    <div class="col-md-4">
        <label for="status" class="form-label fw-semibold">Registration Status</label>
        <select class="form-select" id="status" name="status">
            <option value="1" {{ (string) old('status', $participant->status) === '1' ? 'selected' : '' }}>Active / Registered</option>
            <option value="0" {{ (string) old('status', $participant->status) === '0' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    @endif
</div>

<!-- Section B: Complete Address of Participant -->
<h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
    <i class="fas fa-map-marked-alt me-1"></i> Section B — Complete Address of Participant
</h6>
<div class="mb-3">
    <label class="form-label fw-semibold d-block">Address Type <span class="required-asterisk">*</span></label>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="address_type" id="address_type_village" value="1" {{ $activeAddressType === '1' ? 'checked' : '' }}>
        <label class="form-check-label" for="address_type_village">Village (Block / Rural Area)</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="address_type" id="address_type_municipality" value="2" {{ $activeAddressType === '2' ? 'checked' : '' }}>
        <label class="form-check-label" for="address_type_municipality">Municipality (ULB / Urban Area)</label>
    </div>
    @error('address_type')
    <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label for="state-dropdown" class="form-label fw-semibold">State <span class="required-asterisk">*</span></label>
        <select class="form-select @error('state') is-invalid @enderror" id="state-dropdown" name="state" required>
            <option value="">-- Select State --</option>
            @foreach($states as $st)
                <option value="{{ $st->state_id }}" {{ (string) $activeState === (string) $st->state_id ? 'selected' : '' }}>{{ $st->state_name }}</option>
            @endforeach
        </select>
        @error('state')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="district-dropdown" class="form-label fw-semibold">District <span class="required-asterisk">*</span></label>
        <select class="form-select @error('district') is-invalid @enderror" id="district-dropdown" name="district" required>
            <option value="">-- Select District --</option>
            @if(isset($districts))
                @foreach($districts as $dt)
                    <option value="{{ $dt->district_id }}" {{ (string) $activeDistrict === (string) $dt->district_id ? 'selected' : '' }}>{{ $dt->district_name }}</option>
                @endforeach
            @endif
        </select>
        @error('district')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Village Fields -->
    <div class="col-md-4 village-field {{ $activeAddressType === '1' ? '' : 'd-none' }}">
        <label for="block-dropdown" class="form-label fw-semibold">Block <span class="required-asterisk">*</span></label>
        <select class="form-select @error('block') is-invalid @enderror" id="block-dropdown" name="block">
            <option value="">-- Select Block --</option>
            @if(isset($blocks))
                @foreach($blocks as $blk)
                    <option value="{{ $blk->block_id }}" {{ (string) $activeBlock === (string) $blk->block_id ? 'selected' : '' }}>{{ $blk->block_name }}</option>
                @endforeach
            @endif
        </select>
        @error('block')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 village-field {{ $activeAddressType === '1' ? '' : 'd-none' }}">
        <label for="grampanchayat-dropdown" class="form-label fw-semibold">Grampanchayat <span class="required-asterisk">*</span></label>
        <select class="form-select @error('grampanchayat') is-invalid @enderror" id="grampanchayat-dropdown" name="grampanchayat">
            <option value="">-- Select Grampanchayat --</option>
            @if(isset($grampanchayats))
                @foreach($grampanchayats as $gp)
                    <option value="{{ $gp->gp_id }}" {{ (string) $activeGp === (string) $gp->gp_id ? 'selected' : '' }}>{{ $gp->gp_name }}</option>
                @endforeach
            @endif
        </select>
        @error('grampanchayat')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 village-field {{ $activeAddressType === '1' ? '' : 'd-none' }}">
        <label for="village-dropdown" class="form-label fw-semibold">Village <span class="required-asterisk">*</span></label>
        <select class="form-select @error('village') is-invalid @enderror" id="village-dropdown" name="village">
            <option value="">-- Select Village --</option>
            @if(isset($villages))
                @foreach($villages as $vil)
                    <option value="{{ $vil->village_id }}" {{ (string) $activeVillage === (string) $vil->village_id ? 'selected' : '' }}>{{ $vil->village_name }}</option>
                @endforeach
            @endif
        </select>
        @error('village')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Municipality Field -->
    <div class="col-md-4 municipality-field {{ $activeAddressType === '2' ? '' : 'd-none' }}">
        <label for="municipality-dropdown" class="form-label fw-semibold">Municipality / ULB <span class="required-asterisk">*</span></label>
        <select class="form-select @error('municipality') is-invalid @enderror" id="municipality-dropdown" name="municipality">
            <option value="">-- Select Municipality --</option>
            @if(isset($municipalities))
                @foreach($municipalities as $muni)
                    <option value="{{ $muni->municipality_id }}" {{ (string) $activeMuni === (string) $muni->municipality_id ? 'selected' : '' }}>{{ $muni->municipality_name }}</option>
                @endforeach
            @endif
        </select>
        @error('municipality')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="pin" class="form-label fw-semibold">PIN Code <span class="required-asterisk">*</span></label>
        <input type="text" class="form-control @error('pin') is-invalid @enderror" id="pin" name="pin" maxlength="6" minlength="6" value="{{ old('pin', $isEdit ? $participant->pin : '') }}" required>
        @error('pin')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<!-- Section C: Escort Details -->
<h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
    <i class="fas fa-user-friends me-1"></i> Section C — Escort Details
</h6>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="number_of_escorts" class="form-label fw-semibold">Number of Escorts <span class="required-asterisk">*</span></label>
        <input type="number" class="form-control @error('number_of_escorts') is-invalid @enderror" id="number_of_escorts" name="number_of_escorts" min="0" max="5" value="{{ $activeEscortsCount }}" required>
        @error('number_of_escorts')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div id="escortsContainer" class="mb-4"></div>

<!-- Section D: Participation Details -->
<h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
    <i class="fas fa-trophy me-1"></i> Section D — Participation Details
</h6>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="participation_in" class="form-label fw-semibold">Participation Category <span class="required-asterisk">*</span></label>
        <select class="form-select @error('participation_in') is-invalid @enderror" id="participation_in" name="participation_in" required>
            <option value="">-- Select Category --</option>
            @foreach(['Cultural', 'Sports', 'Others'] as $cat)
                <option value="{{ $cat }}" {{ $activeParticipation === $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>
        @error('participation_in')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 {{ $activeParticipation === 'Others' ? '' : 'd-none' }}" id="otherParticipationWrapper">
        <label for="participation_other" class="form-label fw-semibold">Specify Other Participation <span class="required-asterisk">*</span></label>
        <input type="text" class="form-control @error('participation_other') is-invalid @enderror" id="participation_other" name="participation_other" value="{{ old('participation_other', $isEdit ? $participant->participation_other : '') }}" maxlength="255">
        @error('participation_other')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 mt-3">
        <label class="form-label fw-semibold d-block">Dates of Participation <span class="required-asterisk">*</span></label>
        <div class="form-text small text-muted mb-2">Please select the date(s) on which you will attend the event.</div>
        <div class="row g-2">
            @foreach(\App\Models\DisabilityDayParticipantRegistration::EVENT_DATES as $isoDate => $dateLabel)
            <div class="col-md-3 col-sm-6">
                <div class="form-check p-2 border rounded bg-light">
                    <input class="form-check-input ms-1 attending-date-checkbox attending-date-individual"
                           type="checkbox"
                           name="attending_dates[]"
                           id="admin_attending_date_{{ $isoDate }}"
                           value="{{ $isoDate }}"
                           {{ in_array($isoDate, $activeAttendingDates, true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-medium ms-1" for="admin_attending_date_{{ $isoDate }}">
                        {{ $dateLabel }}
                    </label>
                </div>
            </div>
            @endforeach
            <div class="col-md-3 col-sm-6">
                <div class="form-check p-2 border rounded bg-light border-primary">
                    <input class="form-check-input ms-1 attending-date-checkbox"
                           type="checkbox"
                           name="attending_dates[]"
                           id="admin_attending_date_all"
                           value="{{ \App\Models\DisabilityDayParticipantRegistration::ALL_DAYS_VALUE }}"
                           {{ in_array(\App\Models\DisabilityDayParticipantRegistration::ALL_DAYS_VALUE, $activeAttendingDates, true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold text-primary ms-1" for="admin_attending_date_all">
                        All Days (December 1–3, 2026)
                    </label>
                </div>
            </div>
        </div>
        @error('attending_dates')
        <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<!-- Section E: Transportation & Venue Arrival Details -->
<h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
    <i class="fas fa-bus me-1"></i> Section E — Transportation &amp; Venue Arrival Details
</h6>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="transport_mode" class="form-label fw-semibold">Mode of Transportation <span class="required-asterisk">*</span></label>
        <select class="form-select @error('transport_mode') is-invalid @enderror" id="transport_mode" name="transport_mode" required>
            <option value="">-- Select Mode of Transportation --</option>
            @foreach(\App\Models\DisabilityDayParticipantRegistration::TRANSPORT_MODES as $mode)
                <option value="{{ $mode }}" {{ $activeTransport === $mode ? 'selected' : '' }}>{{ $mode }}</option>
            @endforeach
        </select>
        @error('transport_mode')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 {{ $activeTransport === 'Other' ? '' : 'd-none' }}" id="otherTransportWrapper">
        <label for="transport_mode_other" class="form-label fw-semibold">Specify Other Mode of Transportation <span class="required-asterisk">*</span></label>
        <input type="text" class="form-control @error('transport_mode_other') is-invalid @enderror" id="transport_mode_other" name="transport_mode_other" value="{{ old('transport_mode_other', $isEdit ? $participant->transport_mode_other : '') }}" maxlength="255">
        @error('transport_mode_other')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="expected_arrival_date" class="form-label fw-semibold">Expected Date of Arrival at Venue <span class="required-asterisk">*</span></label>
        <input type="date" class="form-control @error('expected_arrival_date') is-invalid @enderror" id="expected_arrival_date" name="expected_arrival_date" value="{{ $activeArrivalDate }}" required>
        @error('expected_arrival_date')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="expected_arrival_time" class="form-label fw-semibold">Expected Time of Arrival at Venue <span class="required-asterisk">*</span></label>
        <input type="time" class="form-control @error('expected_arrival_time') is-invalid @enderror" id="expected_arrival_time" name="expected_arrival_time" value="{{ $activeArrivalTime }}" required>
        @error('expected_arrival_time')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });

    if ($.fn.select2) {
        $('#institute_id').select2({
            placeholder: "Search and Select School Name",
            allowClear: true,
            width: '100%'
        });
    }

    // Address Type toggle
    $('input[name="address_type"]').on('change', function () {
        if (this.value === '1') {
            $('.village-field').removeClass('d-none');
            $('.municipality-field').addClass('d-none');
        } else {
            $('.village-field').addClass('d-none');
            $('.municipality-field').removeClass('d-none');
        }
    });

    // Location cascades
    $('#state-dropdown').on('change', function () {
        const idState = $(this).val();
        $('#district-dropdown').html('<option value="">-- Select District --</option>');
        $('#block-dropdown').html('<option value="">-- Select Block --</option>');
        $('#grampanchayat-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
        $('#village-dropdown').html('<option value="">-- Select Village --</option>');
        $('#municipality-dropdown').html('<option value="">-- Select Municipality --</option>');
        if (!idState) return;
        $.post("{{ url('frontend/locations/fetch-districts') }}", { state_id: idState, all_districts: 1 }, function (res) {
            if (res && res.districts) {
                $.each(res.districts, function (k, v) {
                    $('#district-dropdown').append('<option value="' + v.district_id + '">' + v.district_name + '</option>');
                });
            }
        }, 'json');
    });

    $('#district-dropdown').on('change', function () {
        const idDistrict = $(this).val();
        const type = $('input[name="address_type"]:checked').val();
        $('#block-dropdown').html('<option value="">-- Select Block --</option>');
        $('#grampanchayat-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
        $('#village-dropdown').html('<option value="">-- Select Village --</option>');
        $('#municipality-dropdown').html('<option value="">-- Select Municipality --</option>');
        if (!idDistrict) return;
        if (type === '1') {
            $.post("{{ url('frontend/locations/fetch-block') }}", { district_id: idDistrict, all_blocks: 1 }, function (res) {
                if (res && res.blocks) {
                    $.each(res.blocks, function (k, v) {
                        $('#block-dropdown').append('<option value="' + v.block_id + '">' + v.block_name + '</option>');
                    });
                }
            }, 'json');
        } else {
            $.post("{{ url('frontend/locations/fetch-municipality') }}", { district_id: idDistrict, all_municipalities: 1 }, function (res) {
                if (res && res.municipalities) {
                    $.each(res.municipalities, function (k, v) {
                        $('#municipality-dropdown').append('<option value="' + v.municipality_id + '">' + v.municipality_name + '</option>');
                    });
                }
            }, 'json');
        }
    });

    $('#block-dropdown').on('change', function () {
        const idBlock = $(this).val();
        $('#grampanchayat-dropdown').html('<option value="">-- Select Grampanchayat --</option>');
        $('#village-dropdown').html('<option value="">-- Select Village --</option>');
        if (!idBlock) return;
        $.post("{{ url('frontend/locations/fetch-grampanchayat') }}", { block_id: idBlock }, function (res) {
            if (res && res.grampanchayats) {
                $.each(res.grampanchayats, function (k, v) {
                    $('#grampanchayat-dropdown').append('<option value="' + v.gp_id + '">' + v.gp_name + '</option>');
                });
            }
        }, 'json');
    });

    $('#grampanchayat-dropdown').on('change', function () {
        const idGp = $(this).val();
        $('#village-dropdown').html('<option value="">-- Select Village --</option>');
        if (!idGp) return;
        $.post("{{ url('frontend/locations/fetch-village') }}", { gp_id: idGp }, function (res) {
            if (res && res.villages) {
                $.each(res.villages, function (k, v) {
                    $('#village-dropdown').append('<option value="' + v.village_id + '">' + v.village_name + '</option>');
                });
            }
        }, 'json');
    });

    // Conditional Participation, Attendance Dates & Transport
    $('#admin_attending_date_all').on('change', function () {
        if (this.checked) {
            $('.attending-date-individual').prop('checked', false);
        }
    });
    $('.attending-date-individual').on('change', function () {
        if (this.checked) {
            $('#admin_attending_date_all').prop('checked', false);
        }
    });

    $('#participation_in').on('change', function () {
        if (this.value === 'Others') {
            $('#otherParticipationWrapper').removeClass('d-none');
        } else {
            $('#otherParticipationWrapper').addClass('d-none');
            $('#participation_other').val('');
        }
    });

    $('#transport_mode').on('change', function () {
        if (this.value === 'Other') {
            $('#otherTransportWrapper').removeClass('d-none');
        } else {
            $('#otherTransportWrapper').addClass('d-none');
            $('#transport_mode_other').val('');
        }
    });

    // Dynamic Escorts
    const existingEscorts = @json($existingEscorts);
    function renderAdminEscorts(count) {
        const container = document.getElementById('escortsContainer');
        container.innerHTML = '';
        count = parseInt(count, 10);
        if (isNaN(count) || count <= 0) {
            container.innerHTML = '<div class="alert alert-light border text-muted small">No escorts declared.</div>';
            return;
        }
        if (count > 5) count = 5;
        for (let i = 1; i <= count; i++) {
            const idx = i - 1;
            const esc = existingEscorts[idx] || {};
            const card = document.createElement('div');
            card.className = 'card border mb-2 p-3 bg-light';
            card.innerHTML = `
                <h6 class="fw-bold mb-2">Escort #${i}</h6>
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Name *</label>
                        <input type="text" name="escorts[${idx}][name]" class="form-control form-control-sm" value="${(esc.name || '').replace(/"/g, '&quot;')}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Mobile (10 digits) *</label>
                        <input type="tel" name="escorts[${idx}][mobile]" class="form-control form-control-sm" maxlength="10" value="${(esc.mobile || '').replace(/"/g, '&quot;')}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Email (Optional)</label>
                        <input type="email" name="escorts[${idx}][email]" class="form-control form-control-sm" value="${(esc.email || '').replace(/"/g, '&quot;')}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Relationship *</label>
                        <input type="text" name="escorts[${idx}][relationship]" class="form-control form-control-sm" value="${(esc.relationship || '').replace(/"/g, '&quot;')}" required>
                    </div>
                </div>
            `;
            container.appendChild(card);
        }
    }

    $('#number_of_escorts').on('input', function () {
        renderAdminEscorts(this.value);
    });
    renderAdminEscorts($('#number_of_escorts').val());

    // Normalize participant name on blur
    $('#participant_name').on('blur', function () {
        this.value = this.value.trim().replace(/\s+/g, ' ');
    });
});
</script>
@endpush
