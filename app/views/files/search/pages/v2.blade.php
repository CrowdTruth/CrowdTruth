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
												<strong><i class="fa fa-fw fa-bars"></i>Formats</strong>
											</a>
										</li>										
										@foreach($mainSearchFilters['formats'] as $key => $value)	
										<li data-query-key="field[format][]" data-query-value="{{$key}}">
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
												<strong><i class="fa fa-fw fa-folder"></i>Domains</strong>
											</a>
										</li>										
										@foreach($mainSearchFilters['domains'] as $key => $value)	
										<li data-query-key="field[domain][]" data-query-value="{{$key}}">
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
												<strong><i class="fa fa-fw fa-file"></i>Document-Types</strong>
											</a>
										</li>										
										@foreach($mainSearchFilters['documentTypes'] as $key => $value)	
										<li data-query-key="field[documentType][]" data-query-value="{{$key}}">
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

									<ul class="nav nav-tabs documentTypesNav">
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
															<th data-query-key="only[0]" data-query-value="_id">ID</th>
															<th data-query-key="only[1]" data-query-value="format">format</th>
															<th data-query-key="only[2]" data-query-value="domain">Domain</th>
															<th data-query-key="only[3]" data-query-value="documentType">Document Type</th>
														</tr>
													</thead>
													<tbody>
												    </tbody>
										    	</table>
											</div>
										</div>

										@if(isset($mainSearchFilters['documentTypes']['job']))
											@include('files.search.layouts.job')
										@endif

										@if(isset($mainSearchFilters['documentTypes']['jobconf']))
											@include('files.search.layouts.jobconf')
										@endif

										@if(isset($mainSearchFilters['documentTypes']['twrex']))
											@include('files.search.layouts.twrex')
										@endif

										@if(isset($mainSearchFilters['documentTypes']['relex-structured-sentence']))
											@include('files.search.layouts.relex-structured-sentence')
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
{{ javascript_include_tag('jquery.dataTables.min.js') }}
{{ javascript_include_tag('dataTables.colVis.js') }}
{{ javascript_include_tag('jquery.dataTables.bootstrap.js') }}
{{ javascript_include_tag('dataTables.fnReloadAjax.js') }}

<script>
$('document').ready(function(){

var baseApiURL = '{{ URL::to("api/v1?datatables=true&noCache") }}';
var dataTableObjects = {};
var selectedRows = [];

// $('.facetedSearchResults').css({ "min-height" : "100% "});

var delay = (function(){
  var timer = 0;
  return function(callback, ms){
  clearTimeout (timer);
  timer = setTimeout(callback, ms);
 };
})();

$('.inputFilters input').keyup(function() {
	var inputFilter = $(this);

	delay(function(){
		var activeTabKey = getActiveTabKey();
		selectedRows[activeTabKey] = [];		
		inputFilter.attr('data-query-value', inputFilter.val());

		if(inputFilter.val() == "")
			inputFilter.removeAttr('data-query-value');

	 	getTabResults(getActiveTabKey());
	}, 300);
});

$('body').on('click', '.checkAll', function(){
	var activeTabKey = getActiveTabKey();

	if (! $(this).is(':checked')) {
		$(activeTabKey + ' input[name=rowchk]').each(function(){
			if ($(this).is(':checked')) {
				$(this).click();
			}
		});
	} else {
		$(activeTabKey + ' input[name=rowchk]').each(function(){
			if (!$(this).is(':checked')) {
				$(this).click();
			}
		});
	}

});

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

	var activeTabKey = getActiveTabKey();
	selectedRows[activeTabKey] = [];
	getTabResults(activeTabKey);
});

$('.filterOption').on('click', function(){
	updateFilters($(this));
	activateNavAndTab($(this));
	getAllTabResults();
});


$('.createBatchButton').on('click', function(){
	var activeTabKey = getActiveTabKey();

	if(typeof selectedRows[activeTabKey] == 'undefined' || selectedRows[activeTabKey].length < 1){
		event.preventDefault();
		alert('Please make a selection first');
	} else {
		var selection = JSON.stringify(selectedRows[activeTabKey]);
		$(this).siblings("[name='selection']").attr('value', selection);
	}
});

function getActiveTabKey(){
	var activeTabKey = '#' + $('.tab-pane.active').attr('id');
	return activeTabKey;
}

function activateNavAndTab(filterOption){
	var key = filterOption.closest('li').attr('data-query-value');

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

function getTabResults(activeTabKey){
	var tabFieldsQuery = '';

	// alert(activeTabKey);

	$('.specificFilters .datatable_options, ' + activeTabKey + ' .datatable_content').find("[data-query-key]").each(function() {

		if($(this).hasClass('btn') && !$(this).hasClass('active'))
			return true;

		if($(this).is('[data-query-value]')){
			if($(this).is('[data-query-operator]')){
				var operator = $(this).attr('data-query-operator') + "=";
			} else {
				var operator = "=";
			}

			tabFieldsQuery += "&" + $(this).attr('data-query-key') + operator + $(this).attr('data-query-value');
		}
	});

	// $(activeTabKey + ' .datatable_content').find("[data-query-key]").each(function() {
	// 	tabFieldsQuery += "&" + $(this).attr('data-query-key');
	// });

	// alert('done');
	// console.clear();
	// console.log(tabFieldsQuery);	

	if(!$(activeTabKey).find('th.db_checkbox').length){
		$(activeTabKey).find('.datatable_content thead tr:first').prepend('<th data-column="_id" class="db_checkbox" style="font-size:0;">Checkbox</th>');
	}

	var aoColumns = [];
	$(activeTabKey).find("[data-column]").each(function() {
		if($(this).is('[data-bvisible]')){
			var bVisible = false;
		} else {
			var bVisible = true;
		}

		aoColumns.push({
			mData : $(this).attr('data-column'),
			bVisible : bVisible
	    });
	});	
	// console.log(aoColumns);

	if (typeof dataTableObjects[activeTabKey] == 'undefined') {
		dataTableObjects[activeTabKey] = $('.documentTypesTabs ' + activeTabKey + ' .datatable_content').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"bFilter": false,
			"bAutoWidth": false,
			"sDom" : '<"top"Ci>r<"bottom"flp><"clear">',
			// "aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, 'All']],
			"sPaginationType": "bootstrap",	        
			"sAjaxSource": baseApiURL + tabFieldsQuery,
			"sAjaxDataProp": "aaData",
			"aoColumns": aoColumns,
			// "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
			"bSortCellsTop": true,
			"aoColumnDefs": [
			    {
			    	"aTargets": [ 0 ],
			    	bSortable: false,
				    "mRender": function ( data, type, full ) {
				        var returndata;

				        if(jQuery.inArray(data, selectedRows[activeTabKey]) == -1)
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
				$(activeTabKey + ' .checkAll').removeAttr('checked');

	            $("input[name=rowchk]").click(function(event){
	            	var val = $(this).attr('value');

		            if($(this).prop("checked")){
		            	if (typeof selectedRows[activeTabKey] == 'undefined') {
		            		selectedRows[activeTabKey] = [];
		            	}		            	
						
						selectedRows[activeTabKey].push(val);
		            }
		            else
		            {
						selectedRows[activeTabKey] = $.grep(selectedRows[activeTabKey], function(value) {
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
		dataTableObjects[activeTabKey].fnReloadAjax(baseApiURL + tabFieldsQuery);
	}	
}

function getAllTabResults(){
	var allTabQuery = '';

	$('.filterOption').each(function() {
		if($(this).closest('li').hasClass('active'))
		{
			allTabQuery += "&" + $(this).closest('li').attr('data-query-key') + "=" + $(this).closest('li').attr('data-query-value');
		}
	});

	var onlyFields = '';

	$('.documentTypesTabs #all th').each(function() {
		onlyFields += "&" + $(this).attr('data-query-key') + "=" + $(this).closest('li').attr('data-query-value');
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
		dataTableObjects['all'].fnReloadAjax('{{ URL::to("api/v1?datatables=true&limit=1000") }}' + allTabQuery + onlyFields);
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