@if(Session::has('success'))
<div class="alert alert-success alert-rounded alert-dismissible fade show"> 
    <i class="ti-user"></i> {{ Session::get('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> 
        <span aria-hidden="true"></span> 
    </button>
</div>
@endif

@if(Session::has('info'))
<div class="alert alert-info alert-rounded alert-dismissible fade show"> 
    <i class="ti-user"></i> {{ Session::get('info') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> 
        <span aria-hidden="true"></span> 
    </button>
</div>
@endif

@if(Session::has('warning'))
<div class="alert alert-warning alert-rounded alert-dismissible fade show"> 
    <i class="ti-user"></i> {{ Session::get('warning') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> 
        <span aria-hidden="true"></span> 
    </button>
</div>
@endif

@if(Session::has('error'))
<div class="alert alert-danger alert-rounded alert-dismissible fade show"> 
    <i class="ti-user"></i> {{ Session::get('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> 
        <span aria-hidden="true"></span> 
    </button>
</div>
@endif