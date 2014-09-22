
<!-- START process_nav -->   

						<ul class="nav nav-tabs" id="processtabs">
							<li{{ (Request::segment(2) == 'batch' ? ' class="active"' : '') }} title='batch'>{{ link_to('jobs/selectbatch', "1. Batch") }}</li>
							<li{{ (Request::segment(2) == 'template' ? ' class="active"' : '') }} title='template'>{{ link_to('jobs/template', "2. Template") }}</li>
							<li{{ (Request::segment(2) == 'platform' ? ' class="active"' : '') }} title='platform'>{{ link_to('jobs/platform', "3. Platform") }}</li>
							<li{{ (Request::segment(2) == 'details' ? ' class="active"' : '') }} title='details'>{{ link_to('jobs/details', "4. Job Details") }}</li>
							<?php $count = 0; ?>
							@if(isset($jobconf) && isset($jobconf['platform']))
								@foreach ($jobconf['platform'] as $p)
								<?php $count++; $link = "jobs/$p"; $ptoupper = strtoupper($p); ?>
							<li{{ (Request::segment(2) == $p ? ' class="active"' : '') }} title="{{$p}}">{{ link_to($link, "5.$count Platform: $ptoupper") }}</li>
								@endforeach
							@endif
							<li{{ (Request::segment(2) == 'submit' ? ' class="active"' : '') }} title='submit'>{{ link_to('jobs/submit', "5. Submit") }}</li>
							<a href='/jobs/clear-task' class="btn btn-danger pull-right">Reset form</a></li>

						</ul>


<!-- END process_nav   -->   
<!-- TODO: put this in a better place. -->
@section('end_javascript')
{{ javascript_include_tag('handlebarsjs-2.0.js') }}
{{ javascript_include_tag('handlebars.swag.js') }}
{{ javascript_include_tag('bootstrap-select.js') }}
{{ javascript_include_tag('bootstrap-datepicker.js') }}
{{ javascript_include_tag('jquery.tablesorter.min.js') }}
{{ javascript_include_tag('jquery.tablesorter.widgets.min.js') }}
{{ javascript_include_tag('generalsearch_manifest') }}

<link rel="stylesheet" type="text/css" href="/custom_assets/bootstrap-select.min.css">

<script>
$(document).ready(function(){
	$("#processtabs > li").click(function(event){
		if($(".jobconf").prop("action").length > 0) {
			event.preventDefault();
	       $(".jobconf").prop("action", "/jobs/form-part/" + $(this).prop('title')).submit();
		}
	});

	
	  // highlight terms based on tags
  Swag.addHelper('highlightSelf', function(user, self, options) {
    
	if(user == '{{Auth::user()->_id}}') {
		user = '<span class="highlightSelf">' + user + '</span>';
	}
	
	return new Handlebars.SafeString(user);
  });
	
calculate();

@if (isset($treejson))
	$('#jstree').jstree({ 'core' : {
	"theme" : {
      "variant" : "large",
      "icons" : false
    },
    "multiple":false,	
    'data' : {{ $treejson }},

} }).on('changed.jstree', function (e, data) {
	var parent =  data.instance.get_node(data.selected).parent;
	var self = data.instance.get_node(data.selected).id;

	if(!(data.instance.is_parent(data.selected))){
		if(data.instance.get_node(data.selected).original.exists == true) {
			$('#templatetext').html('<div class="well">Here is a preview for this template. The variables between @{{...}} will be replaced with values from the batch.</div><iframe id ="question" src="/templates/' +parent + '/' + self + '.html" seamless sandbox="allow-scripts" width="890" height="600"></iframe>');
		} else {
			$('#templatetext').html('<div class="well">There is no preview available for this template</div>');
		}
		$("#template").val(parent + '/' + self);
	}
    //data.instance.get_node(data.selected).text
  });
@endif



Swag.registerHelpers();

$('.selectpicker').selectpicker();
var xhr;
var unitsChart;
var oldTabKey;
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
	if(!$('.listViewButton').hasClass('hidden'))
	{
		$('.listViewButton').click();
	}

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

	// if($(this).attr('href') == "#all_tab")
	// 	return false;

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

$('.select_relex-structured-sentence').click();
$('.documentTypesNav').find('#relex-structured-sentence_nav a').click();
$('.graphViewButton').click();
var workerList = localStorage.getItem("unitList");
if(workerList !=  null) {
    workerList = JSON.parse(workerList);
    for(var iterWorker in workerList){
        updateSelection(workerList[iterWorker]);
    }
    localStorage.removeItem("unitList");
}

getResults();

});


    function calculate(){
        var reward = $('#reward').val();
        var workerunitsPerUnit = $('#workerunitsPerUnit').val();
        var unitsPerTask = $('#unitsPerTask').val();
        var expirationInMinutes = $('#expirationInMinutes').val();
        var unitsCount = {{ $unitscount or 0}};
		var costPerUnit = (reward/unitsPerTask)*workerunitsPerUnit;
		var rph = (reward/expirationInMinutes)*60;

        var el = document.getElementById('costPerUnit');
	    if(el) el.innerHTML= "$" + costPerUnit.toFixed(2);

	    var el0 = document.getElementById('minRewardPerHour');
	    if(el0) el0.innerHTML= "$" + rph.toFixed(2);
	    
	    if(unitsCount > 0) {
	    	var totalCost = (reward/unitsPerTask)*(unitsCount*workerunitsPerUnit);
	        var el1 = document.getElementById('totalCost');
	        if(el1) el1.innerHTML= "<strong>$" + totalCost.toFixed(2)  + "</strong>";
    	}
    } 

$("[data-toggle=tooltip]").tooltip();
</script>
@endsection


