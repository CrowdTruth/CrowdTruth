@extends('layouts.default_new')
@section('content')
			<!-- START /index --> 			
			<div class="col-xs-8 col-md-offset-2">
				<div class='maincolumn CW_box_style'>
@include('layouts.flashdata')	
	<div class="page-header text-center" style="margin:10px;">
						<h2><i class="fa fa-angle-left" style="float:left; color:#999; display:inline-block; cursor:pointer" onclick="javascript:window.history.back()"></i>Presentations <small>by the CrowdTruth team</small></h2>
					</div>
					<div class="row">
						<div style="padding: 20px 20px 40px 20px;">
						
							<b class='presentation-title'>Presentations</b>
							
							<ul class='presentations'>	
								<li>
									<iframe src="//www.slideshare.net/slideshow/embed_code/36692256" width="310" height="271" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="max-width: 100%;" allowfullscreen> </iframe>
									<div>
										<strong><a href="https://www.slideshare.net/laroyo/crowdsourcing-semantic-web-dagstuhl-2014-presentation-lora">Crowdsourcing &amp; Semantic Web: Dagstuhl 2014</a></strong>
										<p>Lora Aroyo, Dagstuh 2014</p>
									</div>
								</li>
								<li>
									<iframe src="//www.slideshare.net/slideshow/embed_code/36692523" width="310" height="271" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="max-width: 100%;" allowfullscreen> </iframe>
									<div>
										<strong><a href="https://www.slideshare.net/laroyo/presentation-chris-crowdsourcing-semantic-web-dagstuhl-2014">How to Measure Quality with Disagreement? or the Three Sides of CrowdTruth</a></strong>
										<p>Chris Welty, Dagstuh 2014</p>
									</div>
								</li>
								<li>
									<iframe src="//www.slideshare.net/slideshow/embed_code/38231843" width="310" height="271" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="max-width: 100%;" allowfullscreen> </iframe>
									<div>
										<strong><a href="https://www.slideshare.net/laroyo/truth-is-a-lie-7-myths-about-human-annotation-cogcomputing-forum-2014">Truth is a Lie: 7 Myths about Human Annotation</a></strong>
										<p>Lora Aroyo, Cognitive Computing Forum 2014</p>
									</div>
								</li>
								<li>
									<iframe src="//www.slideshare.net/slideshow/embed_code/36245658" width="310" height="271" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="max-width: 100%;" allowfullscreen> </iframe>
									<div>
										<strong><a href="https://www.slideshare.net/laroyo/web-sci2014-pechakuchasealincmedia">Crowdsourcing Knowledge-Intensive Tasks In Cultural Heritage: SealincMedia Accurator demonstrator</a></strong>
										<p>Lora Aroyo, ACM Web Science 2014</p>
									</div>
								</li>
								<li>
									<iframe src="//www.slideshare.net/slideshow/embed_code/35116645" width="310" height="271" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="max-width: 100%;" allowfullscreen> </iframe>
									<div>
										<strong><a href="https://www.slideshare.net/laroyo/crowds-niches-teaching-machines-to-diagnose-nlesc-kick-off-ehumanities-projects-2014">Crowds & Niches Teaching Machines to Diagnose: NLeSC Kick off eHumanities projects 2014</a></strong>
										<p>Lora Aroyo, May 26th 2014</p>
									</div>
								</li>
							</ul>
							<div style='clear:both;' />
						
						
						
							<b class='presentation-title'>Student Presentations</b>
							
							<ul class='presentations'>	
								<li>
									<iframe src="//www.slideshare.net/slideshow/embed_code/37462270" width="310" height="271" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="max-width: 100%;" allowfullscreen> </iframe>
									<div>
										<strong><a href="http://www.slideshare.net/CrowdTruth/thesis-presentation-b-timmermans">Crowdsourcing Disagreement on Open-Domain Questions</a></strong>
										<p>Benjamin Timmermans, July 18th 2014</p>
									</div>
								</li>
								<li>
									<iframe src="//www.slideshare.net/slideshow/embed_code/37463102" width="310" height="271" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="max-width: 100%;" allowfullscreen> </iframe>
									<div>
										<strong><a href="http://www.slideshare.net/CrowdTruth/presentation-rens-2014-june-18">Gamification of crowdsourcing tasks: What motivates a medical expert?</a></strong>
										<p>Rens van Honschooten, July 18th 2014</p>
									</div>
								</li>
								<li>
									<iframe src="//www.slideshare.net/slideshow/embed_code/39413587" width="310" height="271" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="max-width: 100%;" allowfullscreen> </iframe>
									<div>
										<strong><a href="https://www.slideshare.net/CrowdTruth/thesis-39413587">Visualization of Disagreement-based Quality Metrics of Crowdsourcing Data</a></strong>
										<p>Tatiana Cristea, September 23rd 2014</p>
									</div>
								</li>
							</ul>
							<div style='clear:both;' />
						</div>
					</div>
				</div>
			</div>
			<style type="text/css">
			.paperlist li {
				padding-bottom: 10px;
			}
			</style>
@stop
