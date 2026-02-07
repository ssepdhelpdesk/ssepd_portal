<footer class="footer">
	<div class="footer-bg">
		<img src="{{ asset('website_assets/assets/img/bg/footer-bg-01.png') }}" class="footer-bg-1" alt="">
		<img src="{{ asset('website_assets/assets/img/bg/footer-bg-02.png') }}" class="footer-bg-2" alt="">
	</div>
	<div class="footer-top">
		<div class="container">
			<div class="row row-gap-4">
				<div class="col-lg-4">
					<div class="footer-about">
						<div class="footer-logo">
							<img src="{{ asset('website_assets/assets/img/logo.svg') }}" alt="">
						</div>
						<p>This platform allows pension beneficiaries to provide consent online for receiving pension through Direct Bank Transfer (DBT).</p>
						<div class="d-flex align-items-center">
									<!-- <a href="javascript:void(0);" class="me-2"><img src="{{ asset('website_assets/assets/img/icon/appstore.svg') }}" alt=""></a>
									<a href="javascript:void(0);"><img src="{{ asset('website_assets/assets/img/icon/googleplay.svg') }}" alt=""></a> -->
									<p class="mb-0">
										Total Visitors: <strong>{{ number_format(\App\Models\VisitorCount::count()) }}</strong>
									</p>
								</div>
							</div>
						</div>
						<div class="col-lg-8">
							<div class="row row-gap-4">
								<div class="col-lg-3">
									<!-- <div class="footer-widget footer-menu">
										<h5 class="footer-title">For Instructor</h5>
										<ul>
											<li><a href="course-grid.html">Search Mentors</a></li>
											<li><a href="login.html">Login</a></li>
											<li><a href="register.html">Register</a></li>
											<li><a href="course-list.html">Booking</a></li>
											<li><a href="student-dashboard.html">Students Dashboard</a></li>
										</ul>
									</div> -->
								</div>
								<div class="col-lg-3">
									<div class="footer-widget footer-menu">
										<h5 class="footer-title">Portals</h5>
										<ul>
											<li><a href="{{ route('website.pensioners.index') }}">SSEPD-IT</a></li>
											<li><a href="javascript:void(0);">EP Beneficiaries</a></li>
											<li><a href="https://siep.ssepdit.in">SIEP</a></li>
											<li><a href="{{route('login')}}">Login</a></li>
											<li><a href="{{route('login')}}"> Register</a></li>
										</ul>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="footer-widget footer-contact">
										<h5 class="footer-title">Newsletter</h5>
										<div class="subscribe-input">
											<form action="javascript:void(0);">
												<input type="email" class="form-control" placeholder="Enter your Email Address">
												<button type="submit" class="btn btn-primary btn-sm inline-flex align-items-center"><i class="isax isax-send-2 me-1"></i>Subscribe</button>
											</form>
										</div>
										<div class="footer-contact-info">
											<div class="footer-address d-flex align-items-center">
												<img src="{{ asset('website_assets/assets/img/icon/icon-20.svg') }}" alt="Img" class="img-fluid me-2">
												<p> SSEPD Department, Red Building, <br> Lokseva Bhawan, Bhubaneswar 751001 </p>
											</div>
											<div class="footer-address d-flex align-items-center">
												<img src="{{ asset('website_assets/assets/img/icon/icon-19.svg') }}" alt="Img" class="img-fluid me-2">
												<p>ssepdsec.od@od.gov.in</p>
											</div>
											<div class="footer-address d-flex align-items-center">
												<img src="{{ asset('website_assets/assets/img/icon/icon-21.svg') }}" alt="Img" class="img-fluid me-2">
												<p>0674- 2392803</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="footer-bottom">
				<div class="container">
					<div class="row row-gap-2">
						<div class="col-md-6">
							<div class="text-center text-md-start">
								<p class="text-white">Copyright {{ date('Y') }} © SSEPD-IT. All rights reserved.</p>
							</div>
						</div>
						<div class="col-md-6">
							<div>
								<ul class="d-flex align-items-center justify-content-center justify-content-md-end footer-link">
									<li><a href="javascript:void(0);">Terms & Conditions</a></li>
									<li><a href="javascript:void(0);">Privacy Policy</a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</footer>