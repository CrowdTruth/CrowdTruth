@extends('layouts.default')

@section('container', 'full-container')

@section('head')
{{ stylesheet_link_tag('bootstrap-select.css') }}
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
											<div class='searchOptions col-xs-3'>
												<select name="search_limit" class="selectpicker">
													<option value="10">10 Records per page</option>
													<option value="25">25 Records per page</option>
													<option value="50">50 Records per page</option>
													<option value="100">100 Records per page</option>
												</select>
											</div>
											<div class='cw_pagination col-xs-9'>
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

										@if(isset($mainSearchFilters['documentTypes']['twrex-structured-sentence']))
										<div class="tab-pane" id="twrex-structured-sentence_tab">
										<div class='row'>
											<div class='searchOptions col-xs-3'>
												<select name="search_limit" class="selectpicker">
													<option value="10">10 Records per page</option>
													<option value="25">25 Records per page</option>
													<option value="50">50 Records per page</option>
													<option value="100">100 Records per page</option>
												</select>
											</div>
											<div class='cw_pagination col-xs-9'>
											</div>
										</div>
										    <table class="table table-striped">
										        <thead data-query-key="field[documentType]" data-query-value="twrex-structured-sentence">
											        <tr>
											            <th>Checkbox</th>
											            <th class="sorting" data-query-key="orderBy[content.relation.noPrefix]">Relation</th>
											            <th class="sorting" data-query-key="orderBy[content.terms.first.text]">Term 1</th>
											            <th class="sorting" data-query-key="orderBy[content.terms.second.text]">Term 2</th>
											        </tr>
													<tr class="inputFilters">
														<td>
															<input type="checkbox" class="checkAll" />
														</td>
														<td>
															<input type='text' data-query-key="field[content.relation.noPrefix]" data-query-operator="[like]" />
														</td>
														<td>
															<input type='text' data-query-key="field[content.terms.first.text]" data-query-operator="[like]" />
														</td>
														<td>
															<input type='text' data-query-key="field[content.terms.second.text]" data-query-operator="[like]" />
														</td>
													</tr>											        
										        </thead>
									        <tbody class='results'>											
												<script class='template' type="text/x-handlebars-template">
											        @{{#each documents}}
											        <tr>
											            <td>Checkbox</td>
											            <td>@{{ this.content.relation.noPrefix }}</td>
											            <td>@{{ this.content.terms.first.text }}</td>
											            <td>@{{ this.content.terms.second.text }}</td>
											        </tr>
											        @{{/each}}
												</script>
									        </tbody>
									    </table>											
									</div>
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
{{ javascript_include_tag('bootstrap-select.js') }}
<script src="//cdn.jsdelivr.net/handlebarsjs/1.3.0/handlebars.js"></script>

<script>
$('document').ready(function(){
$('.selectpicker').selectpicker();
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
	getResults(activeTabKey);
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
	var activeTabKey = '#' + $('.tab-pane.active').attr('id');
	return activeTabKey;
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

$('body').on('click', 'ul.pagination a', function() {
	event.preventDefault();
	getResults($(this).attr('href') + "&noCache");
});

function getResults(baseApiURL){
	if(baseApiURL == undefined)
	{
		var baseApiURL = '{{ URL::to("api/search?noCache") }}';
	}

	var activeTabKey = getActiveTabKey();
	var search_limit = "&limit=" + getSearchLimitValue();
	var tabFieldsQuery = '';

	if(activeTabKey == "#all_tab"){
		$('.filterOption').each(function() {
			if($(this).closest('li').hasClass('active'))
			{
				tabFieldsQuery += "&" + $(this).closest('li').attr('data-query-key') + "=" + $(this).closest('li').attr('data-query-value');
			}
		});	
	}

	$(activeTabKey).find("[data-query-key]").each(function() {
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

	if(tabFieldsQuery == '')
	{
		$(activeTabKey).find('.results').empty();
		$(activeTabKey).find('.cw_pagination').empty();
		return false;
	}

	console.log(tabFieldsQuery);

	$.getJSON(baseApiURL + tabFieldsQuery + search_limit, function(data) {
		console.log(data);

		if(templates[activeTabKey] == undefined)
		{
			templates[activeTabKey] = $(activeTabKey).find('.template').html();
		}

		var template = Handlebars.compile(templates[activeTabKey]);
		var html = template(data);
		$(activeTabKey).find('.cw_pagination').empty().prepend($(data.pagination));
		$(activeTabKey).find('.results').empty().append(html);
	});		
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