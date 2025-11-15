<aside class="left-sidebar">
   <!-- Sidebar scroll-->
   <div class="scroll-sidebar">
      <!-- Sidebar navigation-->
      <nav class="sidebar-nav">
         <ul id="sidebarnav">
            <li class="user-pro">
               <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><img src="{{ (!empty(Auth::user()->profile_photo))? url(Auth::user()->profile_photo_path):url('storage/profile-pic/no_image.jpg') }}" alt="user-img" class="img-circle"><span class="hide-menu">{{Auth::user()->name}} &nbsp;</span></a>
               <ul aria-expanded="false" class="collapse">
                  @can('my-profile-access')
                  <li><a href="{{route('admin.myprofile.index')}}"><i class="ti-user"></i> My Profile</a></li>
                  @endcan
                  @can('my-profile-edit')
                  <li><a href="{{route('admin.myprofile.changePassword')}}"><i class="ti-settings"></i> Account Setting</a></li>
                  @endcan
                  <form method="POST" action="{{ route('logout') }}">
                     @csrf
                     <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="dropdown-item"><i class="fa fa-power-off"></i> Logout</a>
                  </form>
               </ul>
            </li>
            <li class="nav-small-cap">--- PERSONAL</li>
            <li>
               <a class="has-arrow waves-effect waves-dark active" href="javascript:void(0)" aria-expanded="false"><i class="icon-speedometer"></i><span class="hide-menu">Dashboard </span></a>
               <div id="google_translate_element"></div>
            </li>
            @can('user-access')
            <li>
               <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fas fa-user-circle"></i><span class="hide-menu">Users</span></a>
               <ul aria-expanded="false" class="collapse">
                  @can('user-list')
                  <li><a href="{{route('admin.users.index')}}">View Users</a></li>
                  @endcan
                  @can('user-create')
                  <li><a href="{{route('admin.users.create')}}">Create Users</a></li>
                  @endcan
               </ul>
            </li>
            @endcan
            @if(auth()->user()->can('role-access') || auth()->user()->can('permission-access'))
            <li>
               <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fab fa-keycdn"></i><span class="hide-menu">Roles & permissions</span></a>
               <ul aria-expanded="false" class="collapse">
                  @can('role-list')
                  <li><a href="{{route('admin.roles.index')}}">View Roles</a></li>
                  @endcan
                  @can('role-create')
                  <li><a href="{{route('admin.roles.create')}}">Create Roles</a></li>
                  @endcan
                  @can('permission-list')
                  <li><a href="{{route('admin.permissions.index')}}">View Permissions</a></li>
                  @endcan
                  @can('permission-create')
                  <li><a href="{{route('admin.permissions.create')}}">Create Permissions</a></li>
                  @endcan
               </ul>
            </li>
            @endif
            @can('location-access')
            <li>
               <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fas fa-location-arrow"></i><span class="hide-menu">Locations</span></a>
               <ul aria-expanded="false" class="collapse">
                  @can('location-list')
                  <li><a href="{{route('admin.locations.blockwise.index')}}">Block Wise</a></li>
                  <li><a href="{{route('admin.locations.municipalitywise.index')}}">Municipality Wise</a></li>
                  @endcan
                  @can('location-create')
                  <li><a href="app-ticket.html">Support Ticket</a></li>
                  <li><a href="app-contact.html">Contact / Employee</a></li>
                  <li><a href="app-contact2.html">Contact Grid</a></li>
                  <li><a href="app-contact-detail.html">Contact Detail</a></li>
                  @endcan
               </ul>
            </li>
            <li>
               <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fas fa-bell"></i><span class="hide-menu">Notification</span></a>
               <ul aria-expanded="false" class="collapse">
                  @can('location-list')
                  <li><a href="{{route('admin.ssepdnotification.create')}}">Create Notification</a></li>
                  @endcan
               </ul>
            </li>
            @endif
            <li class="nav-small-cap">--- BENEFICIARY SERVICES</li>
            <li>
               <a class="has-arrow waves-effect waves-dark active" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-book-open-page-variant"></i><span class="hide-menu">Beneficiary Services</span></a>
               <ul aria-expanded="false" class="collapse in">                  
                  <li>
                     <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">Pension</a>
                     <ul aria-expanded="false" class="collapse">
                        @can('pension-create')                        
                        <li><a href="{{route('admin.pension.create')}}">Funds Requirements</a></li>
                        <li><a href="{{route('admin.pension.pension_authority_index')}}">Pension Disbursing Officer</a></li>
                        <li><a href="{{route('admin.dailypensiondisbursement.index')}}">Daily Pension Disbursement</a></li>
                        <!-- <li><a href="{{route('admin.monthlypensiondisbursement.index')}}">Daily Pension Disbursement</a></li> -->
                        @endcan                        
                     </ul>
                  </li>
                  <li>
                     <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">Special School</a>
                     <ul aria-expanded="false" class="collapse">
                        @can('special-school-access')
                        <li><a href="{{route('admin.specialschool.index')}}">View Special Schools</a></li>
                        @endcan
                        @can('special-school-create')
                        <li><a href="{{route('admin.specialschool.view_staff_details')}}">View Staff Details</a></li>
                        <li><a href="{{route('admin.specialschool.create')}}">Add New Staff</a></li>
                        <li><a href="{{route('admin.specialschoolconstructions.construction_timeline')}}">Toilet Construction Update</a></li>
                        @endcan
                     </ul>
                  </li>
                  <li>
                     <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">Enhanced Pension</a>
                     <ul aria-expanded="false" class="collapse">
                        @can('pension-3500-list')
                        <li><a href="{{route('admin.oldage3500data.index')}}">OldAge Pension</a></li>
                        <li><a href="{{route('admin.disability3500data.index')}}">Disability Pension</a></li>
                        <li><a href="{{route('admin.oldage3500data.oldage_index_district_block_ulb')}}">OAP GP/Ward Correction</a></li>
                        <li><a href="{{route('admin.disability3500data.disability_index_district_block_ulb')}}">DP GP/Ward Correction</a></li>
                        <li><a href="{{route('admin.oldage3500data.create')}}">OAP Pension DataEnrty</a></li>
                        <li><a href="{{route('admin.disability3500data.create')}}">DP Pension DataEnrty</a></li>
                        <!-- <li><a href="{{route('admin.oldage3500data.index_district')}}">OldAge Pension</a></li>
                        <li><a href="{{route('admin.disability3500data.index_district')}}">Disability Pension</a></li> -->
                        <li><a href="{{route('admin.oldage3500data.oldage_index_district_block_ulb_gp_update')}}">OAP GP Correction</a></li>
                        @endcan
                     </ul>
                  </li>
                  <li>
                     <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">DDRC</a>
                     <ul aria-expanded="false" class="collapse">
                        @can('DDRC-create')
                        <li><a href="{{route('admin.ddrc.create')}}">DDRC Staff Details Entry</a></li>                        
                        @endcan
                     </ul>
                  </li>
               </ul>
            </li>
            <li class="nav-small-cap">--- REPORTS</li>
            <li>
               <a class="has-arrow waves-effect waves-dark active" href="javascript:void(0)" aria-expanded="false"><i class="ti-layout-grid2"></i><span class="hide-menu">Reports</span></a>
               <ul aria-expanded="false" class="collapse in">                  
                  <li>
                     <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">Pension</a>
                     <ul aria-expanded="false" class="collapse">                        
                        @can('pension-access')
                        <!-- <li><a href="{{route('admin.pension.report')}}">Pension Funds Requirement1</a></li> -->
                        <li><a href="{{route('admin.pension.report_without_ajax')}}">Pension Funds Requirement</a></li>
                        <li><a href="{{route('admin.pension.pension_authority_report')}}">Pension Disbursing Officer</a></li>
                        <li><a href="{{route('admin.dailypensiondisbursement.listing_report')}}">Daily Pension Disbursement</a></li>
                        <li><a href="{{route('admin.dailypensiondisbursement.combined_report')}}">GP/Ward wise Combined Daily Pension Disbursement</a></li>
                        <li><a href="{{route('admin.dailypensiondisbursement.pension_disbursement_daily_not_submission')}}">Pension Disbursement – Not Submitted</a></li>
                        <li><a href="{{route('admin.dailypensiondisbursement.daily_pension_disbursement_vs_funds_requirements_beneficiaries')}}">Beneficiary wise Funds Requirements vs Daily Disbursement</a></li>
                        <li><a href="{{route('admin.dailypensiondisbursement.daily_pension_disbursement_fund_vs_funds_requirements')}}">Funds Requirements vs Disbursed Funds</a></li>
                        <li><a href="{{route('admin.dailypensiondisbursement.daily_pension_disbursement_vs_funds_requirements_beneficiaries_and_funds')}}">Funds Requirements vs Daily Disbursement</a></li>
                        <li><a href="{{route('admin.dailypensiondisbursement.month_wise_fund_requirement_comparison_for_district')}}">District wise Monthly Fund Comparison</a></li>
                        <li><a href="{{route('admin.dailypensiondisbursement.month_wise_fund_requirement_comparison_for_block_ulb')}}">Block/ULB Monthly Fund Comparison</a></li>
                        <!-- <li><a href="{{route('admin.dailypensiondisbursement.daily_pension_disbursement_vs_funds_requirements')}}">Disbursement vs Funds Requirements</a></li> -->
                        <!-- <li><a href="{{route('admin.monthlypensiondisbursement.monthly_pension_disbursement_report')}}">Daily Pension Disbursement</a></li>                        
                        <li><a href="{{route('admin.monthlypensiondisbursement.pension_disbursement_daily_submission')}}">Pension Disbursement – Daily Submissions</a></li>
                        <li><a href="{{route('admin.monthlypensiondisbursement.pension_disbursement_daily_not_submission')}}">Pension Disbursement – Not Submitted</a></li>
                        <li><a href="{{route('admin.monthlypensiondisbursement.monthly_pension_disbursement_report_abstract')}}">Daily Pension Disbursement Abstract</a></li> -->
                        @endcan                        
                     </ul>
                  </li>
                  <li>
                     <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">Enhanced Pension</a>
                     <ul aria-expanded="false" class="collapse">
                        @can('pension-3500-access')
                        <li><a href="{{route('admin.reportof3500data.active_ineligible')}}">Active Ineligible Report</a></li>
                        <li><a href="{{route('admin.reportof3500data.sanction_report')}}">Sanction Report</a></li>
                        @endcan
                     </ul>
                  </li>
                  <li>
                     <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">Special School</a>
                     <ul aria-expanded="false" class="collapse">
                        @can('special-school-access')
                        <!-- <li><a href="{{route('admin.specialschool.cumulative_report')}}">Special Schools</a></li> -->
                        <li><a href="{{route('admin.specialschool.school_wise_staff_count_report')}}">Staff Details</a></li>
                        <li><a href="{{route('admin.specialschoolconstructions.school_wise_toilet_construction_report')}}">Toilet Construction</a></li>
                        <!-- <li><a href="{{route('admin.specialschoolconstructions.all_in_one_approval')}}">Toilet Construction</a></li> -->
                        @endcan
                     </ul>
                  </li>
                  <li>
                     <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">DDRC</a>
                     <ul aria-expanded="false" class="collapse">
                        @can('DDRC-access')
                        <li><a href="{{route('admin.ddrc.index')}}">DDRC Staff Deatils</a></li>
                        @endcan
                     </ul>
                  </li>
               </ul>
            </li>
         </ul>
      </nav>
      <!-- End Sidebar navigation -->
   </div>
   <!-- End Sidebar scroll-->
</aside>