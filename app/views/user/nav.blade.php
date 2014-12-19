<ul class="nav nav-tabs">
	<li{{ (Request::is('user/profile') ? ' class="active"' : '') }}>{{ link_to('user/profile', "Profile") }}</li>
	<li{{ (Request::is('user/activity') ? ' class="active"' : '') }}>{{ link_to('user/activity', "Activity") }}</li>
	<li{{ (Request::is('user/settings') ? ' class="active"' : '') }}>{{ link_to('user/settings', "Settings") }}</li>
	<!-- <li{{ (Request::is('preprocess/textrazor') ? ' class="active"' : '') }}>{{ link_to('preprocess/textrazor', "Textrazor") }}</li> -->
</ul>