<!-- START chang/breadcrumb -->   
<ol class="breadcrumb">
	<li{{ (Request::is('preprocess/chang/info') ? ' class="active"' : '') }}>{{ link_to('preprocess/chang/info', "Info") }}</li>
	<li{{ (Request::is('preprocess/chang/actions') ? ' class="active"' : '') }}>{{ link_to('preprocess/chang/actions', "Actions") }}</li>
	
	@if(Request::is('preprocess/chang/preview'))
	<li class='active'>
		{{ link_to('preprocess/chang/preview?URI=' . $entity->_id, "Preview: " . $entity->title) }}
	</li>
	@endif
</ol>
<!-- END chang/breadcrumb   -->   