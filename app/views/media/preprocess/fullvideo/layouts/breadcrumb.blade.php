<!-- START twrex/breadcrumb -->   
<ol class="breadcrumb">
	<!-- <li{{ (Request::is('preprocess/twrex/info') ? ' class="active"' : '') }}>{{ link_to('preprocess/twrex/info', "Info") }}</li> -->
	<li{{ (Request::is('media/preprocess/twrex/actions') ? ' class="active"' : '') }}>{{ link_to('media/preprocess/twrex/actions', "Actions") }}</li>
	
	@if(Request::is('media/preprocess/twrex/preview'))
	<li class='active'>
		{{ link_to('media/preprocess/twrex/preview?URI=' . $entity->_id, "Preview: " . $entity->title) }}
	</li>
	@endif
</ol>
<!-- END twrex/breadcrumb   -->   