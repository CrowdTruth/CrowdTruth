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

		<div class='tab'>
			<div class='row'>
				<div class='col-xs-12 searchOptions'>
				@if(Request::segment(1) == 'workers')
					@if(isset($mainSearchFilters['workers']))
					<select name="documentType" data-query-key="match[documentType][]" class="selectpicker pull-left show-tick" title="Jobs" data-width="auto" data-show-subtext="true">
						<option value="crowdagents" class="select_crowdagents" data-subtext="{{ $mainSearchFilters['workers']['count'] }} Items">Workers</option>
					</select>
					@endif
				@endif

					<div class='tabOptions pull-left'>
					</div>
					<div class="btn-group pull-left" style="margin-left:5px";>
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						Actions <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							@if(Request::segment(1) == 'workers')
								<li><a href="#" onclick="javascript:alert('Mass messaging is currently disabled. Sorry!')">Message workers</a></li>
							@endif
						</ul>
					</div>					
					<select name="search_limit" data-query-key="limit" class="selectpicker pull-right show-tick">
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
				</div>
				<div class='col-xs-12'>
					<div class='searchStats pull-left'>
					</div>
					<div class='cw_pagination pull-right'>
					</div>
				</div>
				<div class='col-xs-12 searchResults'>				
					<ul class="nav nav-tabs documentTypesNav hidden">
						<li id="all_nav">
							<a href="#all_tab" data-toggle="tab">
								All
							</a>
						</li>
						@if(isset($mainSearchFilters['workers']))
							<li id="crowdagents_nav">
								<a href="#crowdagents_tab" data-toggle="tab">
									Workers
								</a>
							</li>
						@endif
					</ul>    								
					<div class="tab-content documentTypesTabs">
						@include('media.search.layouts.hb-all')

						@if(isset($mainSearchFilters['workers']))
							@include('media.search.layouts.hb-crowdagents')
						@endif

						@include('media.search.layouts.hb-modalindividualjob')
						@include('media.search.layouts.hb-modalannotations')
						@include('media.search.layouts.hb-modalindividualunit')						
						
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
                                    <div id="generalBarChart_div" ></div>
                                    </td>
                                </tr>
                            </table>
                            <table>
                                <tr>
                                    <td class="pieDivGraphs pieDivLarge">
                                        <div id="workersPie_div"></div>
                                    </td>
                                    <td>
                                        <div id="workersBar_div"></div>
                                    </td>
                                </tr>
                            </table>
                            <table>
                                <tr>
                                    <td class="pieDivGraphs pieDivLarge">
                                        <div id="jobsPie_div"></div>
                                    </td>
                                    <td>
                                        <div id="jobsBar_div"></div>
                                    </td>
                                </tr>
                            </table>
                            <table>
                                <tr>
                                    <td class="pieDivGraphs pieDivLarge">
                                        <div id="unitsPie_div"></div>
                                    </td>
                                    <td>
                                        <div id="unitsBar_div"></div>
                                    </td>
                                </tr>
                            </table>
                            <table>
                                <tr>
                                    <td class="pieDivGraphs pieDivLarge">
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
{{ javascript_include_tag('generalsearch_manifest') }}
{{ javascript_include_tag('visualization_manifest') }}

<script>
$('document').ready(function(){

Swag.registerHelpers();

$('.selectpicker').selectpicker();
var xhr;
var selectedRows = [];
var templates = {};
var defaultColumns = {};
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
	$(getActiveTabKey() + ' .ctable-responsive').css('max-height', $(window).height() - 185 + "px");	
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

        updateSelection(val);
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

$('body').on('click', '.toSelection', function(){
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
	$('.includeGraph, .specificGraphs').removeClass('hidden');
	
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


    var updateSelection = function(id) {
        var activeTabKey = getActiveTabKey();

        if (typeof selectedRows[activeTabKey] == 'undefined') {
            selectedRows[activeTabKey] = [];
        }

        if(id !== undefined)
        {
            if(jQuery.inArray(id, selectedRows[activeTabKey]) != -1) {
                selectedRows[activeTabKey] = $.grep(selectedRows[activeTabKey], function(value) {
                    return value != id;
                });
            } else {
                selectedRows[activeTabKey].push(id);
            }
        }

        $("input[name=rowchk]").each(function(){
            var val = $(this).attr('value');

            if(jQuery.inArray(val, selectedRows[activeTabKey]) != -1) {
                $(this).prop("checked", true);
            } else {
                $(this).prop("checked", false);
            }
        });

        console.dir(selectedRows[activeTabKey]);
    }

    var getSelection = function() {
        var activeTabKey = getActiveTabKey();

        if (typeof selectedRows[activeTabKey] != 'undefined') {
            return selectedRows[activeTabKey];
        }

        return [];
    }

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
			defaultColumns[activeTabKey] = $('.searchOptions').find(".vbColumns").html();
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
            var selectedCategory = activeTabKey;
            $(activeTabKey + ' .checkAll').removeAttr('checked');
            var unitsChart = new unitsChartFacade(selectedCategory, openModal, getSelection, updateSelection);
            unitsChart.init(getTabFieldsQuery(),"");
        }

        updateSelection();
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
		$(this).addClass('hidden');
		$('.searchOptions .openDefaultColumns').removeClass('hidden');

		$('.searchOptions .tabOptions').find("[data-vbSelector]").each(function() {
			if($(this).attr('data-vb') == "hide")
			{
				$(this).click();
			}
		});
	});

	$('.searchOptions .openDefaultColumns').off().on("click", function() {
		$(this).addClass('hidden');
		$('.searchOptions .openAllColumns').removeClass('hidden');

		$('.searchOptions .tabOptions').find(".vbColumns").empty();
		$('.searchOptions .tabOptions').find(".vbColumns").append(defaultColumns[getActiveTabKey()]);

		initializeVisibleColumns();
		visibleColumns();
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

	// initializeFixedThead();
}

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
	            	console.log(data);
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

$('.select_crowdagents').click();
$('.documentTypesNav').find('#crowdagents_nav a').click();
$('.graphViewButton').click();

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
							           
						console.log(data);

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