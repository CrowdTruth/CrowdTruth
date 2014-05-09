<!-- START files/browse/ breadcrumb -->   
<ol class="breadcrumb">
	<li {{ (Request::is('files/browse') ? 'class="active"' : '') }} >
		{{ link_to('files/browse', "Collections") }}
	</li>
	@if(isset($format))
	<li {{ (Request::is('files/browse/' . $format) ? 'class="active"' : '') }} >
		{{ link_to('files/browse/'. $format, ucfirst($format)) }}		
	</li>
	@endif
	@if(isset($domain))
	<li {{ (Request::is('files/browse/' . $format . '/' . $domain) ? 'class="active"' : '') }} >
		{{ link_to('files/browse/' . $format . '/' . $domain, ucfirst($domain)) }}		
	</li>
	@endif
	@if(isset($documentType))
	<li {{ (Request::is('files/browse/' . $format . '/' . $domain. '/' . $documentType) ? 'class="active"' : '') }} >
		{{ link_to('files/browse/' . $format . '/' . $domain . '/' . $documentType, ucfirst($documentType)) }}		
	</li>
	@endif
	@if(isset($document))
	<li class="active">
		{{ link_to('files/view?URI=' . $document->_id, $document->title) }}		
	</li>
	@endif
</ol>
<!-- END files/browse/ breadcrumb   -->   