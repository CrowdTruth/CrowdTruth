<!-- START preprocess/chang/nav -->   
<ul class="nav nav-tabs">
	<!-- <li{{ (Request::is('preprocess') ? ' class="active"' : '') }}>{{ link_to('preprocess', "Info") }}</li> -->
	<li{{ (Request::is('preprocess/twrex*') ? ' class="active"' : '') }}>{{ link_to('preprocess/twrex', "Twrex") }}</li>
	<li{{ (Request::is('preprocess/fullvideo*') ? ' class="active"' : '') }}>{{ link_to('preprocess/fullvideo', "Full video") }}</li>
	<li{{ (Request::is('preprocess/csvresult*') ? ' class="active"' : '') }}>{{ link_to('preprocess/csvresult', "CSV results") }}</li>
	<!-- <li{{ (Request::is('preprocess/textrazor') ? ' class="active"' : '') }}>{{ link_to('preprocess/textrazor', "Textrazor") }}</li> -->
</ul>
<!-- END preprocess/chang/nav   --> 
