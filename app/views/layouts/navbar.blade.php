
		<div class="navbar navbar-default navbar-fixed-top" role="navigation">
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
						<li{{ (Request::is('media/search') ? ' class="active"' : '') }} data-toggle="tooltip" data-placement="bottom" title="Search Existing Media <br /> View Media Analytics">{{ link_to('media/search', "Media") }}</li>
						<li{{ (Request::segment(1) == 'jobs' ? ' class="active"' : '') }} data-toggle="tooltip" data-placement="bottom" title="View Existing Job Analytics <br /> Create New Jobs">
						{{ link_to('jobs', "Jobs") }}</li>
						<li{{ (Request::segment(1) == 'workers' ? ' class="active"' : '') }} data-toggle="tooltip" data-placement="bottom" title="View Worker Analytics">
						{{ link_to('workers', "Workers") }}</li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Info <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li>{{ link_to('info', "Documentation") }}</li>
								<li>{{ link_to('papers', "Papers") }}</li>
								<li>{{ link_to('team', "Team") }}</li>
								<li class="divider"></li>
								<li><a href="https://www.vu.nl/en/index.asp" target="_blank">VU</a></li>
								<li><a href="https://www.ibm.com" target="_blank">IBM</a></li>
							</ul>
						</li>
					</ul>
					@include('layouts.dynamic_selection_user')
				</div><!--/.nav-collapse -->
			</div>
		</div>