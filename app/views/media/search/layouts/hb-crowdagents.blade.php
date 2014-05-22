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
						<li><a href="#" data-vb="hide" data-vbSelector="total_workerUnits"></i># Judgments</a></li>
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
		            <th class="sorting" data-vbIdentifier="total_workerUnits" data-query-key="orderBy[cache.workerUnits.count]" data-toggle="tooltip" data-placement="top" title="The number of judgements completed by the worker, which is also the number of units the worker has completed work for"># Judgements</th>
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
				<td data-vbIdentifier="total_workerUnits">
					<input class="input-sm form-control" type='text' data-query-key="match[cache.workerUnits.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[cache.workerUnits.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
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
						
						@{{#if this.cache.workerUnits.count}}
						<td data-vbIdentifier="total_workerUnits" data-toggle="tooltip" data-placement="top" title="Spam: @{{ this.cache.workerUnits.spam }} &#xA; Non-Spam: @{{ this.cache.workerUnits.nonspam }}">
							@{{ this.cache.workerUnits.count }}
						</td>
							@{{else}}
						<td data-vbIdentifier="total_workerUnits" data-toggle="tooltip" data-placement="top" title="Spam: 0 &#xA; Non-Spam: 0">0</td>
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

@include('media.search.layouts.hb-modalmessage')
@include('media.search.layouts.hb-modalblock')
@include('media.search.layouts.hb-modalindividualworker')
								
</div>