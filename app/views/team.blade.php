@extends('layouts.default_new')
@section('content')
			<!-- START /index --> 			
<div class="col-xs-12 col-md-8 col-md-offset-2">
	<div class='maincolumn CW_box_style'>
		@include('layouts.flashdata')	
		<div class="page-header text-center" style="margin:10px;">
			<h2><i class="fa fa-angle-left" style="float:left; color:#999; display:inline-block; cursor:pointer" onclick="javascript:window.history.back()"></i>The CrowdTruth team</h2>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<img src="/images/team/team.jpg" width='100%' alt="CrowdTruth Team" class="team-picture" />
					
				<p class="lead text-center">Interested in joining? Please  <a href="/contact" class="noastyle">contact</a> us</p>
			</div>
 
			<div class="col-xs-4">
			  <div class="thumbnail">
				<img src="/images/team/lora.jpg" alt="Lora Aroyo" class="img-circle" />
				  <p class='social-buttons'>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="https://twitter.com/laroyo"><i class="fa fa-twitter"></i></a>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="https://www.linkedin.com/in/laroyo"><i class="fa fa-linkedin"></i></a>
				  </p>
				<div class="caption">
				  <h4>Lora Aroyo</h4>
				  <p>Principal Investigator<br />VU University</p>
				</div>
			  </div>
			</div>
			 

			<div class="col-xs-4">
			  <div class="thumbnail">
				<img src="/images/team/chris.jpg" alt="Chris Welty" class="img-circle" />
				  <p class='social-buttons'>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="https://plus.google.com/+ChrisWelty/"><i class="fa fa-google-plus"></i></a>
				  </p>
				<div class="caption">
				  <h4>Chris Welty</h4>
				  <p>Principal Investigator<br />IBM Research, NY</p>
				</div>
			  </div>
			</div>
			 
			<div class="col-xs-4">
			  <div class="thumbnail">
				<img src="/images/team/rj.jpg" alt="Robert-Jan Sips" class="img-circle" />
				  <p class='social-buttons'>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="http://nl.linkedin.com/in/rsips"><i class="fa fa-linkedin"></i></a>
				  </p>
				<div class="caption">
				  <h4>Robert-Jan Sips</h4>
				  <p>Principal Investigator<br />IBM Netherlands</p>
				</div>
			  </div>
			</div>
			 
			<div class="col-xs-4">
			  <div class="thumbnail">
				<img src="/images/team/oana.jpg" alt="Oana Inel" class="img-circle" />
				  <p class='social-buttons'>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="http://nl.linkedin.com/pub/oana-inel/5a/99/711"><i class="fa fa-linkedin"></i></a>
				  </p>
				<div class="caption">
				  <h4>Oana Inel</h4>
				  <p>Researcher<br />IBM Netherlands<br />VU University</p>
				</div>
			  </div>
			</div>
			 
			<div class="col-xs-4">
			  <div class="thumbnail">
				<img src="/images/team/anca.jpg" alt="Anca Dumitrache" class="img-circle" />
				  <p class='social-buttons'>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="https://twitter.com/anouk_anca"><i class="fa fa-twitter"></i></a>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="https://nl.linkedin.com/in/dumitracheanca"><i class="fa fa-linkedin"></i></a>
				  </p>
				<div class="caption">
				  <h4>Anca Dumitrache</h4>
				  <p>PhD. Student<br />IBM Netherlands<br />VU University</p>
				</div>
			  </div>
			</div>

			<div class="col-xs-4">
			  <div class="thumbnail">
				<img src="/images/team/lukasz.jpg" alt="Lukasz Romaszko" class="img-circle" />
				  <p class='social-buttons'>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="https://www.linkedin.com/pub/lukasz-romaszko/60/898/702"><i class="fa fa-linkedin"></i></a>
				  </p>
				<div class="caption">
				  <h4>Lukasz Romaszko</h4>
				  <p>Researcher<br />IBM Netherlands<br />VU University</p>
				</div>
			  </div>
			</div>

			<div class="col-xs-4">
			  <div class="thumbnail">
				<img src="/images/team/benjamin.jpg" alt="Benjamin Timmermans" class="img-circle" />
				  <p class='social-buttons'>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="https://twitter.com/8w"><i class="fa fa-twitter"></i></a>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="http://nl.linkedin.com/in/btimmermans"><i class="fa fa-linkedin"></i></a>
				  </p>
				<div class="caption">
				  <h4>Benjamin Timmermans</h4>
				  <p>Researcher<br />IBM Netherlands<br /> VU University</p>
				</div>
			  </div>
			</div>

			<div class="col-xs-4">
			  <div class="thumbnail">
				<img src="/images/team/harriette.jpg" alt="Harriëtte Smook" class="img-circle" />
				  <p class='social-buttons'>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="https://twitter.com/harriet_te"><i class="fa fa-twitter"></i></a>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="https://www.linkedin.com/in/harriettesmook"><i class="fa fa-linkedin"></i></a>
				  </p>
				<div class="caption">
				  <h4>Harriëtte Smook</h4>
				  <p>Researcher<br />IBM Netherlands<br /> VU University</p>
				</div>
			  </div>
			</div>

			<div class="col-xs-4">
			  <div class="thumbnail">
				<img src="/images/team/carlos.jpg" alt="Carlos Martinez Ortiz" class="img-circle" />
				  <p class='social-buttons'>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="https://www.linkedin.com/in/carlosmartinezortiz"><i class="fa fa-linkedin"></i></a>
				  </p>
				<div class="caption">
				  <h4>Carlos Martinez Ortiz</h4>
				  <p>Engineer<br />eScience Center<br /><br /></p>
				</div>
			  </div>
			</div>

			<div class="col-xs-4">
			  <div class="thumbnail">
				<img src="/images/team/manfred.jpg" alt="Manfred Overmeen" class="img-circle" />
				  <p class='social-buttons'>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="http://nl.linkedin.com/in/manfredovermeen"><i class="fa fa-linkedin"></i></a>
				  </p>
				<div class="caption">
				  <h4>Manfred Overmeen</h4>
				  <p>Engineer<br />IBM Netherlands</p>
				</div>
			  </div>
			</div>
			
			<div class="col-xs-4">
			  <div class="thumbnail">
				<img src="/images/team/merel.jpg" alt="Merel van Empel" class="img-circle" />
				  <p class='social-buttons'>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="https://www.linkedin.com/pub/merel-van-empel/34/745/363"><i class="fa fa-linkedin"></i></a>
				  </p>
				<div class="caption">
				  <h4>Merel van Empel</h4>
				  <p>Msc. Student<br />VU University</p>
				</div>
			  </div>
			</div>

			<div class="col-xs-4">
			  <div class="thumbnail">
				<img src="/images/team/susanna.jpg" alt="Susanna van de Ven" class="img-circle" />
				  <p class='social-buttons'>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="https://nl.linkedin.com/in/susannevandeven"><i class="fa fa-linkedin"></i></a>
				  </p>
				<div class="caption">
				  <h4>Susanna van de Ven</h4>
				  <p>Msc. Student<br />VU University</p>
				</div>
			  </div>
			</div>

			<div style='clear:both;' />
			<p class="lead text-center" style='padding-top:50px;'>Former team members </p>

			<div class="col-xs-4">
			  <div class="thumbnail">
				<img src="/images/team/male.jpg" alt="Khalid Khamkham" class="img-circle" />
				  <p class='social-buttons'>
				  </p>
				<div class="caption">
				  <h4>Khalid Khamkham</h4>
				  <p>Msc. Student<br />IBM Netherlands<br />VU University</p>
				</div>
			  </div>
			</div>

			<div class="col-xs-4">
			  <div class="thumbnail">
				<img src="/images/team/tatiana.jpg" alt="Tatiana Cristea" class="img-circle" />
				  <p class='social-buttons'>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="https://www.linkedin.com/pub/tatiana-cristea/33/a05/b13"><i class="fa fa-linkedin"></i></a>
				  </p>
				<div class="caption">
				  <h4>Tatiana Cristea</h4>
				  <p>Msc. Student<br />IBM Netherlands<br />VU University</p>
				</div>
			  </div>
			</div>

			<div class="col-xs-4">
			  <div class="thumbnail">
				<img src="/images/team/rens.jpg" alt="Rens van Honschooten" class="img-circle" />
				  <p class='social-buttons'>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="https://www.linkedin.com/pub/rens-van-honschooten/42/a86/5b"><i class="fa fa-linkedin"></i></a>
				  </p>
				<div class="caption">
				  <h4>Rens van Honschooten</h4>
				  <p>Msc. Student<br />IBM Netherlands<br />VU University</p>
				</div>
			  </div>
			</div>
			
			<div class="col-xs-4">
			  <div class="thumbnail">
				<img src="/images/team/arne.jpg" alt="Arne Rutjes" class="img-circle" />
				  <p class='social-buttons'>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="http://nl.linkedin.com/in/arnerutjes"><i class="fa fa-linkedin"></i></a>
				  </p>
				<div class="caption">
				  <h4>Arne Rutjes</h4>
				  <p>Engineer<br />IBM Services Center Benelux</p>
				</div>
			  </div>
			</div>

			<div class="col-xs-4">
			  <div class="thumbnail">
				<img src="/images/team/hui.jpg" alt="Hui Lin" class="img-circle" />
				  <p class='social-buttons'>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="http://nl.linkedin.com/pub/hui-lin/53/92/b5"><i class="fa fa-linkedin"></i></a>
				  </p>
				<div class="caption">
				  <h4>Hui Lin</h4>
				  <p>BSc. Student<br />VU University</p>
				</div>
			  </div>
			</div>
			
			<div class="col-xs-4">
			  <div class="thumbnail">
				<img src="/images/team/jelle.jpg" alt="Jelle van der Ploeg" class="img-circle" />
				  <p class='social-buttons'>
					<a role="button" class="btn btn-default btn-circle" target='_blank' href="http://nl.linkedin.com/pub/jelle-van-der-ploeg/7/284/67a"><i class="fa fa-linkedin"></i></a>
				  </p>
				<div class="caption">
				  <h4>Jelle van der Ploeg</h4>
				  <p>Engineer<br />IBM Services Center Benelux</p>
				</div>
			  </div>
			</div>

			<div class="col-xs-4">
			  <div class="thumbnail">
				<img src="/images/team/male.jpg" alt="Guillermo Soberon" class="img-circle" />
				  <p class='social-buttons'>
				  </p>
				<div class="caption">
				  <h4>Guillermo Soberon</h4>
				  <p>MSc. Student<br />VU University</p>
				</div>
			  </div>
			</div>
				
		</div>
	</div>
</div>

<style type="text/css">
.paperlist li {
	padding-bottom: 10px;
}
.thumbnail {
	border: none;
}
.thumbnail .caption {
	text-align: center;
	padding:0px;
	margin-top:-10px;
}
.thumbnail .img-circle {
	max-height:150px;
}
.btn-circle {
	width: 30px;
	height: 30px;
	text-align: center;
	padding: 6px 0;
	font-size: 12px;
	line-height: 1.42;
	border-radius: 15px;
	opacity: 0;
    -webkit-transition: opacity 0.3s ease-in-out;
    -moz-transition: opacity 0.3s ease-in-out;
    -ms-transition: opacity 0.3s ease-in-out;
    -o-transition: opacity 0.3s ease-in-out;
    transition: opacity 0.3s ease-in-out;
}
.thumbnail:hover .btn-circle {
	opacity: 1;
}
.team-picture {
	border-radius: 25px;
}
.thumbnail .social-buttons {
	height:30px;
	margin-top:-15px;
	text-align: center;
}
.thumbnail .caption p {
	margin:0px;
}
</style>
@stop