<!-- START relex/breadcrumb -->
<ol class="breadcrumb">
	<!-- <li{{ (Request::is('preprocess/relex/info') ? ' class="active"' : '') }}>{{ link_to('preprocess/relex/info', "Info") }}</li> -->
	<li{{ (Request::is('preprocess/relex/actions') ? ' class="active"' : '') }}>{{ link_to('preprocess/relex/actions', "Actions") }}</li>
	
	@if(Request::is('preprocess/relex/preview'))
	<li class='active'>
		{{ link_to('preprocess/relex/preview?URI=' . $entity->_id, "Preview: " . $entity->title) }}
	</li>
	@endif
</ol>
<!-- END relex/breadcrumb   -->