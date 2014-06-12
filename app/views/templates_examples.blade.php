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

.affix {
	display:none;
}

h3 {
	padding:0px 10px 10px 15px;
	border-bottom:1px solid #e5e3e3;
	color:#00086b;
}

.bs-docs-sidebar > ul > li > a {
	font-weight:bold;
}

.group {
	background:#fff;
	box-shadow:inset 0 0 15px rgba(0,0,0,0.05);
	border:1px solid #EEE;
	padding:10px 20px;
	margin-bottom:15px;
}

.explanationText {
	padding:20px;
	background:#fff4f4;
	border:1px solid #EEE;
	border-left:3px solid red;
}

.group img {
	width:100%;
	border-left:3px solid yellow;
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

			    	<ul class='scrollMe'>
			    		<li><a href='#MedicalDomain'>Templates in Medical Domain</a>
					    	<ul>
					    		<li><a href='#FactSpan'>Medical Factors annotation in text (Factor Span correction)</a></li>
					    		<li><a href='#RelEx'>Medical Relation annotation in text (between Factors)</a></li>
					    		<li><a href='#RelDir'>Medical Relation Direction annotation in text (on given Relation)</a></li>
					    		<li><a href='#RelExDir'>Combined Medical Relation Extraction and Relation Direction (between Factors)</a></li>
					    	</ul>
			    		</li>
			    		<li><a href='#ArtDomain'>Templates in Art Domain</a>
					    	<ul>
					    		<li><a href='#WithBoundingBox'>Flower annotation in images (local with bounding box)</a></li>
					    		<li><a href='#WithoutBoungindBox'>Flower annotation in images (global for the whole image)</a></li>
					    	</ul>
			    		</li>
			    		<li><a href='#NewsDomain'>Templates in News Domain</a>
					    	<ul>
					    		<li><a href='#NewsTextEvents'>Event annotation in News text (including event properties)</a></li>
					    		<li><a href='#VideoEvents'>Event annotation in Videos</a></li>
					    	</ul>
			    		</li>
			    		<li><a href='#QADomain'>Templates in Question Answering Domain</a>
					    	<ul>
					    		<li><a href='#Justification'>Question type and answer justification annotation (Passage filtering)</a></li>
					    		<li><a href='#PassageAlignment'>Passage alignment annotation</a></li>
					    	</ul>
			    		</li>
			    	</ul>
			    </nav>
			    <!--Main Content -->
			    <div class='col-xs-9'>
			        <section id="MedicalDomain" class="group">
			            <h3><i class="fa fa-file-o"></i> Templates in Medical Domain:</h3>
			            <ul>
			            	<li id='FactSpan'><h3>FactSpan: Medical Factors annotation in text (Factor Span correction):</h3>
						            <ul>
						            	<li><strong>Goal:</strong> In this template the goal is to correct the span of the machine-selected medical Factors in a sentence. </li>
						            	<li><strong>Annotation setting:</strong> The workers are given a medical sentence with two highlighted terms (factors) in it. For each factor they are asked to determine whether it is complete. If the worker considers the factor not complete, then they highlight words in the sentence that complete the factor. If worker considers the factor complete, then they confirm the highlighted terms and provide an explanation text as a verification step. For this task multiple answers are allowed.</li>
						            	<li><strong>Result vector:</strong> The crowd answers are stored in a vector, which captures each selection of the crowd as following, [3-words-left-of-factor, 2-words-left-of-factor,  1-words-left-of-factor, factor, 1-words-right-of-factor, 2-words-right-of-factor, 3-words-right-of-factor, OTHER, Answer-Validation]</li>
						            	<li><strong>Possible adaptations of this template</strong>This template could be executed in two versions, (1) with one pre-selected Factor, or (2) with two pre-selected Factors. This template has been used as a basis for the Passage Alignment template, in the Question Answering domain.</li>
						            </ul>
								<br/>           
								<img src='/images/templates/FactSpan/FactSpan1.png' />
								<img src='/images/templates/FactSpan/FactSpan2.png' />
								<img src='/images/templates/FactSpan/FactSpan3.png' />						            
			            	</li>
			            	<li id='RelEx'><h3>RelEx: Medical Relation annotation in text (between Factors):</h3>
						            <ul>
						            	<li><strong>Goal:</strong>In this template the goal is to provide the set of relations that are expressed in a sentence between prior selected Factors.</li>
						            	<li><strong>Annotation setting:</strong>The workers are given a medical sentence with two highlighted terms (factors) in it. They are asked to select all relations (out of 11 possible) that are expressed in the sentence between the two factors. If there is no relation expressed in this sentence between the two factors they need to choose NONE. If there is a relation, but it is not on the list of 11 possible relations, they need to choose OTHER. In both cases, provide an explanation text as a verification step. The workers highlight in the sentence the exact words that express the selected relations. For this task multiple answers are allowed.</li>
						            	<li><strong>Result vector:</strong>The crowd answers are stored in a vector, which captures each selection of the crowd as following: [R1, R2, R3, R4, R5, R6, R7, R8, R9, R10, R11, NONE, OTHER, Answer-Validation-NONE, Answer-Validation-OTHER]</li>
						            	<li><strong>Possible adaptations of this template</strong>This template could be executed in three versions, (1) with verification question, (2) without verification questions and (3) with gold sentences. </li>
						            </ul>
								<br/>           
						        <img src='/images/templates/RelEx/RelEx1.png' />            
						        <img src='/images/templates/RelEx/RelEx2.png' />            
						        <img src='/images/templates/RelEx/RelEx3.png' />
			            	</li>
			            	<li id='RelDir'><h3>RelDir: Medical Relation Direction annotation in text (on given Relation): </h3>
						            <ul>
						            	<li><strong>Goal:</strong>In this template the goal is to provide the direction of the selected relation provided by the RelEx task. </li>
						            	<li><strong>Annotation setting:</strong>The workers are given a medical sentence with two highlighted terms (factors) and a relation between the factors. They are asked to select one of three options (1) the relation direction is term1 → term2, (2) the relation direction is term2 → term1, or (3) there is no relation between the two terms. For this task only one answer is allowed. Additionally, gold sentences are used to easily identify low quality or spam.</li>
						            	<li><strong>Possible adaptations of this template:</strong>This template could be executed in two versions, (1) without gold sentences, (2) without gold sentences. </li>
						            	<li><strong>Result vector:</strong>The crowd answers are stored in a vector, which captures each selection of the crowd as following: [DirT1T2, DirT2T1, NoDir] </li>
						            </ul>
								<br/>           
						        <img src='/images/templates/RelDir/RelDir.png' />            
			            	</li>
			            	<li id='RelExDir'><h3>RelExDir: Combined Medical Relation Extraction and Relation Direction:</h3>
						            <ul>
						            	<li><strong>Goal:</strong>In this template the goal is to provide the relation as well as the direction of the selected relation.</li>
						            	<li><strong>Annotation setting:</strong>The workers are given a medical sentence with two highlighted terms (factors) in it. They are asked to select all relations that apply between them. The set of relations contains 23 possible relations: the 11 non-symmetric relations used in RelEx with their inverses (e.g. causes and caused-by), plus NONE and OTHER. The relation definitions available include an explanation of the directionality. </li>
						            	<li><strong>Possible adaptations of this template:</strong>This template could be executed in four versions, (1) with verification question, (2) without verification questions, (3) with gold sentences and (4) without gold sentences. </li>
						            	<li><strong>Result vector:</strong>The crowd answers are stored in a vector, which captures each selection of the crowd as following: [R1, R1-inverse, R2, R2-inverse, R3, R3-inverse, R4, R4-inverse, R5, R5-inverse, R6, R6-inverse, R7, R7-inverse, R8, R8-inverse, R9, R9-inverse, R10, R10-inverse, R11, R11-inverse, NONE, OTHER, Answer-Validation-NONE, Answer-Validation-OTHER, Answer-Validation-Directionality] </li>
						            </ul>        
			            	</li>
			            </ul>
			        </section>
			        <section id="ArtDomain" class="group">
			            <h3><i class="fa fa-file-o"></i> Image Tagging:</h3>
			            <ul>
			            	<li><h3>FlowerEx: Flower annotation in images (local with bounding box): </h3>
						            <ul>
						            	<li><strong>Goal:</strong> In this template the goal is to provide flower annotations in images.</li>
						            	<li><strong>Annotation setting:</strong> The workers are given an image that has a high chance of depicting flowers. They are asked to identify all the flowers that appear by surrounding each flower with a box, and to fill in their names, the total number of flowers and the number of different flower types depicted. </li>
						            	<li><strong>Result vector:</strong> The crowd answers are stored in a vector which captures each selection of the crowd as following: each different flower name with the number of selections [FT1, FT2, … , FTn] </li>
						            	<li><strong>Possible adaptations of this template:</strong>This template could be also executed in a global version: tag flowers by giving only the name and the type and without surrounding the flowers with boxes.</li>
						            </ul>
								<br/>
								<h3 id='WithBoundingBox'> With bounding box </h3>       
					            <img src='/images/templates/Images/WithBoundingBox/imageTaggingWithBoundingBox1.png' />
					            <img src='/images/templates/Images/WithBoundingBox/imageTaggingWithBoundingBox2.png' />
					            <img src='/images/templates/Images/WithBoundingBox/imageTaggingWithBoundingBox3.png' />
					            <img src='/images/templates/Images/WithBoundingBox/imageTaggingWithBoundingBox4.png' />
								<br/>
								<h3 id='WithoutBoundingBox'> Without bounding box </h3>       
					            <img src='/images/templates/Images/WithoutBoundingBox/imageTaggingWithoutBoundingBox1.png' />
					            <img src='/images/templates/Images/WithoutBoundingBox/imageTaggingWithoutBoundingBox2.png' />
					            <img src='/images/templates/Images/WithoutBoundingBox/imageTaggingWithoutBoundingBox3.png' />
			            	</li>
			            </ul>
			        </section>			        
			        <section id="NewsDomain" class="group">
			            <h3><i class="fa fa-file-o"></i> Event Annotation in News:</h3>
			            <ul>
			            	<li id='NewsTextEvents'><h3>EventEx: Event and Event Type Identification: </h3>
						            <ul>
						            	<li><strong>Goal:</strong> In this template the goal is to judge whether a highlighted word phrase expresses an event in a sentence.</li>
						            	<li><strong>Annotation setting:</strong> The workers are given a sentence with one putative event highlighted (capitalized), that was previously extracted. First, they are asked to judge whether the capitalized word phrase refers to an event, an action or none of them and motivate the answer. The second step applies only when the worker considers the capitalized word phrase an event/action. Then, they are asked to choose from a list of event types (Purpose, Arriving_or_Departing, Motion, Communication, Usage, Judgment, Leadership, Success_or_failure, Sending_or_receiving, Action, Attack, Political, Other, Not_applicable) each type that could be applied on the event.</li>
						            	<li><strong>Result vector:</strong> The crowd answers are stored in a vector, which captures each selection of the crowd as following: [EvT1, EvT2, EvT3, EvT4, EvT5, EvT6, EvT7, EvT8, EvT9, EvT10, EvT11, EvT12, OTHER, NOT_APPLICABLE] </li>
						            	<li><strong>Possible adaptations of this template</strong>This template could be executed in 2 versions: (1) with verification/explanation question, (2) without verification/explanation question</li>
						            </ul>
								<br/>           
				                <img src='/images/templates/EventsNews/event/eventnews1.png' />
				                <img src='/images/templates/EventsNews/event/eventnews2.png' />                
				                <img src='/images/templates/EventsNews/event/eventnews3.png' />
			            	</li>
			            	<li id='LocEx'><h3>LocEx, TimeEx, PartEx: Event Location, Time & Participants Identification: </h3>
						            <ul>
						            	<li><strong>Goal:</strong> In this template the goal is to identify the location, the time or the participants of the event that is highlighted in a sentence.</li>
						            	<li><strong>Annotation setting:</strong> The workers are given a sentence with one putative event highlighted. The first question constitutes a control question because it reviews the first step of EventEx: the workers are asked to judge whether the capitalized word phrase refers to an event, an action or none of them and motivate the answer. The second step applies only when the worker considers the capitalized word phrase an event/action. Thus, the workers are asked to indicate whether the text contains a reference for the time/location/participants of the event. If the answer is no, they are asked for a motivation. Otherwise, they are asked to highlight the words referring to the attribute and to choose their type from a type list. Because events could have more that one participant, the template allows a followup question to choose a second participant (replicate the highlight of participants from the sentence and the choosing of participant type).</li>
						            	<li><strong>Result vector:</strong> The crowd answers are stored in a vector, which captures each selection of the crowd as following: [EvRFT1, EvRFT2, … EvRFTn, OTHER, NOT_APPLICABLE], EvRFTi - Event Role Filler (time, location, participant) Type n </li>
						            	<li><strong>Possible adaptations of this template</strong>This template could be executed in 2 versions: (1) with motivation/explanation question, (2) without motivation/explanation question</li>
						            </ul>
								<br/>           
				                <img src='/images/templates/EventsNews/time/time1.png' />
				                <img src='/images/templates/EventsNews/time/time2.png' />                                
			            	</li>
			            </ul>
			        </section>
			        <section id="VideoEvents" class="group">
			            <h3><i class="fa fa-file-o"></i> Event Annotation in Videos:</h3>
			            <ul>
			            	<li><h3>VidEventEx: Event Identification in Video:</h3>
						            <ul>
						            	<li><strong>Goal:</strong> In this template the goal is to identify events and event role fillers in video content.</li>
						            	<li><strong>Annotation setting:</strong> The workers are given a video or a video segment. They are asked to annotate events and event role fillers that are depicted, i.e literally mentioned in the video, or associated, i.e. related to some spoken events/role fillers in the video..</li>
						            	<li><strong>Result vector:</strong> The crowd answers are stored in a vector which captures each selection of the crowd as following: each different annotation with the number of selections [EvA1, EvA2, … , EvAn]</li>
						            	<li><strong>Possible adaptations of this template</strong>This template could be executed in 3 versions: (1) ask only for event annotations or one event role filler at a time, (2) extra question for identifying which events/role fillers were depicted and which associated, (3) cluster the annotations in events, periods, participants and locations.</li>
						            </ul>
								<br/>           
					        <img src='/images/templates/Videos/VideoContent/videoContent1.png' />
					        <img src='/images/templates/Videos/VideoContent/videoContent2.png' />
			            	</li>
			            	<li><h3>DescEventEx: Event Identification in Video Description:</h3>
					            <ul>
					            	<li><strong>Goal:</strong> In this template the goal is to identify events and event role fillers in video metadata description.</li>
					            	<li><strong>Annotation setting:</strong>The workers are given the description of a video. They are asked to highlight each event or event role filler that appears in the video description. </li>
					            	<li><strong>Possible adaptations of this template:</strong>This template could be executed in 3 versions: (1) ask only for event annotations or one event role filler at a time, (2) ask to confirm or reject machine annotations (named or common entities extracted by various NLP tools), (3) cluster the annotations in events, periods, participants and locations.</li>
					            	<li><strong>Result vector:</strong>The crowd answers are stored in a vector which captures each selection of the crowd as following: each different annotation with the total number of selections [EvA1, EvA2, … , EvAn] </li>
					            </ul>                             
			            	</li>			            	
			            </ul>
			        </section>

			        <section id="QADomain" class="group">
			            <h3><i class="fa fa-file-o"></i> Templates in Question Answering Domain:</h3>
			            <ul>
			            	<li id='Justification'><h3>Question type and answer justification annotation (Passage filtering)</h3>
						            <ul>
						            	<li><strong>Goal:</strong> In this template the goal is to find passages that give the answer to a question.</li>
						            	<li><strong>Annotation setting:</strong> The worker is asked to choose whether a given question makes sense, or if the answer to the question is subjective, yes or no, or a different answer. For up to six passages, the worker then has to select those that give the answer to that question. Last, the worker has to give the answer to the question based on what was read in the passages. The answer can be yes, no or other. The worker can also choose that none of the given passages contains the answer, or that the question makes no sense. </li>
						            	<li><strong>Result vector:</strong> The crowd answers are stored in a vector, which captures each selection as the question type T, answer A and justifying passage P of the crowd as following, [T1A1,T1A2,T1A3,T1A4,T1A5, T2A1,T2A2,T2A3,T2A4,T2A5, T3A1,T3A2,T3A3,T3A4,T3A5,T4A1,T4A2,T4A3,T4A4,T4A5, P1, P2, P3, P4, P5, P6, BadCombination, AnswerNoJustification, JustificationNoAnswer] </li>
						            	<li><strong>Possible adaptations of this template</strong>The worker could be made aware that their annotation does not make sense e.g. if the answer to the question is other than yes or no, but the given answer is yes.</li>
						            </ul>
								<br/>     
					            <img src='/images/templates/QA/image00.png' />
			            	</li>
			            	<li id='PassageAlignment'><h3>Passage alignment annotation</h3>
						            <ul>
						            	<li><strong>Goal:</strong> In this template the goal is to find matching terms between a question and a passage.</li>
						            	<li><strong>Annotation setting:</strong> The worker is asked to highlight matching terms from a question and a passage. For each selected pair, the worker has to select whether the terms are identical, synonyms, a generalization, paraphrase, negation or partially overlap. The worker has to explain why it is not possible to match more terms, if less than three pairs have been annotated. </li>
						            	<li><strong>Result vector:</strong> The crowd answers are stored in a vector, which captures each matched pair and its type T of the crowd as following, [questionId, questionTerm, passageId, passageTerm, T1, T2, T3, T4, T5, T6]</li>
						            	<li><strong>Possible adaptations of this template</strong>Identical terms could be matched on forehand.</li>
						            </ul>
								<br/>     
					            <img src='/images/templates/QA/image01.png' />
			            	</li>
			            </ul>
			        </section>
			    </div>
			</div>			
		</div>
	</div>
</div>
@stop

@section('end_javascript')
<script>
	$().ready(function() {
    var $sidebar   = $(".scrollMe"), 
        $window    = $(window),
        offset     = $sidebar.offset(),
        topPadding = 15;

    $window.scroll(function() {
        if ($window.scrollTop() > offset.top) {
            $sidebar.stop().animate({
                marginTop: $window.scrollTop() - offset.top + topPadding
            }, 0);
        } else {
            $sidebar.stop().animate({
                marginTop: 0
            }, 0);
        }
    });
	});
</script>
@stop					