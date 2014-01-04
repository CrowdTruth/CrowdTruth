<!-- START files/browse/ breadcrumb -->   
<ol class="breadcrumb">
	<li {{ (Request::is('files/browse') ? 'class="active"' : '') }} >
		{{ link_to('files/browse', "Collections") }}
	</li>
	@if(isset($fileType))
	<li {{ (Request::is('files/browse/' . $fileType) ? 'class="active"' : '') }} >
		{{ link_to('files/browse/'. $fileType, ucfirst($fileType)) }}		
	</li>
	@endif
	@if(isset($domainType))
	<li {{ (Request::is('files/browse/' . $fileType . '/' . $domainType) ? 'class="active"' : '') }} >
		{{ link_to('files/browse/' . $fileType . '/' . $domainType, ucfirst($domainType)) }}		
	</li>
	@endif
	@if(isset($documentType))
	<li {{ (Request::is('files/browse/' . $fileType . '/' . $domainType. '/' . $documentType) ? 'class="active"' : '') }} >
		{{ link_to('files/browse/' . $fileType . '/' . $domainType . '/' . $documentType, ucfirst($documentType)) }}		
	</li>
	@endif
	@if(isset($document))
	<li class="active">
		{{ link_to('files/view?URI=' . $document->_id, $document->title) }}		
	</li>
	@endif
</ol>
<!-- END files/browse/ breadcrumb   -->   