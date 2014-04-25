<div class="tab-pane" id="job_tab">	
	<div class='row'>
		<div class='searchOptions col-xs-12'>
			<div class="btn-group pull-left">
				<button type="submit" class="btn btn-danger createBatchButton">Save selection</button>
				<a href='#' class="btn btn-warning toCSV">Export results to CSV</a>	
			</div>
			<div class="btn-group pull-right vbColumns" style="margin-left:5px;">
				<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
				Visible Columns <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li><a href="#" data-vb="show" data-vbSelector="checkbox"></i>Select</a></li>					
					<li><a href="#" data-vb="show" data-vbSelector="job_id"></i>Job ID</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="job_title"></i>Job Title</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="job_description"></i>Job Description</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="job_size"></i>Job Size</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="units_per_task"></i>Units per Task</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="req_ann_per_unit"></i>Requested # Annotations per Unit</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="units_per_task"></i>Total # Annotations</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="current_no_ann"></i>Current # Annotations</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="req_ann_per_worker"></i>Requested # Annotations per Worker</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="total_workers"></i>Total # Workers</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="total_spam_workers"></i>Total # Spam Workers</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="cost_per_task"></i>Cost per Task</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="total_job_cost"></i>Total Job Cost</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="completion"></i>Job Completion</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="running_time"></i>Running Time</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="created_at"></i>Created At</a></li>					
				</ul>
			</div>
			<select name="search_limit" class="selectpicker pull-right">
				<option value="10">10 Records per page</option>
				<option value="25">25 Records per page</option>
				<option value="50">50 Records per page</option>
				<option value="100">100 Records per page</option>
				<option value="1000">1000 Records per page</option>
			</select>
		</div>
	</div>
	<div class='row'>
		<div class='col-xs-12'>
			<div class="btn-group pull-left searchStats">
			</div>
			<div class='cw_pagination text-right'>
			</div>
		</div>		
	</div>
    <table class="table table-striped">
        <thead data-query-key="&collection=temp&match[documentType]" data-query-value="job">
	        <tr>
	            <th data-vbIdentifier="checkbox">Checkbox</th>
	            <th class="sorting" data-vbIdentifier="job_id" data-query-key="orderBy[hasConfiguration.content.jobId]">Job Id</th>
	            <th class="sorting" data-vbIdentifier="job_title" data-query-key="orderBy[hasConfiguration.content.title]">Job Title</th>
	            <th class="sorting" data-vbIdentifier="job_description" data-query-key="orderBy[hasConfiguration.content.description]">Job Description</th>
	            <th class="sorting" data-vbIdentifier="job_size" data-query-key="orderBy[unitsCount]">Job Size</th>
	            <th class="sorting" data-vbIdentifier="units_per_task" data-query-key="orderBy[hasConfiguration.content.unitsPerTask]">Units per Task</th>
	            <th class="sorting" data-vbIdentifier="req_ann_per_unit" data-query-key="orderBy[hasConfiguration.content.annotationsPerUnit]">Requested # Annotations per Unit</th>
	            <th class="sorting" data-vbIdentifier="total_ann" data-query-key="orderBy[expectedAnnotationsCount]">Expected # Annotations</th>
			    <th class="sorting" data-vbIdentifier="current_no_ann" data-query-key="orderBy[annotationsCount]">Current # Annotations</th>
			    <th class="sorting" data-vbIdentifier="req_ann_per_worker" data-query-key="orderBy[hasConfiguration.content.annotationsPerWorker]">Requested # Annotations per Worker</th>
	            <th class="sorting" data-vbIdentifier="total_workers" data-query-key="orderBy[workersCount]">Total # Workers</th>
			    <th class="sorting" data-vbIdentifier="total_spam_workers" data-query-key="orderBy[metrics.spammers.count]">Total # Spam Workers</th>
			    <th class="sorting" data-vbIdentifier="cost_per_task" data-query-key="orderBy[hasConfiguration.content.reward]">Cost per Task</th>
			    <th class="sorting" data-vbIdentifier="total_job_cost" data-query-key="orderBy[projectedCost]">Total Job Cost</th>
			    <th class="sorting" data-vbIdentifier="completion" data-query-key="orderBy[completion]">Job Completion</th>
			    <th class="sorting" data-vbIdentifier="running_time" data-query-key="orderBy[running_time]">Running Time</th>
	            <th class="sorting whiteSpaceNoWrap" data-vbIdentifier="created_at" data-query-key="orderBy[created_at]" style="min-width:220px; width:auto;">Created At</th>			    
	        </tr>
		<tr class="inputFilters">
			<td data-vbIdentifier="checkbox">
				<input type="checkbox" class="checkAll" />
			</td>
			<td data-vbIdentifier="job_id">
				<input type='text' data-query-key="match[hasConfiguration.content.jobId]" data-query-operator=">" />
			</td>
			<td data-vbIdentifier="job_title">
				<input type='text' data-query-key="match[hasConfiguration.content.title]" data-query-operator="like" />
			</td>
			<td data-vbIdentifier="job_description">
				<input type='text' data-query-key="match[hasConfiguration.content.description]" data-query-operator="like" />
			</td>
			<td data-vbIdentifier="job_size">
				<input type='text' data-query-key="match[unitsCount]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				<input type='text' data-query-key="match[unitsCount]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
			</td>
			<td data-vbIdentifier="units_per_task">
				<input type='text' data-query-key="match[hasConfiguration.content.unitsPerTask]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				<input type='text' data-query-key="match[hasConfiguration.content.unitsPerTask]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
			</td>
			<td data-vbIdentifier="req_ann_per_unit">
				<input type='text' data-query-key="match[hasConfiguration.content.annotationsPerUnit]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				<input type='text' data-query-key="match[hasConfiguration.content.annotationsPerUnit]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
			</td>
			<td data-vbIdentifier="total_ann">
				<input type='text' data-query-key="match[expectedAnnotationsCount]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				<input type='text' data-query-key="match[expectedAnnotationsCount]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
			</td>
			<td data-vbIdentifier="current_no_ann">
				<input type='text' data-query-key="match[annotationsCount]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				<input type='text' data-query-key="match[annotationsCount]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
			</td>
			<td data-vbIdentifier="req_ann_per_worker">
				<input type='text' data-query-key="match[hasConfiguration.content.annotationsPerWorker]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				<input type='text' data-query-key="match[hasConfiguration.content.annotationsPerWorker]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
			</td>
			<td data-vbIdentifier="total_workers">
				<input type='text' data-query-key="match[workersCount]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				<input type='text' data-query-key="match[workersCount]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
			</td>
			<td data-vbIdentifier="total_spam_workers">
				<input type='text' data-query-key="match[metrics.spammers.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				<input type='text' data-query-key="match[metrics.spammers.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
			</td>
			<td data-vbIdentifier="cost_per_task">
				<input type='text' data-query-key="match[hasConfiguration.content.reward]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				<input type='text' data-query-key="match[hasConfiguration.content.reward]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
			</td>
			<td data-vbIdentifier="total_job_cost">
				<input type='text' data-query-key="match[projectedCost]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				<input type='text' data-query-key="match[projectedCost]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
			</td>
			<td data-vbIdentifier="completion">
				<input type='text' data-query-key="match[completion]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				<input type='text' data-query-key="match[completion]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
			</td>
			<td data-vbIdentifier="running_time">
				<input type='text' data-query-key="match[running_time]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				<input type='text' data-query-key="match[running_time]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
			</td>
			<td data-vbIdentifier="created_at">
<!-- 					<div class="input-daterange">
				    <input type="text" class="input-sm form-control" name="start" data-query-key="match[created_at]" data-query-operator=">=" style="width:49% !important; float:left;" placeholder="Start Date" />
				    <input type="text" class="input-sm form-control" name="end" data-query-key="match[created_at]" data-query-operator="=<" style="width:49% !important; float:right;" placeholder="End Date" />
				</div> -->
			</td>
		</tr>											        
        </thead>
        <tbody class='results'>											
			<script class='template' type="text/x-handlebars-template">
		        @{{#each documents}}
		        <tr class="text-center">
		            <td data-vbIdentifier="checkbox"><input type="checkbox" id="@{{ this._id }}" name="rowchk" value="@{{ this._id }}"></td>
		            <td data-vbIdentifier="job_id">@{{ this.hasConfiguration.content.jobId }}</td>
		            <td data-vbIdentifier="job_title">@{{ this.hasConfiguration.content.title }}</td>
		            <td data-vbIdentifier="job_description">@{{ this.hasConfiguration.content.description }}</td>
		            <td data-vbIdentifier="job_size">@{{ this.unitsCount }}</td>
		            <td data-vbIdentifier="units_per_task">@{{ this.hasConfiguration.content.unitsPerTask }}</td>
		            <td data-vbIdentifier="req_ann_per_unit">@{{ this.hasConfiguration.content.annotationsPerUnit }}</td>
		            <td data-vbIdentifier="total_ann">@{{ this.expectedAnnotationsCount }}</td>
		            <td data-vbIdentifier="current_no_ann">@{{ this.annotationsCount }}</td>
				    <td data-vbIdentifier="req_ann_per_worker">@{{ this.hasConfiguration.content.annotationsPerWorker }}</td>
				    <td data-vbIdentifier="total_workers">@{{ this.workersCount }}</td>
				    <td data-vbIdentifier="total_spam_workers">@{{ this.metrics.spammers.count }}</td>
				    <td data-vbIdentifier="cost_per_task">@{{ this.hasConfiguration.content.reward }}</td>
				    <td data-vbIdentifier="total_job_cost">@{{ this.projectedCost }}</td>
				    <td data-vbIdentifier="completion">@{{ this.completion }}</td>
				    <td data-vbIdentifier="running_time">@{{ this.running_time }}</td>
		            <td data-vbIdentifier="created_at">@{{ this.created_at }}</td>				    
		        </tr>
		        @{{/each}}
			</script>
        </tbody>
    </table>											
</div>