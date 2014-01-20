<!-- START files/browse/ breadcrumb -->   
<ol class="breadcrumb">
	<li {{ (Request::is('files/browse') ? 'class="active"' : '') }} >
		{{ link_to('files/browse', "Collections") }}
	</li>
	@if(isset($type))
	<li {{ (Request::is('files/browse/' . $type) ? 'class="active"' : '') }} >
		{{ link_to('files/browse/'. $type, ucfirst($type)) }}		
	</li>
	@endif
	@if(isset($domain))
	<li {{ (Request::is('files/browse/' . $type . '/' . $domain) ? 'class="active"' : '') }} >
		{{ link_to('files/browse/' . $type . '/' . $domain, ucfirst($domain)) }}		
	</li>
	@endif
	@if(isset($documentType))
	<li {{ (Request::is('files/browse/' . $type . '/' . $domain. '/' . $documentType) ? 'class="active"' : '') }} >
		{{ link_to('files/browse/' . $type . '/' . $domain . '/' . $documentType, ucfirst($documentType)) }}		
	</li>
	@endif
	@if(isset($document))
	<li class="active">
		{{ link_to('files/view?URI=' . $document->_id, $document->title) }}		
	</li>
	@endif
</ol>
<!-- END files/browse/ breadcrumb   -->   