	<div class='hidden' id='modalAnnotations'>

		<script class='template' type="text/x-handlebars-template">
			<!-- Modal -->
			<div class="modal fade bs-example-modal-lg" id="activeTabModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelAnnotations" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
				      		<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="myModalLabelAnnotations"> Annotations Page</h4>
				      		</div>
				      		<div class="modal-body" >
						 <table id="myIndividualWorkerTable" class="tablesorter table table-striped table-condensed" border="1" bordercolor="#C0C0C0"> 
						  <thead> 
						   <tr> 
						    <th class="header">Annotation Id</th>
						    <th class="header">Annotation Vector</th>
						    <th class="header">Unit Id</th>
						    <th class="header">Unit Clarity</th>
						    <th class="header">Job Title</th>
						    <th class="header">Annotation Clarity</th>
						    <th class="header">Annotation Ambiguity</th>
						    <th class="header">Worker Id</th>
						    <th class="header">Worker Platform</th>
						    <th class="header">Worker Avg. Agreement</th>
						    <th class="header">Worker Avg. Cosine</th>
						    <th class="header">Worker Status</th>
						   </tr> 
						  </thead>
						  <tbody>
						 @{{#each this}} 
						  <tr>
						   <td> @{{ @key }} </td>
						   <td> 
    							@{{#each dictionary}}
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
						   </td>
						   <td> @{{ unit._id }} </td>
						   <td> @{{ toFixed unit.avg_clarity 2}} </td>
						   <td> @{{ job.hasConfiguration.content.title }} </td>
						   <td> 
    							@{{#each job.metrics.annotations.withoutSpam}}
							  <table border="1" bordercolor="#C0C0C0">
							    
							     @{{#each this}}
								<tr> 
							        @{{#ifvalue @key value="annot_clarity"}}
								@{{#eachProperty this}}
  								 <td> @{{#abrWords key}} @{{/abrWords}} </td>
							        @{{/eachProperty }}
								</tr>
								<tr>
								@{{#eachProperty this}}
  								 <td> @{{toFixed value 2}} </td>
							        @{{/eachProperty }}
								@{{/ifvalue}}
								</tr>
							     @{{/each }}
							    </tr>
							   </table>
							
    							 @{{/each}}
						   </td>
						   <td> 
    							@{{#each job.metrics.annotations.withoutSpam}}
							  <table border="1" bordercolor="#C0C0C0">
							    
							     @{{#each this}}
								<tr> 
							        @{{#ifvalue @key value="annot_ambiguity"}}
								@{{#eachProperty this}}
  								 <td> @{{#abrWords key}} @{{/abrWords}} </td>
							        @{{/eachProperty }}
								</tr>
								<tr>
								@{{#eachProperty this}}
  								 <td> @{{toFixed value 2}} </td>
							        @{{/eachProperty }}
								@{{/ifvalue}}
								</tr>
							     @{{/each }}
							    </tr>
							   </table>
							
    							 @{{/each}}
						   </td>
						   <td> @{{ agent._id }} </td>
						   <td> @{{ agent.softwareAgent_id }} </td>
						   <td> @{{ toFixed agent.avg_agreement 2}} </td>
						   <td> @{{ toFixed agent.avg_cosine 2}} </td>
						   <td> @{{#booltostring agent.flagged }} @{{/booltostring}} </td>
						  </tr> 
						 @{{/each}}
						  	
						 </tbody>
						</table>
					  	</div>
					</div>
				</div>
			</div>
		</script>

	</div>	