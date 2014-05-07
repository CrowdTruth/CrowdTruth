<div class="tab-pane" id="painting_tab">	
	<div class='row'>
		<div class='tabOptions hidden'>
			<div class='btn-group' style="margin-left:5px;">
				<button type="button" class="btn btn-default openAllColumns">Open all columns</button>
				<button type="button" class="btn btn-default openDefaultColumns hidden">Open default columns</button>
				<div class="btn-group vbColumns">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#" data-vb="hide" data-vbSelector="image_id"></i>ID</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="image_id"></i>Thumbnail</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="image_title"></i>Name</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="image_domain"></i>Domain</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="image_source"></i>Source</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="image_url"></i>URL</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="image_width"></i>Width</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="image_height"></i>Height</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="image_object"></i>Object</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="image_scene"></i>Scene</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="image_classifiers"></i>Classifiers</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="image_facesrek"></i>FacesRekognition</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="image_faces">Faces</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="image_facesnum"># of Faces</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="image_colors">Main colors</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="image_histogram"></i>Histogram</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="number_of_batches"></i>Used In # of Batches</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="number_of_jobs"></i>Used In # of Jobs</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="created_at"></i>Created At</a></li>
					</ul>
				</div>
			</div>
		</div>	
	</div>
	<div class='ctable-responsive'>
	    <table class="table table-striped">
	       	<thead data-query-key="match[documentType]" data-query-value="painting">
		        <tr>
		            <th data-vbIdentifier="checkbox">Select</th>
		            <th class="sorting" data-vbIdentifier="image_id" data-query-key="orderBy[_id]">ID</th>
		            <th class="sorting" data-vbIdentifier="image_content" data-query-key="orderBy[content.URL]">Thumbnail</th>
		            <th class="sorting" data-vbIdentifier="image_title" data-query-key="orderBy[title]">Title</th>
		            <th class="sorting" data-vbIdentifier="image_domain" data-query-key="orderBy[domain]">Domain</th>
		            <th class="sorting" data-vbIdentifier="image_source" data-query-key="orderBy[source]">Source</th>
			    	<th class="sorting" data-vbIdentifier="image_url" data-query-key="orderBy[content.url]">URL</th>
		            <th class="sorting" data-vbIdentifier="image_width" data-query-key="orderBy[content.width]">Width</th>
				    <th class="sorting" data-vbIdentifier="image_height" data-query-key="orderBy[content.height]">Height</th>
				    <th class="sorting" data-vbIdentifier="image_object" data-query-key="orderBy[content.features.Object.matches]">Objects</th>
				    <th class="sorting" data-vbIdentifier="image_scene" data-query-key="orderBy[content.features.Scene]">Scene</th>
				    <th class="sorting" data-vbIdentifier="image_classifiers" data-query-key="orderBy[content.features.Classifier]">Classifiers</th>
				    <th class="sorting" data-vbIdentifier="image_facesrek" data-query-key="orderBy[content.features.FacesRekognition]">FacesRekognition</th>
				    <th class="sorting" data-vbIdentifier="image_faces" data-query-key="orderBy[content.features.Faces]">Faces</th>
				    <th class="sorting" data-vbIdentifier="image_facesnum" data-query-key="orderBy[content.features.FacesNumber.cloudinary]"># Faces</th>
				    <th class="sorting" data-vbIdentifier="image_colors" data-query-key="orderBy[content.features.ColorsMain]">Main colors</th>
				    <th class="sorting" data-vbIdentifier="image_histogram" data-query-key="orderBy[content.features.ColorsHistogram]">Histogram</th>
				    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_batches" data-query-key="orderBy[batches.count]">Used In # of Batches</th>
				    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_jobs" data-query-key="orderBy[jobs.count]">Used In # of Jobs</th>     
				    <th class="sorting whiteSpaceNoWrap" data-vbIdentifier="created_at" data-query-key="orderBy[created_at]" style="min-width:220px; width:auto;">Created At</th>
		        </tr>
			<tr class="inputFilters">
				<td data-vbIdentifier="checkbox">
					<input type="checkbox" class="checkAll" />
				</td>
				<td data-vbIdentifier="image_id">
					<input class="input-sm form-control" type='text' data-query-key="match[_id]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="image_content">
					<input class="input-sm form-control" type='text' data-query-key="match[content.URL]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="image_title">
					<input class="input-sm form-control" type='text' data-query-key="match[title]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="image_domain">
					<input class="input-sm form-control" type='text' data-query-key="match[domain]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="image_source">
					<input class="input-sm form-control" type='text' data-query-key="match[source]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="image_url">
					<input class="input-sm form-control" type='text' data-query-key="match[content.URL]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="image_width">
					<input class="input-sm form-control" type='text' data-query-key="match[content.width]" data-query-operator=">" style="width:49%; float:left;" placeholder="&gt;" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[content.width]" data-query-operator="<" style="width:49%; float:right;" placeholder="&lt;" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="image_height">
					<input class="input-sm form-control" type='text' data-query-key="match[content.height]" data-query-operator=">" style="width:49%; float:left;" placeholder="&gt;" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[content.height]" data-query-operator="<" style="width:49%; float:right;" placeholder="&lt;" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="image_object">
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.Object.matches.tag]" data-query-operator="like" style="width:59%; float:left;" />
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.Object.matches.score]" data-query-operator=">" style="width:39%; float:right;" placeholder="&gt;" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				</td>
				<td data-vbIdentifier="image_scene">
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.Scene.label]" data-query-operator="like" style="width:59%; float:left;"/>
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.Scene.score]" data-query-operator=">" style="width:39%; float:right;" placeholder="&gt;" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				</td>
				<td data-vbIdentifier="image_classifiers">
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.Classifier]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="image_facesrek">
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.FacesRekognition]" data-query-operator=">" style="width:49%; float:left;" placeholder="gt" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.FacesRekognition]" data-query-operator="<" style="width:49%; float:right;" placeholder="lt" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="image_faces">
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.Faces]" data-query-operator=">" style="width:49%; float:left;" placeholder="&gt;" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.Faces]" data-query-operator="<" style="width:49%; float:right;" placeholder="&lt;" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="image_facesnum">
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.FacesNumber]" data-query-operator=">" style="width:49%; float:left;" placeholder="&gt;" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.FacesNumber]" data-query-operator="<" style="width:49%; float:right;" placeholder="&lt;" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="image_colors">
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.ColorsMain]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="image_histogram">
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.ColorsHistogram]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="number_of_batches">
					<input class="input-sm form-control" type='text' data-query-key="match[batches.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[batches.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="number_of_jobs">
					<input class="input-sm form-control" type='text' data-query-key="match[jobs.count]" data-query-operator=">" style="width:49%; float:left;" placeholder=">" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[jobs.count]" data-query-operator="<" style="width:49%; float:right;" placeholder="<" data-toggle="tooltip" data-placement="bottom" title="Less than" />
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
			            <td data-vbIdentifier="image_id">@{{ this._id }}   </td>
			            <td data-vbIdentifier="image_content"><image width="240" height="160" src="@{{this.content.URL}}" />   </td>
			            <td data-vbIdentifier="image_title">@{{ this.title }}   </td>
			            <td data-vbIdentifier="image_domain">@{{ this.domain }}   </td>
			            <td data-vbIdentifier="image_source">@{{ this.source }}   </td>
			            <td data-vbIdentifier="image_url">@{{ this.content.URL }}   </td>
			            <td data-vbIdentifier="image_width">@{{ this.content.width }}   </td>
			            <td data-vbIdentifier="image_height">@{{ this.content.height }}   </td>
			            <td data-vbIdentifier="image_object">
			            	@{{#eachProperty this.content.features.Object.matches}}
			            		@{{value.tag}}: @{{value.score}} <br>			            		
			            	@{{/eachProperty}}
    			
						</td>
			            <td data-vbIdentifier="image_scene">
			            	@{{#eachProperty this.content.features.Scene }}
			            		@{{value.label}}: @{{value.score}} <br>
			            	@{{/eachProperty}}   
		            	</td>
			            <td data-vbIdentifier="image_classifiers">
			            	@{{#eachProperty this.content.features.Classifier }}
			            		@{{key}}: @{{value}}
		            		@{{/eachProperty}}
	            	   </td>
			            <td data-vbIdentifier="image_facesrek">@{{ this.content.features.FacesRekognition }}   </td>
			            <td data-vbIdentifier="image_faces">@{{ this.content.features.Faces }}   </td>
			            <td data-vbIdentifier="image_facesnum">
			            	@{{#eachProperty this.content.features.FacesNumber }}   
			            		@{{key}}: @{{value}}
			            	@{{/eachProperty}}
		            	</td>
			            <td data-vbIdentifier="image_colors">@{{ this.content.features.ColorsMain }}   </td>
			            <td data-vbIdentifier="image_histogram">@{{ this.content.features.ColorsHistogram }}   </td>
					    <td data-vbIdentifier="number_of_batches">@{{ this.batches.count }}</td>
					    <td data-vbIdentifier="number_of_jobs">@{{ this.jobs.count }}</td>
					    <td data-vbIdentifier="created_at">@{{ this.created_at }}</td>
					</tr>
			        @{{/each}}
				</script>
	        </tbody>
	    </table>
	</div>	    
	
	<div class='hidden modalTemplate'>
		<table>
		<script class='template' type="text/x-handlebars-template">
				<!-- Modal -->
				<div class="modal bs-example-modal-lg fade" id="activeTabModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelWorker" aria-hidden="true">
				  <div class="modal-dialog modal-lg">
				    <div class="modal-content">
				      <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabelWorker">Individual worker page</h4>
				      </div>
				      <div class="modal-body" >
					@{{#each documents}}
						@{{ this._id }}
					@{{/each}}				
				      </div>
				    </div>
				  </div>
				</div>
		</script>
		</table>
	</div>			


	<div class='hidden' id='modalTemplateSegments'>
		<script class='template' type="text/x-handlebars-template">
			<!-- Modal -->
			<div class="modal fade" id="activeTabModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelWorker" aria-hidden="true">
				<div class="modal-dialog ">
				    <div class="modal-content">
				      <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabelWorker">Segments of the video</h4>
				      </div>
				      <div class="modal-body" >
					<table>
					@{{#each documents}}
						<tr>
							<td>
								<video width="240" height="160" controls preload="none">
									<source src="@{{ this.content.storage_url }}" type="video/mp4" >
									<source src="@{{ this.content.storage_url }}" type="video/ogg" >
										Your browser does not support the video tag.
									</source>
								</video>
							</td>
							<td align="left">
								<strong>Duration</strong>: @{{ this.content.duration }} <br />
								<strong>Start Time</strong>: @{{ this.content.start_time }} <br />
								<strong>End Time</strong>: @{{ this.content.end_time }} <br />
							</td>
						</tr>
					@{{/each}}
					</table>					
				      </div>
				    </div>
				  </div>
				</div>
		</script>
		
	</div>	
	<div class='hidden' id='modalTemplateKeyframes'>

		<script class='template' type="text/x-handlebars-template">
				<!-- Modal -->
				<div class="modal fade" id="activeTabModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelWorker" aria-hidden="true">
				  <div class="modal-dialog ">
				    <div class="modal-content">
				      <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabelWorker">Keyframes of the video</h4>
				      </div>
				      <div class="modal-body" >
					<table>
					@{{#each documents}}
						<tr>
							<td><img src="@{{ this.content.storage_url }}" width="240" height="160" rel="tooltip" title="Timestamp: @{{ this.content.timestamp }} &#xA; Source name: @{{ this.content.storage_url }} "/></td>
							<td><strong>Timestamp</strong>: @{{ this.content.timestamp }}</td>
						</tr>
					@{{/each}}
					</table>					
				      </div>
				    </div>
				  </div>
				</div>
		</script>

	</div>								
</div>