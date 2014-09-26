
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
					<ul class="nav navbar-nav">
						<li{{ (Request::is('media/search') ? ' class="active"' : '') }} data-toggle="tooltip" id="mediaTabOption" data-placement="bottom" title="Search Existing Media <br /> View Media Analytics">
						<a href='{{ url("media/search") }}'><i class='fa fa-fw fa-files-o'></i> Media</a></li>
						<li{{ (Request::segment(1) == 'jobs' ? ' class="active"' : '') }} data-toggle="tooltip" id="jobTabOption" data-placement="bottom" title="View Existing Job Analytics <br /> Create New Jobs">
						<a href='{{ url("jobs") }}'><i class='fa fa-fw fa-shopping-cart'></i> Jobs</a></li>
						<li{{ (Request::segment(1) == 'workers' ? ' class="active"' : '') }} data-toggle="tooltip" id="workerTabOption" data-placement="bottom" title="View Worker Analytics">
						<a href='{{ url("workers") }}'><i class='fa fa-fw fa-users'></i> Workers</a></li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class='fa fa-fw fa-info'></i> Info <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href='{{ url("info") }}'><i class='fa fa-fw fa-info'></i> Documentation</a></li>
								<li><a href='{{ url("templates/examples") }}'><i class='fa fa-fw fa-file-code-o'></i> Examples</a></li>
								<li><a href='{{ url("papers") }}'><i class='fa fa-fw fa-flask'></i> Papers</a></li>
								<li><a href='{{ url("presentations") }}'><i class='fa fa-fw fa-slideshare'></i> Presentations</a></li>
								<li><a href='{{ url("team") }}'><i class='fa fa-fw fa-puzzle-piece'></i> Team</a></li>
								<li><a href='{{ url("contact") }}'><i class='fa fa-fw fa-envelope-o'></i> Contact</a></li>
								<li class="divider"></li>
								<li><a href="https://www.vu.nl/en/index.asp" target="_blank"><i class='fa fa-fw fa-university'></i> VU University</a></li>
								<li><a href="https://www.ibm.com" target="_blank"><i class='fa fa-fw fa-university'></i> IBM</a></li>
							</ul>
						</li>
					</ul>
					@include('layouts.dynamic_selection_user')
				</div><!--/.nav-collapse -->
			</div>
		</div>