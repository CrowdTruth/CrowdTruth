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




					<select name="documentType" data-query-key="match[documentType]" class="documentType selectpicker pull-left show-tick" multiple data-selected-text-format="count>3" title="Choose Document-Type(s)" data-width="auto" data-show-subtext="true">
						<option value="all" class="select_all" data-subtext="{{ $unitcount }} Items">All</option>
                    	@foreach($types as $project => $doctypes)
							<optgroup label="{{ $project }}">
                        		@foreach($doctypes as $doctype => $count)
									<option value="{{ $project }}__{{ $doctype }}" class="select_{{ $doctype }}" data-subtext="{{ $count }} Items">{{ $doctype }}</option>
								@endforeach
							</optgroup>
						@endforeach
					</select>


					<div class='btn-group pull-left' style="margin-left:5px";>
						{{ Form::open([ 'action' => 'MediaController@postKeys', 'name' => 'theForm', 'id' => 'theForm' ]) }}
							<select class="columns selectpicker show-tick" multiple title="Select columns" data-live-search="true" data-selected-text-format="count>3" style="display: none;">
							</select>
						{{ Form::close() }}
					</div>

		

					
					<div class="btn-group pull-left specificFilterButton" style="margin-left:5px;">
						<button type="button" class="btn btn-default specificFilter" data-original-title="" title="">
							Specific Filters
						</button>
					</div>
			
					<div class='specificFilterContent hidden'>
						<table class='table table-striped table-condensed specificFilterOptions'>
							<tbody>
								<tr>
									<td>Relation In Sentence</td>
									<td class="text-right">
										<div class="btn-group" id='relationInSentence'>
										  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.relationInSentence]" data-query-value="1"><i class="fa fa-check"></i></button>
										  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.relationInSentence]" data-query-value="0"><i class="fa fa-minus"></i></button>
										  <button type="button" class="btn btn-sm btn-info active">Not Applied</button>
										</div>
									</td>
								</tr>
								<tr>
									<td>Relation Outside Terms</td>
									<td class="text-right">
										<div class="btn-group" id='relationOutsideTerms'>
										  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.relationOutsideTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
										  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.relationOutsideTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
										  <button type="button" class="btn btn-sm btn-info active relexNone">Not Applied</button>
										</div>
									</td>
								</tr>
								<tr>
									<td>Relation Between Terms</td>
									<td class="text-right">
										<div class="btn-group" id='relationBetweenTerms'>
										  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.relationBetweenTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
										  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.relationBetweenTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
										  <button type="button" class="btn btn-sm btn-info active relexNone">Not Applied</button>
										</div>
									</td>
								</tr>
								<tr>
									<td>Semicolon Between Terms</td>
									<td class="text-right">
										<div class="btn-group" id='semicolonBetweenTerms'>
										  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.semicolonBetweenTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
										  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.semicolonBetweenTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
										  <button type="button" class="btn btn-sm btn-info active relexNone">Not Applied</button>
										</div>
									</td>
								</tr>
								<tr>
									<td>Comma-separated Terms</td>
									<td class="text-right">
										<div class="btn-group" id='commaSeparatedTerms'>
										  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.commaSeparatedTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
										  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.commaSeparatedTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
										  <button type="button" class="btn btn-sm btn-info active relexNone">Not Applied</button>
										</div>
									</td>
								</tr>
								<tr>
									<td>Parenthesis Around Terms</td>
									<td class="text-right">
										<div class="btn-group" id='parenthesisAroundTerms'>
										  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.parenthesisAroundTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
										  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.parenthesisAroundTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
										  <button type="button" class="btn btn-sm btn-info active relexNone">Not Applied</button>
										</div>
									</td>
								</tr>
								<tr>
									<td>Overlapping Terms</td>
									<td class="text-right">
										<div class="btn-group" id='overlappingTerms'>
										  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.overlappingTerms]" data-query-value="1"><i class="fa fa-check"></i></button>
										  <button type="button" class="btn btn-sm btn-default" data-query-key="match[content.properties.overlappingTerms]" data-query-value="0"><i class="fa fa-minus"></i></button>
										  <button type="button" class="btn btn-sm btn-info active relexNone">Not Applied</button>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>				

					<div class="actions btn-group pull-left" style="margin-left:5px">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						Actions <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="{{ URL::to('media/preprocess') }}">Pre-process Media</a></li>
							<li><a href="{{ URL::to('media/importresults') }}">Import Results</a></li>
							<li><a href="#" class='toSelection'>Save Selection as Batch</a></li>
							<li><a href="#" class='toCSV'>Export results to CSV</a></li>
						</ul>
					</div>
					<select name="search_limit" data-query-key="limit" class="limit selectpicker pull-right show-tick">
						<option value="10">10 Records per page</option>
						<option value="25">25 Records per page</option>
						<option value="50">50 Records per page</option>
						<option value="100">100 Records per page</option>
						<option value="250">250 Records per page</option>
						<option value="500">500 Records per page</option>
						<option value="1000">1000 Records per page</option>
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
						<div class="tab-pane active" id="all_tab">


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
						</div>
							
						<div class='status text-center'>
							<div class='loading'>
								<i class="fa fa-spinner fa-spin fa-4x"></i><br /><br />Loading
							</div>
							<div class='error' style='display:none;'>
							<i class="fa fa-exclamation-triangle fa-4x"></i><br /><br />Oops! Something has gone wrong.
							</div>
						</div>
						
						@include('media.search.layouts.hb-modalworkerunits')
						@include('media.search.layouts.hb-modalindividualworker')
						@include('media.search.layouts.hb-modalindividualjob')

						{{-- load all modal templates --}}
						@include('media.search.layouts.hb-modalindividualunit')
						@include('media.search.layouts.hb-modalindividualannotatedmetadata')
						@include('media.search.layouts.hb-modalindividualfullvideo')
						@include('media.search.layouts.hb-modalindividualmetadata')
						@include('media.search.layouts.hb-modalindividualrelex')
						@include('media.search.layouts.hb-modalvideokeyframes')
						@include('media.search.layouts.hb-modalvideosegments')
						
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



  Swag.addHelper('dynamicField', function(value) {
    
    var youtube = /^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/;

    if(!value) {
    	string = "";
    } else if(/^(http\:\/\/.*\.ggpht\.com.*|.*\.(jpg|jpeg|png|gif))$/i.test(value)) {
		// image
		string = '<img style="max-width:100px; max-height:100px;border:0px;" src="' + value + '" />';
	} else if(/^.*\.(mp3|ogg|wmv)$/i.test(value)) {
		// sound
		string = '<audio class="audio" src="' + value + '" preload="none" controls="controls">Please update your browser to the latest version in order to complete this task.</audio>';
	
	} else if(/^.*\.(avi|mpeg|mpg|mp4)$/i.test(value)) {
		// video
		string = '<video width="240" height="160" controls="" preload="none" data-toggle="tooltip" data-placement="top" title="" data-original-title="Click to play"><source src="' + value + '" type="video/mp4">Your browser does not support the video tag.</video>';
	} else if(youtube.test(value)) {
	    var youtubeId = value.match(youtube);
		string = '<iframe width="240" height="160" src="https://www.youtube.com/embed/' + youtubeId[1] + '" frameborder="0" allowfullscreen></iframe>';
	} else if(!isNaN(value)) {
		// number
		string = value;
	} else {
	 	// other
		string = value;
	}
	
	return new Handlebars.SafeString(string);
  });

  
  
	// set the unit modal based on the document type. only available in some specific cases
  Swag.addHelper('unitModal', function(id, documentType) {

	var useModal;
	
	switch(documentType) {
		case 'fullvideo' :
			useModal = '<a class="testModal" id="' + id + '" data-modal-query="unit=' + id + '" data-api-target="{{ URL::to("api/analytics/unit?") }}" data-target="#modalIndividualFullvideo" data-toggle="tooltip" data-placement="top" title="Click to see the individual unit page">' + id + '</a>';
		break;
		case 'metadatadescription' :
			useModal = '<a class="testModal" id="' + id + '" data-modal-query="unit=' + id + '" data-api-target="{{ URL::to("api/analytics/unit?") }}" data-target="#modalIndividualMetadata" data-toggle="tooltip" data-placement="top" title="Click to see the individual unit page">' + id + '</a>';
		break;
		case 'relex-structured-sentence' :
			useModal = '<a class="testModal" id="' + id + '" data-modal-query="unit=' + id + '" data-api-target="{{ URL::to("api/analytics/unit?") }}" data-target="#modalIndividualRelex" data-toggle="tooltip" data-placement="top" title="Click to see the individual unit page">' + id + '</a>';
		break;
		default:
			useModal = id;
	}
	
    return new Handlebars.SafeString(useModal);
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



var getActiveTabKey = function(){
	return '#all_tab';
}

// document type selection
var lastDocuments = [];
$('.search .documentType').change(function(){

	var documents = $(this).val();

	//if nothing is selected, select All
	if(!documents) {
		$('.search .documentType option[value=all]').attr('selected',true);
		documents = ['all'];
	} else if(documents.length > 1 && documents[0] == 'all' && lastDocuments[0] == 'all') { // unselect 'all' if any other document type is selected
		$('.search .documentType option[value=all]').attr('selected',false);
		delete(documents[0]);
	} else if(documents.length > 1 && documents[0] == 'all' && lastDocuments[0] != 'all') { // unselect all other document types if 'all' is selected
		$('.search .documentType option[value!=all]').attr('selected',false);
		documents = ['all'];
	}
	$('.search .documentType').selectpicker('refresh');
	

	// if document type is relex, show the relex specific filters
	if(documents[0] == 'relex-structured-sentence') {
		$('.specificFilterButton').show();
		initializeSpecificFilter();
	} else {
		$('.specificFilterButton').hide();
	}

	documents = documents.sort();
	lastDocuments = documents;

	getColumns(documents);
});



$('.search .limit').change(function() {
	getResults();
});

// create popover button for relex specific filters
var initializeSpecificFilter = function() {
	$(".specificFilter").popover({
	    trigger: "manual",
	    html: true,
	    'animation' : false,
	    'containter' : 'body',
	    'content' : function(){ return $('.specificFilterContent').html() },
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
		// event.preventDefault();
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

// toggle relex specific options on click
$('body').on('click', '.specificFilterOptions button', function(){
	$(this).siblings().removeClass('btn-info active').addClass('btn-default');
	$(this).removeClass('btn-default').addClass('btn-info active');
	var html = $(this).parent().html();
	var selector = $(this).parent().attr('id');
	$('.specificFilterContent #' + selector).empty().append(html);
	getResults();
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
	getResults();
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
	var date = $(this).val();

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
	var tabFieldsQuery = '&match[type]=unit';

	var documents = $('.search .documentType').val();

	var operator = '=';
	if(documents[0] != 'all') {
		documents = documents[0].split('__');
		tabFieldsQuery += "&match[documentType]" + operator + documents[1];
	}


	
	// find filter values
	$('.inputFilters, .specificFilterContent').find("[data-query-key]").each(function() {
	
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
	});

	// go through sorting
	$('.sorting, .sorting_asc, .sorting_desc').each(function() {
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
	
	return tabFieldsQuery;
}

// function to get columns available for selected document types
function getColumns(documents) {

			formData = 'documents=' + documents.join('|');
			$.ajax({
				type: "POST",
				url: $("#theForm").attr("action"),
				data: formData,
				success: function(data) {

					def = ['_id','project','documentType','created_at','user_id']; // default visible columns
					// create select list with default options
					var columnList = '<optgroup data-icon="fa fa-flag" class="columnSelected" label="Selected">';
					for(key in def) {
						columnList += '<option data-icon=" fa-fw" value="' + def[key] + '" format="" class="select_' + def[key] + '" selected>' + def[key] + '</option>';
					}
					columnList += '</optgroup>';

					// list with other options
					columnList += '<optgroup class="columnNotSelected" label="Available">';
					columnList += '<option data-icon=" fa-fw" value="avg_clarity" format="" class="select_avg_clarity">avg_clarity</option>';

					for(key in data) {
						label = 
						columnList += '<option data-icon=" fa-fw" value="' + data[key] + '" format="" class="select_' + data[key] + '">' + data[key] + '</option>';
					}
					columnList += '</optgroup>';
					

					$('select.columns').html(columnList);
					
					$('select.columns').selectpicker('refresh');
					getResults();
					refreshColumns();
				}
			});
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

		
		if($('.graphViewButton').hasClass('hidden')) {
			$('.searchResults .checkAll').removeAttr('checked');
			$('.search .results').hide();
						
			// spoof category to support visualizations		
			var category = '#' + $('.search .documentType').val()[0] + '_tab';

			// TEMP FIX
			var category = '#all_tab';

			var availableVis = ['#relex-structured-sentence_tab','#fullvideo_tab','#metadatadescription_tab','#annotatedmetadatadescription_tab','#all_tab','#drawing_tab','#painting_tab'];
			if(availableVis.indexOf(category)>=0) { // do not update if there is no visualization for this document type	
				unitsChart = new unitsChartFacade(category, openModal, getSelection, updateSelection);
				// problem
				unitsChart.init(getTabFieldsQuery(),"");
			}
		} else {
			updateSelection();
		}
		
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
	var filters = '<td><input type="checkbox" class="checkAll" id="checkAll"/></td>';

	var columns = $('.columns').val();
	for(var i = 0; i < columns.length; i++) {

		var $column = $('.columns option[value="' + columns[i] + '"]');

		// move selected to selected group
		if(!$column.parent().hasClass('columnSelected')) {
			$column.appendTo('.columns .columnSelected');
		}

		identifiers += '<th class="sorting" data-vbIdentifier="' + columns[i] + '" data-query-key="orderBy[' + columns[i] + ']">' + $column.text() + '</th>';	
		
		// change filter based on format
		if($column.attr('format') == 'number') {
			filters += '<td data-vbIdentifier="' + columns[i] + '" style="width: 100px;">' +
			'<input class="input-sm form-control" type="text" data-query-key="match[' + $column.val() + ']" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />' +
			'<input class="input-sm form-control" type="text" data-query-key="match[' + $column.val() + ']" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" /></td>';
		} else if($column.attr('format') == 'time') {
			filters += '<td data-vbIdentifier="' + columns[i] + '" style="width: 200px;"><div class="input-daterange">' +
				'<input type="text" class="input-sm form-control" name="start" data-query-key="match[' + columns[i] + ']" data-query-operator=">=" style="width:49% !important; float:left;" placeholder="Start Date" />' +
				'<input type="text" class="input-sm form-control" name="end" data-query-key="match[' + columns[i] + ']" data-query-operator="=<" style="width:49% !important; float:right;" placeholder="End Date" />' +
				'</div></td>';		
		} else { // default filter is string matching
			filters += '<td><input class="input-sm form-control" type="text" data-query-key="match[' + columns[i] + ']" data-query-operator="like" placeholder="Filter" /></td>';
		}
	}

	// for each option in the selected list, check if it is still selectedCategory
	$('.columnSelected option:not(:selected)').prependTo('.columns .columnNotSelected');
	
	// refresh possible changes in column list
	$('.selectpicker').selectpicker('refresh');

	
	// update identifiers and filters
	$('.identifiers').html(identifiers);
	$('.inputFilters').html(filters);

	$("#checkAll").click(function () {
		$("input[name=rowchk]").prop('checked', $(this).prop('checked'));

		selectedRows = [];
		if($(this).prop('checked')) {	// Check all
			$.each($("input[name=rowchk]"), function(index, value) {
				selectedRows.push(value.id);
			});
		}
	});
}

// create template based on selected columns
var dynamicTemplate = function() {

	var template = '@{{#each documents}}<tr><td data-vbIdentifier="checkbox"><input type="checkbox" id="@{{ this._id }}" name="rowchk" value="@{{ this._id }}"></td>';

	var columns = $('.columns').val();
	for(var i = 0; i < columns.length; i++) {
		if(columns[i] == '_id') {
			// for the ID the best modal is applied through handlebars to show the invidial unit
			template += '<td data-vbIdentifier="id">@{{ unitModal this._id this.documentType }}</td>';
		} else if (columns[i].indexOf('content.') === 0) {

			// display based on actual content
			template += '<td data-vbIdentifier="id">@{{ dynamicField this.' + columns[i] + ' }}</td>';

		} else {
			// default display
			template += '<td data-vbIdentifier="id">@{{ this.' + columns[i] + ' }}</td>';
		}
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


    var modalTarget = modalAnchor.attr('data-target');

    $('#activeTabModal').remove();

    var query = modalAnchor.attr('data-modal-query');

    $.getJSON(baseApiURL + query, function(data) {

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

$(document).on('click', '.mediaselector', function (e) {
        e.stopPropagation();
    });


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
