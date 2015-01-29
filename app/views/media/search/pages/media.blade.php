@extends('layouts.default_new')

@section('container', 'full-container')

@section('head')
{{ stylesheet_link_tag('bootstrap-select.css') }}
{{ stylesheet_link_tag('bootstrap-dropdown-checkbox.css') }}
{{ stylesheet_link_tag('bootstrap.datepicker3.css') }}

{{ javascript_include_tag('jquery-1.10.2.min.js') }}

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

		<div class='tab'>
			<div class='search row'>
				<div class='col-xs-12'>
				@if(isset($mainSearchFilters['media']['documentTypes']))
					<select name="documentType" data-query-key="match[documentType]" class="documentType selectpicker pull-left show-tick" title="Choose Document-Type(s)" data-width="auto" data-show-subtext="true">
						@foreach($mainSearchFilters['media']['documentTypes'] as $key => $value)
							<option value="{{$key}}" class="select_{{$key}}" data-subtext="{{ $value['count'] }} Items">{{ $value['label'] }}</option>
							@if($key == 'all')
								<option data-divider="true"></option>
							@endif
						@endforeach
					</select>
				@endif
				
					<div class='btn-group pull-left' style="margin-left:5px";>
						<select class="columns selectpicker show-tick" multiple title="Select columns" data-live-search="true" style="display: none;">
							<optgroup data-icon="fa fa-flag" label="Selected">
								<option value="_id" class="select_id" selected>ID</option>
								<option value="format" class="select_format" selected>Format</option>
								<option value="domain" class="select_domain" selected>Domain</option>
								<option value="documentType" class="select_documentType" selected>Type</option>
								<option value="title" class="select_title" selected>Filename</option>
								<option value="created_at" class="select_created_by" selected>Created At</option>
								<option value="user_id" class="select_user_id" selected>User id</option>
							</optgroup>
							<optgroup label="Properties">
								@foreach($keys as $id => $key)
									<option data-icon="fa fa-file-text-o" value="{{$id}}" class="select_{{$id}}">{{ $key['format'] . $key['label'] }}</option>
								@endforeach
							</optgroup>
						</select>
					</div>

					<div class="actions btn-group pull-left" style="margin-left:5px">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						Actions <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="{{ URL::to('media/preprocess') }}">Pre-process Media</a></li>
							<li><a href="#" class='toSelection'>Save Selection as Batch</a></li>
							<li><a href="#" class='toCSV'>Export results to CSV</a></li>
						<li role="presentation" class="divider"></li>
						<li role="presentation" class="dropdown-header">Search Index</li>
							<li><a href="{{ URL::to('media/listindex') }}">View</a></li>
							<li><a href="{{ URL::to('media/refreshindex') }}">Refresh</a></li>
						</ul>
					</div>
					<select name="search_limit" data-query-key="limit" class="limit selectpicker pull-right show-tick">
						<option value="10">10 Records per page</option>
						<option value="25">25 Records per page</option>
						<option value="50">50 Records per page</option>
						<option value="100">100 Records per page</option>
					</select>
					<div class='switchViews pull-right' style="margin-right:5px;">
						<button type="button" class="btn btn-info listViewButton hidden" style="margin-left:5px;">
							Switch to List View
						</button>
						<button type="button" class="btn btn-info graphViewButton" style="margin-left:5px;">
							Switch to Graph View
						</button>
					</div>
					<div class='col-xs-12'>
						<div class='stats pull-left'>
							No Items Found
						</div>
						<div class='navigation pull-right'>
						</div>
					</div>
				</div>
				<div class='col-xs-12 searchResults'>
					<div class="tab-content documentTypesTabs">


						<div class='ctable-responsive'>		
							<table class="table table-striped">
								<thead data-query-key="" data-query-value="">
									<tr class='identifiers'>
									</tr>
									<tr class="inputFilters">
									</tr>											        
								</thead>
								<tbody class='results'>
								</tbody>
							</table>
						</div>	
						
						<div class='status text-center'>
							<div class='loading'>
								<i class="fa fa-spinner fa-spin fa-4x"></i><br /><br />Loading
							</div>
							<div class='error' style='display:none;'>
							<i class="fa fa-exclamation-triangle fa-4x"></i><br /><br />Oops! Something has gone wrong.
							</div>
						</div>
						<div class='includeGraph hidden'>
                            <table>
                                <tr class="pieDivGraphs">
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
                                        <div id="optional1_div"></div>
                                    </td>
                                    <td>
                                        <div id="optional2_div"></div>
                                    </td>
                                    <td>
                                        <div id="optional3_div"></div>
                                    </td>
                                </tr>
                            </table>
                            <table>
                                <tr >
                                    <td>
                                        <div id="specificBarChart_div" ></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div id="specificBarChartMaster_div"></div>
                                    </td>
                                </tr>
                            </table>
                            <table >
                                <tr >
                                    <td>
                                    <div id="generalBarChart_div" ></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div id="generalBarChartMaster_div"></div>
                                    </td>
                                </tr>
                            </table >
                            <table style="border: solid thin #274B6D">
                                <tr>
                                    <td class="pieDivGraphs pieDivLarge">
                                        <div id="workersPie_div"></div>
                                    </td>
                                    <td>
                                        <div id="workersBar_div"></div>
                                    </td>
                                </tr>
                            </table>
                            <table style="border: solid thin #274B6D">
                                <tr>
                                    <td class="pieDivGraphs pieDivLarge">
                                        <div id="jobsPie_div"></div>
                                    </td>
                                    <td>
                                        <div id="jobsBar_div"></div>
                                    </td>
                                </tr>
                            </table>
                            <table style="border: solid thin #274B6D">
                                <tr>
                                    <td class="pieDivGraphs pieDivLarge">
                                        <div id="unitsPie_div"></div>
                                    </td>
                                    <td>
                                        <div id="unitsBar_div"></div>
                                    </td>
                                </tr>
                            </table>
                            <table style="border: solid thin #274B6D">
                                <tr >
                                    <td class="pieDivGraphs">
                                        <div id="annotationsPie_div"></div>
                                    </td>
                                    <td>
                                        <div id="annotationsAfter_div"></div>
                                    </td>
                                    <td class="pieDivGraphs">
                                        <div id="annotationsMetricAfter_0_div"></div>
                                    </td>

                                </tr>
                                <tr class='annotationHidden hide'>
                                    <td></td>
                                    <td>
                                        <div id="annotationsBefore_div"></div>
                                    </td>
                                    <td class="pieDivGraphs">
                                        <div id="annotationsMetricBefore_0_div"></div>
                                    </td>

                                </tr>
                                <tr class='annotationHidden hide'>
                                    <td></td>
                                    <td>
                                        <div id="annotationsDiff_div"></div>
                                    </td>
                                    <td class="pieDivGraphs">
                                        <div id="annotationsMetricDiff_0_div"></div>
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
{{ javascript_include_tag('generalsearch_manifest') }}
{{ javascript_include_tag('visualization_manifest') }}

<script>
$('document').ready(function(){

  // highlight own username
  Swag.addHelper('highlightSelf', function(user, self, options) {
    
	if(user == '{{Auth::user()->_id}}') {
		user = '<span class="highlightSelf">' + user + '</span>';
	}
	
	return new Handlebars.SafeString(user);
  });

Swag.registerHelpers();

$('.selectpicker').selectpicker({
    iconBase: 'fa',
    tickIcon: 'fa-check'
});

var xhr;
var unitsChart;
var oldTabKey;
var selectedRows = [];
var templates = {};
var defaultColumns = {};
var lastQueryResult;

var getSearchLimitValue = function(){
	return $('.search .limit').val();
}

var delay = (function(){
	var timer = 0;
	return function(callback, ms){
		clearTimeout (timer);
		timer = setTimeout(callback, ms);
	};
})();

$('.search .documentType').change(function(){
	getResults();
});


$('.search .limit').change(function() {
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

$('body').on('keyup', '.inputFilters input', function(){
	var inputFilter = $(this);

	delay(function(){
		selectedRows = [];
		inputFilter.attr('data-query-value', inputFilter.val());

		if(inputFilter.val() == "")
			inputFilter.removeAttr('data-query-value');

	 	getResults();
	}, 300);
});

$('body').on('click', 'input[name=rowchk]', function(event){
	var val = $(this).attr('value');

	updateSelection(val);
});

$('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
	// $('.specificFilters .specificFilterOptions').prependTo($('.tab-pane.active'));
	// $('.cw_specificFilters').addClass('hidden');

	$('.searchOptions .tabOptions > *').appendTo('.tab-pane.active .tabOptions');
});

$('body').on('click', '.toSelection', function(){

	if(typeof selectedRows == 'undefined' || selectedRows.length < 1){
		event.preventDefault();
		alert('Please make a selection first');
	} else {
		var searchQuery = JSON.stringify(lastQueryResult.searchQuery);

		// alert(searchQuery);

		var form = $('<form action="{{ URL::action("MediaController@anyBatch") }}" method="post"></form>');
		$('body').append(form);

		$.each(selectedRows, function(index, value){
			form.append($('<input type="checkbox" name="selection[]" value="' + value + '" checked >'))
		});

		form.append($("<input type='checkbox' name='searchQuery[]' value='" + searchQuery + "' checked >"));
		form.submit();
	}
});

$('.searchResults').on('click', "th", function(){
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

	$('.search .results').show();
});

$('.graphViewButton').click(function() {
	$(this).addClass('hidden');
	$('.listViewButton').removeClass('hidden');
	$('.includeGraph, .specificGraphs').removeClass('hidden');

	$('.search .results').hide();

});

$('body').tooltip({
    selector: '[data-toggle=tooltip]',
    container: 'body',
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

var updateSelection = function(id) {

	if (typeof selectedRows == 'undefined') {
		selectedRows = [];
	}

	if(id !== undefined)
	{
	    if(jQuery.inArray(id, selectedRows) != -1) {
			selectedRows = $.grep(selectedRows, function(value) {
			  return value != id;
			});			
	    } else {
			selectedRows.push(id);
	    }
	}

    $("input[name=rowchk]").each(function(){
    	var val = $(this).attr('value');

        if(jQuery.inArray(val, selectedRows) != -1) {
			$(this).prop("checked", true);
        } else {
        	$(this).prop("checked", false);
        }
    });

}

var getSelection = function() {

	if (typeof selectedRows != 'undefined') {
		return selectedRows;
	}

	return [];
}

function getTabFieldsQuery(){
	var tabFieldsQuery = '';

	var documentType = $('.search .documentType option:selected').val();
	var operator = '=';
	if(documentType == 'all') {
		tabFieldsQuery += "&match[tags]=unit";
	} else {
		tabFieldsQuery += "&" + "match[documentType]" + operator + documentType;
	}

	// go through sorting
	$('.sorting, .sorting_asc, .sorting_desc').each(function() {
	console.log($(this).text());
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

	// hide old results and show loading screen
	$('.search .results').hide();
	$('.status .error').hide();
	$('.status .loading').show();
	$('.status').show();

	if(baseApiURL == undefined)
	{
		var baseApiURL = '{{ URL::to("api/search?noCache") }}';
	}

	var searchLimitQuery = "&limit=" + getSearchLimitValue();
	var tabFieldsQuery = getTabFieldsQuery();

	console.log(baseApiURL + tabFieldsQuery + searchLimitQuery);

	abortAjax(xhr);

	xhr = $.getJSON(baseApiURL + tabFieldsQuery + searchLimitQuery, function(data) {

		lastQueryResult = data;
	
		var template = Handlebars.compile(dynamicTemplate());
		var html = template(data);
		$('.navigation').empty().prepend($(data.pagination));
		$('.navigation').find('.pagination').addClass('pagination-sm');
		$('.search .results').empty().append(html);
		$('.search .results').show('slow');
		

		var searchStats = Handlebars.compile($('.searchStatsTemplate').html());
		var searchStats = searchStats(data);
		$('.search .stats').html(searchStats);

		
		if($('.graphViewButton').hasClass('hidden')){
            var selectedCategory = activeTabKey;
			$(activeTabKey + ' .checkAll').removeAttr('checked');
            if(!(oldTabKey == activeTabKey))
            {
                unitsChart = new unitsChartFacade(selectedCategory, openModal, getSelection, updateSelection);
                unitsChart.init(getTabFieldsQuery(),"");
                oldTabKey = activeTabKey;
            } else {
                unitsChart.init(getTabFieldsQuery(),"");
            }
		}

		updateSelection();
		$('.search .loading').hide();
		$('.status').hide();

	}).fail(function() {
		$('.status .loading').hide();
		$('.status .error').show();
		$('.status').show();
	});
}

function abortAjax(xhr) {
	if(xhr && xhr.readystate != 4){
		xhr.abort();
	}
}

$('.inputFilters').on('click', '.filterChange', function() {
	$(this).parents('.filter').children('.filterField').remove();
});

// refresh columns in table
var refreshColumns = function() {

	// create identifiers and filters
	var identifiers = '<th data-vbIdentifier="checkbox">Select</th>';
	var filters = '<td><input type="checkbox" class="checkAll" /></td>';

	var columns = $('.columns').val();
	for(var i = 0; i < columns.length; i++) {
		identifiers += '<th class="sorting" data-vbIdentifier="' + columns[i] + '" data-query-key="orderBy[' + columns[i] + ']">' + $('.columns option[value=' + columns[i] + ']').text() + '</th>';	
		filters += '<td><input class="input-sm form-control" type="text" data-query-key="match[' + columns[i] + ']" data-query-operator="like" placeholder="Filter" /></td>';		
	}

	// update identifiers and filters
	$('.identifiers').html(identifiers);
	$('.inputFilters').html(filters);
	
}

// create template based on selected columns
var dynamicTemplate = function() {

	var template = '@{{#each documents}}<tr><td data-vbIdentifier="checkbox"><input type="checkbox" id="@{{ this._id }}" name="rowchk" value="@{{ this._id }}"></td>';

	var columns = $('.columns').val();
	for(var i = 0; i < columns.length; i++) {
		template += '<td data-vbIdentifier="id">@{{ this.' + columns[i] + ' }}</td>';
	}
	template += '</tr>@{{/each}}';
	return template;
}

// on adding or removal of a column, refresh the table identifiers and filters
$('.columns').on('change', function(){
	refreshColumns();
	
	
	// update results
	var template = Handlebars.compile(dynamicTemplate());
	var html = template(lastQueryResult);
	$('.search .results').empty().append(html);
	$('.search .results').show('slow');
});



var fixedThead;

var initializeFixedThead = function(){
	if(typeof fixedThead == 'undefined') {
		fixedThead = $(getActiveTabKey() + ' .cResults table').floatThead({
			scrollContainer: function($table){
				return $table.closest('.ctable-responsive');
			},
			useAbsolutePositioning: false
		});
	} else {
		fixedThead.trigger('reflow');
	}
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
var openModal = function(modalAnchor , activeTabKey){
    if(baseApiURL == undefined)
    {
        var baseApiURL = modalAnchor.attr('data-api-target');
    }
    console.log(modalAnchor);
    //var activeTabKey =  '#' + $('.tab-pane.active').attr('id');
    var modalTarget = modalAnchor.attr('data-target');
    //alert(modalTarget);

    $('#activeTabModal').remove();

    var query = modalAnchor.attr('data-modal-query');
    console.log(baseApiURL + query);
    $.getJSON(baseApiURL + query, function(data) {
        console.dir(activeTabKey);

        var template = Handlebars.compile($(modalTarget + ' .template').html());

        var html = template(data);

        $('body').append(html);

        $('#activeTabModal').modal();

        $(".tablesorter.table.table-striped").tablesorter({
            // *** Appearance ***
            // fix the column widths
            widthFixed : true,
            // include zebra and any other widgets, options:
            // 'uitheme', 'filter', 'stickyHeaders' & 'resizable'
            // the 'columns' widget will require custom css for the
            // primary, secondary and tertiary columns
            widgets    : [ 'uitheme', 'zebra' ],

            // *** Functionality ***
            // starting sort direction "asc" or "desc"
            sortInitialOrder : "asc",
            // extract text from the table - this is how is
            // it done by default
            textExtraction : {
                0: function(node) { return $(node).text(); },
                1: function(node) { return $(node).text(); }
            },

            // Setting this option to true will allow you to click on the
            // table header a third time to reset the sort direction.
            sortReset: true,

            // The key used to select more than one column for multi-column
            // sorting.
            sortMultiSortKey : "shiftKey",

            // *** Customize header ***
            onRenderHeader  : function() {
                // the span wrapper is added by default
                $(this).find('span').addClass('headerSpan');
            },
            // jQuery selectors used to find the header cells.
            selectorHeaders : 'thead th',

            // *** css classes to use ***
            cssAsc        : "headerSortUp",
            cssChildRow   : "expand-child",
            cssDesc       : "headerSortDown",
            cssHeader     : "header",
            tableClass    : 'tablesorter',

            // *** widget css class settings ***
            // column classes applied, and defined in the skin
            widgetColumns : { css: ["primary", "secondary", "tertiary"] },
            // find these jQuery UI class names by hovering over the
            // Framework icons on this page:
            // http://jqueryui.com/themeroller/
            widgetUitheme : { css: [
                "ui-icon-arrowthick-2-n-s", // Unsorted icon
                "ui-icon-arrowthick-1-s",   // Sort up (down arrow)
                "ui-icon-arrowthick-1-n"    // Sort down (up arrow)
            ]
            },
            // pick rows colors to match ui theme
            widgetZebra: { css: ["ui-widget-content", "ui-state-default"] },

            // *** prevent text selection in header ***
            cancelSelection : true,

            // *** send messages to console ***
            debug : false
        });

    });
}

var openStaticModal = function(modalAnchor , activeTabKey){


    var modalTarget = modalAnchor.attr('data-target');
    var staticData = modalAnchor.attr('data-static');

        var template = Handlebars.compile($(activeTabKey).find(modalTarget + ' .template').html());

        var html = template();

        $('#activeTabModal').remove();

        $('body').append(html);

        $('#activeTabModal').modal();
		//rel=static-val or static-inner
		$('span[rel="static-html"]').html(staticData);
		$('input[rel="static-val"]').val(staticData);


        $(".ajaxform").submit(function(e)
		{
		    var postData = $(this).serializeArray();
		    var formURL = $(this).attr("action");
		    $.ajax(
		    {
		        url : formURL,
		        type: "POST",
		        data : postData,
		        success:function(data, textStatus, jqXHR)
		        {
	            	alert(data.message);

		        },
		        error: function(jqXHR, textStatus, errorThrown)
		        {
		            console.log(errorThrown);
		        }
		    });
		    e.preventDefault(); //STOP default action
		    e.unbind(); //unbind. to stop multiple form submit.
		});
}



$('body').on('click', '.testModal', function(){
    var activeTabKey =  '#' + $('.tab-pane.active').attr('id');

    if($(this).is('[data-static]')){
    	openStaticModal($(this),activeTabKey);
    } else {
   	 	openModal($(this),activeTabKey);
	}
});

$('.select_all').click();
var workerList = localStorage.getItem("unitList");
if(workerList !=  null) {
    workerList = JSON.parse(workerList);
    for(var iterWorker in workerList){
        updateSelection(workerList[iterWorker]);
    }
    localStorage.removeItem("unitList");
}

getResults();
refreshColumns();

});

function jobactions(job, action, index){
	var newstatus = '';
 	if(action == 'pause') newstatus = 'paused';
 	else if(action == 'order' || action == 'resume') newstatus = 'running';
 	else if(action == 'cancel') newstatus = 'canceled';

	if(action=='cancel'){
		if(!confirm('Do you really want to '+action+' job '+job+'?')){
			return false;
		}
	}
	$.ajax(
		    {
		        url : '/api/actions/'+job+'/'+action,
		        type: "GET",
		        success:function(data, textStatus, jqXHR)
					{


						if(data.status=='ok'){
							$('#'+action+index).hide();
							$('#'+'status'+index).html(newstatus);
						} else {
							alert(data.message);
						}

					},
		        error: function(jqXHR, textStatus, errorThrown)
		        {
		            alert(errorThrown);
		        }
		    });
}

</script>

@stop
