<!-- START preprocess/chang/nav -->   
<ul class="nav nav-tabs">
	<li{{ (Request::is('preprocess') ? ' class="active"' : '') }}>{{ link_to('preprocess', "Info") }}</li>
	<li{{ (Request::is('preprocess/chang*') ? ' class="active"' : '') }}>{{ link_to('preprocess/chang', "Chang") }}</li>
	<li{{ (Request::is('preprocess/textrazor') ? ' class="active"' : '') }}>{{ link_to('preprocess/textrazor', "Textrazor") }}</li>
</ul>
<!-- END preprocess/chang/nav   --> 