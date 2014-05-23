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
						<li><a href="#" data-vb="show" data-vbSelector="image_content"></i>Thumbnail</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="image_title"></i>Title</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="image_description"></i>Description</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="image_author"></i>Author</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="image_domain"></i>Domain</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="image_source"></i>Source</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="image_url"></i>URL</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="image_width"></i>Width</a></li>
						<li><a href="#" data-vb="hide" data-vbSelector="image_height"></i>Height</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="image_object"></i>Object</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="image_scene"></i>Scene</a></li>
						<li><a href="#" data-vb="show" data-vbSelector="image_classifiers"></i>Classifiers</a></li>
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
	       	<thead data-query-key="&collection=temp&match[documentType]" data-query-value="painting">
		        <tr>
		            <th data-vbIdentifier="checkbox" data-toggle="tooltip" data-placement="top" title="Check to select this row">Select</th>
		            <th class="sorting" data-vbIdentifier="image_id" data-query-key="orderBy[_id]" data-toggle="tooltip" data-placement="top" title="The ID of the image">ID</th>
		            <th class="sorting" data-vbIdentifier="image_content" data-query-key="orderBy[content.url]" data-toggle="tooltip" data-placement="top" title="Thumbnail of the image (240x160px)">Thumbnail</th>
		            <th class="sorting" data-vbIdentifier="image_title" data-query-key="orderBy[content.title]" data-toggle="tooltip" data-placement="top" title="The image title">Title</th>
		            <th class="sorting" data-vbIdentifier="image_description" data-query-key="orderBy[content.description]" data-toggle="tooltip" data-placement="top" title="The image description">Description</th>
		            <th class="sorting" data-vbIdentifier="image_author" data-query-key="orderBy[content.author]" data-toggle="tooltip" data-placement="top" title="The author of the image">Author</th>
		            <th class="sorting" data-vbIdentifier="image_domain" data-query-key="orderBy[domain]" data-toggle="tooltip" data-placement="top" title="The domain the image is uploaded under">Domain</th>
		            <th class="sorting" data-vbIdentifier="image_source" data-query-key="orderBy[source]" data-toggle="tooltip" data-placement="top" title="The source of the image, in case of API this is the hostname">Source</th>
			    	<th class="sorting" data-vbIdentifier="image_width" data-query-key="orderBy[content.width]" data-toggle="tooltip" data-placement="top" title="The width of the image in px">Width</th>
				    <th class="sorting" data-vbIdentifier="image_height" data-query-key="orderBy[content.height]" data-toggle="tooltip" data-placement="top" title="The height of the image in px">Height</th>
				    <th class="sorting" data-vbIdentifier="image_object" data-query-key="orderBy[content.features.object]" data-toggle="tooltip" data-placement="top" title="Objects together with their confidence score that were found in the image">Objects</th>
				    <th class="sorting" data-vbIdentifier="image_scene" data-query-key="orderBy[content.features.scene]" data-toggle="tooltip" data-placement="top" title="The scene with a confidence score that was found in the image">Scene</th>
				    <th class="sorting" data-vbIdentifier="image_classifiers" data-query-key="orderBy[content.features.Classifier]" data-toggle="tooltip" data-placement="top" title="Custom classifier confidence scores for this image">Classifiers</th>
				    <th class="sorting" data-vbIdentifier="image_facesnum" data-query-key="orderBy[content.features.FacesNumber]" data-toggle="tooltip" data-placement="top" title="Number of faces recognized in the image by the different APIs. For sorting and applying filters the Cloudinary score takes prominence."># Faces</th>
				    <th class="sorting" data-vbIdentifier="image_colors" data-query-key="orderBy[content.features.ColorsMain]" data-toggle="tooltip" data-placement="top" title="Main colors recognized in the image, the size of the bar indicates its percentage. Hover over for details.">Main colors</th>
				    <th class="sorting" data-vbIdentifier="image_histogram" data-query-key="orderBy[content.features.ColorsHistogram]" data-toggle="tooltip" data-placement="top" title="A histogram of the colors found in the image, the size of the ba indicates its percentage. Hover over for details. ">Histogram</th>
				    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_batches" data-query-key="orderBy[batches.count]" data-toggle="tooltip" data-placement="top" title="Number of batches in which this image is used">Used In # of Batches</th>
				    <th class="sorting whiteSpaceNormal" data-vbIdentifier="number_of_jobs" data-query-key="orderBy[jobs.count]" data-toggle="tooltip" data-placement="top" title="Number of jobs in which this image is used">Used In # of Jobs</th>     
				    <th class="sorting whiteSpaceNoWrap" data-vbIdentifier="created_at" data-query-key="orderBy[created_at]" style="min-width:220px; width:auto;" data-toggle="tooltip" data-placement="top" title="The time at which this image was uploaded to CrowdTruth">Created At</th>
		        </tr>
			<tr class="inputFilters">
				<td data-vbIdentifier="checkbox">
					<input type="checkbox" class="checkAll" />
				</td>
				<td data-vbIdentifier="image_id">
					<input class="input-sm form-control" type='text' data-query-key="match[_id]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="image_content">
					<input class="input-sm form-control" type='text' data-query-key="match[content.url]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="image_title">
					<input class="input-sm form-control" type='text' data-query-key="match[content.title]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="image_description">
					<input class="input-sm form-control" type='text' data-query-key="match[content.description]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="image_author">
					<input class="input-sm form-control" type='text' data-query-key="match[content.author]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="image_domain">
					<input class="input-sm form-control" type='text' data-query-key="match[domain]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="image_source">
					<input class="input-sm form-control" type='text' data-query-key="match[source]" data-query-operator="like" />
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
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.object.tag]" data-query-operator="like" style="width:59%; float:left;" />
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.object.score]" data-query-operator=">" style="width:39%; float:right;" placeholder="&gt;" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				</td>
				<td data-vbIdentifier="image_scene">
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.scene.label]" data-query-operator="like" style="width:59%; float:left;"/>
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.scene.score]" data-query-operator=">" style="width:39%; float:right;" placeholder="&gt;" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
				</td>
				<td data-vbIdentifier="image_classifiers">
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.Classifier]" data-query-operator="like" />
				</td>
				<td data-vbIdentifier="image_facesnum">
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.FacesNumber]" data-query-operator=">" style="width:49%; float:left;" placeholder="&gt;" data-toggle="tooltip" data-placement="bottom" title="Greater than" />
					<input class="input-sm form-control" type='text' data-query-key="match[content.features.FacesNumber]" data-query-operator="<" style="width:49%; float:right;" placeholder="&lt;" data-toggle="tooltip" data-placement="bottom" title="Less than" />
				</td>
				<td data-vbIdentifier="image_colors">
					<!-- <input class="input-sm form-control" type='text' data-query-key="match[content.features.ColorsMain]" data-query-operator="like" /> -->
				</td>
				<td data-vbIdentifier="image_histogram">
					<!-- <input class="input-sm form-control" type='text' data-query-key="match[content.features.ColorsHistogram]" data-query-operator="like" /> -->
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
			            <td data-vbIdentifier="image_id">
			            	<a class='testModal' id='@{{ this._id }}' data-modal-query="unit=@{{this._id}}" data-api-target="{{ URL::to('api/analytics/unit?') }}" data-target="#modalIndividualUnit" data-toggle="tooltip" data-placement="top" title="Click to see the individual unit page">
			            		@{{ this._id }}   
		            		</a>
	            		</td>
			            <td data-vbIdentifier="image_content"><a href="@{{this.content.url}}"><image width="240" height="160" src="@{{this.content.url}}" /></a></td>
			            <td data-vbIdentifier="image_title">@{{ this.content.title }}   </td>
			            <td data-vbIdentifier="image_description">@{{ this.content.description }}   </td>
			            <td data-vbIdentifier="image_author">@{{ this.content.author }}   </td>
			            <td data-vbIdentifier="image_domain">@{{ this.domain }}   </td>
			            <td data-vbIdentifier="image_source">@{{ this.source }}   </td>
			            <td data-vbIdentifier="image_width">@{{ this.content.width }}   </td>
			            <td data-vbIdentifier="image_height">@{{ this.content.height }}   </td>
			            <td data-vbIdentifier="image_object">
			            	
			              @{{#if  this.content.features.object}}
				            	@{{#eachProperty this.content.features.object}}
				            		@{{value.label}}: @{{value.score}} <br>			            		
				            	@{{/eachProperty}}

       			 		   @{{/if}}
  						</td>
			            <td data-vbIdentifier="image_scene">
			            	@{{#if  this.content.features.scene}}
				            	@{{#eachProperty this.content.features.scene }}
				            		@{{value.label}}: @{{value.score}} <br>
				            	@{{/eachProperty}}   
			            	@{{/if}}
		            	</td>
			            <td data-vbIdentifier="image_classifiers">
			            	@{{#if this.content.features.classifier}}
				            	@{{#eachProperty this.content.features.classifier }}
				            		@{{value.label}}: @{{value.score}}
			            		@{{/eachProperty}}
		            		@{{/if}}
	            	   </td>
			           

 						
						
						<td data-vbIdentifier="image_facesnum">
			            	@{{#if this.content.features.faces}}
				            	@{{#eachProperty this.content.features.faces }}


				            		@{{#is value.label "facesNumber"}}
				            			@{{value.label}}: @{{value.score}}
				            		@{{/is}}
			            		@{{/eachProperty}}
		            		@{{/if}}
	            	   </td>


			            <td data-vbIdentifier="image_colors">								    	
				    		@{{#if this.content.features.ColorsMain}}
				            	<table class="table table-striped table-condensed" style="height: 50px;">						    
							    	<tbody>
										<tr>
								    			@{{#each this.content.features.ColorsMain }}
													<td style='background:@{{ this.label }}; width:@{{ this.score }}%; padding: 0 0 0 0;' data-toggle="tooltip" data-placement="top" title=" @{{this.label}}, @{{this.score}}% ">
														&nbsp;
													</td>
												@{{/each}}									
										</tr>
									</tbody>
								</table>							  							
							@{{/if}}
			            </td>
			            <td data-vbIdentifier="image_histogram">
				    		@{{#if this.content.features.ColorsHistogram}}
				            	 <table class="table table-striped table-condensed" style="height: 50px;">						    
							    	<tbody>
										<tr>
								    		@{{#each this.content.features.ColorsHistogram }}
												<td style='background:@{{ this.label }}; width:@{{ this.score }}%; padding: 0 0 0 0;' data-toggle="tooltip" data-placement="top" title=" @{{this.label}}, @{{this.score}}% ">
													&nbsp;
												</td>
											@{{/each}}									
										</tr>
									</tbody>
								</table>							  
							@{{/if}}
			            </td>
					    <td data-vbIdentifier="number_of_batches">@{{ this.batches.count }}</td>
					    <td data-vbIdentifier="number_of_jobs">@{{ this.jobs.count }}</td>
					    <td data-vbIdentifier="created_at">@{{ this.created_at }}</td>
					</tr>
			        @{{/each}}
				</script>
	        </tbody>
	    </table>
	</div>	    
	

					
</div>