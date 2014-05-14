@extends('layouts.default')

@section('content')
			<!-- START /index --> 	
			<style>
				h4 {padding-top:20px;}
			</style>		
			<div class="col-xs-12">
				<div class='maincolumn CW_box_style'>
@include('layouts.flashdata')	
<style>
h4 {margin-top:40px;}
img {padding:10px;}
</style>
					<div class="row">
						<div class="col-xs-3">
						<ul class="nav nav-pills nav-stacked"  data-spy="scroll" data-target=".scrolldiv">
						 <li><h4>How to use CrowdTruth</h4></li>
						 <li><a href="#process1">1. Exploring media</a></li>
						 <li><a href="#process2">2. Upload new media</a></li>
						 <li><a href="#process3">3. Pre-process existing media</a></li>
						 <li><a href="#process4">4. Create a media batch</a></li>
						 <li><a href="#process5">5. Create new crowdsourcing job</a></li>
						 <li><a href="#process6">6. Executing crowdsourcing job</a></li>
						 <li><a href="#process7">7. Collecting results from crowdsourcing platforms</a></li>
						 <li><a href="#process8">8. Analyze job results and data</a></li>
						 <li><a href="#process9">9. Analyze workers jobs and contributions</a></li>
						 <li><a href="#process10">10. Export data from CrowdTruth</a></li>
						 <li><h4>The inner workings</h4></li>
						 <li><a href="#inner1">1. The datamodel</a></li>
						 <li><a href="#inner2">2. Provenance</a></li>
						 <li><a href="#inner3">3. API</a></li>
						 <li><a href="#inner4">5. AMT SDK</a></li>
						 <li><a href="#inner5">4. CF SDK</a></li>
						 <li><a href="#inner6">6. View Code</a></li>
						 <li><a href="#inner7">7. Extending CrowdTruth</a></li>
						 <li><a href="#inner7a">&nbsp;&nbsp;A. Adding a crowdsourcing platform</a></li>
						 <li><a href="#inner7b">&nbsp;&nbsp;B. Adding a template</a></li>
						 <li><a href="#inner7c">&nbsp;&nbsp;C. Adding an online source</a></li>
						 <li><a href="#inner7d">&nbsp;&nbsp;D. Adding a pre-processor</a></li>
						 <li><a href="#inner7e">&nbsp;&nbsp;E. Adding a domain</a></li>
						 <li><a href="#inner7f">&nbsp;&nbsp;F. Adding a documentType</a></li>
						</ul>
					</div>
						<div class="scrolldiv col-xs-9">
							<h2 id="top">CrowdTruth Documentation</h2>
								<p>Welcome to the CrowdTruth framework.</p>
								<p>Crowd Truth Framework implements an approach to machine-human computing for collecting annotation data on text, images and videos. The approach is focussed specifically on collecting gold standard data for training and evaluation of cognitive computing systems. The original framework was inspired by the IBM Watson project for providing improved (multi-perspective) gold standard (medical) text annotation data for the training and evaluation of various IBM Watson components, such as Medical Relation Extraction, Medical Factor Extraction and Question-Answer passage alignment.</p>
								<p>The CrowdTruth framework supports the composition of crowd-truth gathering workﬂows, where a sequence of micro-annotation-tasks can be configured and sent out to a number of crowdsourcing platforms (e.g. CrowdFlower and Amazon Mechanical Turk) and applications (e.g. Expert annotation game Dr. Detective). The CrowdTruth framework has a special focus on micro-tasks for knowledge extraction in medical text (e.g. medical documents, from various sources such as Wikipedia articles or patient case reports). The main steps involved in the CrowdTruth workﬂow are: (1) exploring & processing of input data, (2) collecting of annotation data, and (3) applying disagreement analytics on the results. These steps are realized in an automatic end-to-end workﬂow, that can support a continuous collection of high quality gold standard data with feedback loop to all steps of the process.</p>
							<h3 id="header1">How to use CrowdTruth</h3>
								<p>In this section you will find guidance with all the activities you can perform on the framework.</p>
							<h4 id="process1">Explore media</h4>
								<img src="images/media.png"></img><br><br>
								<p>When you select the Media tab in the top of the screen you get an overview of all the media that is uploaded to your instance of the CrowdTruth framework. Mouse-over the column heads to explore the several options to filter and sort your selection with the purpose to create a ‘batch’. A batch is a set of media that you can include in a job. How this is done, is explained in <a href="#process4">this section</a>.</p>
							<h4 id="process2">Upload new media</h4>
								<p>You can upload media to the platform either by uploading your own files or by adressing online resources. Start by pressing the upload media button in the top right corner of the screen. The form consists of two parts. 
								<img src="images/uploadmedia.png" class="pull-right"></img>
								</p>
								<p>In the upper part you can upload media from your workstation onto the platform. First, you have to select the format of the media you want to upload. This can be either image, video or text. Currently, the domains are limited and coupled to the media format. For instance, when you have selected text, you get the following options for domain: medical, article, other. In your own instance of CrowdTruth you can add domains. Please have a look at the section <a href="#inner7e">‘Adding a domain’</a>. The following step is to select the document type, this is a specific for the type of media you want to upload and can be similar across domains but will differ per media format. For instance, you can upload a document type ‘article’ in the domain medical. This will obviously be a text format. Similarly, you can upload an ‘article’ in the domain art, in case the article is about art. When this combination is not possible yet, please have a look at the sections <a href="#inner7f">‘Adding a documentType’</a> and <a href="#inner7e">‘Adding a domain’</a>. </p>
								<p>In the bottom form you can select one of our online APIs to pull media from. Currently there are two APIs, one for video material and one for images. The APIs are extendable, please have a look at the section <a href="#inner7c">‘Adding an online source’</a>.</p>
							<h4 id="process3">Pre-process existing media</h4>
								<p>
									<img src="images/preprocess.png" class="pull-right"></img>In order to receive high-quality annotations from the crowd, you might have to pre-process your media files into a certain format. Currently we offer pre-processing for video and RelEx. The video pre-processor recognizes key-frames and gives the number and length of segments. The RelEx-preprocessor highlights the sentence terms and the relationship. It is possible to add your own pre-processors based on the type of documents you are working with. Read more about it in <a href="#inner7d">this section</a>.</p>
							<h4 id="process4">Create media batch</h4>
								<p>By applying filters to your selection you can select a set of media files that fit well together in one crowdsourcing job. For example, you would like to receive annotation data on the direction of the relation between two terms in medical sentences with 30 to 40 words where the relationship is symptom. You could approach this as follows: </p>
								<ol type="1">
									<li>Filter on media type RelEx</li>
									<li>In the domain column, type in the field above it ‘medical’</li>
									<li>In the relation column, type in the field above it 'symptom'</li>
									<li>Click on the downward pointing caret on the 'Open all columns'-button and select #Words</li>
									<li>Fill in 30 and 40 in the input fields above the column that just appeared</li>
									<li>Now, click on 'Select all', followed by 'Create batch' to create a batch out of the selected sentences</li>
								</ol>
								<p>As you can see there are also filters specifically for some of the documentTypes that show when you hover over the ‘Specific filters’-button. Additionally, you can sort the rows based on any of the columns that show, both ascending and descending. This will help you to get insight in your dataset, by for instance being able to easily explore how the longest sentences perform compared to shorter sentences. In a similar fashion, you can create a selection based on any requirements you have for a batch. Of course, you can also analyze a selection of your media with these flexible filtering and sorting options.</p>
							<h4 id="process5">Creating new crowdsourcing job</h4>
								<p>After creating a batch, you can create a job to send to a crowdsourcing platform. A job consists out of three elements, a batch, a job configuration and a question template. By now you should have the first. In the process of creating a job, we are gonna make the job configuration and we are gonna select a question template for the job.</p>
								<p>When you open the Jobs tab you get an overview of all the jobs that are in the database. In the section <a href="#inner8">‘Analyze job results and data’</a> you can read more about what you can do with these. There are two ways of creating a new job, the first one is to start from scratch by clicking on the ‘Create job’-button, the other is by duplicating an existing job. You can duplicate an existing job by selecting a job, followed by clicking ‘Duplicate job’ under the actions available for that job.<img src="images/duplicatejob.png" class="pull-right"></img></p>
								<p>After opening the job-creation wizard the first step is to select the batch you want to use for the job. In the second step of the wizard you can select a template. Currently we offer a set of templates that are shown based on the document type of the batch. Essentially, the templates are the shape in which you let the workers perform the task. It is possible to upload your own templates which are a html file with javascript. You can read how to do this in <a href="#inner3">this</a> section.</p>
								<p>Next, you have to select the platform that you want to send your job to. The choice for your platform can depend on several factors, for instance, because CrowdFlower connects to many other crowdsourcing platforms it is generally faster, however the level of spammers is generally higher than on AMT. Of course, you can also send it to multiple platforms.</p>
								<p>After you have selected the template that fulfills your request for the kind of annotations you want and the platform(s) you want to send it to, you can give the job more shape by giving it a configuration. Most of the configuration gets filled in automatically with the standard information that is provided with the template you selected earlier. Depending on the crowdsourcing platform(s) you selected, you have to configure the specifics for these platforms. Finally, you get an overview of the job details and an example of what the questions will look like for the workers. The only thing that is left to do to create the job, is to submit the form.</p>
							<h4 id="process6">Execute crowdsourcing job</h4>
								<p>Once created, the job is send to the platform and it is waiting there for you to order it. You can simply order the job by selecting it and selecting ‘Order job’ under actions.</p>								
							<h4 id="process7">Collecting results from crowdsourcing platforms</h4>
								<p>After you have ordered a job on one of the platforms, the CrowdTruth framework polls this server every 20 minutes during the daytime and hourly overnight. It automatically performs metrics on the data it receives from the server. This means that when you see information on your job or on the workers in either the jobs or workers tab, this already passed through the metrics provided by the CrowdTruth framework.</p>
							<h4 id="process8">Analyze job results and data</h4>
								<p>After ordering jobs and the automatic retrieval of them, you can find your jobs and data under the Jobs-tab. The table, similar as the table for Media, can show all the properties of the jobs, no matter the status (running/finished/cancelled/etc). Depending on the status you can perform actions on them in the status column. Simply mouse-over the status of the job to see what the possible actions are. You can for instance, duplicate the job. This will take you to the job creation wizard and will pre-select the right batch for you. It will also load the same job configuration for you, which makes the workflow incredibly easy. You can for instance run the same batch with a different template or with a different reward with just a few mouse-clicks. Concerning the table, lastly, it is equally easy to apply filters on the jobs or sort the jobs differently as it is with Media. When you choose to open all columns you can filter on all the properties of the job. To see what each property is, you can hover over the table header for more information.</p>
								<img src="images/individualjob.png" class="col-xs-12"></img>
								<p>The Jobs-tab offers two more detailed sources of information and analytics. First, you can click on the Job-id to get information on the particular job. This pop-up has four tabs, showing general job information, descriptive statistics on the job level, descriptive statistics on the unit level and descriptive statistics on the worker-level, all in relation to the job. Secondly, for a visualization of the analytics, you can click the 'Graph view'-button. This will show relevant visualizations on the selection of jobs. Thus, similarly as in the table view, you can apply filters to these pie- and barcharts. First, in the table, apply your filters and consequently choose to have a graph-view.</p>
								<img src="images/jobgraphs.png" class="col-xs-12"></img>
							<h4 id="process9">Analyze workers and contributions</h4>
								<p>In many ways similar to the jobs page, the worker page can display a wealth of information. Please take your time to explore this page, mouseover and click on the charts and legends to see what the possibilities are. First choose which columns you want to show at the top, apply any filters you like and select a number of individual workers to compare them in detail. Notice that workers of different platforms can be compared just as easily. To get a deeper understanding of the differences, you can click on individual annotations and even open a page with the details of the annotated units. The possibilities are almost endless, and we encourage you to go explore on your own :)</p>
								<img src="images/workerpage.png" class="col-xs-12"></img>
							<h4 id="process10">Export data from CrowdTruth</h4>
								<p>On the Media tab, apply your filters and press Actions->Export to CSV to export the selected units. On every graph view page, you can export individual charts by pressing the icon in the top right corner.</p><p><img src="images/exportmedia.png"></img><img src="images/exportchart.png"></img></p>
							<h3 id="header2">Inside CrowdTruth</h3>
								<p>This section describes the modules of the framework, and how you can add new modules to the system.</p>
							<h4 id="inner1">1. The datamodel</h4>
								<p>In order for the Crowd Truth framework to handle various types of data, a suitable data storage system with an appropriate data model is needed. Traditional SQL database systems, rely heavily on predefined schemas whereby the structure of the data is known beforehand. However, for our use case, structuring of data is something that we are trying to achieve, this means that input data for the Crowd Truth framework is oftentimes only semi-structured at best. </p>
								<p>We opted for MongoDB instead: a document-oriented NoSQL database that does not rely on predefined schemas, rather the structure of the data stored can be defined dynamically at any point in time because of its flexible schema. This flexibility is a key requirement, because for the purposes of structuring and annotation of data, we often do not know what we will want to know yet. An example of this are the various online processing APIs that return back results in a specific JSON format. The structure of this JSON format will be different for every online processing tool, but will also be affected by the parameters used during any specific API call. MongoDB allows us to store any of these JSON results in documents without any conversion, because its storage design is inherently based on a JSON like structure called BSON. However, storing data without defining some structure makes it very difficult to query and find the content afterwards. In order to alleviate this problem we have defined a data model that is abstract enough to be able to store any type of data, yet specific enough to be able to query this data afterwards.</p>
								<p></p>
								<p>For an exact datamodel please follow this <a href="/documentation_assets/datamodel.pdf">link</a>.</p>
							<h4 id="inner2">2. Provenance</h4>
								<p>The datamodel follows the open Provenance model. Which means that every entity is generated with an activity. Each activity has an agent that performed the activity, in our case this can be a crowdagent, useragent or a software agent. Please, look at the diagram below for a high-level concept of our datamodel.</p>
								<p style="text-align:center; margin:0px;padding:0px;"><img height="443px" width="622px" style="align:center;" src="/documentation_assets/provenance.png"></p>
								<p>In PROV, physical, digital, conceptual, or other kinds of thing are called entities. Examples of such entities are a web page, a chart, and a spellchecker. Provenance records can describe the provenance of entities, and an entity’s provenance may refer to many other entities. For example, a document D is an entity whose provenance refers to other entities such as a chart inserted into D, and the dataset that was used to create that chart. Entities may be described as having different attributes and be described from different perspectives. For example, document D as stored in my file system, the second version of document D, and D as an evolving document, are three distinct entities for which we may describe provenance.</p>
								<p>Activities are how entities come into existence and how their attributes change to become new entities, often making use of previously existing entities to achieve this. They are dynamic aspects of the world, such as actions, processes, etc. For example, if the second version of document D was generated by a translation from the first version of the document in another language, then this translation is an activity.</p>
								<p>Activities generate new entities. For example, writing a document brings the document into existence, while revising the document brings a new version into existence. Activities also make use of entities. For example, revising a document to fix spelling mistakes uses the original version of the document as well as a list of corrections. Generation does not always occur at the end of an activity, and an activity may generate entities part-way through. Likewise, usage does not always occur at the beginning of an activity.</p>
								<p>An agent takes a role in an activity such that the agent can be assigned some degree of responsibility for the activity taking place. An agent can be a person, a piece of software, an inanimate object, an organization, or other entities that may be ascribed responsibility. When an agent has some responsibility for an activity, PROV says the agent was associated with the activity, where several agents may be associated with an activity and vice-versa).</p>
							<h4 id="inner3">3. API</h4>
								<p></p>
							<h4 id="inner4">5. AMT SDK</h4>
								<p>Mechanical Turk provides a REST API with XML response for handling jobs, annotations and workers. We created a thin wrapper that behaves like a high-level intermediary between any PHP project and mTurk. The methods and classes mimic the API itself as closely as possible. The wrapper handles the request, conversion and any errors that the API comes up with to alleviate the process of communicating with the API. To ensure reusability, no features specific to our implementation are included in the wrapper, and we incorporated most of the functionality mTurk has to offer. The wrapper is embedded in the Mechanical Turk package within our framework in a directory called lib. When creating a job, the package calls the createJob method with a HIT object as parameter, and the created ID's are returned. Similarly, we call the getAssignments method with a HIT id to get all the annotations to this HIT. </p>
							<h4 id="inner5">4. CF SDK</h4>
								<p>The CrowdFlower SDK works in a comparable way to the AMT SDK. CrowdFlower, however, works with JSON responses and we use Curl requests to communicate to their API. Every request that we want to send to CrowdFlower is handled by the SDK.</p>

							<h4 id="inner6">6. View code</h4>
								<p>The CrowdTruth framework is open-source and available in this <a href="https://github.com/laroyo/CrowdTruth"target="_blank">repository</a>. Feel free to fork it, in order to extend or alter it. In case you improve our framework, we would enjoy and appreciate including it in the original CrowdTruth framework.</p>
							<h4 id="inner7">7. Extending CrowdTruth</h4>
								<p>This section will describe several components of the framework that can be extended.</p>
								<h5 id="inner7a">A. Add a new platform</h5>
									<p align="justify">So, you want to add a new platform to the crowdsourcing framework. Great! Before you start, it’s useful to familiarize yourself a little bit with the PHP framework called Laravel, which is the basis for our distribution. Adding a plaform, in essence, boils down to creating a Laravel Package and implementing the necessary methods. This makes sure that distributing your code is easy and other people can add platforms without having to change the base code of the framework.</p>
									<p align="justify">In this document we guide you through the process of creating the package. Basically, there are two ways of starting. One is to copy one of our own packages (Cw/CrowdFlower and Cw/Mturk) and replacing the code with your own. The other is to start from scratch with a clean new package. However, we encourage you to look at our packages for inspiration and to check out things that are not covered in this guide. For this guide, we’ll assume you start from scratch.</p>
									<h6>Overview</h6>
									<ol type="1">
										<li>Create a new Laravel Package</li>
										<li>Create your main class</li>
										<li>Create the View</li>
										<li>Translate the QuestionTemplate</li>
										<li>Retrieve and save annotated data</li>
										<li>Share</li>
									</ol>
									<h5>1. Create a new package</h6>
									<p>For this step, please refer to <a href="http://laravel.com/docs/packages">the Laravel documentation</a>. More info can be found on various blogs on the internet. The generated clean package has a class called [packagename]ServiceProvider. In the course of this guide, we’ll fill the <code>boot()</code> method of this class with a few lines of code to register the necessary classes.</p>
									<h6>2. Create your main class</h6>
									<p>This class will handle most calls. In here, Jobs can be created, ordered, paused, resumed and cancelled. You can also provide validation rules for the fields that you may add in step 3, creating the View. The class has to extend ‘\FrameWork’ (the backslash is necessary because of the namespace). Check out /app/models/FrameWork to see the required methods and some explanation. Now is also a good time to add your platform to the ‘platform’ array in /app/config/config.php. Use a shorthand notation of just a few characters.</p>
									<p>In the <code>boot()</code> method of your ServiceProvider, write these lines:</p>
									<pre>$this->app->bind('cf', function() {<br>&nbsp&nbsp return new Crowdflower;<br>&nbsp});</pre>
									<p>Where cf is the short name of your platform, the same as in the config. This is how our framework finds and instantiates your main class.</p>
									<h6>3. Create the view</h6>
									<p>Views are Laravels way of presenting pages. You need to make a View that contains the fields of any extra configuration that you might want to add to a newly created Job. [todo: let it extend a layout]. The names and values of the form fields that you include here will be inserted one-on-one into the JobConfiguration. </p>
									<p>You need to tell your main class where the View is. In the method createView() in your main class, do at least this: return View::make('packagename::viewname');. It’s also possible to do stuff here (for instance, display an error when the API key is not found with Session::flash('flashError', 'This is an error.'); There’s also flashNotice and flashSuccess  for other types of messages. Hand variables to your View by adding ->with('variablename', $thedata) to the return statement. </p>
									<p>In some cases, you might want to perform some logic on the input fields after they were submitted. For this, we have the method called ‘updateJobConf($jc)’ in our main class. It fires when the user goes to another tab after your page, and takes a JobConfiguration and returns a JobConfiguration. In the meantime, the input fields are available under Input::get(‘inputname’) and you can manipulate the JobConfiguration in any way you see fit. Keep in mind that the actual configuration options are an  array in the ‘content’ field of the JobConfiguration.</p>
									<h6>4. Translate the QuestionTemplate</h6>
									<p>Besides the JobConfiguration, you’ll probably want to include a question for your workers. This part of the framework is still under construction. Currently, a user can upload templates in different formats. Which extension the user is allowed to upload for use with your platform is set in the getExtension() method in your main class. You have access to this file in public/templates/$template. In the next version, QuestionTemplates in JSON format are the norm. What this means for you, is that you’ll have to write a class to translate the JSON to the format that is specific to your crowdsourcing platform. If that format is HTML, you’re lucky because we already include that option in the base code of our platform.</p>
									<h6>5. Retrieve and save annotated data</h6>
									<p>We need to add a CLI command for retrieving the jobs on the platform. For this, we use <a href="http://laravel.com/docs/commands" >Laravel’s Command functionality</a>. The name of the command should be ‘[shorthand platform name]:retrievejobs’ and the classname RetrieveJobs. In the fire() method, you contact your API and save the information in the database. This step is important and has a lot of things that need to be in there, so pay attention. It’s probably best to look at our code and copy/adapt what you need.</p>
									<strong>Register the command</strong>
									<p>After creating the command, open up composer.json (in the root of your package) and add <code>src/commands</code> to the autoload classmap.</p>
									<p>Add the following lines to the <code>boot()</code> method of your ServiceProvider class. Replace ‘cf’ by the shorthand name of your platform.</p>
									<pre>$this->app['cf.retrievejobs'] = $this->app->share(function(){<br>&nbsp&nbsp return new RetrieveJobs;<br>});<br><br>$this->commands('cf.retrievejobs');<br></pre>							
									<strong>Add the code to the fire() method</strong>
									<p><a href="#inner1">The Provenance model</a> requires us to save an Agent and an Activity for every Entity we save. The Job already has an activity, but the annotations need a new one. The agent is the CrowdAgent and the softwareAgentId is the shorthand name of your platform. There are many more ‘rules’ and they are important, so please study <a href="#inner1">the data model</a> carefully.</p>
									<p>When you get the data:</p>
									<ul>
										<li>Initiate a new Annotation object and give it the necessary properties. After this, do: <br><code>Queue::push('Queues\SaveAnnotation', array('annotation' => serialize($annotation)));</code></li>
										<li>Create or update the CrowdAgent and do:<br><code>Queue::push('Queues\UpdateCrowdAgent', array('crowdagent' => serialize($agent)));</code></li>
										<li>Finally, update the Job (this is necessary to update the vectors and completion count)<br><code>Queue::push('Queues\UpdateJob', array('job' => serialize($job)));</code></li>
									</ul>
									<strong>How does retrieveJobs get called?</strong>
									<p>You may choose to only manually update the jobs that use your platform by doing php artisan cf:retrievejobs form the root directory of the framework in a console. The main reason we made this a CLI job however, is that you now can create a cronjob (linux) or a scheduled task (windows). </p>
									<p>Another way of handling this, if your platform supports it, is using a webhook. To do this, create a Route in your ServiceProvider’s <code>boot()</code> method somewhat like this:</p>
									<pre>Route::any('my_webhook_route', function(){<br>&nbsp&nbsp $judgments = Input::get(‘judgments’);<br>&nbsp&nbsp \Artisan::call('cf:retrievejobs', array('--judgments' => serialize($judgments)));<br>});</pre>
									<p>Lastly, our framework supports messaging from services like iron.io and Amazon SQS (todo). These function more or less the same as a webhook. If you decide to use this, please point <a href="http://laravel.com/docs/queues">the Queue object</a> that listens for message to your CLI command; in this way the user can still click ‘refresh’ on a job to check if all the data made its way to our framework safely (todo).</p>
									<h6>6. Share</h6>
									<p>There’s a good chance that other researchers can use the platform you created a package for as well. Please make your code publicly available and let is know, so we can add information about your package to our website!</p>
								<h5 id="inner7b">B. Add a new template</h5>
									<p>One part of our framework is that users can create and upload their own templates for questions. There will probably be three stages in the implementation of this:</p>
									<ol type="1">
										<li>Upload templates specifically for each platform</li>
										<li>Create and edit a JSON template online, which will be transformed to the platformspecific format</li>
										<li>An online template builder</li>
									</ol>
									<p>Right now, we’re in the first stage. In this document, we’ll describe how to create templates for the two platforms that are included in the standard version of our framework; CrowdFlower and Amazon Mechanical Turk.</p>
									<h6>Current implementation</h6>
									<strong>CrowdFlower</strong>
									<p>This platform uses it’s own format, called CML. Please refer to the CrowdFlower documentation or their online questionbuilder to see what this is like. Parameters that have to be replaced by, for instance, terms in a twrex-structured-sentence (one of the text formats we use for IBM’s Watson), have to be in this format: &#123;{terms_first_text}}, where the underscore implies a deeper level in the array that’s in the sentence’s ‘content’ field. For other formats, the references work the same. CSS and JavaScript have to be uploaded under the same name and will be automatically included.</p>
									<strong>Amazon Mechanical Turk</strong>
									<p>Mechanical Turk uses HTML for it’s questions. Some special rules do apply however. For an HTML template to work correctly with our framework, only the HTML inside the form should be included. So you can leave the &lt;head>, &lt;body> and &lt;form> tags behind and just start with the &lt;input> fields. CSS and JavaScript have to be included in &lt;style> and &lt;script> tags. References to external CSS and JS are allowed, but only if the asset is hosted on a server that supports SSL (https://). The format of the parameters is the same as with CrowdFlower (see above). Every &lt;input> name has to be: {uid}_fieldname. To have multiple questions on a single page is also supported, please check out our RelDir template for this. Since this will be handled by the framework automatically in the future, we won’t go into this here.</p>
									<strong>Vectors</strong>
									<p>Right now, creating custom rules for how annotation vectors are generated has to be done in the source code (in the Annotation class).</p>
									<h6>Future implementation</h6>
									<strong>Template</strong>
									<p>To get an idea of how to create JSON templates in the next version of the framework, please refer to the discussion document at (link). When we implement this, detailed instructions will be available. We aim to make this process as straightforward as possible.</p>
									<strong>Vectors</strong>
									<p>Vectors will normally be generated based on any multiple choice elements in the template. All the possible options are included in the vector. For a single annotation, the value of the field will be 1 if the worker selected it, and 0 when he didn’t. The aggregated values form the vector of a unit. This may look like this (Relation Direction):</p>
									<pre>"entity/text/medical/twrex-structured-sentence/2425" : {<br>&nbsp "Choice1" : 0,<br>&nbsp "Choice2" : 7,<br>&nbsp "Choice3" : 5<br>}</pre>
									<p>For some tasks, you’d want to have special rules that don’t correspond one on one to the QuestionTemplate. An example of this is our Factor Span task, which generates vectors like these:</p>
									<pre>"term1" : {<br>&nbsp "[WORD_-3]" : 0,<br>&nbsp "[WORD_-2]" : 1, <br>&nbsp "[WORD_-1]" : 3,<br>&nbsp "[WORD_+1]" : 0, <br>&nbsp "[WORD_+2]" : 0, <br>&nbsp "[WORD_+3]" : 0, <br>&nbsp "[WORD_OTHER]" : 0, <br>&nbsp "[NIL]" : 3, <br>&nbsp "[CHECK_FAILED]" : 2 <br>} </pre>
									<p>based on which words the user selected in a sentence (we ask them if a specific term is complete). We are still discussing ways to make it possible for the user to create custom vector rules like this one.</p>
								<h5 id="inner7c">C. Add a new online source</h5>
									<p>Adding a new online source to the CrowdTruth platform is a piece of cake. Let us guide you through the steps you have to take to add your own customized source. This can be any API you want, on the condition it spits out image, text or video.  We take the ImageGetter that takes images from the Rijksmuseum API as an example in this tutorial. We expect you to be able to design and develop a basic webpage and create the service that talks to the API and stores the result to the database. Our platform is build with the Laravel framework and mostly written in PHP. In the end of this tutorial we will point out some examples of using other technologies or languages in combination with the CrowdTruth framework.</p>
									<h6>Overview</h6>
									<ol type="1">
										<li>Create a new page containing your new form</li>
										<li>Add navigation towards your page</li>
										<li>Adding data correctly</li>									
									</ol>
									<h6>1. Create a new page containing your new form</h6>
									<p>The CrowdTruth framework uses the Laravel architecture concerning where to find your views, models etc. You will find an empty template to make your form that fits in the framework in the following folder: <code>ROOT_FOLDER/app/views/onlinesource/..</code></p>
									<p>The template is called onlinesourcetemplate.blade.php. It extends the main layout of the framework. By adding custom CSS in the head-section you can adjust the styling. You can use this template by renaming it into a more suitable name, but keep it in the same folder!</p>
									<p>You can get some inspiration for what a form can look like by checking out the imagegetter.blade.php file. This file contains the form for the Rijksmuseum API, but it heavily relies on JavaScript. More about using other languages in the last section.</p>									
									<h6>2. Add navigation towards your page</h6>
									<p>After you finish your page, you need to set up the navigation towards it. The fieldwork has already been done for this. Let us show you where you need to make a few changes.</p>
									<p>First, go to the following file: <code>ROOT_FOLDER/app/views/media/pages/upload.blade.php</code></p>
									<p>This file contains the view for uploading media or selecting an online source. Scroll down past the first panel that is for uploading media until you find yourself at the online sources panel. In the panel body there is a select box with the id source_name. The selectbox has one option commented out:</p>
									<pre> &lt;!-- &lt;option value="source_template" data-toggle="source_name">New online source&lt;/option> --></pre>
									<p>Undo the commenting by taking away the arrow tags at the beginning and end of the line. Consequently change the source_template in the value attribute to source_*yourtemplate*, and the text between the option tags to the title of your online source. This should result in the following:</p>
									<pre> &lt;option value="source_myAPI" data-toggle="source_name">My API&lt;/option></pre>
									<p>Next we have to catch the post-request of the form in the controller. You find the controller in the place: <code>ROOT_FOLDER/app/controllers/MediaController.php</code> </p>
									<p>The postOnlinedata method receives the form’s post-request. You can follow the example of the imagegetter and replicate it with the code block underneath it. You just have to uncomment the lines and change two things.</p>
									<pre>/* Change template to add online source */<br>// if (Input::get("source_name") == "source_template"){<br>// 	return Redirect::to('onlinesource/onlinesourcetemplate')<br>// } </pre>
									<p>First, in the top line you have to compare the input’s source_name field with the name you gave along with your template, and change the redirection adress to the page you created. It will turn into something like this:</p>
									<pre>if (Input::get("source_name") == "source_myAPI"){<br>&nbsp&nbsp	 	return Redirect::to('onlinesource/myAPI')<br>}</pre>
									<p>The final step you have to do is to make sure that the redirect you made above points towards your new page. For this, you have to go to the OnlineSourceController: <code>ROOT_FOLDER/app/controllers/OnlineSourceController.php</code></p>
									<p>We need to add a method to the OnlineSourceController that makes the view you created earlier. Again, you will find a block of code that is commented out to help you with this:</p>
									<pre>/* Rename the template below to fit your online source */<br>// public function getOnlinesourcetemplate() {<br>// 	return View::make('onlinesource.onlinesourcetemplate');<br>// }</pre>
									<p>We simply change the name of the method and the name of the file that the View makes. For our example it will look something like this:</p>
									<pre>public function getMyAPI() {<br>&nbsp&nbsp return View::make('onlinesource.myAPI);<br>}</pre>
									<p>Right now, you should have added your own online source to the platform, congrats!</p>
									<h6>3. Adding data correctly</h6>
									<p>The CrowdTruth framework heavily relies on provenance in its datamodel for research purposes. When you choose to add data to the database, it is a good idea to take the existing datamodel in regard for consistency. Simply said, provenance ensures that you always now who or what changed an entity and by with which activity they did so. Therefore, keep as rules of thumb, that every mutation to an entity is an activity that generates a new entity. Also, mention the agent that performed that activity in the new activity. For more information you should consult the <a href="#inner1">documentation on our datamodel</a>.</p>
								<h5 id="inner7d">D. Add a new pre-processing tool</h5>
									<p></p>
								<h5 id="inner7e">E. Add a new domain</h5>
									<p></p>
								<h5 id="inner7f">F. Add a new documentType</h5>
									<p></p>
							<p><a href="#top">Back to the top</a></p>
						</div>
					</div>
				</div>
			</div>
@stop