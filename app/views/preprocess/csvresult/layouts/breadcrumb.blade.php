<!-- START preprocess/csvresult/breadcrumb -->   
<ol class="breadcrumb">
	<!-- <li{{ (Request::is('preprocess/csvresult/info') ? ' class="active"' : '') }}>{{ link_to('preprocess/csvresult/info', "Info") }}</li> -->
	<li{{ (Request::is('preprocess/csvresult/actions') ? ' class="active"' : '') }}>{{ link_to('preprocess/csvresult/actions', "Actions") }}</li>
	
	@if(Request::is('preprocess/csvresult/preview'))
	<li class='active'>
		{{ link_to('preprocess/csvresult/preview?URI=' . $entity->_id, "Preview: " . $entity->title) }}
	</li>
	@endif
</ol>
<!-- END preprocess/csvresult/breadcrumb   -->   