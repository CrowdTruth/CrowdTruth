<ul class="nav nav-tabs">
	<li{{ (Request::is('user/*/') ? ' class="active"' : '') }}>{{ link_to('user/' . $user['id'], "Profile") }}</li>
	<li{{ (Request::is('user/*/activity') ? ' class="active"' : '') }}>{{ link_to('user/' . $user['id'] . '/activity', "Activity") }}</li>
	<li{{ (Request::is('user/*/settings') ? ' class="active"' : '') }}>{{ link_to('user/' . $user['id'] . '/settings', "Settings") }}</li>
	<!-- <li{{ (Request::is('preprocess/textrazor') ? ' class="active"' : '') }}>{{ link_to('preprocess/textrazor', "Textrazor") }}</li> -->
</ul>