<!-- START files/browse/ breadcrumb -->   
<ol class="breadcrumb">
	@if(Request::segment(2) != '')
	<li {{ (Request::is('files/' . Request::segment(2)) ? 'class="active"' : '') }} >
		{{ link_to('files/'. Request::segment(2), "Collections") }}
	</li>
	@endif
	@if(Request::segment(3) != '')
	<li {{ (Request::is('files/browse/' . Request::segment(3)) ? 'class="active"' : '') }} >
		{{ link_to('files/browse/'. Request::segment(3), ucfirst(Request::segment(3))) }}		
	</li>
	@endif
	@if(Request::segment(4) != '')
	<li {{ (Request::is('files/browse/' . Request::segment(3) . '/' . Request::segment(4)) ? 'class="active"' : '') }} >
		{{ link_to('files/browse/' . Request::segment(3) . '/' . Request::segment(4), ucfirst(Request::segment(4))) }}		
	</li>
	@endif
	@if(Request::segment(5) != '')
	<li {{ (Request::is('files/browse/' . Request::segment(3) . '/' . Request::segment(4). '/' . Request::segment(5)) ? 'class="active"' : '') }} >
		{{ link_to('files/browse/' . Request::segment(3) . '/' . Request::segment(4). '/' . Request::segment(5), ucfirst(Request::segment(5))) }}		
	</li>
	@endif
</ol>
<!-- END files/browse/ breadcrumb   -->   