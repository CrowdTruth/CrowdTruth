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
					<div><strong>Platform Name: </strong> @{{ this.infoStat.softwareAgent_id }} </div>
						<div><strong data-toggle="tooltip" data-placement="top" title="CrowdTruth Id: @{{ this.infoStat._id }}"> Crowdagent ID: </strong> @{{ this.infoStat.platformAgentId }} </div>
						<div><strong>Active Since: </strong> @{{ this.infoStat.created_at }} </div>
						<div><strong>Last Seen: </strong> @{{ this.infoStat.updated_at }} </div>
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
						<div><strong>Flagged: </strong> @{{#booltostring this.infoStat.flagged }} @{{/booltostring}}</div>
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
						<div><strong data-toggle="tooltip" data-placement="top" title="# Spam Annotations: @{{ this.infoStat.cache.workerunits.spam }} </br> # NonSpam Annotations: @{{ this.infoStat.cache.workerunits.nonspam }}"> @{{ this.infoStat.cache.workerunits.count }} Annotation(s) in Total </strong> </div>
						<div><strong data-toggle="tooltip" data-placement="top" title="Messages: @{{ this.infoStat.messagesRecieved.messages }}"> @{{ this.infoStat.messagesRecieved.count}} Message(s) Sent to This Worker </strong></div>
						<hr/>
						<table style="width: 100%" border="1" bordercolor="#C0C0C0" text-align="center">
						 <tr text-align="center">
						  <td> <strong>  </strong> </th>
						  <td text-align="center"> <strong> Across worker jobs </strong> </th>
						  <td text-align="center"> <strong> Across CrowdTruth jobs </strong> </th>
						 </tr>
						 <tr>
						  <td> <strong> AVG. Worker Agreement</strong></td> 
						  <td> <strong> @{{ toFixed this.infoStat.avg_agreement 2 }} </strong></td>
						  <td> <strong> @{{ toFixed this.infoStat.avgAgreementAcrossJobs 2 }} </strong> </td>
						 </tr>
					    	 <tr>
						  <td> <strong> AVG. Worker Cosine </strong> </td> 
						  <td> <strong> @{{ toFixed this.infoStat.avg_cosine 2 }} </td>
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
						    <td> @{{#ifarray platformJobId }} @{{/ifarray}} </td>
							@{{#each metrics.workers.withFilter}}
							<td> @{{ toFixed avg_worker_agreement 2 }} </td>
							<td> @{{ toFixed ann_per_unit 2 }} </td>
						    	<td> @{{ toFixed worker_cosine 2 }} </td>
						    	<td> @{{ toFixed no_of_units 0 }} </td>
							@{{/each}}
						    <td> @{{ toFixed metrics.aggWorkers.mean.avg_worker_agreement 2}} </td>
						    <td> @{{ toFixed metrics.aggWorkers.mean.ann_per_unit 2}} </td>
						    <td> @{{ toFixed metrics.aggWorkers.mean.worker_cosine 2}} </td>
						    <td> @{{ toFixed metrics.aggWorkers.mean.no_of_units 0}} </td>
						    <td> @{{ toFixed metrics.aggWorkers.stddev.avg_worker_agreement 2}} </td>
						    <td> @{{ toFixed metrics.aggWorkers.stddev.ann_per_unit 2}} </td>
						    <td> @{{ toFixed metrics.aggWorkers.stddev.worker_cosine 2}} </td>
						    <td> @{{ toFixed metrics.aggWorkers.stddev.no_of_units 0}} </td>
			
						    @{{#inArrayNew ../infoStat.cache.spammer.jobs this.platformJobId }}
							<td> Spammer </td>
						    @{{else}}
							<td> Non Spammer </td>
						    @{{/inArrayNew}}
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
						 @{{#each this.workerunitContent}}

						 @{{#each workerunitType}}
						 <tr>
						  <td data-toggle="tooltip" data-placement="top" title="CrowdTruth ID: @{{ ../_id}} </br> Domain: @{{ ../unitContent.domain }} </br> Sentence: @{{ ../unitContent.content.sentence.formatted}} </br> Term1: @{{ ../unitContent.content.terms.first.formatted }} </br> Term2: @{{ ../unitContent.content.terms.second.formatted }} </br> Relation: @{{ ../unitContent.content.relation.noPrefix }}"> @{{ ../unitContent.documentType }} </td>
						  <td> @{{ job_info.jobConf.content.title}} </td>  
						  @{{#each job_info.metrics.units.withoutSpam}}
						   @{{#ifvalue ../../_id value=@key}}
						    <td> @{{ toFixed max_relation_Cos 2}} </td>
						   @{{/ifvalue}}
						  @{{/each}} 
						  <td>
						  @{{#each workerunit}}
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