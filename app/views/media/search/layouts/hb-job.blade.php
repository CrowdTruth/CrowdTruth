<div class="tab-pane" id="job_tab">	
	<div class='row'>
		<div class='tabOptions col-xs-12'>
			<div class='btn-group vbColumns' style="margin-left:5px;">
				<button type="button" class="btn btn-default openAllColumns">Open all columns</button>
				<button type="button" class="btn btn-default openDefaultColumns hidden">Open default columns</button>
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li role="presentation" class="dropdown-header">General</li>
					<li><a href="#" data-vb="show" data-vbSelector="checkbox"></i>Select</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="status"></i>Status and actions</a></li>						
					<li><a href="#" data-vb="show" data-vbSelector="job_id"></i>Job</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="job_type"></i>Type</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="created_at"></i>Created</a></li>	
					<li><a href="#" data-vb="hide" data-vbSelector="created_by"></i>Created By</a></li>
					<li role="presentation" class="divider"></li>
					<li role="presentation" class="dropdown-header">Content</li>
					<li><a href="#" data-vb="show" data-vbSelector="job_title"></i>Title</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="job_description"></i>Description</a></li>
					<li role="presentation" class="divider"></li>
					<li role="presentation" class="dropdown-header">Status</li>
					<li><a href="#" data-vb="show" data-vbSelector="completion"></i>Completion</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="running_time"></i>Run Time</a></li>
					<li><a href="#" data-vb="show" data-vbSelector="total_job_cost"></i>Cost</a></li>
					<li role="presentation" class="divider"></li>
					<li role="presentation" class="dropdown-header">Statistics</li>
					<li><a href="#" data-vb="hide" data-vbSelector="job_size"></i>Units</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="clarity"></i>Clarity</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="units_per_task"></i>units/mTask</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="req_ann_per_unit"></i>Workers/mTask Requested</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="total_ann"></i>Requested Judgements</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="current_no_ann"></i>Judgements</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="req_ann_per_worker"></i>mTasks/Worker limit</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="total_workers"></i>Workers</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="total_spam_workers"></i>Spammers</a></li>
					<li><a href="#" data-vb="hide" data-vbSelector="cost_per_task"></i>Cost/mTask</a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class='ctable-responsive'>	
	    <table class="table table-striped">
	        <thead data-query-key="&collection=temp&match[documentType]" data-query-value="job">
		        <tr>
		            <th data-vbIdentifier="checkbox" data-toggle="tooltip" data-placement="top" title="Check to select this row" style='min-width:60px;'>Select</th>
		            <th data-vbIdentifier="status" data-toggle="tooltip" data-placement="top" title="Mouseover for actions">Status</th>
		            <th class="sorting" data-vbIdentifier="job_id" data-query-key="orderBy[platformJobId]" data-toggle="tooltip" data-placement="top" title="ID of the job from the platform that ran it">Job</th>
		            <th class="sorting" data-vbIdentifier="job_title" data-query-key="orderBy[hasConfiguration.content.title]" data-toggle="tooltip" data-placement="top" title="Title of the job published on the platform">Title</th>
		            <th class="sorting" data-vbIdentifier="job_type" data-query-key="orderBy[type]" data-toggle="tooltip" data-placement="top" title="Task type for the Job">Type</th>
		            <th class="sorting" data-vbIdentifier="job_description" data-query-key="orderBy[hasConfiguration.content.description]" data-toggle="tooltip" data-placement="top" title="Descripton">Description</th>
		            <th class="sorting" data-vbIdentifier="job_size" data-query-key="orderBy[unitsCount]" data-toggle="tooltip" data-placement="top" title="The number of units in the job - <br /> set by the job definition">Units</th>
		            <th class="sorting" data-vbIdentifier="clarity" data-query-key="orderBy[avg_clarity]" data-toggle="tooltip" data-placement="top" title="Clarity of the results">Clarity</th>     
					<th class="sorting" data-vbIdentifier="units_per_task" data-query-key="orderBy[hasConfiguration.content.unitsPerTask]" data-toggle="tooltip" data-placement="top" title="Number of media units (e.g. sentences, images) to be presented in each micro-task - <br /> set by the job definition">units/mTask</th>
		            <th class="sorting" data-vbIdentifier="req_ann_per_unit" data-query-key="orderBy[hasConfiguration.content.workerunitsPerUnit]" data-toggle="tooltip" data-placement="top" title="Number of workers requested per micro-task - <br /> set by the job definition">Workers/mTask Requested</th>
		            <th class="sorting" data-vbIdentifier="total_ann" data-query-key="orderBy[expectedWorkerunitsCount]" data-toggle="tooltip" data-placement="top" title="Total number of judgements requested for the job - <br /> [# units] * [Workers/mTask Requested]">Requested Judgements</th>
				    <th class="sorting" data-vbIdentifier="current_no_ann" data-query-key="orderBy[workerunitsCount]" data-toggle="tooltip" data-placement="top" title="Number of judgements gathered so far - <br /> [# mTasks Complete Actual] * [units/mTask]">Judgements</th>
				    <th class="sorting" data-vbIdentifier="req_ann_per_worker" data-query-key="orderBy[hasConfiguration.content.workerunitsPerWorker]" data-toggle="tooltip" data-placement="top" title="Maximum number of micro-tasks per worker - <br /> set by the job definition">mTasks/Worker limit</th>
		            <th class="sorting" data-vbIdentifier="total_workers" data-query-key="orderBy[workersCount]" data-toggle="tooltip" data-placement="top" title="Number of workers who have completed at least one mTask">Workers</th>
				    <th class="sorting" data-vbIdentifier="total_spam_workers" data-query-key="orderBy[metrics.spammers.count]" data-toggle="tooltip" data-placement="top" title="Number of workers labelled as spam">Spammers</th>
				    <th class="sorting" data-vbIdentifier="cost_per_task" data-query-key="orderBy[hasConfiguration.content.reward]" data-toggle="tooltip" data-placement="top" title="Amount paid to each worker per micro-task, set by the job definition">Cost/mTask</th>
				    <th class="sorting" data-vbIdentifier="total_job_cost" data-query-key="orderBy[projectedCost]" data-toggle="tooltip" data-placement="top" title="Amount paid so far - <br /> [# mTasks Complete Actual] * [Cost/mTask]">Cost</th>
				    <th class="sorting" data-vbIdentifier="completion" data-query-key="orderBy[completion]" data-toggle="tooltip" data-placement="top" title="Percent of job complete so far">Completion</th>
				    <th class="sorting" data-vbIdentifier="running_time" data-query-key="orderBy[runningTimeInSeconds]" data-toggle="tooltip" data-placement="top" title="Amount of time the job has taken so far">Run Time</th>
		            <th class="sorting sorting_desc whiteSpaceNoWrap" data-vbIdentifier="created_at" data-query-key="orderBy[created_at]" style="min-width:220px; width:auto;" data-toggle="tooltip" data-placement="top" title="When the job was created in the framework">Created</th>
				    <th class="sorting whiteSpaceNoWrap" data-vbIdentifier="created_by" data-query-key="orderBy[user_id]">Created By</th>
		        </tr>
			<tr class="inputFilters">
				<td data-vbIdentifier="checkbox">
					<input type="checkbox" class="checkAll" />
				</td>
				<td data-vbIdentifier="status"><span style="width:130px; display:inline-block;"><small>Mouseover for actions</small></span></td>
				<td data-vbIdentifier="job_id">
					<input class="input-sm form-control" type='text' data-query-key="match[platformJobId]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="job_title">
					<input class="input-sm form-control" type='text' data-query-key="match[hasConfiguration.content.title]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="job_type">
					<input class="input-sm form-control" type='text' data-query-key="match[type]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="job_description">
					<input class="input-sm form-control" type='text' data-query-key="match[hasConfiguration.content.description]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="job_size">
					<input class="input-sm form-control" type='text' data-query-key="match[unitsCount]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[unitsCount]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="clarity">
					<input class="input-sm form-control" type='text' data-query-key="match[avg_clarity]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[avg_clarity]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="units_per_task">
					<input class="input-sm form-control" type='text' data-query-key="match[hasConfiguration.content.unitsPerTask]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[hasConfiguration.content.unitsPerTask]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="req_ann_per_unit">
					<input class="input-sm form-control" type='text' data-query-key="match[hasConfiguration.content.workerunitsPerUnit]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[hasConfiguration.content.workerunitsPerUnit]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="total_ann">
					<input class="input-sm form-control" type='text' data-query-key="match[expectedWorkerunitsCount]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[expectedWorkerunitsCount]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="current_no_ann">
					<input class="input-sm form-control" type='text' data-query-key="match[workerunitsCount]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[workerunitsCount]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="req_ann_per_worker">
					<input class="input-sm form-control" type='text' data-query-key="match[hasConfiguration.content.workerunitsPerWorker]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[hasConfiguration.content.workerunitsPerWorker]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="total_workers">
					<input class="input-sm form-control" type='text' data-query-key="match[workersCount]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[workersCount]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="total_spam_workers">
					<input class="input-sm form-control" type='text' data-query-key="match[metrics.spammers.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[metrics.spammers.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="cost_per_task">
					<input class="input-sm form-control" type='text' data-query-key="match[hasConfiguration.content.reward]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[hasConfiguration.content.reward]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="total_job_cost">
					<input class="input-sm form-control" type='text' data-query-key="match[projectedCost]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[projectedCost]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="completion">
					<input class="input-sm form-control" type='text' data-query-key="match[completion]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[completion]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="running_time">
					<input class="input-sm form-control" type='text' data-query-key="match[running_time]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[running_time]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="created_at">
						<div class="input-daterange">
					    <input type="text" class="input-sm form-control" name="start" data-query-key="match[created_at]" data-query-operator=">=" style="width:49% !important; float:left;" placeholder="Start Date" />
					    <input type="text" class="input-sm form-control" name="end" data-query-key="match[created_at]" data-query-operator="=<" style="width:49% !important; float:right;" placeholder="End Date" />
					</div>
				</td>
				<td data-vbIdentifier="created_by">
					<input class="input-sm form-control" type='text' data-query-key="match[user_id]" data-query-operator="like" />	
				</td>
			</tr>											        
	        </thead>
	        <tbody class='results'>											
				<script class='template' type="text/x-handlebars-template">
			        @{{#each documents}}
			        <tr class="text-center">
			        	<td data-vbIdentifier="checkbox"><input type="checkbox" id="@{{ this._id }}" name="rowchk" value="@{{ this._id }}"></td>
			            <td data-vbIdentifier="status" class="actiontd" style='padding: 0px;'>
			            	<div id="status@{{@index}}" class="currentstatus status-@{{this.status}}">@{{this.status}}</div>
							<div class="btn-group actionbar">
								<a class="btn btn-default btn-sm" href="/jobs2/duplicate/@{{this._id}}" data-toggle="tooltip" data-placement="top" title="Duplicate and edit job"><i class="fa fa-files-o"></i></a>
								<a class="btn btn-default btn-sm" href="/jobs2/refresh/@{{this._id}}" data-toggle="tooltip" data-placement="top" title="Refresh the job settings"><i class="fa fa-refresh"></i></a>

								@{{#if this.url}}
								    <a class="btn btn-default btn-sm" href="@{{this.url}}" target="_blank" data-toggle="tooltip" data-placement="top" title="Visit task"><i class="fa fa-external-link"></i></a>
								@{{/if}}
								@{{#is this.status 'unordered'}}
								    <a class="btn btn-default btn-sm" href="#" onclick="javascript:jobactions('@{{this._id}}', 'order', @{{@index}})"  id="order@{{@index}}" data-toggle="tooltip" data-placement="top" title="Order job on the platform. Warning: may take a long time for mTurk"><i class="fa fa-play"></i></a>
									<a class="btn btn-danger btn-sm" href="/jobs2/delete/@{{this.platformJobId}}" data-toggle="tooltip" data-placement="top" title="DELETE this job"><i class="fa fa-remove"></i></a>
								@{{/is}}
								@{{#is this.status 'running'}}
								    <a class="btn btn-default btn-sm" href="#" onclick="javascript:jobactions('@{{this._id}}', 'pause', @{{@index}})" data-toggle="tooltip" data-placement="top" title="Pause job"><i class="fa fa-pause" id="pause@{{@index}}"></i></a>
								    <a class="btn btn-default btn-sm" id="cancel@{{@index}}" href="#" onclick="javascript:jobactions('@{{this._id}}', 'cancel', @{{@index}})" data-toggle="tooltip" data-placement="top" title="Cancel job"><i class="fa fa-stop"></i></a>
								@{{/is}}
								@{{#is this.status 'paused'}}
								    <a class="btn btn-default btn-sm"  id="resume@{{@index}}" href="#" onclick="javascript:jobactions('@{{this._id}}', 'resume', @{{@index}})" data-toggle="tooltip" data-placement="top" title="Resume job"><i class="fa fa-play"></i></a>
								    <a class="btn btn-default btn-sm"  id="cancel@{{@index}}" href="#" onclick="javascript:jobactions('@{{this._id}}', 'cancel', @{{@index}})"data-toggle="tooltip" data-placement="top" title="Cancel job"><i class="fa fa-stop"></i></a>	
								@{{/is}}
							</div>
	
			            </td>
			            
			            <td data-vbIdentifier="job_id">
					<a class='testModal' data-modal-query="job=@{{this._id}}" data-api-target="{{ URL::to('api/analytics/job?') }}" data-target="#modalIndividualJob" data-toggle="tooltip" data-placement="top" title="Click to see the individual job page">
						@{{#ifarray this.platformJobId }} @{{/ifarray}}
					</a>
				    </td>
			            <td data-vbIdentifier="job_title">@{{ this.hasConfiguration.content.title }}</td>
			            <td data-vbIdentifier="job_type">@{{ this.type }}</td>
			            <td data-vbIdentifier="job_description">@{{ this.hasConfiguration.content.description }}</td>
			            <td data-vbIdentifier="job_size">@{{ this.unitsCount }}</td>
			            <td data-vbIdentifier="clarity">@{{ this.avg_clarity }}</td>
			            <td data-vbIdentifier="units_per_task">@{{ this.hasConfiguration.content.unitsPerTask }}</td>
			            <td data-vbIdentifier="req_ann_per_unit">@{{ this.hasConfiguration.content.workerunitsPerUnit }}</td>
			            <td data-vbIdentifier="total_ann">@{{ this.expectedWorkerunitsCount }}</td>
			            <td data-vbIdentifier="current_no_ann">@{{ this.workerunitsCount }}</td>
					    <td data-vbIdentifier="req_ann_per_worker">@{{ this.hasConfiguration.content.workerunitsPerWorker }}</td>
					    <td data-vbIdentifier="total_workers">@{{ this.workersCount }}</td>
					    <td data-vbIdentifier="total_spam_workers">@{{ this.metrics.spammers.count }}</td>
					    <td data-vbIdentifier="cost_per_task">@{{ toPrice this.hasConfiguration.content.reward }}</td>
					    <td data-vbIdentifier="total_job_cost">@{{ toPrice this.projectedCost }}</td>
					    <td data-vbIdentifier="completion" class="status-@{{this.status}}">@{{ createPercentage this.completion }}%</td>
					    <td data-vbIdentifier="running_time">@{{ duration this.runningTimeInSeconds }}</td>
			            <td data-vbIdentifier="created_at">@{{ this.created_at }}</td>
					    <td data-vbIdentifier="created_by">@{{ highlightSelf this.user_id }}</td>
			        </tr>
			        @{{/each}}
				</script>
	        </tbody>
	    </table>
	</div>	
	<style>
		.actionbar { 
		    -webkit-transition: opacity 1s ease-out;
		    opacity: 0; 
		    height: 0px;
		    overflow: hidden;
		    float:left;
			white-space:nowrap;
		}

		.actiontd:hover .actionbar {    
			opacity: 1;
		    height:auto;
		    float:none;
		}
		.actiontd:hover .currentstatus {
			display: none;
		}
		.btn-sm {
			padding: 6px 8px;
		}
	</style>

@include('media.search.layouts.hb-modalindividualjob')

					
</div>