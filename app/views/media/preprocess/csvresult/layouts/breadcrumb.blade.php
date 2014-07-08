<!-- START preprocess/csvresult/breadcrumb -->   
<ol class="breadcrumb">
	<!-- <li{{ (Request::is('preprocess/csvresult/info') ? ' class="active"' : '') }}>{{ link_to('preprocess/csvresult/info', "Info") }}</li> -->
	<li{{ (Request::is('preprocess/csvresult/inputdata') ? ' class="active"' : '') }}>{{ link_to('preprocess/csvresult/inputdata', "Input Data") }}</li>
	<li{{ (Request::is('preprocess/csvresult/workerunitdata') ? ' class="active"' : '') }}>{{ link_to('preprocess/csvresult/workerunitdata', "Workerunit Data") }}</li>
	
	@if(Request::is('preprocess/csvresult/preview'))
	<li class='active'>
		{{ link_to('preprocess/csvresult/preview?URI=' . $entity->_id, "Preview: " . $entity->title) }}
	</li>
	@endif
</ol>
<!-- END preprocess/csvresult/breadcrumb   -->   