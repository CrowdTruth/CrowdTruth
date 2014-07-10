<!-- START relex/breadcrumb -->
<ol class="breadcrumb">
	<!-- <li{{ (Request::is('preprocess/relex/info') ? ' class="active"' : '') }}>{{ link_to('preprocess/relex/info', "Info") }}</li> -->
	<li{{ (Request::is('media/preprocess/metadatadescription/actions') ? ' class="active"' : '') }}>{{ link_to('media/preprocess/metadatadescription/actions', "Actions") }}</li>
	
	@if(Request::is('media/preprocess/metadatadescription/preview'))
	<li class='active'>
		{{ link_to('media/preprocess/metadatadescription/preview?URI=' . $entity->_id, "Preview: " . $entity->title) }}
	</li>
	@endif
</ol>
<!-- END relex/breadcrumb   -->
