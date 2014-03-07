@extends('layouts.default')

@section('container', 'full-container')

@section('head')
{{ stylesheet_link_tag('dataTables.bootstrap.css') }}
@stop

@section('content')
				<!-- START search_content --> 
				<div class="col-xs-12">
					<div class='maincolumn CW_box_style'>
@include('layouts.flashdata')						
@include('files.layouts.nav')
						<div class='tab'>
							<div class='row'>

								<div class='col-xs-4 facetedSearchFilters'>
									@if(isset($mainSearchFilters['formats']))
									<ul class="nav nav-pills nav-stacked cw_formats">
										<li class="header">
											<a href="#">
												<strong>Formats</strong>
											</a>
										</li>										
										@foreach($mainSearchFilters['formats'] as $key => $value)	
										<li data-field-query="field[format][]={{$key}}" data-value="{{$key}}">
											<a href="#" class="filterOption">
												<i class="fa fa-circle-o fa-fw"></i>
												<span class="badge pull-right">{{ $value['count'] }}</span>
												{{ $key }}
											</a>
										</li>
										@endforeach
									</ul>
									@endif

									@if(isset($mainSearchFilters['domains']))
									<ul class="nav nav-pills nav-stacked cw_domains">
										<li class="header">
											<a href="#">
												<strong>Domains</strong>
											</a>
										</li>										
										@foreach($mainSearchFilters['domains'] as $key => $value)	
										<li data-field-query="field[domain][]={{$key}}" data-value="{{$key}}">
											<a href="#" class="filterOption">
												<i class="fa fa-circle-o fa-fw"></i>
												<span class="badge pull-right">{{ $value['count'] }}</span>
												{{ $key }}
											</a>
										</li>
										@endforeach
									</ul>
									@endif

									@if(isset($mainSearchFilters['documentTypes']))
									<ul class="nav nav-pills nav-stacked cw_documentTypes">
										<li class="header">
											<a href="#">
												<strong>Document-Types</strong>
											</a>
										</li>										
										@foreach($mainSearchFilters['documentTypes'] as $key => $value)	
										<li data-field-query="field[documentType][]={{$key}}" data-value="{{$key}}">
											<a href="#" class="filterOption">
												<i class="fa fa-circle-o fa-fw"></i>
												<span class="badge pull-right">{{ $value['count'] }}</span>
												{{ $key }}
											</a>
										</li>
										@endforeach
									</ul>
									@endif

									<div class='specificFilters'>

									</div>

									<div class='facetedSearchForm'>
										{{ Form::open(array('action' => 'FilesController@anyBatch')) }}
											<input type="text" name="selection" value="" class="hidden" />
											<button type="submit" class="btn btn-info createBatchButton" style="width:100%">Create Batch</button>
										{{ Form::close() }}
									</div>
								</div>

    							<div class='col-xs-8 facetedSearchResults'>

									<ul class="nav nav-pills documentTypesNav">
										<li class="active" id="all_nav">
											<a href="#all_tab" data-toggle="tab">
												Aggregated
											</a>
										</li>

										@foreach($mainSearchFilters['documentTypes'] as $key => $value)
										<li class="hidden" id="{{$key}}_nav">
											<a href="#{{$key}}_tab" data-toggle="tab">
												{{$key}}
											</a>
										</li>
										@endforeach
									</ul>    								

									<div class="tab-content documentTypesTabs">
										<div class="tab-pane active" id="all_tab">
											<div class='table-responsive'>
												<table class='table table-striped'>
													<thead>
														<tr>
															<th data-field-query="only[0]=_id">ID</th>
															<th data-field-query="only[2]=format">format</th>
															<th data-field-query="only[3]=domain">Domain</th>
															<th data-field-query="only[4]=documentType">Document Type</th>
														</tr>
													</thead>
													<tbody>
												    </tbody>
										    	</table>
											</div>
										</div>

										@if(isset($mainSearchFilters['documentTypes']['job']))
											@include('files.search.layouts.job-searchtemplate')
										@endif

										@if(isset($mainSearchFilters['documentTypes']['jobconf']))
											@include('files.search.layouts.jobconf-searchtemplate')
										@endif

										@if(isset($mainSearchFilters['documentTypes']['twrex']))
											@include('files.search.layouts.twrex-searchtemplate')
										@endif

										@if(isset($mainSearchFilters['documentTypes']['twrex-structured-sentence']))
											@include('files.search.layouts.twrex-structured-sentence-searchtemplate')
										@endif

									</div>
    							</div>
    						</div>
						</div>
					</div>
				</div>
				<!-- STOP search_content --> 				
@stop

@section('end_javascript')
{{ javascript_include_tag('jquery.visible.js') }}
{{ javascript_include_tag('jquery.dataTables.min.js') }}
{{ javascript_include_tag('dataTables.colVis.js') }}
{{ javascript_include_tag('jquery.dataTables.bootstrap.js') }}
{{ javascript_include_tag('dataTables.fnReloadAjax.js') }}

<script>

$('document').ready(function(){

var baseApiURL = '{{ URL::to("api/v2?datatables=true&noCache") }}';
var dataTableObjects = {};
var selectedRows = [];

$('.facetedSearchResults').css({ "min-height" : "1000px "});

$('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
	$('.specificFilters .datatable_options').prependTo($('.tab-pane.active'));
});

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	if($(this).attr('href') == "#all_tab")
		return false;

	if($(this).closest('li').hasClass('active')){
		$('.specificFilters').empty().append($('.tab-pane.active .datatable_options'));
		getTabResults($(this).attr('href'));
	}
});

$('body').on('click', '.specificFilters button', function(){
	$(this).siblings().removeClass('btn-success active').addClass('btn-info');
	$(this).removeClass('btn-info').addClass('btn-success active');

	var keyTab = '#' + $('.tab-pane.active').attr('id');
	selectedRows[keyTab] = [];
	getTabResults(keyTab);
});

$('.filterOption').on('click', function(){
	updateFilters($(this));
	activateNavAndTab($(this));
	getAllTabResults();
});


$('.createBatchButton').on('click', function(){
	var keyTab = '#' + $('.tab-pane.active').attr('id');

	if(typeof selectedRows[keyTab] == 'undefined' || selectedRows[keyTab].length < 1){
		event.preventDefault();
		alert('Please make a selection first');
	} else {
		var selection = JSON.stringify(selectedRows[keyTab]);
		$(this).siblings("[name='selection']").attr('value', selection);
	}
});

function activateNavAndTab(filterOption){
	var key = filterOption.closest('li').attr('data-value');
	
	if(!$('.documentTypesTabs').find('#' + key + '_tab').length)
		return;

	if(filterOption.closest('li').hasClass('active'))
	{
		$('.documentTypesNav').find('#' + key + '_nav').removeClass('hidden');
	}
	else
	{
		$('#all_nav a').click();
		$('.documentTypesNav').find('#' + key + '_nav').addClass('hidden');
	}
}

function getTabResults(keyTab){
	var tabFieldsQuery = '';

	// alert(keyTab);

	$('.specificFilters .datatable_options').find("[data-field-query]").each(function() {
		if($(this).hasClass('active')){
			tabFieldsQuery += "&" + $(this).attr('data-field-query');
		}
	});

	$(keyTab + ' .datatable_content').find("[data-field-query]").each(function() {
		tabFieldsQuery += "&" + $(this).attr('data-field-query');
	});

	console.clear();
	console.log(tabFieldsQuery);	

	if(!$(keyTab).find('th.db_checkbox').length){
		$(keyTab).find('.datatable_content thead tr').prepend('<th data-key="_id" class="db_checkbox">ID</th>');
	}

	var aoColumns = [];
	$(keyTab).find("[data-key]").each(function() {
		aoColumns.push({
			mData : $(this).attr('data-key')
	    });
	});	
	// console.log(aoColumns);

	if (typeof dataTableObjects[keyTab] == 'undefined') {
		dataTableObjects[keyTab] = $('.documentTypesTabs ' + keyTab + ' .datatable_content').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"bFilter": false,
			"bAutoWidth": false,
			"sDom" : '<"top"i>r<"bottom"flp><"clear">',
			// "aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, 'All']],
			"sPaginationType": "bootstrap",	        
			"sAjaxSource": baseApiURL + tabFieldsQuery,
			"sAjaxDataProp": "aaData",
			"aoColumns": aoColumns,
			"aoColumnDefs": [
			    {
			    	"aTargets": [ 0 ],
			    	bSortable: false,
				    "mRender": function ( data, type, full ) {
				        var returndata;

				        if(jQuery.inArray(data, selectedRows[keyTab]) == -1)
				        {
				        	returndata = '<input type="checkbox" id="'+data+'" name="rowchk" value="'+data+'">';
				        }
				        else
				        {
				        	returndata = '<input type="checkbox" id="'+data+'" name="rowchk" value="'+data+'" checked="checked">';
				        }

				        return returndata;
					}
				}
			],
			"fnDrawCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
	            $("input[name=rowchk]").click(function(event){
	            	var val = $(this).attr('value');

		            if($(this).prop("checked")){
		            	if (typeof selectedRows[keyTab] == 'undefined') {
		            		selectedRows[keyTab] = [];
		            	}		            	
						
						selectedRows[keyTab].push(val);
		            }
		            else
		            {
						selectedRows[keyTab] = $.grep(selectedRows[keyTab], function(value) {
						  return value != val;
						});
		            }

		            console.log(selectedRows);
           		});
	        }     
		});
	}
	else
	{	
		dataTableObjects[keyTab].fnReloadAjax(baseApiURL + tabFieldsQuery);
	}	
}

function getAllTabResults(){
	var allTabQuery = '';

	$('.filterOption').each(function() {
		if($(this).closest('li').hasClass('active'))
		{
			allTabQuery += "&" + $(this).closest('li').attr('data-field-query');
		}
	});

	var onlyFields = '';

	$('.documentTypesTabs #all th').each(function() {
		onlyFields += "&" + $(this).attr('data-field-query');
	});	

	// alert(onlyFields);

	console.log(allTabQuery);

	if (typeof dataTableObjects['all'] == 'undefined') {
		dataTableObjects['all'] = $('.documentTypesTabs #all_tab table').dataTable({
	        "bProcessing": true,
	        "bServerSide": true,
	        "bFilter": false,
	        "sDom" : '<"top"i>r<"bottom"flp><"clear">',
		    // "aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, 'All']],
		    "sPaginationType": "bootstrap",	        
	        "sAjaxSource": baseApiURL + allTabQuery + onlyFields,
	        "sAjaxDataProp": "aaData",
			"aoColumns": [
			  { "mData": "_id" },
			  { "mData": "format" },
			  { "mData": "domain" },
			  { "mData": "documentType" }
			]
			});
	}
	else
	{	
		dataTableObjects['all'].fnReloadAjax('{{ URL::to("api/v2?datatables=true&limit=1000") }}' + allTabQuery + onlyFields);
	}
}

function updateFilters(filterOption){
	if(filterOption.closest('li').hasClass('active')){
		filterOption.closest('li').removeClass('active');
		filterOption.children('i').removeClass('fa-check-o').addClass('fa-circle-o');
	} else {
		filterOption.closest('li').addClass('active');
		filterOption.children('i').removeClass('fa-circle-o').addClass('fa-check-circle-o');
	}	
}

});

</script>

@stop