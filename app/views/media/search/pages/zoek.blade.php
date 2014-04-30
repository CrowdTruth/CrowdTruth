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
				<div class='col-xs-12 searchOptions'>
					@if(isset($mainSearchFilters['formatsq']))
						<select name="format" data-query-key="match[format][]" class="selectpicker pull-left" title='Choose format(s)' data-width="auto" multiple>
							@foreach($mainSearchFilters['formats'] as $key => $value)	
							<option value="{{$key}}" data-subtext="{{ $value['count'] }} Items">{{ ucfirst($key) }}</option>
							@endforeach
						</select>	
					@endif
					@if(isset($mainSearchFilters['domainsq']))
						<select name="domain" data-query-key="match[domain][]" class="selectpicker pull-left" title='Choose domain(s)' data-width="auto" multiple>
							@foreach($mainSearchFilters['domains'] as $key => $value)	
							<option value="{{$key}}" data-subtext="{{ $value['count'] }} Items">{{ ucfirst($key) }}</option>
							@endforeach
						</select>	
					@endif
					@if(isset($mainSearchFilters['documentTypes']))
						<select name="documentType" data-query-key="match[documentType][]" class="selectpicker pull-left show-tick" title='Choose Document-Type(s)' data-width="auto" data-show-subtext="true">
							<optgroup label="Media-Type">
								@foreach($mainSearchFilters['documentTypes'] as $key => $value)	
								<option value="{{$key}}" class="select_{{$key}}" data-subtext="{{ $value['count'] }} Items">{{ ucfirst($key) }}</option>
								@endforeach
							</optgroup>
						</select>	
					@endif
					<div class='tabOptions pull-left'>

					</div>
					<div class='switchViews'>
						<button type="button" class="btn btn-default listViewButton hidden" style="margin-left:5px;">
							Switch to List View
						</button>						
						<button type="button" class="btn btn-default graphViewButton" style="margin-left:5px;">
							Switch to Graph View
						</button>						
					</div>
					<select name="search_limit" data-query-key="limit" class="selectpicker pull-right">
						<option value="10">10 Records per page</option>
						<option value="25">25 Records per page</option>
						<option value="50">50 Records per page</option>
						<option value="100">100 Records per page</option>
					</select>
				</div>
				<div class='col-xs-12'>
					<div class='searchStats pull-left'>
					</div>
					<div class='cw_pagination pull-right'>
					</div>
				</div>
				<div class='col-xs-12'>				
					<ul class="nav nav-tabs documentTypesNav hidden">
						<li id="all_nav">
							<a href="#all_tab" data-toggle="tab">
								All
							</a>
						</li>						
						@foreach($mainSearchFilters['documentTypes'] as $key => $value)
						<li id="{{$key}}_nav">
							<a href="#{{$key}}_tab" data-toggle="tab">
								{{$key}}
							</a>
						</li>
						@endforeach
					</ul>    								
					<div class="tab-content documentTypesTabs">
						<div class="tab-pane active" id="all_tab">
						    <table class="table table-striped">
						        <thead>
							        <tr>
							            <th>Checkbox</th>
							            <th class="sorting" data-query-key="orderBy[_id]">ID</th>
							            <th class="sorting" data-query-key="orderBy[format]">Format</th>
							            <th class="sorting" data-query-key="orderBy[domain]">Domain</th>
							            <th class="sorting" data-query-key="orderBy[documentType]">Document-Type</th>
							            <th class="sorting" data-query-key="orderBy[title]">Title</th>
							        </tr>
									<tr class="inputFilters">
										<td>
											<input type="checkbox" class="checkAll" />
										</td>
										<td>
											<input class="input-sm form-control" type='text' data-query-key="match[_id]" data-query-operator="like" />
										</td>
										<td>
											<input class="input-sm form-control" type='text' data-query-key="match[format]" data-query-operator="like" />
										</td>
										<td>
											<input class="input-sm form-control" type='text' data-query-key="match[domain]" data-query-operator="like" />
										</td>
										<td>
											<input class="input-sm form-control" type='text' data-query-key="match[documentType]" data-query-operator="like" />
										</td>
										<td>
											<input class="input-sm form-control" type='text' data-query-key="match[title]" data-query-operator="like" />
										</td>										
									</tr>											        
						        </thead>
						        <tbody class='results'>											
									<script class='template' type="text/x-handlebars-template">
								        @{{#each documents}}
								        <tr>
								            <td>Checkbox</td>
								            <td>@{{ this._id }}</td>
								            <td>@{{ this.format }}</td>
								            <td>@{{ this.domain }}</td>
								            <td>@{{ this.documentType }}</td>
								            <td>@{{ this.title }}</td>							            
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
						
						<div class='includeGraph hidden'>
                            <table>
                                <tr>
                                    <td>
                                        <div id="domain_div"></div>
                                    </td>
                                    <td>
                                        <div id="format_div"></div>
                                    </td>
                                    <td>
                                        <div id="user_div"></div>
                                    </td>
                                    <td>
                                        <div id="relation_div"></div>
                                    </td>
                                    <td>
                                        <div id="jobs_div"></div>
                                    </td>
                                </tr>
                            </table>
                            <table>
                                <tr >
                                    <td>
                                       <div id="unitsWordCountChart_div" ></div>
                                    </td>
                                </tr>
                            </table>
                            <table>
                                <tr >
                                    <td>
                                    <div id="unitsJobChart_div" ></div>
                                    </td>
                                </tr>
                            </table>
                            </table>
                            <table>
                                <tr>
                                    <td>
                                        <div id="workersPie_div"></div>
                                    </td>
                                    <td>
                                        <div id="workersBar_div"></div>
                                    </td>
                                </tr>
                            </table>
                            <table>
                                <tr>
                                    <td>
                                        <div id="jobsPie_div"></div>
                                    </td>
                                    <td>
                                        <div id="jobsBar_div"></div>
                                    </td>
                                </tr>
                            </table>
                            <table>
                                <tr>
                                    <td>
                                        <div id="annotationsPie_div"></div>
                                    </td>
                                    <td>
                                        <div id="annotationsBar_div"></div>
                                    </td>
                                </tr>
                            </table>
                            <div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title" id="myModalLabel"></h4>
                                        </div>
                                        <div class="modal-body">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

						<script class='searchStatsTemplate' type="text/x-handlebars-template">
							Showing @{{ count.from }} to @{{ count.to }} of @{{ count.total}} entries
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

{{ javascript_include_tag('visualizations/d3.min.js')}}
{{ javascript_include_tag('visualizations/jquery.mediaTable.js') }}
{{ javascript_include_tag('highcharts.js') }}
{{ javascript_include_tag('modules/exporting.js') }}
{{ javascript_include_tag('visualizations/unitsChartFacade.js') }}
{{ javascript_include_tag('visualizations/unitsWorkerDetails.js') }}
{{ javascript_include_tag('visualizations/unitsAnnotationDetails.js') }}
{{ javascript_include_tag('visualizations/unitsJobDetails.js') }}
{{ javascript_include_tag('visualizations/pieChartGraph.js') }}
{{ javascript_include_tag('visualizations/barChartGraph.js') }}
{{ javascript_include_tag('visualizations/unitChartDetails.js') }}
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

var getActiveTabKey = function(){
	return '#' + $('.tab-pane.active').attr('id');
}

var getSearchLimitValue = function(){
	return $('.searchOptions').find("[name='search_limit']").val();
}

var updateReponsiveTableHeight = function() {
	$(getActiveTabKey() + ' .ctable-responsive').css('max-height', $(window).height() - 290 + "px");	
}

var delay = (function(){
	var timer = 0;
	return function(callback, ms){
		clearTimeout (timer);
		timer = setTimeout(callback, ms);
	};
})();

$('.searchOptions').on('change', ".selectpicker", function(){
	if($(this).attr('name') == "documentType")
	{
		if($(this).val() != null)
		{
			if($(this).val().length == 1)
			{
				$('.documentTypesNav').find('#' + $(this).val()[0] + '_nav a').click();
			} else {

				$('.documentTypesNav').find('#' + $(this).val() + '_nav a').click();
			}
			getResults();
			return;				
		}	
	}

	if($(this).attr('name') == "search_limit"){
		getResults();
		return;
	}

	$('.documentTypesNav').find('#all_nav a').click();

	getResults();
});

var initializeSpecificFilter = function() {
	$(".searchOptions .specificFilter").popover({
	    trigger: "manual",
	    html: true,
	    'animation' : false,
	    'containter' : 'body',
	    'content' : function(){ return $('.searchOptions .specificFilterContent').html() },
	    'placement' : 'bottom',
	     template: '<div class="popover tssPopover"><div class="arrow"></div><div class="popover-content"></div></div>'
	}).on("mouseenter", function () {
	        var _this = this;
	        $(this).popover("show"); 
	        $(".popover").on("mouseleave", function () {
	            $(_this).popover('hide');
	        });
	}).on("mouseleave", function () {
	    var _this = this;
	    setTimeout(function () {
	        if (!$(".popover:hover").length) {
	            $(_this).popover("hide");
	        }
	    }, 100);
	}); 	
}

var getGeneralFilterQueries = function() {
	var generalFilterQuery = "";

	$('.searchOptions .selectpicker').each(function() {
		if($(this).is('[data-query-key]')){
			if($(this).val())
			{
				$(this).find("option:selected").each(function() {
					generalFilterQuery += "&" + $(this).parent().attr('data-query-key') + "=" + $(this).val();
			    });
			}
		}
	});

	return generalFilterQuery;
}

$('.searchOptions .tabOptions').on('click', "[data-vbSelector]", function(){
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

$('body').on('keyup', '.inputFilters input', function(){
	var inputFilter = $(this);

	delay(function(){
		var activeTabKey = getActiveTabKey();
		selectedRows[activeTabKey] = [];		
		inputFilter.attr('data-query-value', inputFilter.val());

		if(inputFilter.val() == "")
			inputFilter.removeAttr('data-query-value');

	 	getResults();
	}, 200);
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
	// $('.specificFilters .specificFilterOptions').prependTo($('.tab-pane.active'));
	// $('.cw_specificFilters').addClass('hidden');

	$('.searchOptions .tabOptions > *').appendTo('.tab-pane.active .tabOptions');
});

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	var activeTabKey = getActiveTabKey();

	if($(this).attr('href') == "#all_tab")
		return false;

	$(activeTabKey + ' .tabOptions > *').appendTo('.searchOptions .tabOptions');

	if($(this).closest('li').hasClass('active')){
		// $('.specificFilters').empty().append($('.tab-pane.active .specificFilterOptions'));
		// $('.cw_specificFilters').removeClass('hidden');
		if(templates[activeTabKey] == undefined)
		{		
			getResults();
		}
	}

	initializeSpecificFilter();
});

$('body').on('click', '.specificFilterOptions button', function(){
	$(this).siblings().removeClass('btn-info active').addClass('btn-default');
	$(this).removeClass('btn-default').addClass('btn-info active');

	var html = $(this).parent().html();
	var selector = $(this).parent().attr('id');
	$('.searchOptions .tabOptions #' + selector).empty().append(html);

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

$('body').on('click', 'ul.pagination a', function(e) {
	e.preventDefault();
	getResults($(this).attr('href') + "&noCache");
});

$('body').on('click', '.toCSV', function(e) {
	e.preventDefault();
	location.href = "{{ URL::to("api/search?tocsv&limit=100000") }}" + getTabFieldsQuery();
});

$('.listViewButton').click(function() {
	$(this).addClass('hidden');
	$('.graphViewButton').removeClass('hidden');
	$('.includeGraph').addClass('hidden');
	
	$(getActiveTabKey() + ' tbody.results').show();
});

$('.graphViewButton').click(function() {
	$(this).addClass('hidden');
	$('.listViewButton').removeClass('hidden');
	$('.includeGraph').removeClass('hidden');
	
	$(getActiveTabKey() + ' tbody.results').hide();
	getResults();
});

$('body').tooltip({
    selector: '[data-toggle=tooltip]',
    container: 'body',
    html: true
});

$(window).resize(function() {
	updateReponsiveTableHeight();
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
		tabFieldsQuery = getGeneralFilterQueries();
	}

	$('.searchOptions .specificFilterOptions, ' + activeTabKey).find("[data-query-key]").each(function() {
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

	console.log(tabFieldsQuery);

	$('.searchStats').text('Processing...');

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
		$('.cw_pagination').empty().prepend($(data.pagination));
		$('.cw_pagination').find('.pagination').addClass('pagination-sm');
		$(activeTabKey).find('.results').empty().append(html);

		var searchStats = Handlebars.compile($('.searchStatsTemplate').html());
		var searchStats = searchStats(data);
		$('.searchStats').empty().append(searchStats);

		// $(activeTabKey + ' .hb_popover').popover({
		// 	placement : "left",
		// 	html : true,
		// 	trigger : "hover",
		// 	title : "default",
		// 	content : function(){ return $(this).find('.hidden').html() },
 	// 		container: 'body',
  //           template: '<div class="popover popover-medium"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'			
		// });

		if(templates[activeTabKey + 'date'] == undefined) 
		{
			templates[activeTabKey + 'date'] = activeTabKey + 'date';
		} else {
			// alert(templates[activeTabKey + 'date']);
		}

		initializeVisibleColumns();
		visibleColumns();		

		// console.dir(selectedRows[activeTabKey]);
		// console.log('starting search');

		
		
		if($('.graphViewButton').hasClass('hidden')){
			$(activeTabKey + ' .checkAll').removeAttr('checked');
			var unitsChart = new unitsChartFacade();
			unitsChart.init(getTabFieldsQuery(),"");		
		}


			
        $("input[name=rowchk]").each(function(){
        	var val = $(this).attr('value');

	        if(jQuery.inArray(val, selectedRows[activeTabKey]) != -1) {
				if(!$(this).is(':checked')) {
					$(this).prop("checked", true);
				}
	        }
        });
	});

	updateReponsiveTableHeight();
}

function abortAjax(xhr) {
	if(xhr && xhr.readystate != 4){
		xhr.abort();
	}
}

var initializeVisibleColumns = function(){
	if($('.searchOptions .tabOptions').find(".vbColumns").length){
		$('.searchOptions .tabOptions').find("[data-vbSelector]").each(function() {
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
	}

	$('.searchOptions .openAllColumns').off().on("click", function() {
		$('.searchOptions .tabOptions').find("[data-vbSelector]").each(function() {
			if($(this).attr('data-vb') == "hide")
			{
				$(this).click();
			}
		});
	});
}

var visibleColumns = function(){
	var activeTabKey = getActiveTabKey();

	$('.searchOptions .tabOptions').find("[data-vbSelector]").each(function() {

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

var updateFilters = function(filterOption){
	if(filterOption.closest('li').hasClass('active')){
		filterOption.closest('li').removeClass('active');
		filterOption.children('i').removeClass('fa-check-circle-o').addClass('fa-circle-o');
	} else {
		filterOption.closest('li').addClass('active');
		filterOption.children('i').removeClass('fa-circle-o').addClass('fa-check-circle-o');
	}	
}

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

$('.select_twrex-structured-sentence').click();

});

</script>

@stop