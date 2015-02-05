
		<div class="navbar navbar-default navbar-static-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					{{ link_to('/', "CrowdTruth", array('class' => 'navbar-brand')); }}

				</div>
				<div class="navbar-collapse collapse">

					<ul class='progress-nav'>
						<li class='progress-step progress-nav-active'>
							<a href='{{ url("media/preprocess/relex/actions") }}'>
								<i class='progress-icon fa fa-files-o'></i>
								Files
							</a>
						</li>

						<li class="progress progress-step">
						  <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: {{ (Request::is('jobs') || Request::is('media/search') ? '100' : (Request::is('media/preprocess/text/configure') ? '50' : '0')) }}%">
						  </div>
						</li>
							
						<li class='progress-step {{ (Request::is('media/search') || Request::is('jobs') ? 'progress-nav-active' : '') }}'>
								<a href='{{ url("media/search") }}'>
									<i class='progress-icon fa fa-database'></i>
									Media
								</a>
							</li>

							<li class="progress progress-step">
							  <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: {{ (Request::is('jobs') ? '100' : '0') }}%">
							  </div>
							</li>
							
						<li class='progress-step {{ (Request::is('jobs') ? 'progress-nav-active' : '') }}'>
								<a href='{{ url("jobs") }}'>
									<i class='progress-icon fa fa-shopping-cart'></i>
									Jobs
								</a>
							</li>

						<li class='progress-step-other {{ (Request::segment(1) == 'workers' ? ' active' : '') }} data-toggle="tooltip" id="workerTabOption" data-placement="bottom" title="View Worker Analytics" style='text-align:center;'>
							<a href='{{ url("workers") }}'><i class='progress-icon fa fa-users' style='font-size: 14px;'></i>Workers</a></li>
						<li class="dropdown progress-step-other">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class='progress-icon fa fa-info'></i> Info <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href='https://github.com/CrowdTruth/CrowdTruth/wiki'><i class='fa fa-fw fa-info'></i> Documentation</a></li>
								<li><a href='https://github.com/CrowdTruth/CrowdTruth/'><i class='fa fa-fw fa-github'></i> Source code</a></li>
								<li><a href='https://github.com/CrowdTruth/CrowdTruth/wiki/Creating-Templates'><i class='fa fa-fw fa-file-code-o'></i> Templates</a></li>
								<li class="divider"></li>
								<li><a href='http://crowdtruth.org'><i class='fa fa-fw fa-newspaper-o'></i> Blog</a></li>
								<li><a href='http://crowdtruth.org/papers'><i class='fa fa-fw fa-flask'></i> Papers</a></li>
								<li><a href='http://crowdtruth.org/presentations'><i class='fa fa-fw fa-slideshare'></i> Presentations</a></li>
								<li><a href='http://crowdtruth.org/partners'><i class='fa fa-fw fa-university'></i> Partners</a></li>
								<li><a href='http://crowdtruth.org/team'><i class='fa fa-fw fa-puzzle-piece'></i> Team</a></li>
								<li><a href='http://crowdtruth.org/contact'><i class='fa fa-fw fa-envelope-o'></i> Contact</a></li>
							</ul>
						</li>
					</ul>
					@include('layouts.dynamic_selection_user')
				</div><!--/.nav-collapse -->
			</div>
		</div>