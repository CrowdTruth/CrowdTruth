<ul class="nav nav-tabs">
	<li{{ (Request::is('project/*') && !Request::is('project/*/*') ? ' class="active"' : '') }}>
		<a href="{{ URL::to('project/' . $project) }}">
			<i class="fa fa-user fa-fw"></i> Project
		</a>
	</li>
	<li{{ (Request::is('project/*/activity') ? ' class="active"' : '') }}>
		<a href="#">
			<i class="fa fa-bar-chart fa-fw"></i> Activity
		</a>
	</li>
	<li{{ (Request::is('project/*/settings') ? ' class="active"' : '') }}>
		<a href="{{ URL::to('project/' . $project . '/settings') }}">
			<i class="fa fa-gears fa-fw"></i> Settings
		</a>
	</li>
	<!-- <li{{ (Request::is('preprocess/textrazor') ? ' class="active"' : '') }}>{{ link_to('preprocess/textrazor', "Textrazor") }}</li> -->
</ul>