<div class="tab-pane" id="crowdagents_tab">	
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
						<li><a href="#" data-vb="show" data-vbSelector="actions"></i>Actions</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="worker_id"></i>Worker ID</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="worker_platform"></i>Platform</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="worker_location"></i>Country</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="active_since"></i>Active Since</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="last_seen"></i>Last Seen</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="no_diff_media_types"></i># Media Types</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="no_diff_job_types"></i># Job Types</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="no_jobs"></i># Jobs </a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="no_diff_media_domains"></i># Media Domains</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="no_diff_media_formats"></i># Media Formats</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="total_annotations"></i># Judgments</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="no_jobs_identified_spammer"></i># Jobs Spammed</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="flagged"></i>Blocked</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="internal_worker_avg_agr"></i>Avg. Agreement</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="internal_worker_avg_cos"></i>Avg. Cosine</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="platform_worker_quality"></i>Platform Worker Quality</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="no_sent_messages"></i># Sent Messages</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class='ctable-responsive'>		
	    <table class="table table-striped">
	        <thead data-query-key="collection" data-query-value="crowdagents">
		        <tr>
		            <th data-vbIdentifier="checkbox" data-toggle="tooltip" data-placement="top" title="Check to select this row">Select</th>
		            <th data-vbIdentifier="actions" data-toggle="tooltip" data-placement="top" title="Block or message a single worker">Actions</th>
		            <th class="sorting" data-vbIdentifier="worker_id" data-query-key="orderBy[platformAgentId]" data-toggle="tooltip" data-placement="top" title="ID of the worker on their platform">Worker Id</th>
		            <th class="sorting" data-vbIdentifier="worker_platform" data-query-key="orderBy[softwareAgent_id]" data-toggle="tooltip" data-placement="top" title="Platform (e.g. AMT, Crowdflower) the worker is from">Platform</th>
		            <th class="sorting" data-vbIdentifier="worker_location" data-query-key="orderBy[country]" data-toggle="tooltip" data-placement="top" title="Country the worker is from">Country</th>
		            <th class="sorting" data-vbIdentifier="active_since" data-query-key="orderBy[created_at]" data-toggle="tooltip" data-placement="top" title="Date first micro-task was completed">Active Since</th>
		            <th class="sorting" data-vbIdentifier="last_seen" data-query-key="orderBy[updated_at]" data-toggle="tooltip" data-placement="top" title="Date last micro-task was completed">Last Seen</th>
		            <th class="sorting" data-vbIdentifier="no_diff_media_types" data-query-key="orderBy[cache.mediaTypes.distinct]" data-toggle="tooltip" data-placement="top" title="Number of different media types worker has completed microtasks for"># Media Types</th>
		            <th class="sorting" data-vbIdentifier="no_diff_job_types" data-query-key="orderBy[cache.jobTypes.distinct]" data-toggle="tooltip" data-placement="top" title="Number of different job types worker has completed microtasks for"># Job Types</th>
			    <th class="sorting" data-vbIdentifier="no_jobs" data-query-key="orderBy[cache.jobTypes.count]" data-toggle="tooltip" data-placement="top" title="Number of different job types worker has completed microtasks for"># Jobs</th>
			    <th class="sorting" data-vbIdentifier="no_diff_media_domains" data-query-key="orderBy[cache.mediaDomains.distinct]" data-toggle="tooltip" data-placement="top" title="Number of different domains worker has completed microtasks for"># Domains</th>
			    <th class="sorting" data-vbIdentifier="no_diff_media_formats" data-query-key="orderBy[cache.mediaFormats.distinct]" data-toggle="tooltip" data-placement="top" title="Number of different media formats worker has completed microtasks for"># Formats</th>
		            <th class="sorting" data-vbIdentifier="total_annotations" data-query-key="orderBy[cache.annotations.count]" data-toggle="tooltip" data-placement="top" title="The number of judgements completed by the worker, which is also the number of units the worker has completed work for"># Judgements</th>
			    <th class="sorting" data-vbIdentifier="no_jobs_identified_spammer" data-query-key="orderBy[cache.spammer.count]" data-toggle="tooltip" data-placement="top" title="The number of jobs in which worker was identified as low quality or spam"># Jobs Spammed</th>
				    <th class="sorting" data-vbIdentifier="flagged" data-query-key="orderBy[cache.flagged]" data-toggle="tooltip" data-placement="top" title="Whether the worker was blocked from future microtasks on the platform">Blocked</th>
				    <th class="sorting" data-vbIdentifier="internal_worker_avg_agr" data-query-key="orderBy[cache.avg_agreement]" data-toggle="tooltip" data-placement="top" title="CrowdTruth Average Worker Agreement score. Higher scores indicate better quality workers.">Avg. Agreement</th>
				    <th class="sorting" data-vbIdentifier="internal_worker_avg_cos" data-query-key="orderBy[cache.avg_cosine]" data-toggle="tooltip" data-placement="top" title="CrowdTruth Average Cosine Similarity.  Higher Scores indicate better quality workers.">Avg. Cosine</th>
				    <th class="sorting" data-vbIdentifier="platform_worker_quality" data-query-key="orderBy[cfWorkerTrust]" data-toggle="tooltip" data-placement="top" title="Worker Quality score or label from the platform">Platform Worker Quality</th>
				    <th class="sorting" data-vbIdentifier="no_sent_messages" data-query-key="orderBy[cache.sentMessagesToWorkers.count]" data-toggle="tooltip" data-placement="top" title="Messages sent to worker"># Sent Messages</th>
		        </tr>
			<tr class="inputFilters">
				<td data-vbIdentifier="checkbox">
					<input type="checkbox" class="checkAll" />
				</td>
				<td data-vbIdentifier="actions">
				</td>
				<td data-vbIdentifier="worker_id">
					<input class="input-sm form-control" type='text' data-query-key="match[platformAgentId]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="worker_platform">
					<input class="input-sm form-control" type='text' data-query-key="match[softwareAgent_id]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="worker_location">
					<input class="input-sm form-control" type='text' data-query-key="match[country]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="active_since">
					<input class="input-sm form-control" type='text' data-query-key="match[created_at]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="last_seen">
					<input class="input-sm form-control" type='text' data-query-key="match[updated_at]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="no_diff_media_types">
					<input class="input-sm form-control" type='text' data-query-key="match[cache.mediaTypes.distinct]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[cache.mediaTypes.distinct]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="no_diff_job_types">
					<input class="input-sm form-control" type='text' data-query-key="match[cache.jobTypes.distinct]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[cache.jobTypes.distinct]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="no_jobs">
					<input class="input-sm form-control" type='text' data-query-key="match[cache.jobTypes.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[cache.jobTypes.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="no_diff_media_domains">
					<input class="input-sm form-control" type='text' data-query-key="match[cache.mediaDomains.distinct]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[cache.mediaDomains.distinct]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="no_diff_media_formats">
					<input class="input-sm form-control" type='text' data-query-key="match[cache.mediaFormats.distinct]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[cache.mediaFormats.distinct]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="total_annotations">
					<input class="input-sm form-control" type='text' data-query-key="match[cache.annotations.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[cache.annotations.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="no_jobs_identified_spammer">
					<input class="input-sm form-control" type='text' data-query-key="match[cache.spammer.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[cache.spammer.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="flagged">
					<input class="input-sm form-control" type='text' data-query-key="match[cache.flagged]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="internal_worker_avg_agr">
					<input class="input-sm form-control" type='text' data-query-key="match[cache.avg_agreement]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[cache.avg_agreement]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="internal_worker_avg_cos">
					<input class="input-sm form-control" type='text' data-query-key="match[cache.avg_cosine]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[cache.avg_cosine]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="platform_worker_quality">
					<input class="input-sm form-control" type='text' data-query-key="match[cfWorkerTrust]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[cfWorkerTrust]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="no_sent_messages">
					<input class="input-sm form-control" type='text' data-query-key="match[cache.sentMessagesToWorkers.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[cache.sentMessagesToWorkers.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
			</tr>											        
	        </thead>
	        <tbody class='results'>								
				<script class='template' type="text/x-handlebars-template">
			        @{{#each documents}}
			        <tr class="text-center">
			        	<td data-vbIdentifier="checkbox"><input type="checkbox" id="@{{ this._id }}" name="rowchk" value="@{{ this._id }}"></td>
			        	<td data-vbIdentifier="actions">
							<div class="btn-group">
								<a class="btn btn-default btn-sm testModal" data-static="@{{ this._id }}" data-target="#modalMessage"><i class="fa fa-envelope-o" data-toggle="tooltip" data-placement="top" title="Send message"></i></a>
								<a class="btn btn-default btn-sm testModal" data-static="@{{ this._id }}" data-target="#modalBlock"><i class="fa fa-flag-o" data-toggle="tooltip" data-placement="top" title="Block worker"></i></a>
							</div>

			        	</td>
			            <td data-vbIdentifier="worker_id">
					<a class='testModal' id='@{{ this.platformAgentId }}' data-modal-query="agent=@{{this._id}}" data-api-target="{{ URL::to('api/analytics/worker?') }}" data-target="#modalIndividualWorker" data-toggle="tooltip" data-placement="top" title="Click to see the individual worker page">
						@{{ this.platformAgentId }}
					</a>
				    </td>
			            <td data-vbIdentifier="worker_platform">@{{ this.softwareAgent_id }}</td>
				    @{{#if this.country}}
			            <td data-vbIdentifier="worker_location">@{{ this.country }}</td>
				    @{{else}}
				    <td data-vbIdentifier="worker_location"> USA </td>
				    @{{/if}}
			            <td data-vbIdentifier="active_since">@{{ this.created_at }}</td>
			            <td data-vbIdentifier="last_seen">@{{ this.updated_at }}</td>
				    <td data-vbIdentifier="no_diff_media_types" data-toggle="tooltip" data-placement="top" title="@{{#each this.cache.mediaTypes.types }}@{{ @key }}<br />@{{/each}}">@{{ this.cache.mediaTypes.distinct }}</td>
			            <td data-vbIdentifier="no_diff_job_types" data-toggle="tooltip" data-placement="top" title="@{{#each this.cache.jobTypes.types }}@{{ @key }}<br />@{{/each}}">@{{ this.cache.jobTypes.distinct }}</td>
				    <td data-vbIdentifier="no_jobs">@{{ this.cache.jobTypes.count }}</td>
				    <td data-vbIdentifier="no_diff_media_domains" data-toggle="tooltip" data-placement="top" title="@{{#each this.cache.mediaDomains.domains }}@{{ @key }}<br />@{{/each}}">@{{ this.cache.mediaDomains.distinct }}</td>
					    <td data-vbIdentifier="no_diff_media_formats" data-toggle="tooltip" data-placement="top" title="@{{#each this.cache.mediaFormats.formats }}@{{ @key }}<br />@{{/each}}">@{{ this.cache.mediaFormats.distinct }}</td>
						
						@{{#if this.cache.annotations.count}}
						<td data-vbIdentifier="total_annotations" data-toggle="tooltip" data-placement="top" title="Spam: @{{ this.cache.annotations.spam }} &#xA; Non-Spam: @{{ this.cache.annotations.nonspam }}">
							@{{ this.cache.annotations.count }}
						</td>
							@{{else}}
						<td data-vbIdentifier="total_annotations" data-toggle="tooltip" data-placement="top" title="Spam: 0 &#xA; Non-Spam: 0">0</td>
						@{{/if}}

				    	<td data-vbIdentifier="no_jobs_identified_spammer" data-toggle="tooltip" data-placement="top" title="@{{#each this.cache.spammer.jobs }}@{{ this }}<br />@{{/each}}">@{{ this.cache.spammer.count }}</td>
					    <td data-vbIdentifier="flagged">@{{ this.cache.flagged }}</td>
					    <td data-vbIdentifier="internal_worker_avg_agr">@{{ toFixed this.cache.avg_agreement 2}}</td>
					    <td data-vbIdentifier="internal_worker_avg_cos">@{{ toFixed this.cache.avg_cosine 2}}</td>
					    <td data-vbIdentifier="platform_worker_quality">@{{ toFixed this.cfWorkerTrust 2 }}</td>
					    <td data-vbIdentifier="no_sent_messages">@{{ this.cache.sentMessagesToWorkers.count }}</td>
					</tr>
			        @{{/each}}
				</script>
	        </tbody>
	    </table>
	</div>	

<div class='hidden' id='modalMessage'>

		<script class='template' type="text/x-handlebars-template">
				<!-- Modal -->
				<div class="modal fade" id="activeTabModal">
				  <div class="modal-dialog">
				    <div class="modal-content">
				      <form id="messageform" class="ajaxform" name="input" action="/api/actions/message" method="post">
				      <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				        <h4 class="modal-title">Send message</h4>
				      </div>
				      <div class="modal-body">
				      	
						<input class="form-control" type="text" rel="static-val" name="messageto" id="messageto" placeholder="To (comma separated)" required /><br>
						<input class="form-control" type="text" name="messagesubject" id="messagesubject" placeholder="Subject" required /><br>
						<textarea class="form-control" name="messagecontent" placeholder="Message" rows="6" required></textarea>
						
						
				      </div>
				      <div class="modal-footer">
				        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				        <input type="submit" class="btn btn-primary" />
				      </div>
				      </form>
				    </div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
		</script>

	</div>								
	<div class='hidden' id='modalBlock'>

		<script class='template' type="text/x-handlebars-template">
				<!-- Modal -->
				<div class="modal fade" id="activeTabModal">
				  <div class="modal-dialog">
				    <div class="modal-content">
				      <form id="messageform" class="ajaxform" name="input" action="/api/actions/block" method="post">
				      <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				        <h4 class="modal-title">Block worker</h4>
				      </div>
				      <div class="modal-body">
				      	<p>
				      	Please write the reason for blocking worker <b><span rel="static-html"></span></b> below.
				      	</p>
				      	<input type="hidden" rel="static-val" name="workerid">
					<textarea class="form-control" name="blockmessage" placeholder="Message" rows="6" required></textarea>
						
						
				      </div>
				      <div class="modal-footer">
				        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				        <input type="submit" class="btn btn-primary" />
				      </div>
				      </form>
				    </div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
		</script>
	</div>						

	<div class='hidden' id='modalIndividualWorker'>

		<script class='template' type="text/x-handlebars-template">
				<!-- Modal -->
				<div class="modal fade bs-example-modal-lg" id="activeTabModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelWorker" aria-hidden="true">
				  <div class="modal-dialog modal-lg">
				    <div class="modal-content">
				      <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabelWorker">Individual Worker Page</h4>
				      </div>
				      <div class="modal-body" >
					<div class="panel-group" id="accordion">
					  <div class="panel panel-default">
					    <div class="panel-heading">
					     <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
					      <h4 class="panel-title">
						  Worker Information 
					      </h4>
					     </a>
					    </div>
					    <div id="collapseOne" class="panel-collapse collapse in">
					      <div class="panel-body">
						<div><strong>Platform Name: </strong> @{{ this.infoStat.softwareAgent_id }} </div>
						<div><strong data-toggle="tooltip" data-placement="top" title="CrowdTruth Id: @{{ this.infoStat._id }}"> Crowdagent ID: </strong> @{{ this.infoStat.platformAgentId }} </div>
						<div><strong>Active Since: </strong> @{{ this.infoStat.created_at }} </div>
						<div><strong>Last Seen: </strong> @{{ this.infoStat.updated_at }} </div>
						@{{#if this.infoStat.country}}
						<div><strong>Location: </strong> @{{ this.infoStat.country }} </div>
						@{{else}}
						<div><strong>Location: </strong> USA </div>
						@{{/if}}
						<div><strong data-toggle="tooltip" data-placement="top" title="Job Id(s) as Spammer: @{{ this.infoStat.cache.spammer.jobs }}">Current Status: </strong> marked as spammer in <strong>@{{ this.infoStat.cache.spammer.count }}</strong> job(s) </div>
						<div><strong>Flagged: </strong> @{{ this.infoStat.cache.flagged }} </div>
					      </div>
					    </div>
					  </div>
					  <div class="panel panel-default">
					    <div class="panel-heading">
					     <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
					      <h4 class="panel-title">
						  Worker Stats
					      </h4>
					     </a>
					    </div>
					    <div id="collapseTwo" class="panel-collapse collapse">
					      <div class="panel-body">
						<div><strong data-toggle="tooltip" data-placement="top" title="@{{#each this.infoStat.cache.mediaDomains.domains }}@{{ @key }}<br />@{{/each}}"> @{{ this.infoStat.cache.mediaDomains.distinct }} Distinct Media Domain(s) </strong></div>
						<div><strong data-toggle="tooltip" data-placement="top" title="@{{#each this.infoStat.cache.mediaTypes.types }}@{{ @key }}<br />@{{/each}}"> @{{ this.infoStat.cache.mediaTypes.distinct }} Distinct Media Type(s) </strong> </div>
						<div><strong data-toggle="tooltip" data-placement="top" title="@{{#each this.infoStat.cache.mediaFormats.formats }}@{{ @key }}<br />@{{/each}}"> @{{ this.infoStat.cache.mediaFormats.distinct }} Distinct Media Format(s) </strong>  </div>
						<div><strong data-toggle="tooltip" data-placement="top" title="@{{#each this.infoStat.cache.jobTypes.types }}@{{ @key }}<br />@{{/each}}"> @{{ this.infoStat.cache.jobTypes.distinct }} Distinct Job Type(s) </strong> </div>
						<div><strong> @{{ this.infoStat.cache.jobTypes.count }} Job(s) as Contributor</strong> </div>
						<div><strong data-toggle="tooltip" data-placement="top" title="# Spam Annotations: @{{ this.infoStat.cache.annotations.spam }} </br> # NonSpam Annotations: @{{ this.infoStat.cache.annotations.nonspam }}"> @{{ this.infoStat.cache.annotations.count }} Annotation(s) in Total </strong> </div>
						<div><strong data-toggle="tooltip" data-placement="top" title="Messages: @{{ this.infoStat.cache.sentMessagesToWorkers.messages }}"> @{{ this.infoStat.cache.sentMessagesToWorkers.count}} Message(s) Sent to This Worker </strong></div>
						<hr/>
						<table style="width: 100%" border="1" bordercolor="#C0C0C0" text-align="center">
						 <tr text-align="center">
						  <td> <strong>  </strong> </th>
						  <td text-align="center"> <strong> Across worker jobs </strong> </th>
						  <td text-align="center"> <strong> Across CrowdTruth jobs </strong> </th>
						 </tr>
						 <tr>
						  <td> <strong> AVG. Worker Agreement</strong></td> 
						  <td> <strong> @{{ toFixed this.infoStat.cache.avg_agreement 2 }} </strong></td>
						  <td> <strong> @{{ toFixed this.infoStat.avgAgreementAcrossJobs 2 }} </strong> </td>
						 </tr>
					    	 <tr>
						  <td> <strong> AVG. Worker Cosine </strong> </td> 
						  <td> <strong> @{{ toFixed this.infoStat.cache.avg_cosine 2 }} </td>
						  <td> <strong> @{{ toFixed this.infoStat.avgCosineAcrossJobs 2 }} </strong> </td>
						 </tr>
						</table>
					     	</br><div align="center"><strong>Platform Score: @{{ toFixed this.infoStat.cfWorkerTrust 2 }} </strong></div>
					      </div>
					    </div>
					  </div>
					  <div class="panel panel-default">
					    <div class="panel-heading">
					     <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
					      <h4 class="panel-title">
						  Worked on Jobs
					      </h4>
					     </a>
					    </div>
					    <div id="collapseThree" class="panel-collapse collapse">
					      <div class="panel-body">
						<table class="tablesorter table table-striped table-condensed" border="1" bordercolor="#C0C0C0">
						 <thead>
						  <tr>
						    <th class="header" rowspan="3"><strong>Job Id</strong></th>
						    <th class="header" colspan="4"><strong>Worker Metrics</strong></th>
						    <th class="header" colspan="8"><strong>Workers Metrics Across Job</strong></th>
						    <th class="header" rowspan="3"><strong>Status</strong></th>
						  </tr>
						  <tr>
						    <th class="header" rowspan="2">Avg Agr</td>
						    <th class="header" rowspan="2"># Ann / Unit</td>
						    <th class="header" rowspan="2">Cosine</td>
						    <th class="header" rowspan="2"># Ann Units</td>
						    <th class="header" colspan="4"><strong>Mean</strong></td>
						    <th class="header" colspan="4"><strong>Stddev</strong></td>
						  </tr>
						  <tr>
						    <th class="header">Avg Agr</td>
						    <th class="header"># Ann / Unit</td>
						    <th class="header">Cosine</td>
						    <th class="header"># Ann Units</td>
						    <th class="header">Avg Agr</td>
						    <th class="header"># Ann / Unit</td>
						    <th class="header">Cosine</td>
						    <th class="header"># Ann  Units</td>
						  </tr>
						 </thead>
						 <tbody>
						  @{{#each this.jobContent}} 
						  <tr>
						    <td> @{{ platformJobId }} </td>
							@{{#each metrics.workers.withFilter}}
							<td> @{{ toFixed avg_worker_agreement.avg 2 }} </td>
							<td> @{{ toFixed ann_per_unit.avg 2 }} </td>
						    	<td> @{{ toFixed worker_cosine.avg 2 }} </td>
						    	<td> @{{ toFixed no_of_units.avg 0 }} </td>
							@{{/each}}
						    <td> @{{ toFixed metrics.aggWorker.mean.avg_worker_agreement.avg 2}} </td>
						    <td> @{{ toFixed metrics.aggWorker.mean.ann_per_unit.avg 2}} </td>
						    <td> @{{ toFixed metrics.aggWorker.mean.worker_cosine.avg 2}} </td>
						    <td> @{{ toFixed metrics.aggWorker.mean.no_of_units.avg 0}} </td>
						    <td> @{{ toFixed metrics.aggWorker.stddev.avg_worker_agreement.avg 2}} </td>
						    <td> @{{ toFixed metrics.aggWorker.stddev.ann_per_unit.avg 2}} </td>
						    <td> @{{ toFixed metrics.aggWorker.stddev.worker_cosine.avg 2}} </td>
						    <td> @{{ toFixed metrics.aggWorker.stddev.no_of_units.avg 0}} </td>
						    @{{#inArray ../infoStat.cache.spammer.jobs this.platformJobId }}
							<td> Spammer </td>
						    @{{else}}
							<td> Non Spammer </td>
						    @{{/inArray}}
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
						  Worked on Units
					      </h4>
					     </a>
					    </div>
					    <div id="collapseFour" class="panel-collapse collapse">
					      <div class="panel-body">
						<table id="myIndividualWorkerTable" class="tablesorter table table-striped table-condensed" border="1" bordercolor="#C0C0C0"> 
						<thead> 
						<tr> 
						    <th class="header">Unit Format</th>
						    <th class="header">Job Title</th>
						    <th class="header">Unit Clarity</th>
						    <th class="header">Worker Annotation Vector</th>
						    <th class="header">Unit Vector</th>
						</tr> 
						</thead>
						<tbody>
						 @{{#each this.annotationContent}} 
						 @{{#each annotationType}} 
						 <tr>
						  <td data-toggle="tooltip" data-placement="top" title="CrowdTruth ID: @{{ ../_id}} </br> Domain: @{{ ../unitContent.domain }} </br> Sentence: @{{ ../unitContent.content.sentence.formatted}} </br> Term1: @{{ ../unitContent.content.terms.first.formatted }} </br> Term2: @{{ ../unitContent.content.terms.second.formatted }} </br> Relation: @{{ ../unitContent.content.relation.noPrefix }}"> @{{ ../unitContent.documentType }} </td>
						  <td> @{{ job_info.jobConf.content.title}} </td>  
						  @{{#each job_info.metrics.units.withoutSpam}}
						   @{{#ifvalue ../../_id value=@key}}
						    <td> @{{ toFixed max_relation_Cos.avg 2}} </td>
						   @{{/ifvalue}}
						  @{{/each}} 
						  <td>
						  @{{#each annotation}}
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
						
						   @{{#each job_info.results.withoutSpam}}
						    @{{#ifvalue ../../_id value=@key}}
						     
						     <td> 
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
						   @{{/each }}
						   </td>
						    @{{/ifvalue}}
						   @{{/each}}  
						  
						  </tr>
						
						  @{{/each}}
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
