	<div class='hidden' id='modalIndividualJob'>
		<script class='template' type="text/x-handlebars-template">
				<!-- Modal -->
				<div class="modal fade bs-example-modal-lg" id="activeTabModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelJob" aria-hidden="true">
				  <div class="modal-dialog modal-lg">
				    <div class="modal-content">
				      <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabelJob">Individual Job Page</h4>
				      </div>
				      <div class="modal-body" >
					<div><strong>Platform Name: </strong> @{{ this.infoStat.softwareAgent_id }} </div>
					<div><strong data-toggle="tooltip" data-placement="top" title="CrowdTruth Id: @{{ this.infoStat._id }}"> Job ID: </strong> @{{ this.infoStat._id }}</div>
					<div><strong>Creation Date: </strong> @{{ this.infoStat.startedAt }} </div>
					<div><strong>Finish Date: </strong> @{{ this.infoStat.finishedAt }} </div>
					<div><strong>Type: </strong> @{{ this.infoStat.type }} </div>
					<div><strong>Title: </strong> @{{ this.infoStat.jobConf.content.title }} </div>
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
						<div><strong data-toggle="tooltip" data-placement="top" title="CrowdTruth Id: @{{ this.infoStat._id }}"> Job ID: </strong> @{{ this.infoStat._id }} </div>
						<div><strong>Running Time: </strong> @{{#formatTime this.infoStat.runningTimeInSeconds }}@{{/formatTime}} </div>
						<div><strong>Creation Date: </strong> @{{ this.infoStat.startedAt }} </div>
						<div><strong>Finish Date: </strong> @{{ this.infoStat.finishedAt }} </div>
						<div><strong>Media Domain: </strong> @{{ this.infoStat.domain}} </div>
						<div><strong>Media Format: </strong> @{{ this.infoStat.format }} </div>
						<div><strong>Type: </strong> @{{ this.infoStat.type }} </div>
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
						<div><strong> @{{ this.infoStat.workerunitsCount }} Annotation(s) </strong>  </div>
						@{{#if this.infoStat.metrics.filteredUnits.count }}
						<div><strong> @{{ this.infoStat.metrics.filteredUnits.count }} Filtered Unit(s) </strong> </div>
						@{{else}}
						<div><strong> 0 Filtered Unit(s) </strong> </div>
						@{{/if}}
						@{{#if this.infoStat.metrics.spammers.count }}
						<div><strong> @{{ this.infoStat.metrics.spammers.count }} Filtered Worker(s) as Spammer(s) </strong> </div>
						@{{else}}
						<div><strong> 0 Filtered Worker(s) as Spammer(s) </strong> </div>
						@{{/if}}
						@{{#if this.infoStat.metrics.filteredWorkerunits.count }}
						<div><strong> @{{ this.infoStat.metrics.filteredWorkerunits.count }} Filtered Annotation(s) </strong> </div>
						@{{else}}
						<div><strong> 0 Filtered Annotation(s) </strong> </div>
						@{{/if}}
						<hr/>
						<table style="width: 100%; text-align: center; align: center;" border="1" bordercolor="#C0C0C0" text-align="center">
						 <tr align="center">
						  <td colspan="12" align="center"> <strong> Aggregated Units </strong> </td>
						 </tr>
						 <tr>
						  <td text-align="center" colspan="2"> <strong> Workers </strong> </td>
						  <td text-align="center" colspan="2"> <strong> Clarity </strong> </td>
						  <td text-align="center" colspan="2"> <strong> Norm Magnitude </strong> </td>
						  <td text-align="center" colspan="2"> <strong> Magnitude </strong> </td>
						  <td text-align="center" colspan="2"> <strong> Norm Rel Magnitude All </strong> </td>
						  <td text-align="center" colspan="2"> <strong> Norm Rel Magnitude </strong> </td>
						 </tr>
						  <td text-align="center">Avg</td>
						  <td text-align="center">Stdev</td>
						  <td text-align="center">Avg</td>
						  <td text-align="center">Stdev</td>
						  <td text-align="center">Avg</td>
						  <td text-align="center">Stdev</td>
						  <td text-align="center">Avg</td>
						  <td text-align="center">Stdev</td>
						  <td text-align="center">Avg</td>
						  <td text-align="center">Stdev</td>
						  <td text-align="center">Avg</td>
						  <td text-align="center">Stdev</td>
						 </tr>
						 <tr>
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.mean.no_annotators 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.stddev.no_annotators 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.mean.max_relation_Cos 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.stddev.max_relation_Cos 2}} </td>						  
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.mean.norm_magnitude 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.stddev.norm_magnitude 2}} </td>						  
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.mean.magnitude 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.stddev.magnitude 2}} </td>						  
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.mean.norm_relation_magnitude_all 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.stddev.norm_relation_magnitude_all 2}} </td>						  
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.mean.norm_relation_magnitude 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggUnits.stddev.norm_relation_magnitude 2}} </td>
						 </tr>
						</table>
						<hr/>
						<div align="center">
						<table border="1" bordercolor="#C0C0C0" style="align: center;" class="noSortingTable">
						 <tr>
						  <td colspan="8"> <strong> Aggregated Workers </strong> </td>
						 </tr>
						 <tr>
						  <td text-align="center" colspan="2"> <strong> Units </strong> </td>
						  <td text-align="center" colspan="2"> <strong> Ann. per Unit </strong> </td>
						  <td text-align="center" colspan="2"> <strong> Worker Agreement </strong> </td>
						  <td text-align="center" colspan="2"> <strong> Worker Cosine </strong> </td>
						 </tr>
						  <td text-align="center">Avg</td>	
						  <td text-align="center">Stdev</td>	
						  <td text-align="center">Avg</td>	
						  <td text-align="center">Stdev</td>	
						  <td text-align="center">Avg</td>	
						  <td text-align="center">Stdev</td>	
						  <td text-align="center">Avg</td>	
						  <td text-align="center">Stdev</td>	
						 </tr>
						 <tr>
						  <td> @{{ toFixed this.infoStat.metrics.aggWorkers.mean.no_of_units 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggWorkers.stddev.no_of_units 2}} </td>
						 <td> @{{ toFixed this.infoStat.metrics.aggWorkers.mean.ann_per_unit 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggWorkers.stddev.ann_per_unit 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggWorkers.mean.avg_worker_agreement 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggWorkers.stddev.avg_worker_agreement 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggWorkers.mean.worker_cosine 2}} </td>
						  <td> @{{ toFixed this.infoStat.metrics.aggWorkers.stddev.worker_cosine 2}} </td>						 </tr>
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
						     <th class="header" colspan="7">Unit Metrics</th>
						     <th class="header" rowspan="2">Annotation Vector</th>
						  </tr>
						  <tr>
						    <th> # Workers </th>	
						    <th> Max Rel Cos </th>	
						    <th> Vector Length </th>	
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
						    @{{#inArray @root.infoStat.metrics.filteredunits.list @key }}
						    <td> True </td>
						    @{{else}}
						    <td> False </td>
						    @{{/inArray}}
						    <td> @{{ toFixed avg.no_annotators 2}} </td>
						    <td> @{{ toFixed avg.max_relation_Cos 2}} </td>
						    <td> @{{ avg.vector_size }} </td>
						    <td> @{{ toFixed avg.norm_magnitude 2}} </td>
						    <td> @{{ toFixed avg.magnitude 2}} </td>
						    <td> @{{ toFixed avg.norm_relation_magnitude_all 2}} </td>
						    <td> @{{ toFixed avg.norm_relation_magnitude 2}} </td>
						    <td>
						    @{{#each @root.infoStat.results.withoutSpam}}
						    @{{#ifvalue ../key value=@key}}
						     <!--@{{#each this}} 
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
							  @{{/each}}-->
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
						    <th class="header" colspan="6">Worker Metrics Across Job</th>
						</tr> 
						<tr>
						    <th> Avg. Worker Agreement </th>
						    <th> Avg. Worker Cosine </th>
						    <th> Avg. Worker Agreement</th>	
						    <th> Worker Cosine </th>	
						    <th> # Ann / Unit </th>
						    <th> # Ann Units </th>
						    <th> Contradictions </th>
						    <th> Spammer </th>
						  </tr>
						</thead>
						<tbody>
						 @{{#each this.infoStat.metrics.workers.withoutFilter}} 
						 @{{type @key}}
						 <tr>
						  <td> @{{ @key }} </td>
						  @{{#each @root.infoStat.workers }}
						    @{{#ifvalue ../key value=@key}}
						     <td> @{{ softwareAgent_id}} </td>  
						     <td> @{{ cfWorkerTrust}} </td> 
						     <td> @{{ toFixed avg_agreement 2}} </td> 
						     <td> @{{ toFixed avg_cosine 2}} </td> 
						   @{{/ifvalue}}
						  @{{/each}} 
						   <td> @{{ toFixed avg_worker_agreement 2 }} </td>
						   <td> @{{ toFixed worker_cosine 2 }} </td>
						   <td> @{{ toFixed ann_per_unit 2 }} </td>
						   <td> @{{ toFixed no_of_units }} </td>
						   <td> @{{ contradiction }} </td>
						   <td> @{{ spam }} </td>
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