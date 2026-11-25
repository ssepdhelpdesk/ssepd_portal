@section('title') 
Disability Day 2026 || Add Participant Registration
@endsection 
@extends('dashboard.layouts.main')

@section('style')
<style>
    .font-monospace {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }
    .required-asterisk {
        color: #dc3545;
        font-weight: bold;
    }
</style>
@endsection 

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.disability_day_participant.index') }}">Participant Registrations</a></li>
                <li class="breadcrumb-item active">Add New</li>
            </ol>
        </div>
        <div class="col-md-5 align-self-center text-end">
            <a href="{{ route('admin.disability_day_participant.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    @include('dashboard.component.message')

    @if(isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="fas fa-exclamation-triangle me-2"></i> Please correct the highlighted errors:</strong>
        <ul class="mb-0 mt-2 ps-3">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 text-dark">
                        <i class="fas fa-user-plus text-success me-2"></i> Register New Participant (Admin)
                    </h5>
                </div>
                <form action="{{ route('admin.disability_day_participant.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @include('dashboard.disability_day_participant._form')
                    </div>
                    <div class="card-footer bg-white border-top py-3 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.disability_day_participant.index') }}" class="btn btn-light border">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> Save Participant Registration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
@stack('scripts')
@endsection
