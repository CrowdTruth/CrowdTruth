<div class="tab-pane" id="job_tab">	
	<div class='row'>
		<div class='tabOptions col-xs-12'>
			<div class='btn-group' style="margin-left:5px;">
				<button type="button" class="btn btn-default openAllColumns">Open all columns</button>
				<button type="button" class="btn btn-default openDefaultColumns hidden">Open default columns</button>
				<div class="btn-group vbColumns">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#" data-vb="show" data-vbSelector="checkbox"></i>Select</a></li>					
						<li><a href="#" data-vb="show" data-vbSelector="job_id"></i>Job ID</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="job_title"></i>Job Title</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="job_description"></i>Job Description</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="job_size"></i># Units</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="units_per_task"></i>units/mTask</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="req_ann_per_unit"></i>Workers/mTask Requested</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="total_ann"></i># Judgements Requested</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="current_no_ann"></i># Judgements Actual</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="req_ann_per_worker"></i>mTasks/Worker limit</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="total_workers"></i># Workers Actual</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="total_spam_workers"></i># Spammers Actual</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="cost_per_task"></i>Cost/mTask</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="total_job_cost"></i>Cost Actual</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="completion"></i>% Complete Actual</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="running_time"></i>Run Time Actual</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="created_at"></i>Created</a></li>					
					</ul>
				</div>	
			</div>
		</div>
	</div>
	<div class='ctable-responsive'>	
	    <table class="table table-striped">
	        <thead data-query-key="&collection=temp&match[documentType]" data-query-value="job">
		        <tr>
		            <th data-vbIdentifier="checkbox" data-toggle="tooltip" data-placement="top" title="Check to select this row">Select</th>
		            <th class="sorting" data-vbIdentifier="job_id" data-query-key="orderBy[hasConfiguration.content.jobId]" data-toggle="tooltip" data-placement="top" title="ID of the job from the platform that ran it">Job Id</th>
		            <th class="sorting" data-vbIdentifier="job_title" data-query-key="orderBy[hasConfiguration.content.title]" data-toggle="tooltip" data-placement="top" title="Title of the job published on the platform">Job Title</th>
		            <th class="sorting" data-vbIdentifier="job_description" data-query-key="orderBy[hasConfiguration.content.description]" data-toggle="tooltip" data-placement="top" title="Descripton">Job Description</th>
		            <th class="sorting" data-vbIdentifier="job_size" data-query-key="orderBy[unitsCount]" data-toggle="tooltip" data-placement="top" title="The number of units in the job - <br /> set by the job definition"># Units</th>
		            <th class="sorting" data-vbIdentifier="units_per_task" data-query-key="orderBy[hasConfiguration.content.unitsPerTask]" data-toggle="tooltip" data-placement="top" title="Number of media units (e.g. sentences, images) to be presented in each micro-task - <br /> set by the job definition">units/mTask</th>
		            <th class="sorting" data-vbIdentifier="req_ann_per_unit" data-query-key="orderBy[hasConfiguration.content.annotationsPerUnit]" data-toggle="tooltip" data-placement="top" title="Number of workers requested per micro-task - <br /> set by the job definition">Workers/mTask Requested</th>
		            <th class="sorting" data-vbIdentifier="total_ann" data-query-key="orderBy[expectedAnnotationsCount]" data-toggle="tooltip" data-placement="top" title="Total number of judgements requested for the job - <br /> [# units] * [Workers/mTask Requested]"># Judgements Requested</th>
				    <th class="sorting" data-vbIdentifier="current_no_ann" data-query-key="orderBy[annotationsCount]" data-toggle="tooltip" data-placement="top" title="Number of judgements gathered so far - <br /> [# mTasks Complete Actual] * [units/mTask]"># Judgements Actual</th>
				    <th class="sorting" data-vbIdentifier="req_ann_per_worker" data-query-key="orderBy[hasConfiguration.content.annotationsPerWorker]" data-toggle="tooltip" data-placement="top" title="Maximum number of micro-tasks per worker - <br /> set by the job definition">mTasks/Worker limit</th>
		            <th class="sorting" data-vbIdentifier="total_workers" data-query-key="orderBy[workersCount]" data-toggle="tooltip" data-placement="top" title="Number of workers who have completed at least one mTask"># Workers Actual</th>
				    <th class="sorting" data-vbIdentifier="total_spam_workers" data-query-key="orderBy[metrics.spammers.count]" data-toggle="tooltip" data-placement="top" title="Number of workers labelled as spam"># Spammers Actual</th>
				    <th class="sorting" data-vbIdentifier="cost_per_task" data-query-key="orderBy[hasConfiguration.content.reward]" data-toggle="tooltip" data-placement="top" title="Amount paid to each worker per micro-task, set by the job definition">Cost/mTask</th>
				    <th class="sorting" data-vbIdentifier="total_job_cost" data-query-key="orderBy[projectedCost]" data-toggle="tooltip" data-placement="top" title="Amount paid so far - <br /> [# mTasks Complete Actual] * [Cost/mTask]">Cost Actual</th>
				    <th class="sorting" data-vbIdentifier="completion" data-query-key="orderBy[completion]" data-toggle="tooltip" data-placement="top" title="Percent of job complete so far">% Complete Actual</th>
				    <th class="sorting" data-vbIdentifier="running_time" data-query-key="orderBy[runningTimeInSeconds]" data-toggle="tooltip" data-placement="top" title="Amount of time the job has taken so far">Run Time Actual</th>
		            <th class="sorting whiteSpaceNoWrap" data-vbIdentifier="created_at" data-query-key="orderBy[created_at]" style="min-width:220px; width:auto;" data-toggle="tooltip" data-placement="top" title="When the job was created in the framework">Created</th>			    
		        </tr>
			<tr class="inputFilters">
				<td data-vbIdentifier="checkbox">
					<input type="checkbox" class="checkAll" />
				</td>
				<td data-vbIdentifier="job_id">
					<input class="input-sm form-control" type='text' data-query-key="match[platformJobId]" data-query-operator=">" />
				</td>
				<td data-vbIdentifier="job_title">
					<input class="input-sm form-control" type='text' data-query-key="match[hasConfiguration.content.title]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="job_description">
					<input class="input-sm form-control" type='text' data-query-key="match[hasConfiguration.content.description]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="job_size">
					<input class="input-sm form-control" type='text' data-query-key="match[unitsCount]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[unitsCount]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="units_per_task">
					<input class="input-sm form-control" type='text' data-query-key="match[hasConfiguration.content.unitsPerTask]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[hasConfiguration.content.unitsPerTask]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="req_ann_per_unit">
					<input class="input-sm form-control" type='text' data-query-key="match[hasConfiguration.content.annotationsPerUnit]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[hasConfiguration.content.annotationsPerUnit]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="total_ann">
					<input class="input-sm form-control" type='text' data-query-key="match[expectedAnnotationsCount]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[expectedAnnotationsCount]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="current_no_ann">
					<input class="input-sm form-control" type='text' data-query-key="match[annotationsCount]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[annotationsCount]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="req_ann_per_worker">
					<input class="input-sm form-control" type='text' data-query-key="match[hasConfiguration.content.annotationsPerWorker]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[hasConfiguration.content.annotationsPerWorker]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
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
			</tr>											        
	        </thead>
	        <tbody class='results'>											
				<script class='template' type="text/x-handlebars-template">
			        @{{#each documents}}
			        <tr class="text-center">
			            <td data-vbIdentifier="checkbox"><input type="checkbox" id="@{{ this._id }}" name="rowchk" value="@{{ this._id }}"></td>
			            <td data-vbIdentifier="job_id">
					<a class='testModal' data-modal-query="job=@{{this._id}}" data-api-target="{{ URL::to('api/analytics/job?') }}" data-target="#modalIndividualJob" data-toggle="tooltip" data-placement="top" title="Click to see the individual job page">
						@{{ this.platformJobId }}
					</a>
				    </td>
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
					    <td data-vbIdentifier="completion">@{{ toFixed this.completion 2 }}</td>
					    <td data-vbIdentifier="running_time">@{{ this.running_time }}</td>
			            <td data-vbIdentifier="created_at">@{{ this.created_at }}</td>				    
			        </tr>
			        @{{/each}}
				</script>
	        </tbody>
	    </table>
	</div>	
	<div class='hidden' id='modalIndividualJob'>
		<script class='template' type="text/x-handlebars-template">
				<!-- Modal -->
				<div class="modal fade bs-example-modal-lg" id="activeTabModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelJob" aria-hidden="true">
				  <div class="modal-dialog modal-lg">
				    <div class="modal-content">
				      <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabelJob">Individual Worker Page</h4>
				      </div>
				      <div class="modal-body" >
					<div class="panel-group" id="accordion">
					  <div class="panel panel-default">
					    <div class="panel-heading clearfix">
					     <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
					      <h4 class="panel-title pull-left">
         						Job Information 
     					      </h4>  
					     </a>
						<div class='pull-right' style='width:300px; max-height:20px; margin-left:10px;'>      
						<div class="progress">
						 <div class="progress-bar" data-toggle="tooltip" data-placement="top" title="@{{#createPercentage this.infoStat.completion}}@{{/createPercentage}}% Complete" role="progressbar" aria-valuenow="@{{ this.infoStat.completion}}" aria-valuemin="0" aria-valuemax="1" style="width: @{{#createPercentage this.infoStat.completion}}@{{/createPercentage}}%">
						   <span data-toggle="tooltip" data-placement="top" title="@{{#createPercentage this.infoStat.completion}}@{{/createPercentage}}% Complete" class="sr-only">@{{ this.infoStat.completion}}% Complete (success)</span>
						 </div>
						</div>
						</div>	

					     <div class='pull-right'>
					     	@{{ this.infoStat.status}}
					     </div>	
					   </div>
					    <div id="collapseOne" class="panel-collapse collapse in">
					      <div class="panel-body">
						<div><strong>Platform Name: </strong> @{{ this.infoStat.softwareAgent_id }} </div>
						<div><strong data-toggle="tooltip" data-placement="top" title="CrowdTruth Id: @{{ this.infoStat._id }}"> Job ID: </strong> @{{ this.infoStat.platformJobId }} </div>
						<div><strong>Running Time: </strong> @{{#formatTime this.infoStat.runningTimeInSeconds }}@{{/formatTime}} </div>
						<div><strong>Creation Date: </strong> @{{ this.infoStat.startedAt }} </div>
						<div><strong>Finish Date: </strong> @{{ this.infoStat.finishedAt }} </div>
						<div><strong>Media Domain: </strong> @{{ this.infoStat.domain}} </div>
						<div><strong>Media Format: </strong> @{{ this.infoStat.format }} </div>
						<div><strong>Type: </strong> @{{ this.infoStat.jobConf.content.type }} </div>
						<div><strong>Title: </strong> @{{ this.infoStat.jobConf.content.title }} </div>
						<div><strong>Instructions: </strong> @{{ this.infoStat.jobConf.content.instructions }} </div>
					      </div>
					    </div>
					  </div>
					  <div class="panel panel-default">
					    <div class="panel-heading">
					     <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
					      <h4 class="panel-title">
						  Job Stats
					      </h4>
					     </a>
					    </div>
					    <div id="collapseTwo" class="panel-collapse collapse">
					      <div class="panel-body">
						<div><strong> @{{ this.infoStat.unitsCount }} Unit(s)  </strong></div>
						<div><strong> @{{ this.infoStat.workersCount }} Worker(s) </strong> </div>
						<div><strong> @{{ this.infoStat.annotationsCount }} Annotation(s) </strong>  </div>
						<div><strong> @{{ this.infoStat.metrics.filteredUnits.count }} Filtered Unit(s) </strong> </div>
						<div><strong> @{{ this.infoStat.metrics.spammers.count }} Filtered Worker(s) as Spammer(s) </strong> </div>
						<div><strong> @{{ this.infoStat.metrics.filteredAnnotations.count }} Filtered Annotation(s) </strong> </div>
						<hr/>
						<table style="width: 100%; text-align: center; align: center;" border="1" bordercolor="#C0C0C0" text-align="center">
						 <tr align="center">
						  <td colspan="12" align="center"> <strong> Aggregated Units </strong> </td>
						 </tr>
						 <tr>
						  <td text-align="center" colspan="6"> <strong> Mean </strong> </td>
						  <td text-align="center" colspan="6"> <strong> Stddev </strong> </td>
						 </tr>
						  <td> # Workers </td>	
						  <td> Max Rel Cos </td>	
						  <td> Norm Magnitude </td>
						  <td> Magnitude </td>
						  <td> Norm Rel Magnitude All </td>
						  <td> Norm Rel Magnitude </td>
						  <td> # Workers </td>	
						  <td> Max Rel Cos </td>	
						  <td> Norm Magnitude </td>
						  <td> Magnitude </td>
						  <td> Norm Rel Magnitude All </td>
						  <td> Norm Rel Magnitude </td>
						 </tr>
						 <tr>
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.mean.no_annotators.avg 2}} </td>	
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.mean.max_relation_Cos.avg 2}} </td>	
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.mean.norm_magnitude.avg 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.mean.magnitude.avg 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.mean.norm_relation_magnitude_all.avg 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.mean.norm_relation_magnitude.avg 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.stddev.no_annotators.avg 2}} </td>	
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.stddev.max_relation_Cos.avg 2}} </td>	
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.stddev.norm_magnitude.avg 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.stddev.magnitude.avg 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.stddev.norm_relation_magnitude_all.avg 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.stddev.norm_relation_magnitude.avg 2}} </td>
						 </tr>
						</table>
						<hr/>
						<div align="center">
						<table border="1" bordercolor="#C0C0C0" style="align: center;" class="noSortingTable">
						 <tr>
						  <td colspan="8"> <strong> Aggregated Workers </strong> </td>
						 </tr>
						 <tr>
						  <td colspan="4"> <strong> Mean </strong> </td>
						  <td colspan="4"> <strong> Stddev </strong> </td>
						 </tr>
						  <td> # Ann / Unit </td>	
						  <td> # of Units </td>	
						  <td> Avg. worker agreement </td>
						  <td> Worker Cosine </td>
						  <td> # Ann / Unit </td>	
						  <td> # of Units </td>	
						  <td> Avg. worker agreement </td>
						  <td> Worker Cosine </td>
						 </tr>
						 <tr>
						  <td> @{{ toFixed this.infoStat.metrics.aggWorker.mean.ann_per_unit.avg 2}} </td>	
						  <td> @{{ toFixed this.infoStat.metrics.aggWorker.mean.no_of_units.avg 2}} </td>	
						  <td> @{{ toFixed this.infoStat.metrics.aggWorker.mean.avg_worker_agreement.avg 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggWorker.mean.worker_cosine.avg 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggWorker.stddev.ann_per_unit.avg 2}} </td>	
						  <td> @{{ toFixed this.infoStat.metrics.aggWorker.stddev.no_of_units.avg 2}} </td>	
						  <td> @{{ toFixed this.infoStat.metrics.aggWorker.stddev.avg_worker_agreement.avg 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggWorker.stddev.worker_cosine.avg 2}} </td>						 </tr>
						</table>
						</div>
					      </div>
					    </div>
					  </div>
					  <div class="panel panel-default">
					    <div class="panel-heading">
					     <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
					      <h4 class="panel-title">
						  Job Units
					      </h4>
					     </a>
					    </div>
					    <div id="collapseThree" class="panel-collapse collapse">
					      <div class="panel-body">
						<table class="tablesorter table table-striped table-condensed" border="1" bordercolor="#C0C0C0">
						 <thead>
						  <tr>
						     <th class="header" rowspan="2">Unit Format</th>
    						     <th class="header" rowspan="2">Filtered</th>
						     <th class="header" colspan="6">Unit Metrics</th>
						     <th class="header" rowspan="2">Annotation Vector</th>
						  </tr>
						  <tr>
						    <th> # Workers </th>	
						    <th> Max Rel Cos </th>	
						    <th> Norm Magnitude </th>
						    <th> Magnitude </th>
						    <th> Norm Rel Magnitude All </th>
						    <th> Norm Rel Magnitude </th>
						  </tr>
						 </thead>
						 <tbody>
						  @{{#each this.infoStat.metrics.units.withoutSpam}} 
						  @{{type @key}}
						  <tr>
						    <td> @{{ @key }} </td>
						    @{{#inArray @root.infoStat.metrics.filteredUnits.list @key }}
						    <td> True </td>
						    @{{else}}
						    <td> False </td>
						    @{{/inArray}}
						    <td> @{{ toFixed no_annotators.avg 2}} </td>	
						    <td> @{{ toFixed max_relation_Cos.avg 2}} </td>	
						    <td> @{{ toFixed norm_magnitude.avg 2}} </td>
						    <td> @{{ toFixed magnitude.avg 2}} </td>
						    <td> @{{ toFixed norm_relation_magnitude_all.avg 2}} </td>
						    <td> @{{ toFixed norm_relation_magnitude.avg 2}} </td>
						    <td>
						    @{{#each @root.infoStat.results.withoutSpam}}
						    @{{#ifvalue ../key value=@key}}
						     @{{#each this}} 
						      <table border="1" bordercolor="#C0C0C0">
						       <tr> 
						       @{{#eachProperty this}}
  							<td> @{{#abrWords key}} @{{/abrWords}} </td>
						       @{{/eachProperty }}
							    </tr>
							    <tr> 
							     @{{#eachProperty this}}
  								<td>@{{value}} </td>
							     @{{/eachProperty }}
							    </tr>
							   </table>
							  @{{/each}}
						       @{{/ifvalue}}
    							 @{{/each}}
						    </td>
						  </tr>
						  @{{/each}}
						 </tbody>
						</table>
						
					      </div>
					    </div>
					  </div>
					  <div class="panel panel-default">
					    <div class="panel-heading">
					     <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
					      <h4 class="panel-title">
						  Job Workers 
					      </h4>
					     </a>
					    </div>
					    <div id="collapseFour" class="panel-collapse collapse">
					      <div class="panel-body">
						<table id="myIndividualWorkerTable" class="tablesorter table table-striped table-condensed" border="1" bordercolor="#C0C0C0"> 
						<thead> 
						<tr> 
						    <th class="header" rowspan="2">Worker Id</th>
						    <th class="header" rowspan="2">Platform</th>
						    <th class="header" rowspan="2">Platform Quality</th>
						    <th class="header" colspan="2">Worker Metrics Across all Jobs</th>
						    <th class="header" colspan="4">Worker Metrics Across Job</th>
						</tr> 
						<tr>
						    <th> Avg. Worker Agreement </th>
						    <th> Avg. Worker Cosine </th>
						    <th> Avg. Worker Agreement</th>	
						    <th> Worker Cosine </th>	
						    <th> # Ann / Unit </th>
						    <th> # Ann Units </th>
						  </tr>
						</thead>
						<tbody>
						 @{{#each this.infoStat.metrics.workers.withFilter}} 
						 @{{type @key}}
						 <tr>
						  <td> @{{ @key }} </td>
						  @{{#each @root.infoStat.workers }}
						    @{{#ifvalue ../key value=@key}}
						     <td> @{{ softwareAgent_id}} </td>  
						     <td> @{{ cfWorkerTrust}} </td> 
						     <td> @{{ toFixed cache.avg_agreement 2}} </td> 
						     <td> @{{ toFixed cache.avg_cosine 2}} </td> 
						   @{{/ifvalue}}
						  @{{/each}} 
						   <td> @{{ toFixed avg_worker_agreement.avg 2 }} </td>
						   <td> @{{ toFixed worker_cosine.avg 2 }} </td>
						   <td> @{{ toFixed ann_per_unit.avg 2 }} </td>
						   <td> @{{ toFixed no_of_units.avg 2 }} </td>
						  </tr>
						  @{{/each}}  	
						 </tbody>
						</table>
					      </div>
					    </div>
					  </div>
					</div>
				      </div>
				    </div>
				  </div>
				</div>
		</script>

	</div>						
</div>
