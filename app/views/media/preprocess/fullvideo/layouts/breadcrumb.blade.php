<!-- START relex/breadcrumb -->
<ol class="breadcrumb">
	<!-- <li{{ (Request::is('preprocess/relex/info') ? ' class="active"' : '') }}>{{ link_to('preprocess/relex/info', "Info") }}</li> -->
	<li{{ (Request::is('media/preprocess/fullvideo/actions') ? ' class="active"' : '') }}>{{ link_to('media/preprocess/fullvideo/actions', "Actions") }}</li>
	
	@if(Request::is('media/preprocess/fullvideo/preview'))
	<li class='active'>
		{{ link_to('media/preprocess/fullvideo/preview?URI=' . $entity->_id, "Preview: " . $entity->title) }}
	</li>
	@endif
</ol>
<!-- END relex/breadcrumb   -->
