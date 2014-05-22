@extends('layouts.default')

@section('container', 'full-container')

@section('head')
{{ stylesheet_link_tag('bootstrap-select.css') }}
{{ stylesheet_link_tag('bootstrap-dropdown-checkbox.css') }}

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
															<input type='text' data-query-key="field[_id]" data-query-operator="[like]" />
														</td>
														<td>
															<input type='text' data-query-key="field[title]" data-query-operator="[like]" />
														</td>
														<td>
															<input type='text' data-query-key="field[domain]" data-query-operator="[like]" />
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
											@include('files.search.layouts.job')
										@endif

										@if(isset($mainSearchFilters['documentTypes']['jobconf']))
											@include('files.search.layouts.jobconf')
										@endif

										@if(isset($mainSearchFilters['documentTypes']['twrex']))
											@include('files.search.layouts.twrex')
										@endif

										@if(isset($mainSearchFilters['documentTypes']['relex-structured-sentence']))
											@include('files.search.layouts.hb-relex-structured-sentence')
										@endif

										@if(isset($mainSearchFilters['documentTypes']['relex-structured-sentence']))
											@include('files.search.layouts.hb-painting')
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
{{ javascript_include_tag('handlebarsjs-2.0.js') }}
{{ javascript_include_tag('handlebars.swag.js') }}
{{ javascript_include_tag('bootstrap-select.js') }}


<script>
$('document').ready(function(){

Swag.registerHelpers();

$('.selectpicker').selectpicker();
var xhr;
var selectedRows = [];
var templates = {};

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

$('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
	$('.specificFilters .datatable_options').prependTo($('.tab-pane.active'));
});

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	var activeTabKey = getActiveTabKey();

	if($(this).attr('href') == "#all_tab")
		return false;

	if($(this).closest('li').hasClass('active')){
		$('.specificFilters').empty().append($('.tab-pane.active .datatable_options'));
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
    selector: '[data-toggle=tooltip]'
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

	$('.specificFilters, ' + activeTabKey).find("[data-query-key]").each(function() {
		if($(this).hasClass('btn') && !$(this).hasClass('active')){
			return;
		}

		if($(this).is('[data-query-value]')){
			if($(this).is('[data-query-operator]')){
				var operator = $(this).attr('data-query-operator') + "=";
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

	console.log(tabFieldsQuery);

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

	console.log(tabFieldsQuery);

	abortAjax(xhr);

	xhr = $.getJSON(baseApiURL + tabFieldsQuery + searchLimitQuery, function(data) {
		// console.log(data);

		if(templates[activeTabKey] == undefined)
		{
			templates[activeTabKey] = $(activeTabKey).find('.template').html();
		}

		var template = Handlebars.compile(templates[activeTabKey]);
		var html = template(data);
		$(activeTabKey).find('.cw_pagination').empty().prepend($(data.pagination));
		$(activeTabKey).find('.results').empty().append(html);

		$('.hb_popover').popover({
			placement : "left",
			html : true,
			trigger : "hover",
			title : "default",
			content : function(){ return $(this).find('.hidden').html() },
 			container: 'body',
            template: '<div class="popover popover-medium"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'			
		});

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

</script>

@stop