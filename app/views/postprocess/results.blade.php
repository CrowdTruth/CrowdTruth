
					<div id="results" class="col-md-10" ng-app="dataRetrieval" ng-controller="resourceCtrl">
						<div class="panel panel-default" ng-repeat="result in results">
						<!-- Top row is panel heading with creation date and creator -->
						<div class="panel-heading clearfix">
							<div style="width: 25%; float:left;">
								<a class=""></a>
							</div>
	              			<div style="float:left;">
	              				Created on @{{result.created_at}} by @{{result.user_id}}
		              		</div>
			           		<div class="pull-right" style="width: 33%;">
			           			<div class="progress" style="margin-bottom: 0px;">	
			           				<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40%" aria-valuemin="0" aria-valuemax="100" style="width: 40 %;">
		   								<span class="sr-only">40% Complete</span>
		  							</div>
		              			</div>
		              		</div>
	               		</div>
	               		<!-- First content row with title and description of the ct and the elapsed time -->
	               		<div class="panel-body" style="padding-top: 0px; padding-bottom: 0px;">
		               		<div class="row" style="border-bottom: 1px solid #eee;">
			               		<div class="col-md-10" style="border-right: 1px solid #eee;">
			               			<h4>@{{result.has_configuration.content.title}}</h4>
			               			<p>@{{result.has_configuration.content.description }}</p>
			               			<strong style="font-size: 18px;"><i class="fa fa-file"></i> @{{result.has_configuration.type}}</strong> 
			               		</div>
			               		<div class="col-md-2" style="text-align: center; padding-top: 15px;">
			               			<strong style="font-size: 24px; "><i class="fa fa-clock-o fa-2x"></i><br> @{{ElapsedTime}}</strong></p>
			               		</div>	
			               	</div>
			               	<div class="row" style="height: 90px;">
			               		<!-- This row has the following content: #sentences, judgments/unit and template info; block with worker info; block of costs, block of completion percentage; -->
			               		<div class="col-md-2" style="border-right: 1px solid #eee; height: 100%; text-align: center; display: table-cell; vertical-align: middle; padding-top: 10px;">
			               			<strong style="font-size: 24px;"><i class="fa fa-bars"></i> @{{result.has_configuration.content.judgmentsPerUnit}}</strong><br>
			               			<strong style="font-size: 24px;"><i class="fa fa-gavel"></i> @{{result.has_configuration.content.unitsPerTask}}</strong><br>
			               		</div>
			               		<div class="col-md-4" style="border-right: 1px solid #eee; height: 100%; text-align: center; display: table-cell; padding-top: 10px; vertical-align: middle;"> 
			               			<h2><i class="fa fa-users"></i> @{{result.has_configuration.content.platform}} </h2>
			               		</div>
			               		<div class="col-md-2" style="border-right: 1px solid #eee; text-align: center; display: table-cell; padding-top: 5px; font-size: 32px; vertical-align: middle;"> 
			                   		<i class="fa fa-flag"></i>  %</strong><br>
			                   		<button class="btn btn-sm">Workers</button>
				               	</div>
							    <div class="col-md-2" style="border-right: 1px solid #eee; height: 100%; text-align: center; display: table-cell; padding-top: 10px; vertical-align: middle;">
							    	<i class="fa fa-dollar"></i><strong> /</strong> <i class="fa fa-gavel"></i> <strong> $ @{{result.has_configuration.content.reward}}</strong>
							       	<h2><i class="fa fa-dollar"></i></h2>
							    </div>
							    <div class="col-md-2" style="text-align: center; height: 100%; display: table-cell; vertical-align: middle; padding-top: 10px;">
							    	<strong> <i class="fa fa-gavel"></i> / </strong>
							    	<h2><i class="fa fa-check-circle"></i> %</h2>
			               		</div>
							</div>
							 <!-- Here starts the hidden details field, see js at bottom of page -->
							<div id="" class="row" style="display: none;">
					            <table class="table table-striped">
					           	
					 	    	</table>
	          				</div>
	          			</div>
						<!-- Here starts the panel footer -->
	               		<div class="panel-footer">
	               			<div class="row">
	               				<div style="padding: 3px; padding-left: 5px; float: left;" >
	               					<button class="btn btn-primary" action="">Analyse</button>
	               				</div>
					  			<div data-toggle="buttons" style="float:left; padding: 3px;">
						  			<label class="btn btn-primary">
										<input type="checkbox" name="Details" id="" onChange="">Details
									</label>
								</div>
								<div class="btn-group" style="float: left; padding: 3px;">
									<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user fa-fw"></i>Actions
						    			<span class="caret"></span>
					   				</button>
					 				<ul class="dropdown-menu" role="menu">
					       				<li><a href="#"><i class="fa fa-folder-open fa-fw"></i>Pause Job</a></li>
					       				<li><a href=""><i class="fa fa-sign-out fa-fw"></i>Cancel Job</a></li>
					       				<li class="divider"></li>
					       				<li><a href=""><i class="fa fa-sign-out fa-fw"></i>Duplicate Job</a></li>
					       				<li><a href=""><i class="fa fa-sign-out fa-fw"></i>Delete Job</a></li>
					   				</ul>
								</div>
							</div>
						</div>								
					<!--End of panel  -->
					</div>	
				
			<!-- Close results column -->
			</div>