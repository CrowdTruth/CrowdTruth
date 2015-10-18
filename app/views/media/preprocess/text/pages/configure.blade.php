@extends('media.preprocess.layouts.default')

@section('head')

{{ stylesheet_link_tag('bootstrap-select.css') }}
<script>
{{--
	Load document preview -- document preview will display a preview of how the file 
	looks (how many columns it has and how they look like). Document preview is generated
	by the server and displayed by displayDocumentPreview.
--}}
	function previewTable() {
		formUrl = $("#docPreviewForm").attr("action");
		formData = $("#docPreviewForm").serialize();

		$.ajax({
			type: "POST",
			url: formUrl,
			data: formData,
			success: function(data) {
				displayDocumentPreview(data);
			}
		});
	}

{{--
	Display the results from previewTable. Generated data is displayed on HTML Document preview table.
--}}
	function displayDocumentPreview(data) {
	    var table = $("#docPreviewTable");
	    table.find("tr").remove();
	    
	    if(data.headers.length>0) {
		    rowStr = "<tr>";
		    for(col in data.headers) {
			    rowStr += "<th>"+data.headers[col]+"</th>";
			}
		    rowStr += "</tr>";
		    table.append(rowStr);
		}
		document.columns = data.headers;

	    for(row in data.content) {
		    rowStr = "<tr>";
		    for(col in data.content[row]) {
			    rowStr += "<td>"+data.content[row][col]+"</td>";
			}
		    rowStr += "</tr>";
		    table.append(rowStr);
	    }
	}

{{--
	Generate the DIV element which holds an individual document property. 
	This div element contains the name of the property,  the function to be applied 
	and the column such function should be applied to.
--}}
	function getPropertyDiv(parentGroupId, propName){
	    propId = parentGroupId + '_' + propName;
	    divStr = '' +
	        '		<div class="panel panel-default" id="' + propId + '_div">' +
			'		<div class="panel-heading">' +
			'			<div class="row">' +
			'			<div class="col-xs-4" style="padding-top:7.5px;">' +
			'				<i class="fa fa-edit"></i> ' + propName +
			'				<a class="text-danger" href="#" name="' + propId + '_close" id="' + propId + '_close"><i class="fa fa-remove"></i></a>' +
			'			</div>' +
			'			<div class="col-xs-8">' +
			'				<select class="selectpicker" data-container="body" name="' + propId + '_function" id="' + propId + '_function">' +		
			'          			<option value="text">Text</option>' + 
			'          			<option value="number">Number</option>' + 
			'					<optgroup label="Function">' +
{{-- Load the available functions --}}
	        @foreach ($functions as $function)
			'          			<option value="{{ $function->getName() }}">{{ $function->getName()	 }}</option>' + 
			@endforeach
			'					</optgroup>' +
	        '				</select> ' +
			'			</div>' +
			'			<div class="clearfix"></div>' +
			'		</div>' +
			'		</div>' +
	        '		<div class="panel-body">' +
			'			 <input type="hidden" name="' + propId + '_propName" id="' + propId + '_propName" value="' + propId + '" class="propertyName"/>' +
	        '            <input type="hidden" name="' + propId + '_propParent" id="' + propId + '_propParent" value="' + parentGroupId + '"/>' +	        
	        '			<div id="' + propId + '_params">' +
	        '			</div>' +
	        '		</div>' +
	        '		</div>';
	    return divStr;
	}

{{--
	Create a new SELECT element listing all columns available. 
--}}
	function getColumnsSelector(selectorName) {
		colsSelect = '<select class="form-control" name="' + selectorName + '" id="' + selectorName + '">';
		// Load the available columns
    	for(col in document.columns) {
    		colsSelect += '  <option value="' +col + '">' + document.columns[col] + '</option>';
    	}
    	colsSelect += '</select>'; 
        return colsSelect;
	}

{{--
	Create a new SELECT element listing all available properties.
--}}
	function getPropertySelector(selectorName) {
	    inputs = [];
	    $('.propertyName').each(function(i, obj) {
	         inputs.push($(this).val());
	    });

	    propSelect = '<select class="form-control" name="' + selectorName + '" id="' + selectorName + '">';
		for(idx in inputs) {
			inputValue = inputs[idx];
			inputName = inputValue.split("_").join(".");
			propSelect += '  <option value="' + inputValue + '">' + inputName + '</option>';
		}
		propSelect += '</select>';
		return propSelect;
	}

{{--
	Create a new SELECT element listing all available groups.
--}}
	function getGroupSelector(selectorName) {
	    inputs = [];
	    $('.groupName').each(function(i, obj) {
	         inputs.push($(this).val());
	    });

	    groupSelect = '<select class="form-control" name="' + selectorName + '" id="' + selectorName + '">';
		for(idx in inputs) {
			inputValue = inputs[idx];
			inputName = inputValue.split("_").join(".");
			groupSelect += '  <option value="' + inputValue + '">' + inputName + '</option>';
		}
		groupSelect += '</select>';
		return groupSelect;
	}
	
{{--
	Generate the DIV element for a group. This div element contains the group name
	and allows for the creation of subgroups and properties inside it. 
--}}
	function getGroupDiv(parentGroupId, groupName) {
		groupId = parentGroupId + "_" + groupName;
	    divStr = '' +
	        '		<div class="panel panel-default" id="' + groupId + '_div">' +
			'		<div class="panel-heading">' +
			'			<div class="row">' +
			'				<div class="col-xs-3"">' +
			'					<i class="fa fa-folder-open"></i> ' + groupName +
			'					<a class="text-danger" href="#" name="' + groupId + '_close" id="' + groupId + '_close"><i class="fa fa-remove"></i></a>' +
			'				</div>' +
			'			</div>' +
			'		</div>' +
	        '		<div class="panel-body" id="' + groupId + '_props">' +
	        '		</div>' +
			'		<div class="panel-footer input-group">' +			
	        '  	 		<input type="hidden" name="' + groupId + '_groupName" id="' + groupId + '_groupName" value="' + groupId + '" class="groupName"/>' +
			'		    <input type="hidden" name="' + groupId + '_groupParent" id="' + groupId + '_groupParent" value="' + parentGroupId + '"/>' +
			'			<input class="form-control" type="text" value="" id="' + groupId + '_newName" placeholder="Add new" />' +
			'			<span class="input-group-btn">' +
			'				<button type="button" class="btn btn-default" id="' + groupId + '_newProp"><i class="fa fa-edit"></i> Property</button>' +
			'				<button type="button" class="btn btn-default" id="' + groupId + '_newGroup"><i class="fa fa-folder-open"></i> Group</button>' +
			'			</span>' +
			'		</div>' +
	        '		</div>';
	    return divStr;
	}

{{--
	Launch the addNewProperty function after button has been clicked
--}}
	function newPropertyAction(parentGroupId) {
		propName = $("#" + parentGroupId + "_newName").val();
		$("#" + parentGroupId + "_newName").val("");
		return addNewProperty(parentGroupId, propName);
	}

{{--
	Create a new property DIV as a child of the given parent group. 
--}}
	function addNewProperty(parentGroupId, propName) {
	  props = parentGroupId + "_props";
	  propId = parentGroupId + "_" + propName;
	  // Add property div 
	  $("#" + props).append(getPropertyDiv(parentGroupId, propName));
	  
	  	// load selectpicker for new elements	
		$('.selectpicker').selectpicker('refresh');
	  
	  // Add actions to buttons on the div 
	  $("#" + propId + "_close").click(function(){
	      $(this).closest(".panel").remove();
		  return false;
	  });
	  $("#" + propId + "_function").change(function(){
		  functionName = $(this).val();
		  propId = this.id.replace("_function",  "");
	      selectFunction(functionName, propId);
	  });
	  $("#" + propId + "_function").change();
	  doPreview();
	  return false;
    }

{{--
	Launch the addNewGroup function after button has been clicked
--}}
	function newGroupAction(parentGroupId) {
		groupName = $("#" + parentGroupId + "_newName").val();
		$("#" + parentGroupId + "_newName").val("");
		return addNewGroup(parentGroupId, groupName);
	}

{{--
	Create a new group DIV as a child of the given parent group. 
--}}
	function addNewGroup(parentGroupId, groupName) {
		props = parentGroupId + "_props";
		
		// Add group div 
		$("#" + props).append(getGroupDiv(parentGroupId, groupName));
		
		// Add actions to buttons on the div
		$("#" + groupId + "_newProp").click(function(){
			groupId1 = this.id.replace("_newProp",  "");
			newPropertyAction(groupId1);
		});
		$("#" + groupId + "_newGroup").click(function(){
			groupId1 = this.id.replace("_newGroup",  "");
			newGroupAction(groupId1);
		});
		$("#" + groupId + "_close").click(function(){
		    $(this).closest(".panel").remove();
			return false;
		});
		doPreview();
	}

{{--
	Create a DIV containing the HTML code to capture the properties for a given 
	function. The DIV uses the given propId to label properties fields.
--}}
	function selectFunction(functionName, propId) {
		divName = propId + "_params";
	    switch(functionName) {
			case 'text': divHtml = textPreprocessor(propId); break;
			case 'number': divHtml = numberPreprocessor(propId); break;
  		  @foreach ($functions as $function)
	    	case '{{ $function->getName() }}': divHtml = {{ $function->getParameterJSFunctionName() }}(propId); break;
		  @endforeach
	    }
    	$('#' + divName).html(divHtml);
	}

{{--
	Post theForm to perform a given postAction. Once the post action is successfully 
	completed, the successFunction is called.
--}}
	function makePost(postAction, successFunction) {
		$('#postAction').val(postAction);
		formUrl = $("#theForm").attr("action");
		formData = $("#theForm,#docPreviewForm").serialize();
		$.ajax({
			type: "POST",
			url: formUrl,
			data: formData,
			success: successFunction
		});
	}

{{--
	Perform 'Read file' action.
--}}
	function doPreview() {
		$("#delimiter-form").val($('#delimiter').val());
		$("#separator-form").val($('#separator').val());
		makePost('processPreview', function(data) {
			$("#contentPreview").html(data);
		});
	}

	function doSubmit() {
		// Copy values from #docPreviewForm to #theForm
		$("#delimiter-form").val($('#delimiter').val());
		$("#separator-form").val($('#separator').val());
		$('#postAction').val('process');
	}

{{--
	Perform 'save configuration' action.
--}}
	function saveConfiguration() {
		makePost('saveConfig', function(data) {
			alert(data['status']);
		});
	}

{{--
	Load given configuration -- initialize Content structure as required.
--}}
	function loadConfig(config) {
		// Select 'File settings' as required
		$('#useHeaders').prop('checked', config["useHeaders"]);
		$('#delimiter').val(config["delimiter"]);
		$('#separator').val(config["separator"]);
				
		// Add groups as required
		for (n in config['groups']) {
			parent = config['groups'][n]['parent'];
			name = config['groups'][n]['name'];
			addNewGroup(parent, name);
		}

		// Add properties as required
		for (n in config['props']) {
			parent = config['props'][n]['parent'];
			name = config['props'][n]['name'];
			fun = config['props'][n]['function'];
			params = config['props'][n]['values'];
			
			addNewProperty(parent, name);
			propName = parent + '_' + name;

			// Select function
			$('#' + propName + '_function').val(fun);
			$('.selectpicker').selectpicker('refresh');
			selectFunction(fun, propName);

			// Load function parameters
			for (key in params) {
				value = params[key];
				$('#' + propName + '_' + key).val(value);
			}
		}
	}
	</script>

<!-- START getParameterJSFunction's -->
@foreach ($functions as $function) 
{{ $function->getParameterJSFunction() }} 
@endforeach
<!-- FINISH getParameterJSFunction's -->
@stop

@section('relexContent')

<!-- START status messages -->
@if (isset($status['error']))
	<div class="panel panel-danger">
		<div class="panel-heading">
			<h4><i class="fa fa-exclamation-triangle fa-fw"></i>Error</h4>
		</div>
		<div class="panel-body CW_messages">
			<ul class="list-group">
				<li class="list-group-item"><span class='message'> {{ $status['error'] }} </li>
			</ul>
		</div>
	</div>
@endif

@if(isset($status['success']))
	<div class="panel panel-success">
		<div class="panel-heading">
			<h4><i class="fa fa-check fa-fw"></i>Success</h4>
		</div>
		<div class="panel-body CW_messages">
			<ul class="list-group">
			<li class="list-group-item"><span class='message'> {{ $status['success'] }} </li>
			</ul>
		</div>
	</div>
@endif
<!-- End status messages -->

<!-- START preprocess/text/configure -->
<div class="page-header">
	<p>
		Document: <b>{{$docTitle}}</b>
	</p>
</div>
	
{{ Form::open(array('action' => 'preprocess\TextController@postConfigure', 'name' => 'docPreviewForm', 'id' => 'docPreviewForm', 'class' => 'form-horizontal')) }}
{{ Form::hidden('URI', $URI) }}
{{ Form::hidden('postAction', 'tableView') }}
<div class="panel panel-default">
	<div class="panel-heading">
		Read File Content
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="form-group">
			{{ Form::label('delimiter', 'Text delimiter:', [ 'class' => 'col-md-3 control-label' ]) }}
				<div class='col-xs-3'>
					<select class='form-control docPreview' id="delimiter" name="delimiter" />
						<option value='"'>"</option>
						<option value="'">'</option>
					</select>
				</div>
			</div>
			<div class="form-group">
			{{ Form::label('separator', 'Seperated by', [ 'class' => 'col-md-3 control-label' ]) }}
				<div class='col-xs-3'>
					<select class='form-control docPreview' id="separator" name="separator" />
						<option value=','>Comma</option>
						<option value=';'>Semicolon</option>
						<option value=':'>Colon</option>
						<option value='	'>Tab</option>
						<option value=' '>Space</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-xs-offset-3 col-xs-6">
					{{ Form::checkbox('useHeaders', 'tick', true, [ 'class' => 'docPreview' ]) }} First row contains headers
				</div>
			</div>
		</div>
		<hr />
	</div>
	
	<div class="col-xs-5">
		<div class="panel panel-default">
			<div class="panel-heading">
				Input
			</div>
			<pre style="height: 130px; margin:0px; overflow: auto; border: none; word-wrap: normal;">{{ $docPreview }}</pre>
		</div>
	</div>
	
	<div class="col-xs-7">
		<div class="panel panel-default">
			<div class="panel-heading">
				Output
			</div>	
			<div style="height: 130px; overflow: auto;">
				<table name="docPreviewTable" id="docPreviewTable">
				@if($previewTable!=null)
					<thead>
						<tr>
						@foreach ($previewTable['headers'] as $column)
							<th>{{ $column }}</th> 
						@endforeach
						</tr>
					</thead>
					<tbody>
						@foreach ($previewTable['content'] as $row)
						<tr>
							@foreach ($row as $column)
							<td>{{ str_limit($column, 30) }}</td>
							@endforeach
						</tr>
						@endforeach
					</tbody>
				@endif
				</table>
			</div>
		</div>
	</div>
	
	<div class="clearfix"></div>
</div>
{{ Form::close() }}

<!-- BEGIN DYNAMIC STRUCTURE FORM  -->
{{ Form::open(array('action' => 'preprocess\TextController@postConfigure', 'name' => 'theForm', 'id' => 'theForm', 'class' => 'form-horizontal' )) }}
{{ Form::hidden('URI', $URI) }}
{{ Form::hidden('postAction', 'preview', [ 'id' => 'postAction' ]) }}
{{ Form::hidden('delimiter', '', [ 'id' => 'delimiter-form' ]) }}
{{ Form::hidden('separator', '', [ 'id' => 'separator-form' ]) }}

<div class="panel panel-default">
	<div class="panel-heading">
		Content Structure
	</div>
	<div class="panel-body">	
		<div class="row">
			<div class="form-group">
				<label for="document" class="col-sm-3 control-label">Document Type</label>
				<div class="col-sm-5">
					@if(count($docTypes)>0)
					<select name="document" id="document" class="selectpicker pull-left show-tick" title="Select a Document" data-show-subtext="true" data-container="body">
						<option data-hidden="true"></option>
						<option value="new">Create New</option>					
						<option data-divider="true"></option>
						@foreach($docTypes as $dc)
							<option value="{{ $dc }}">{{ $dc }}</option>
						@endforeach
					</select>
					@else
						<input type="text" name="new_doctype" id="new_doctype" class="form-control" placeholder="Name" />
					@endif
					<p class='help-block'>This is an identifier for the content of the document. E.g. tweets, events</p>
				</div>
			</div>
		</div>
		<hr />
	</div>

	<div class="col-xs-7">
		<div class="panel panel-default">
			<div class="panel-heading">
				Structure
			</div>
			<div class="panel-body">
				<div class="" id="root_props"></div>
			</div>
			<div class="panel-footer input-group">
				<input class='form-control' type="text" value="" id="root_newName" placeholder="Add new" />
				<input type="hidden" name="root_groupName" id="root_groupName" value="root"/>
				<span class="input-group-btn">
					<button type="button" class="btn btn-default" id="root_newProp"><i class="fa fa-edit"></i> Property</button>
					<button type="button" class="btn btn-default" id="root_newGroup"><i class="fa fa-folder-open"></i> Group</button>
				</span>
			</div>
		</div>
	</div>
	<div class="col-xs-5">
		<div class="panel panel-default">
			<div class="panel-heading">
				Output
			</div>
			<pre name='contentPreview' id='contentPreview' style="border: none; overflow: auto; word-wrap: normal; margin:0px;"></pre>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

<div class="panel panel-default">
	<div class="panel-footer">
		<button class='btn btn-primary' onClick="doSubmit()"><i class="fa fa-gear"></i> Save Content</button>
	</div>
</div>

{{ Form::close() }}	
<!-- END DYNAMIC STRUCTURE FORM  -->

<!-- STOP preprocess/text/configure -->
@stop


<style>
.bootstrap-select {
	margin-bottom:0px !important;
}
#docPreviewTable {
    border-collapse: collapse;
    border-style: hidden;
	font-size:12px;
}
#docPreviewTable td {
	white-space: nowrap;
    border: 1px solid black;
}
#docPreviewTable th {
    border: 1px solid black;
	text-align: center;
	background-color: #e8e8e8;
	padding:5px;
}
</style>

@section('end_javascript')
{{ javascript_include_tag('bootstrap-select.js') }}
<script>
	// TODO: Couldn't this be added dynamically? 
	$(document).ready(function(){
		$('.selectpicker').selectpicker();
		$("#root_newProp").click(function(){
		  newPropertyAction("root");
		});
		$("#root_newGroup").click(function(){
		  newGroupAction("root");
		});
		
		previewTable();
		
		$('.docPreview').change(function(){
			previewTable();
		});
		
		$('#root_props').on('change', 'select', function() {
			doPreview();
		});
		
		$('#root_props').on('change', 'input', function() {
			doPreview();
		});

		@if($configuration!=null)
			// Load known columns and configuration if a configuration exists.
			document.columns = {{ json_encode($previewTable['headers']) }};
			config = {{ json_encode($configuration) }};
			loadConfig(config);
		@endif
	});
</script>
@stop
