
		<div class="navbar navbar-default navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					{{ link_to('/', "Crowd-Watson", array('class' => 'navbar-brand')); }}

				</div>
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<li{{ (Request::segment(1) == 'home' ? ' class="active"' : '') }}>{{ link_to('/', "Home") }}</li>
						<li{{ (Request::segment(1) == 'files' ? ' class="active"' : '') }}>{{ link_to('files', "Files") }}</li>
						<li{{ (Request::segment(1) == 'preprocess' ? ' class="active"' : '') }}>{{ link_to('preprocess', "Pre-Process") }}</li>
						<li{{ (Request::segment(1) == 'process' ? ' class="active"' : '') }}>{{ link_to('process', "Process") }}</li>
						<li{{ (Request::segment(1) == 'postprocess' ? ' class="active"' : '') }}>{{ link_to('postprocess', "Post-Process") }}</li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">More <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="#">Crowd Truth</a></li>
								<li><a href="#">Dr. Detective</a></li>
								<li class="divider"></li>
								<li><a href="#">VU</a></li>
								<li><a href="#">IBM</a></li>
							</ul>
						</li>
					</ul>
					@include('layouts.dynamic_selection_user')
				</div><!--/.nav-collapse -->
			</div>
		</div>