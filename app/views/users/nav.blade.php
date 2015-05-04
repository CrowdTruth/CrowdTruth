<ul class="nav nav-tabs">
	<li{{ (Request::is('user/*') && !Request::is('user/*/*') ? ' class="active"' : '') }}>
		<a href="{{ URL::to('user/' . $user['_id']) }}">
			<i class="fa fa-user fa-fw"></i> Profile
		</a>
	</li>
	<li{{ (Request::is('user/*/activity') ? ' class="active"' : '') }}>
		<a href="{{ URL::to('user/' . $user['_id'] . '/activity') }}">
			<i class="fa fa-bar-chart fa-fw"></i> Activity
		</a>
	</li>
	<li{{ (Request::is('user/*/settings') ? ' class="active"' : '') }}>
		<a href="{{ URL::to('user/' . $user['_id'] . '/settings') }}">
			<i class="fa fa-gears fa-fw"></i> Settings
		</a>
	</li>
	<!-- <li{{ (Request::is('preprocess/textrazor') ? ' class="active"' : '') }}>{{ link_to('preprocess/textrazor', "Textrazor") }}</li> -->
</ul>