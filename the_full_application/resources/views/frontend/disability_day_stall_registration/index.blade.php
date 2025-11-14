@section('title') 
SSEPD || Stall Registration
@endsection 
@extends('frontend.layouts.main')
@section('style')
@endsection 
@section('content')
<!-- Inner Banner -->
<div class="inner-banner inner-banner-bg13">
	<div class="container">
		<div class="inner-title text-center">
			<h3>Apply for Stall – International Day of Persons with Disabilities 2025</h3>
			<ul>
				<li>
					<a href="{{route('frontend.disabilitydaystallregistration.index')}}">Home</a>
				</li>
				<li>Apply for Stall</li>
			</ul>
		</div>
	</div>
</div>
<!-- Inner Banner End -->
<!-- Contact Widget Area -->
<div class="contact-widget-area pb-70">
	<div class="container">
		<div class="section-title text-center mb-45 pt-30">
			<!-- <span>SEND MESSAGE</span>
			<h2>Please fillout this registration from.</h2> -->
			@if(session('success'))
			<div class="alert alert-success">{!! session('success') !!}</div>
			@endif
			@if(session('error'))
			<div class="alert alert-danger">{{ session('error') }}</div>
			@endif
		</div>
		
		<div class="contact-form">
			<form class="from-prevent-multiple-submits" method="POST" 
			action="{{ route('frontend.disabilitydaystallregistration.store') }}" 
			onsubmit="return Validate()" name="vform" enctype="multipart/form-data">
			@csrf
			@method('post')
			<div class="row">
				{{-- Organization Name --}}
				<div class="col-lg-12">
					<div class="form-group">
						<input type="text" name="name_of_the_organization" id="name_of_the_organization"
						value="{{ old('name_of_the_organization') }}"
						class="form-control @error('name_of_the_organization') is-invalid @enderror"
						required placeholder="Organization Name">
						@error('name_of_the_organization')
						<small class="text-danger">{{ $message }}</small>
						@enderror
					</div>
				</div>

				{{-- Contact Person Name --}}
				<div class="col-lg-6">
					<div class="form-group">
						<input type="text" name="contact_person_name" id="contact_person_name"
						value="{{ old('contact_person_name') }}"
						class="form-control @error('contact_person_name') is-invalid @enderror"
						required placeholder="Contact Person Name">
						@error('contact_person_name')
						<small class="text-danger">{{ $message }}</small>
						@enderror
					</div>
				</div>

				{{-- Email --}}
				<div class="col-lg-6">
					<div class="form-group">
						<input type="email" name="email" id="email" 
						value="{{ old('email') }}"
						class="form-control @error('email') is-invalid @enderror"
						required placeholder="Email">
						@error('email')
						<small class="text-danger">{{ $message }}</small>
						@enderror
					</div>
				</div>

				{{-- Mobile Number --}}
				<div class="col-lg-6">
					<div class="form-group">
						<input type="text" name="phone_number" id="phone_number" maxlength="10" pattern="\d{10}" inputmode="numeric"
						oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
						value="{{ old('phone_number') }}"
						class="form-control @error('phone_number') is-invalid @enderror"
						required placeholder="Mobile Number">
						@error('phone_number')
						<small class="text-danger">{{ $message }}</small>
						@enderror
					</div>
				</div>

				{{-- Purpose --}}
				<div class="col-lg-6">
					<div class="form-group">
						<input type="text" name="purpose_of_requirement_of_stall" id="purpose_of_requirement_of_stall"
						value="{{ old('purpose_of_requirement_of_stall') }}"
						class="form-control @error('purpose_of_requirement_of_stall') is-invalid @enderror"
						required placeholder="Your Purpose of Requirement of Stall">
						@error('purpose_of_requirement_of_stall')
						<small class="text-danger">{{ $message }}</small>
						@enderror
					</div>
				</div>

				{{-- Address --}}
				<div class="col-lg-12">
					<div class="form-group">
						<textarea name="organization_address" id="organization_address" cols="20" rows="5"
						class="form-control @error('organization_address') is-invalid @enderror"
						required placeholder="Your organization address">{{ old('organization_address') }}</textarea>
						@error('organization_address')
						<small class="text-danger">{{ $message }}</small>
						@enderror
					</div>
				</div>

				{{-- Submit Button --}}
				<div class="col-lg-12 col-md-12">
					<button type="submit" class="default-btn">
						Submit
					</button>
				</div>
			</div>
		</form>

	</div>
</div>
</div>
<!-- Contact Widget Area End -->
@endsection 
@section('script')
<script>
	document.addEventListener("DOMContentLoaded", () => {
		const form = document.querySelector("form");
		const submitBtn = form.querySelector("button[type='submit']");
		let isSubmitting = false;

		form.addEventListener("submit", function (e) {
			e.preventDefault();
			clearErrors();

			if (!validateForm()) return;

			if (isSubmitting) return;
			isSubmitting = true;
			submitBtn.disabled = true;
			submitBtn.innerHTML = "Submitting...";

			form.submit();
		});

		function validateForm() {
			let valid = true;

			const orgName = getValue("name_of_the_organization");
			const contactPerson = getValue("contact_person_name");
			const email = getValue("email");
			const phone = getValue("phone_number");
			const purpose = getValue("purpose_of_requirement_of_stall");
			const address = getValue("organization_address");

			if (!orgName) valid = showError("name_of_the_organization", "Organization name is required.");
			if (!contactPerson) valid = showError("contact_person_name", "Contact person name is required.");
			if (!email || !isValidEmail(email)) valid = showError("email", "Please enter a valid email address.");
			if (!phone || !isValidPhone(phone)) valid = showError("phone_number", "Please enter a valid 10-digit mobile number.");
			if (!purpose) valid = showError("purpose_of_requirement_of_stall", "Purpose of stall requirement is required.");
			if (!address) valid = showError("organization_address", "Organization address is required.");

			return valid;
		}

		function getValue(id) {
			return document.getElementById(id).value.trim();
		}

		function showError(id, message) {
			const input = document.getElementById(id);
			const error = document.createElement("div");
			error.className = "text-danger small mt-1";
			error.innerText = message;
			input.closest(".form-group").appendChild(error);
			input.classList.add("is-invalid");
			input.scrollIntoView({ behavior: "smooth", block: "center" });
			return false;
		}

		function clearErrors() {
			document.querySelectorAll(".text-danger").forEach(el => el.remove());
			document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
		}

		function isValidEmail(email) {
			return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
		}

		function isValidPhone(phone) {
			return /^[6-9]\d{9}$/.test(phone);
		}
	});
</script>
@endsection
