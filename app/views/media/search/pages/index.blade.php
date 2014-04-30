@extends('layouts.default_new')

@section('container', 'full-container')

@section('head')
{{ stylesheet_link_tag('bootstrap-select.css') }}
{{ stylesheet_link_tag('bootstrap-dropdown-checkbox.css') }}
{{ stylesheet_link_tag('bootstrap.datepicker3.css') }}

<style>
.container {
	-webkit-transform:translatez(0);-webkit-backface-visibility:hidden;-webkit-perspective:1000;
}
</style>
@stop

@section('content')
<!-- START search_content --> 
<div class="col-xs-12">
	<div class='maincolumn CW_box_style'>
@include('layouts.flashdata')						
@include('media.layouts.nav_new')
		<div class='tab'>
			<div class='row'>
				<div class='col-xs-3'>
					<div class='facetedSearchFilters'>
						<div class="panel-group searchAccordion" id="accordion_1">
							@if(isset($mainSearchFilters['formats']))
							<div class="panel cw_formats">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" href="#collapseFormats">
											<i class="fa fa-fw fa-bars"></i>
											Media Formats
										</a>
									</h4>
								</div>
								<div id="collapseFormats" class="panel-collapse collapse in">
									<ul class="nav nav-pills nav-stacked">
										@foreach($mainSearchFilters['formats'] as $key => $value)	
										<li data-query-key="match[format][]" data-query-value="{{$key}}">
											<a href="#" class="filterOption">
												<i class="fa fa-circle-o fa-fw"></i>
												<span class="badge pull-right">{{ $value['count'] }}</span>
												{{ $key }}
											</a>
										</li>
										@endforeach
									</ul>	
								</div>
							</div>
							@endif
							@if(isset($mainSearchFilters['domains']))
							<div class="panel cw_domains">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion_2" href="#collapseDomains">
											<i class="fa fa-fw fa-folder"></i>
											Media Domains
										</a>
									</h4>
								</div>
								<div id="collapseDomains" class="panel-collapse collapse in">
									<ul class="nav nav-pills nav-stacked">
										@foreach($mainSearchFilters['domains'] as $key => $value)	
										<li data-query-key="match[format][]" data-query-value="{{$key}}">
											<a href="#" class="filterOption">
												<i class="fa fa-circle-o fa-fw"></i>
												<span class="badge pull-right">{{ $value['count'] }}</span>
												{{ $key }}
											</a>
										</li>
										@endforeach
									</ul>	
								</div>
							</div>
							@endif
							@if(isset($mainSearchFilters['documentTypes']))
							<div class="panel cw_documentTypes">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion_3" href="#collapseDocumentTypes">
											<i class="fa fa-fw fa-file"></i>
											Media Types
										</a>
									</h4>
								</div>
								<div id="collapseDocumentTypes" class="panel-collapse collapse in">
									<ul class="nav nav-pills nav-stacked">
										@foreach($mainSearchFilters['documentTypes'] as $key => $value)	
										<li data-query-key="match[format][]" data-query-value="{{$key}}">
											<a href="#" id='{{$key}}' class="filterOption">
												<i class="fa fa-circle-o fa-fw"></i>
												<span class="badge pull-right">{{ $value['count'] }}</span>
												{{ $key }}
											</a>
										</li>
										@endforeach
									</ul>	
								</div>
							</div>
							@endif
							<div class="panel cw_generalFilters">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" href="#collapseGeneralFilters">
											<i class="fa fa-fw fa-filter"></i>
											General Filters
										</a>
									</h4>
								</div>
								<div id="collapseGeneralFilters" class="panel-collapse collapse">
									<table class='filterSwitches'>
										<tbody>
											<tr>
												<td>
													Created At
												</td>
												<td class="input-daterange">
								    				<input type="text" class="input-sm form-control" name="start" data-query-key="match[created_at]" data-query-operator=">=" style="width:49% !important; float:left;" placeholder="Start Date" />
								    				<input type="text" class="input-sm form-control" name="end" data-query-key="match[created_at]" data-query-operator="<=" style="width:49% !important; float:right;" placeholder="End Date" />
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="panel cw_specificFilters hidden">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" href="#collapseSpecificFilters">
											<i class="fa fa-fw fa-filter"></i>
											Specific Filters
										</a>
									</h4>
								</div>
								<div id="collapseSpecificFilters" class="panel-collapse collapse specificFilters">
								</div>
							</div>
						</div>
	<!-- 					<div class='facetedSearchForm'>
							{{ Form::open(array('action' => 'MediaController@anyBatch')) }}
								<input type="text" name="selection" value="" class="hidden" />
								<button type="submit" class="btn btn-info createBatchButton" style="width:100%">Save selection</button>
							{{ Form::close() }}
						</div> -->
					</div>
				</div>	

				<div class='col-xs-9 facetedSearchResults'>
					<ul class="nav nav-tabs documentTypesNav">



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
						<div class='row'>
							<div class='searchOptions col-xs-12'>
								<select name="search_limit" class="selectpicker pull-left">
									<option value="10">10 Records per page</option>
									<option value="25">25 Records per page</option>
									<option value="50">50 Records per page</option>
									<option value="100">100 Records per page</option>
								</select>
								<div class='visibleColumns pull-left'>
								</div>
							</div>
						</div>
						<div class='row'>
							<div class='col-xs-12 cw_pagination'>
							</div>								
						</div>
						    <table class="table table-striped">
						        <thead>
							        <tr>
							            <th>Checkbox</th>
							            <th class="sorting" data-query-key="orderBy[_id]">ID</th>
							            <th class="sorting" data-query-key="orderBy[title]">Title</th>
							            <th class="sorting" data-query-key="orderBy[domain]">Domain</th>
							        </tr>
									<tr class="inputFilters">
										<td>
											<input type="checkbox" class="checkAll" />
										</td>
										<td>
											<input type='text' data-query-key="match[_id]" data-query-operator="[like]" />
										</td>
										<td>
											<input type='text' data-query-key="match[title]" data-query-operator="[like]" />
										</td>
										<td>
											<input type='text' data-query-key="match[domain]" data-query-operator="[like]" />
										</td>
									</tr>											        
						        </thead>
					        <tbody class='results'>											
								<script class='template' type="text/x-handlebars-template">
							        @{{#each documents}}
							        <tr>
							            <td>Checkbox</td>
							            <td>@{{ this._id }}</td>
							            <td>@{{ this.title }}</td>
							            <td>@{{ this.domain }}</td>
							        </tr>
							        @{{/each}}
								</script>
					        </tbody>
					    </table>											
					</div>
						@if(isset($mainSearchFilters['documentTypes']['job']))
							@include('media.search.layouts.hb-job')
						@endif

						@if(isset($mainSearchFilters['documentTypes']['crowdagents']))
							@include('media.search.layouts.hb-crowdagents')
						@endif

						@if(isset($mainSearchFilters['documentTypes']['twrex']))
							@include('media.search.layouts.twrex')
						@endif

						@if(isset($mainSearchFilters['documentTypes']['twrex-structured-sentence']))
							@include('media.search.layouts.hb-twrex-structured-sentence')
						@endif

						@if(isset($mainSearchFilters['documentTypes']['fullvideo']))
							@include('media.search.layouts.hb-fullvideo')
						@endif

						@if(isset($mainSearchFilters['documentTypes']['twrex-structured-sentence']))
							@include('media.search.layouts.hb-painting')
						@endif

						<script class='searchStatsTemplate' type="text/x-handlebars-template">
							<button class="btn btn-sm btn-info disabled resultsInfo" style="font-weight:bold; opacity:0.8 !important;">Showing @{{ count.from }} to @{{ count.to }} of @{{ count.total}} entries</button>
						</script>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- STOP search_content --> 				
@stop

@section('end_javascript')
{{ javascript_include_tag('handlebarsjs-2.0.js') }}
{{ javascript_include_tag('handlebars.swag.js') }}
{{ javascript_include_tag('bootstrap-select.js') }}
{{ javascript_include_tag('bootstrap-datepicker.js') }}

<script>
$('document').ready(function(){


Swag.registerHelpers();

$('.selectpicker').selectpicker();
var xhr;
var selectedRows = [];
var templates = {};
var lastQueryResult;

// $('.maincolumn').css({"min-height:" : ($(window).height()) +  "px"});

// $(window).scroll(function(){
//    if ($(window).scrollTop() > 125){
//     $(".facetedSearchFilters").css({"margin-top": ($(window).scrollTop()) - 125 + "px"});
//    } else {
//     $(".facetedSearchFilters").css({"margin-top": 0 + "px"});
//    }
// });

var delay = (function(){
	var timer = 0;
	return function(callback, ms){
		clearTimeout (timer);
		timer = setTimeout(callback, ms);
	};
})();


$('body').on('keyup', '.inputFilters input', function(){
	var inputFilter = $(this);

	delay(function(){
		var activeTabKey = getActiveTabKey();
		selectedRows[activeTabKey] = [];		
		inputFilter.attr('data-query-value', inputFilter.val());

		if(inputFilter.val() == "")
			inputFilter.removeAttr('data-query-value');

	 	getResults();
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

$('body').on('click', 'input[name=rowchk]', function(event){
	var val = $(this).attr('value');
	var activeTabKey = getActiveTabKey();

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

    console.dir(selectedRows[activeTabKey]);
});

$('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
	$('.specificFilters .specificFilterOptions').prependTo($('.tab-pane.active'));
	$('.cw_specificFilters').addClass('hidden');
});

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	var activeTabKey = getActiveTabKey();

	if($(this).attr('href') == "#all_tab")
		return false;

	if($(this).closest('li').hasClass('active')){
		$('.specificFilters').empty().append($('.tab-pane.active .specificFilterOptions'));
		$('.cw_specificFilters').removeClass('hidden');
		if(templates[activeTabKey] == undefined)
		{		
			getResults();
		}
	}
});

$('body').on('click', '.specificFilters button', function(){
	$(this).siblings().removeClass('btn-success active').addClass('btn-info');
	$(this).removeClass('btn-info').addClass('btn-success active');

	var activeTabKey = getActiveTabKey();
	selectedRows[activeTabKey] = [];
	getResults();
});

$('body').on('click', '.createBatchButton', function(){
	var activeTabKey = getActiveTabKey();

	if(typeof selectedRows[activeTabKey] == 'undefined' || selectedRows[activeTabKey].length < 1){
		event.preventDefault();
		alert('Please make a selection first');
	} else {
		var searchQuery = JSON.stringify(lastQueryResult.searchQuery);

		// alert(searchQuery);

		var form = $('<form action="{{ URL::action("MediaController@anyBatch") }}" method="post"></form>');
		$('body').append(form);

		$.each(selectedRows[activeTabKey], function(index, value){
			form.append($('<input type="checkbox" name="selection[]" value="' + value + '" checked >'))
		});

		form.append($("<input type='checkbox' name='searchQuery[]' value='" + searchQuery + "' checked >"));
		

		form.submit();
	}
});

$('.filterOption').on('click', function(){
	updateFilters($(this));
	activateNavAndTab($(this));
	getResults();
});

$('.tab-pane').on('change', "[name='search_limit']", function(){
	getResults();
});

$('.tab-pane').on('click', "th", function(){
	if($(this).hasClass('sorting')){
		$(this).removeClass().addClass('sorting_asc');
	} else if($(this).hasClass('sorting_asc')){
		$(this).removeClass().addClass('sorting_desc');
	} else {
		$(this).removeClass().addClass('sorting');
	}

	getResults();
});

function getActiveTabKey(){
	return '#' + $('.tab-pane.active').attr('id');
}

function getSearchLimitValue(){
	return $(getActiveTabKey()).find("[name='search_limit']").val();
}

function activateNavAndTab(filterOption){
	var key = filterOption.closest('li').attr('data-query-value');

	if(!$('.documentTypesTabs').find('#' + key + '_tab').length)
		return;

	if(filterOption.closest('li').hasClass('active'))
	{
		$('.documentTypesNav').find('#' + key + '_nav').removeClass('hidden');
		$('.documentTypesNav').find('#' + key + '_nav a').click();
	}
	else
	{
		$('#all_nav a').click();
		$('.documentTypesNav').find('#' + key + '_nav').addClass('hidden');
	}
}

$('body').on('click', 'ul.pagination a', function(e) {
	e.preventDefault();
	getResults($(this).attr('href') + "&noCache");
});

$('body').on('click', '.toCSV', function(e) {
	e.preventDefault();
	location.href = "{{ URL::to("api/search?tocsv&limit=100000") }}" + getTabFieldsQuery();
});

$('body').tooltip({
    selector: '[data-toggle=tooltip]',
    html: true
});

$('.input-daterange').datepicker({
	format: "yyyy-mm-dd",
	clearBtn: true,
    orientation: "top right"
});

$('.input-daterange input').on('changeDate', function(e) {
	// alert($(this).val());
	var date = $(this).val();
	console.log('test' + date);

	if(date == "") {
		$(this).removeAttr('data-query-value');					
	} else {
		$(this).attr('data-query-value', date);
	}

	getResults();
});

function getTabFieldsQuery(){
	var activeTabKey = getActiveTabKey();
	var tabFieldsQuery = '';

	if(activeTabKey == "#all_tab"){
		$('.filterOption').each(function() {
			if($(this).closest('li').hasClass('active'))
			{
				tabFieldsQuery += "&" + $(this).closest('li').attr('data-query-key') + "=" + $(this).closest('li').attr('data-query-value');
			}
		});	
	}

	$('.cw_generalFilters, .specificFilters, ' + activeTabKey).find("[data-query-key]").each(function() {
		if($(this).hasClass('btn') && !$(this).hasClass('active')){
			return;
		}

		if($(this).is('[data-query-value]')){
			if($(this).is('[data-query-operator]')){
				var operator = "[" + encodeURIComponent($(this).attr('data-query-operator')) + "]=";
			} else {
				var operator = "=";
			}

			tabFieldsQuery += "&" + $(this).attr('data-query-key') + operator + $(this).attr('data-query-value');
		}

		if($(this).hasClass('sorting_asc')){
			tabFieldsQuery += "&" + $(this).attr('data-query-key') + "=asc";
		} else if($(this).hasClass('sorting_desc')){
			tabFieldsQuery += "&" + $(this).attr('data-query-key') + "=desc";
		}
	});

	// console.log(tabFieldsQuery);

	return tabFieldsQuery;
}

function getResults(baseApiURL){
	if(baseApiURL == undefined)
	{
		var baseApiURL = '{{ URL::to("api/search?noCache") }}';
	}

	var activeTabKey = getActiveTabKey();
	var searchLimitQuery = "&limit=" + getSearchLimitValue();
	var tabFieldsQuery = getTabFieldsQuery();

	if(tabFieldsQuery == '')
	{
		$(activeTabKey).find('.results').empty();
		$(activeTabKey).find('.cw_pagination').empty();
		return false;
	}

	// console.log(tabFieldsQuery);

	$(activeTabKey + ' .resultsInfo').addClass('btn-danger').text('Processing...');

	abortAjax(xhr);

	xhr = $.getJSON(baseApiURL + tabFieldsQuery + searchLimitQuery, function(data) {
		// console.log(data);

		lastQueryResult = data;

		if(templates[activeTabKey] == undefined)
		{
			templates[activeTabKey] = $(activeTabKey).find('.template').html();
		}

		var template = Handlebars.compile(templates[activeTabKey]);
		var html = template(data);
		$(activeTabKey).find('.cw_pagination').empty().prepend($(data.pagination));
		$(activeTabKey).find('.cw_pagination').find('.pagination').addClass('pagination-sm');
		$(activeTabKey).find('.results').empty().append(html);

		var searchStats = Handlebars.compile($('.searchStatsTemplate').html());
		var searchStats = searchStats(data);
		$(activeTabKey).find('.searchStats').empty().append(searchStats);

		$(activeTabKey + ' .hb_popover').popover({
			placement : "left",
			html : true,
			trigger : "hover",
			title : "default",
			content : function(){ return $(this).find('.hidden').html() },
 			container: 'body',
            template: '<div class="popover popover-medium"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'			
		});

		if(templates[activeTabKey + 'date'] == undefined) 
		{
			templates[activeTabKey + 'date'] = activeTabKey + 'date';
		} else {
			// alert(templates[activeTabKey + 'date']);
		}

		if($(activeTabKey).find(".vbColumns").length){
			$(activeTabKey).find("[data-vbSelector]").each(function() {
				if($(this).attr('data-vb') == "show")
				{
					$(this).find('.fa').remove();
					$(this).prepend('<i class="fa fa-check-circle-o fa-fw"></i>');
				}
				else
				{
					$(this).find('.fa').remove();
					$(this).prepend('<i class="fa fa-circle-o fa-fw"></i>');
				}
			});

			$(activeTabKey).find("[data-vbSelector]").off().on("click", function() {
				if($(this).attr('data-vb') == "show")
				{
					$(this).attr('data-vb', 'hide');
					$(this).find('.fa').remove();
					$(this).prepend('<i class="fa fa-circle-o fa-fw"></i>');
				}
				else
				{
					$(this).attr('data-vb', 'show');					
					$(this).find('.fa').remove();
					$(this).prepend('<i class="fa fa-check-circle-o fa-fw"></i>');
				}

				visibleColumns();
			});

			visibleColumns();
		}

		// console.dir(selectedRows[activeTabKey]);
		// console.log('starting search');

		$(activeTabKey + ' .checkAll').removeAttr('checked');

        $("input[name=rowchk]").each(function(){
        	var val = $(this).attr('value');

	        if(jQuery.inArray(val, selectedRows[activeTabKey]) != -1) {
				if(!$(this).is(':checked')) {
					$(this).prop("checked", true);
				}
	        }
        });
	});		
}

function abortAjax(xhr) {
	if(xhr && xhr.readystate != 4){
		xhr.abort();
	}
}

function visibleColumns(){
	var activeTabKey = getActiveTabKey();

	$(activeTabKey).find("[data-vbSelector]").each(function() {

		var vbSelector = $(activeTabKey).find($("[" + "data-vbIdentifier='" + $(this).attr('data-vbSelector') + "']"));

		if($(this).attr('data-vb') == "show")
		{
			vbSelector.removeClass('hidden');
		}
		else
		{
			vbSelector.addClass('hidden');
		}
	});	
}

function updateFilters(filterOption){
	if(filterOption.closest('li').hasClass('active')){
		filterOption.closest('li').removeClass('active');
		filterOption.children('i').removeClass('fa-check-circle-o').addClass('fa-circle-o');
	} else {
		filterOption.closest('li').addClass('active');
		filterOption.children('i').removeClass('fa-circle-o').addClass('fa-check-circle-o');
	}	
}


});

/*
$('body').on('show.bs.modal', '.modal', function(){
	if(baseApiURL == undefined)
	{
		var baseApiURL = '{{ URL::to("api/search?noCache") }}';
	}

	var activeTabKey = getActiveTabKey();
	var modal = $(this);
	var id = modal.attr('data-entityid');
	var reqType = $(this).closest('td').attr('id');

	if (reqType.indexOf("keyframe") > -1) {
		$.getJSON(baseApiURL + "&only[]=content.storage_url&only[]=content.timestamp&match[documentType]=keyframe&match[parents][]=" + id, function(data) {
		console.log(data);
		var template = Handlebars.compile($(activeTabKey).find('.modalKeyFrameTemplate .template').html());
		var html = template(data);
		modal.find('.modal-body').empty().append(html);
	});
	}
	if (reqType.indexOf("segment") > -1) {
		$.getJSON(baseApiURL + "&only[]=content.storage_url&only[]=content.duration&match[documentType]=videosegment&only[]=content.start_time&only[]=content.end_time&match[parents][]=" + id, function(data) {
		console.log(data);
		var template = Handlebars.compile($(activeTabKey).find('.modalVideoSegmentTemplate .template').html());
		var html = template(data);
		modal.find('.modal-body').empty().append(html);
		});
	}

});
*/

$('body').on('click', '.testModal', function(){
	if(baseApiURL == undefined)
	{
		var baseApiURL = '{{ URL::to("api/search?noCache") }}';
	}

	var activeTabKey =  '#' + $('.tab-pane.active').attr('id');
	var modalTarget = $(this).attr('data-target');
	//alert(modalTarget);

	var query = $(this).attr('data-modal-query');

		$.getJSON(baseApiURL + query, function(data) {
		console.log(data);

		var template = Handlebars.compile($(activeTabKey).find(modalTarget + ' .template').html());

		var html = template(data);
		
		$('#activeTabModal').remove();
		$('body').append(html);
		
		$('#activeTabModal').modal();

		});
});



</script>

@stop
