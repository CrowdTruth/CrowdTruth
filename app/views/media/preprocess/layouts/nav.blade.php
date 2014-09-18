<!-- START preprocess/chang/nav -->   
<ul class="nav nav-tabs">
	<!-- <li{{ (Request::is('preprocess') ? ' class="active"' : '') }}>{{ link_to('preprocess', "Info") }}</li> -->
	<li{{ (Request::is('preprocess/relex*') ? ' class="active"' : '') }}>{{ link_to('preprocess/relex', "Relex") }}</li>
	<li{{ (Request::is('preprocess/fullvideo*') ? ' class="active"' : '') }}>{{ link_to('preprocess/fullvideo', "Full video") }}</li>
	<li{{ (Request::is('preprocess/metadatadescription*') ? ' class="active"' : '') }}>{{ link_to('preprocess/metadatadescription', "Metadata Description") }}</li>	
	<li{{ (Request::is('preprocess/csvresult*') ? ' class="active"' : '') }}>{{ link_to('preprocess/csvresult', "CSV results") }}</li>
	<!-- <li{{ (Request::is('preprocess/textrazor') ? ' class="active"' : '') }}>{{ link_to('preprocess/textrazor', "Textrazor") }}</li> -->
</ul>
<!-- END preprocess/chang/nav   --> 