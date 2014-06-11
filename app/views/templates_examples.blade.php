@extends('layouts.default_new')

@section('container', 'full-container')

@section('head')
{{ stylesheet_link_tag('bootstrap-select.css') }}
{{ stylesheet_link_tag('bootstrap-dropdown-checkbox.css') }}
{{ stylesheet_link_tag('bootstrap.datepicker3.css') }}

<style>
.container {
	-webkit-transform:translatez(0);-webkit-backface-visibility:hidden;-webkit-perspective:1000;
}

/*.group {
    background: yellow;
    width: 200px;
    height: 500px;
}
.group .subgroup {
    background: orange;
    width: 150px;
    height: 200px;
}*/
.fixed {
    position: fixed;
}

/* sidebar */
.bs-docs-sidebar {
    padding-left: 20px;
    margin-top: 20px;
    margin-bottom: 20px;
}

/* all links */
.bs-docs-sidebar .nav>li>a {
    color: #999;
    border-left: 2px solid transparent;
    padding: 4px 20px;
    font-size: 13px;
    font-weight: 400;
}

/* nested links */
.bs-docs-sidebar .nav .nav>li>a {
    padding-top: 1px;
    padding-bottom: 1px;
    padding-left: 30px;
    font-size: 12px;
}

/* active & hover links */
.bs-docs-sidebar .nav>.active>a, 
.bs-docs-sidebar .nav>li>a:hover, 
.bs-docs-sidebar .nav>li>a:focus {
    color: #563d7c;                 
    text-decoration: none;          
    background-color: transparent;  
    border-left-color: #563d7c; 
}
/* all active links */
.bs-docs-sidebar .nav>.active>a, 
.bs-docs-sidebar .nav>.active:hover>a,
.bs-docs-sidebar .nav>.active:focus>a {
    font-weight: 700;
}
/* nested active links */
.bs-docs-sidebar .nav .nav>.active>a, 
.bs-docs-sidebar .nav .nav>.active:hover>a,
.bs-docs-sidebar .nav .nav>.active:focus>a {
    font-weight: 500;
}

/* hide inactive nested list */
.bs-docs-sidebar .nav ul.nav {
    display: block;           
}
/* show active nested list */
.bs-docs-sidebar .nav>.active>ul.nav {
    display: block;           
}

.affix {
	top:50px;
}

h3 {
	padding:15px 10px 10px 15px;
	border-bottom:1px solid #e5e3e3;
	color:#00086b;
}

section img {
	margin:0px 5px;
	border:1px solid #e5e3e3;
	border-top:none;
	border-bottom:none;
}


</style>
@stop

@section('content')
<!-- START search_content -->
<div class="col-xs-12">
	<div class='maincolumn CW_box_style'>
		<div class="page-header text-center" style="margin:10px;">
			<h2><a href='/'><i class="fa fa-angle-left" style="float:left; color:#999; display:inline-block; cursor:pointer"></i></a>Template Examples</h2>
		</div>		
@include('layouts.flashdata')

		<div class='tab'>
			<div class="row">
			    <!--Nav Bar -->
			    <nav class="col-xs-3 bs-docs-sidebar" style='position:relative;'>
			        <ul id="sidebar" class="nav nav-stacked" data-spy="affix" data-offset-top="140">
			            <li>
			                <a href="#EventsNews">News Events</a>
			                <ul class="nav nav-stacked">
			                    <li><a href="#Event">Event</a></li>
			                    <li><a href="#Time">Time</a></li>
			                </ul>
			            </li>
			            <li>
			                <a href="#FactSpan">Factor Span</a>
			            </li>
			            <li>
			                <a href="#Images">Images</a>
			                <ul class="nav nav-stacked">
			                    <li><a href="#WithBoundingBox">With bounding box</a></li>
			                    <li><a href="#WithoutBoundingBox">Without bounding box</a></li>
			                </ul>
			            </li>
			            <li>
			                <a href="#RelDir">Relation Direction</a>
			            </li>
			            <li>
			                <a href="#RelEx">Relation Extraction</a>
			            </li>
			            <li>
			                <a href="#Videos">Videos</a>
			                <ul class="nav nav-stacked">
			                    <li><a href="#VideoContent">VideoContent</a></li>
			                </ul>
			            </li>
			        </ul>
			    </nav>
			    <!--Main Content -->
			    <div class="col-xs-9">
			        <section id="EventsNews" class="group">
			            <h3><i class="fa fa-file-o"></i> Event Annotation in News</h3>
			            <div id="Event" class="subgroup">
							<div class='well'>
							<strong>Event annotation + event type:</strong> For the event extraction template we use a sentence with one
							putative event highlighted (capitalized), that was previously extracted. Each template is based on conditional statements ("if clause"), which lead the worker through the template parts. First, the worker is asked to judge whether the capitalized word phrase refers to an event, an action or none of them and motivate the answer. The second step applies only when the worker considers the capitalized word phrase an event/action. Then, he/she is asked to choose from the aforementioned list of event types each type that could be applied on the event.
							</div>
			                <img src='/images/templates/EventsNews/event/eventnews1.png' />
			                <img src='/images/templates/EventsNews/event/eventnews2.png' />                
			                <img src='/images/templates/EventsNews/event/eventnews3.png' />
			            </div>
			            <div id="Time" class="subgroup">
			                <h4>Time</h4>
			                <div class='well'>
			                	<strong>Event time/location + time/location type:</strong> The worker is given a sentence with one putative event highlighted. The first question constitutes a control question because it reviews the
first step of Event annotation + event type: the worker is asked to judge whether the capitalized word phrase refers to an event, an action or none of them and motivate the answer. The second step applies only when the worker considers the capitalized word phrase an event/action. Thus, the worker is asked to indicate whether the text contains a reference for the time/location of the event. If the answer is no, he/she is asked for a motivation. Otherwise, he/she is asked to highlight the words referring to the attribute and to choose its type from a type list.
							</div>
							<div class='well'>
								<strong>Event participants + participants type:</strong> The worker is given a sentence with one putative event highlighted. The first step consists of an iteration of the steps described above for Event
time/location. Because events could have more that one participant, the template allows a followup question to choose a second participant (replicate the highlight of participants from the sentence and the choosing of participant type).
							</div>
			                <img src='/images/templates/EventsNews/time/time1.png' />
			                <img src='/images/templates/EventsNews/time/time2.png' />                                
			            </div>
			        </section>
			        <section id="FactSpan" class="group">
			            <h3><i class="fa fa-file-o"></i> Factor Span</h3>
 <div class='well'>
	The crowd is given a sentence, together with the two highlighted seed terms, or factors, from distant supervision. For each factor, the crowd is asked to determine whether it is complete. If it is not, the workers highlight the words that would complete the factor. If the worker selects that the factor is complete, they are asked to re-highlight the factor and write a sentence with the factor as a verification step – it is important to make the most common selection a bit harder to ensure spammers will identify themselves. This type of template could ask for completing/verifying one or both factors in one task.
</div>			            
			            <img src='/images/templates/FactSpan/FactSpan1.png' />
			            <img src='/images/templates/FactSpan/FactSpan3.png' />
			            <img src='/images/templates/FactSpan/FactSpan3.png' />
			        </section>
			        <section id="Images" class="group">
			            <h3><i class="fa fa-file-o"></i> Images</h3>
			            <div class='well'>
			            	<strong>Depicted Flower Identification with Bounding Box</strong> - Rijksmuseum Amsterdam Use Case: The crowd is given an image that has a high chance of depicting flowers. We ask the crowd to identify all the flowers in them (by surrounding each flower with a box), and to fill in their names, the total number of flowers and the number of different flower types depicted. Another version of this template asks to tag flowers by giving only the name and the type, without surrounding the flowers with a box.
			            </div>
			            <div id="WithBoundingBox" class="subgroup">
			                <h4>With bounding box</h4>
				            <img src='/images/templates/Images/WithBoundingBox/imageTaggingWithBoundingBox1.png' />
				            <img src='/images/templates/Images/WithBoundingBox/imageTaggingWithBoundingBox2.png' />
				            <img src='/images/templates/Images/WithBoundingBox/imageTaggingWithBoundingBox3.png' />
				            <img src='/images/templates/Images/WithBoundingBox/imageTaggingWithBoundingBox4.png' />
			            </div>
			            <div id="WithoutBoundingBox" class="subgroup">
			                <h4>Without bounding box</h4>
				            <img src='/images/templates/Images/WithoutBoundingBox/imageTaggingWithoutBoundingBox1.png' />
				            <img src='/images/templates/Images/WithoutBoundingBox/imageTaggingWithoutBoundingBox2.png' />
				            <img src='/images/templates/Images/WithoutBoundingBox/imageTaggingWithoutBoundingBox3.png' />
			            </div>
			        </section>
			        <section id="RelDir" class="group">
			            <h3><i class="fa fa-file-o"></i> Relation Direction</h3>
			            <div class='well'>
			            The workers are given a sentence, two factors, and a relation that was given a high score in the RelEx task. They are asked to decide on the direction of the relation with regard to the two factors it connects. The workers can also select that no relation exists between the factors. For this task multiple answers are not permitted, all possible annotations for a task are disjoint. Since this task is so easy, golden units are inserted to more easily catch spam.
			            </div>	            
				        <img src='/images/templates/RelDir/RelDir.png' />            
			        </section>
			        <section id="RelEx" class="group">
			            <h3><i class="fa fa-file-o"></i> Relation Extraction</h3>
						<div class='well'>
							The workers are shown the medical sentence with two factors highlighted, and are asked to select all relations that are expressed in the sentence between them - 11 possible relations -, or NONE and OTHER. The workers have to also highlight in the sentence the words that express the relations, but if selecting NONE, the workers must explain in their own words why they made this selection, to increase the difficulty for selecting this choice. Other versions of this template could remove the last two verification question and add gold standard sentences. 
						</div>
						<div class='well'>
						<strong>Combined Relation Extraction and Relation Direction (RelExDir):</strong> As with RelEx, the workers are shown the medical sentences with two factors, and then are asked to check all relations that apply between them, except they get a set of 23 possible relations: the 11 non-symmetric relations from Table 2 with their inverses (e.g. causes and caused-by), plus NONE and OTHER. The relation definitions available include an explanation of the directionality.
						</div>      
				        <img src='/images/templates/RelEx/RelEx1.png' />            
				        <img src='/images/templates/RelEx/RelEx2.png' />            
				        <img src='/images/templates/RelEx/RelEx3.png' />
			        </section>

			        <section id="Videos" class="group">
			            <h3><i class="fa fa-file-o"></i> Event Annotation in Videos</h3>
						<div class='well'>
						<strong>Event Identification in Video Description:</strong> The crowd is given a set of previously extracted named and common entities from various NLP tools and is asked to confirm or reject any machine annotations on this text. Another approach consists in asking the crowd to highlight in the video description each possible event and event role filler (time, participants, location). 
						<br /><br />
						<strong>Event Identification in Video:</strong> The crowd is given a video or a video segment and is asked to annotate events that are depicted, i.e. literally mentioned in the video, or associated, i.e. related to some spoken events/role fillers in the video.						
						</div>			            
			            <div id="VideoContent" class="subgroup">
					        <img src='/images/templates/Videos/VideoContent/videoContent1.png' />
					        <img src='/images/templates/Videos/VideoContent/videoContent2.png' />
			            </div>
			        </section>    
			    </div>
			</div>			
		</div>
	</div>
</div>
@stop

@section('end_javascript')
@stop					