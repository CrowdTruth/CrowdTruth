<?php

class ProcessController extends BaseController {

	public function getIndex() {
        return Redirect::to('process/batch');
	}

	public function getTemplatebuilder(){
		return View::make('process.tabs.templatebuilder');

	}

	public function getBatch() {
		$batch = Batch::where('documentType', 'batch')->first(); 
		$jc = JobConfiguration::where('documentType', 'jobconf')->first();
		$qt = QuestionTemplate::where('documentType', 'questiontemplate')->first();
		
		//$job = Job::where('documentType', 'job')->where('softwareAgent_id', 'cf')->first();
/*		$job = new Job();
		//dd($job->jobConfiguration_id);
		dd($job->jobConfiguration);*/

		$batches = Batch::where('documentType', 'batch')->get(); 
		$batch = unserialize(Session::get('batch'));
		if(!$batch) $selectedbatchid = ''; 
		else $selectedbatchid = $batch->_id;
		return View::make('process.tabs.batch')->with('batches', $batches)->with('selectedbatchid', $selectedbatchid);
	}

	public function getTemplate() {
		// Create array for the tree
		$jc = unserialize(Session::get('jobconf'));	
		$currenttemplate = Session::get('template');
		if(empty($currenttemplate)) $currenttemplate = 'generic/default';
		$treejson = $this->makeDirTreeJSON($currenttemplate);

		return View::make('process.tabs.template')
			->with('treejson', $treejson)
			->with('currenttemplate', $currenttemplate)
			->with('jobconf', $jc->content);
	}

	public function getDetails() {
		$jc = unserialize(Session::get('jobconf'));
		$template = Session::get('template');
		$batch = unserialize(Session::get('batch'));
		$questiontemplateid = Session::get('questiontemplateid');
		//$j = new Job($batch, $template, $jc, $questiontemplate);
		$questionids = array();
		$goldfields = array();
		$unitscount = count($batch->wasDerivedFrom);
/*		try {
			$questionids = $j->getQuestionIds();
			$goldfields = $j->getGoldFields();	
		} catch (AMTException $e) {
			Session::flash('flashError', $e->getMessage());
		} 

		// Compare QuestionID's and goldfields.
		if($diff = array_diff($goldfields, $questionids))
			if(count($diff) == 1)
				Session::flash('flashNotice', 'Field \'' . array_values($diff)[0] . '\' is in the answerkey but not in the HTML template.');
			elseif(count($diff) > 1)
				Session::flash('flashNotice', 'Fields \'' . implode('\', \'', $diff) . '\' are in the answerkey but not in the HTML template.');
*/
		return View::make('process.tabs.details')
			->with('jobconf', $jc->content)
			->with('goldfields', $goldfields)
			->with('unitscount', $unitscount);
	}

	public function getPlatform() {
		$jc = unserialize(Session::get('jobconf'));
		return View::make('process.tabs.platform')->with('jobconf', $jc->content);
	}

	public function getSubmit() {
		$jc = unserialize(Session::get('jobconf'));
		$template = Session::get('template');
		$batch = unserialize(Session::get('batch'));
		$questiontemplateid = Session::get('questiontemplateid');
		$treejson = $this->makeDirTreeJSON($template, false);

		try {
			$questions = array();//$j->getPreviews();
		} catch (Exception $e) {
			$questions = array('couldn\'t generate previews.');
			Session::flash('flashNotice', $e->getMessage());
		}

		if(!$jc->validate()){
			$msg = '<ul>';
			foreach ($jc->getErrors()->all() as $message)
				$msg .= "<li>$message</li>";
			$msg .= '</ul>';

			Session::flash('flashError', $msg);
		} 

		return View::make('process.tabs.submit')
			->with('treejson', $treejson)
			->with('questions',  $questions)
			->with('table', $jc->toHTML())
			->with('template', '')//$jc->content['template'])
			->with('frameheight', $jc->content['frameheight'])
			->with('jobconf', $jc->content);
	}

	public function getClearTask(){
		Session::forget('jobconf');
		Session::forget('origjobconf');
		Session::forget('template');
		Session::forget('questiontemplateid');
		Session::forget('batch');
		return Redirect::to("process/batch");
	}

	/*
	* Save the jobdetails to the database.
	*/
	public function postSaveDetails(){
		try {
			$jc = unserialize(Session::get('jobconf'));
			if($jc->store())
				Session::flash('flashSuccess', 'Saved Job configuration to database!');
			else Session::flash('flashNotice', 'This Job configuration already exists.');
		} catch (Exception $e) {
			Session::flash('flashError', $e->getMessage());
		}

		return Redirect::to("process/submit");
	}


	/*
	* Every time you click a tab or the 'next' button, this function fires. 
	* It combines the Input fields with the JobConfiguration that we already have in the Session.
	*/
	public function postFormPart($next){
		$jc = unserialize(Session::get('jobconf', serialize(new JobConfiguration)));
		if(isset($jc->content)) 
			$jcc = $jc->content;
		else $jcc = array();

		$template = Session::get('template');

		if(Input::has('batch')){
			// TODO: CSRF
			$batch = Batch::where('documentType', 'batch') /* TODO find a way to assume this */
							->where('_id', Input::get('batch'))
							->first();

			Session::put('batch', serialize($batch));
		
		}

		if(Input::has('template')){
			// Create the JobConfiguration object if it doesn't already exist.
			$ntemplate = Input::get('template');
			if (empty($template) or ($template != $ntemplate))	
				$jc = JobConfiguration::fromJSON(Config::get('config.templatedir') . "$ntemplate.json");
			$template = $ntemplate;
			$origjobconf = 'jcid'; // TODO!

			// FOR TESTING -> hardcoded questiontemplate. We need more of these.
			$testdata = json_decode(file_get_contents(Config::get('config.templatedir') . 'relation_direction/relation_direction_multiple.questiontemplate.json'), true);
			$e = new QuestionTemplate;
			$e->content = $testdata;
			$e->save();
			Session::put('questiontemplateid', $e->_id);

		} else {
			if (empty($jc)){
				// No JobConfiguration and no template selected, not good.
				if($next != 'template')
					Session::flash('flashNotice', 'Please select a template first.');
				return Redirect::to("process/template");
			} else {
				// There already is a JobConfiguration object. Merge it with Input!
				$jcc = array_merge($jcc, Input::get());	

				// If leaving the details page...
				if(Input::has('title')){
					$jcc['answerfields'] = Input::get('answerfields', false);
					if($next == 'nextplatform'){
						if(isset($jcc['platform'][0])){
							$next = $jcc['platform'][0];
						} else {
							Session::flash('flashNotice', 'Please select a platform first');
							Redirect::to("process/platform");
						}
					}
				}

				// If leaving the Platform page....:
				if(Input::has('platform'))
					$jcc['platform'] = Input::get('platform', array());


				// DEFAULT VALUES
				if(!isset($jcc['eventType'])) $jcc['eventType'] = 'HITReviewable'; 
				if(!isset($jcc['frameheight'])) $jcc['frameheight'] = 650;
				
				$jc->content = $jcc;	

				// After specific platform tab, call the method and determine which is next.
				$pid = Input::get('platformid', false);
				if($pid){
					$platform = App::make($pid);
					$jc = $platform->updateJobConf($jc);

					if($next == 'nextplatform'){
						$nextindex = array_search($pid, $jc->content['platform']) + 1;
						if(array_key_exists($nextindex, $jc->content['platform']))
							$next = $jc->content['platform'][$nextindex];
						else
							$next = 'submit';	
					}				
				}
			}		
		}

		Session::put('jobconf', serialize($jc));
		Session::put('template', $template);

		try {
			return Redirect::to("process/$next");
		} catch (Exception $e) {
			Session::flash('flashError', $e->getMessage()); // Todo: is this a good way? -> logging out due to timeout
			return Redirect::to("process");
		}
	}

	/*
	* Send it to the platforms.
	*/
	public function postSubmitFinal($ordersandbox = 'order'){
		$jc = unserialize(Session::get('jobconf'));
		$template = Session::get('template');
		$batch = unserialize(Session::get('batch'));
		$questiontemplateid = Session::get('questiontemplateid');
		$jobs = array();

		try{

			// Save activity
			$activity = new MongoDB\Activity;
			$activity->label = "Job is uploaded to crowdsourcing platform.";
			$activity->softwareAgent_id = 'jobcreator'; // TODO: JOB softwareAgent_id = $platform. Does this need to be the same?
			$activity->save();

			// Save jobconf if necessary
			$jcid = $jc->_id;
			if(!$jcid){
				$hash = md5(serialize($jc->content));
            	if($existingid = JobConfiguration::where('hash', $hash)->pluck('_id'))
	                $jcid = $existingid; // Don't save, it already exists.
	            else {
		            $jc->hash = $hash;
					$jc->activity_id = $activity->_id;
					$jc->save();
					$jcid = $jc->_id;
				}
			}	

			// Publish jobs
			foreach($jc->content['platform'] as $platformstring){
				$j = new Job;
				$j->template = $template; // TODO: remove
				$j->batch_id = $batch->_id;
				$j->questionTemplate_id = $questiontemplateid;
				$j->jobConf_id = $jcid;
				$j->softwareAgent_id = $platformstring;
				$j->activity_id = $activity->_id;
				$j->publish(($ordersandbox == 'sandbox' ? true : false));
				$jobs[] = $j;
			}

			// Success.
			Session::flash('flashSuccess', "Created " . ($ordersandbox == 'sandbox' ? 'but didn\'t order yet' : 'and ordered') . " job(s) on " . 
							strtoupper(implode(', ', $jc->content['platform'])));
			return Redirect::to("jobs/listview");

		} catch (Exception $e) {

			// Undo creation and delete jobs
			if(isset($jobs))
			foreach($jobs as $j){
				if(isset($j->platformJobId))
					$j->undoCreation($j->platformJobId);
				$j->forceDelete();
			}		

			//delete activity
			if($activity) $activity->forceDelete();
			
			Session::flash('flashError', $e->getMessage()); throw $e; //for debugging
			return Redirect::to("process/submit");
		}

		
	}

	/*
	* SANDBOX PREVIEW
	*/
	public function postSubmitSandbox(){
		$jc = unserialize(Session::get('jobconf'));
		$template = Session::get('template');
		$batch = unserialize(Session::get('batch'));
		$questiontemplateid = Session::get('questiontemplateid');
		try {
			$j = new Job($batch, $template, $jc, $questiontemplateid);

			$ids = $j->publish(true);

			$msg = "Created:<br><ul>";
			foreach($jc->content['platform'] as $platformstring){
			 $c = count($ids[$platformstring]['platformjobid']);
			 $msg .= "<li>" . ($c > 0 ? $c : 'No') . "UNORDERED job" . ($c == 1 ? '' : 's') . " on " . strtoupper($platformstring) . "</li>";
			}

			$msg.= "</li>";

			Session::flash('flashSuccess', $msg);
		} catch (Exception $e) {
			Session::flash('flashError', $e->getMessage());
			return Redirect::to("process/submit");
		}

		return Redirect::to("jobs/listview");
		
	}


	/*
	* Create the JSON necessary for jstree to use.
	*/
	private function makeDirTreeJSON($currenttemplate, $pretty = true){
		$r = array();
		$path = Config::get('config.templatedir');
		foreach(File::directories($path) as $dir){
			$dirname = substr($dir, strlen($path));
		   	if($pretty) $displaydir = ucfirst(str_replace('_', ' ', $dirname));
		   	else $displaydir = $dirname;

			$r[] = array('id' => $dirname, 'parent' => '#', 'text' => $displaydir); 

			foreach(File::allFiles($dir) as $file){
				$filename = $file->getFileName();
				if (substr($filename, -5) == '.json') {
		   			$filename = substr($filename, 0, -5);
		   			if($pretty) $displayname = ucfirst(str_replace('_', ' ', $filename));
		   			else $displayname = $filename;
		   			if("$dirname/$filename" == $currenttemplate)
		   				$r[] = array('id' => $filename, 'parent' => $dirname, 'text' => $displayname, 'state' => array('selected' => 'true'));
		   			else
		   				$r[] = array('id' => $filename, 'parent' => $dirname, 'text' => $displayname);
		   		}	
			}
		}
		return json_encode($r);
	}

	// catch all
	public function missingMethod($parameters = array())
	{
	   $jc = unserialize(Session::get('jobconf'));
	   try{
	   		$platform = App::make($parameters[0]);
	   		return $platform->createView()->with('jobconf', $jc->content)->with('countries', $this->countries);
		} catch (ReflectionException $e){
			return Redirect::to("process/batch");
		}
	  // 
	}


	protected $countries = array(
	'AF' => 'Afghanistan',
	'AX' => 'Aland Islands',
	'AL' => 'Albania',
	'DZ' => 'Algeria',
	'AS' => 'American Samoa',
	'AD' => 'Andorra',
	'AO' => 'Angola',
	'AI' => 'Anguilla',
	'AQ' => 'Antarctica',
	'AG' => 'Antigua And Barbuda',
	'AR' => 'Argentina',
	'AM' => 'Armenia',
	'AW' => 'Aruba',
	'AU' => 'Australia',
	'AT' => 'Austria',
	'AZ' => 'Azerbaijan',
	'BS' => 'Bahamas',
	'BH' => 'Bahrain',
	'BD' => 'Bangladesh',
	'BB' => 'Barbados',
	'BY' => 'Belarus',
	'BE' => 'Belgium',
	'BZ' => 'Belize',
	'BJ' => 'Benin',
	'BM' => 'Bermuda',
	'BT' => 'Bhutan',
	'BO' => 'Bolivia',
	'BA' => 'Bosnia And Herzegovina',
	'BW' => 'Botswana',
	'BV' => 'Bouvet Island',
	'BR' => 'Brazil',
	'IO' => 'British Indian Ocean Territory',
	'BN' => 'Brunei Darussalam',
	'BG' => 'Bulgaria',
	'BF' => 'Burkina Faso',
	'BI' => 'Burundi',
	'KH' => 'Cambodia',
	'CM' => 'Cameroon',
	'CA' => 'Canada',
	'CV' => 'Cape Verde',
	'KY' => 'Cayman Islands',
	'CF' => 'Central African Republic',
	'TD' => 'Chad',
	'CL' => 'Chile',
	'CN' => 'China',
	'CX' => 'Christmas Island',
	'CC' => 'Cocos (Keeling) Islands',
	'CO' => 'Colombia',
	'KM' => 'Comoros',
	'CG' => 'Congo',
	'CD' => 'Congo, Democratic Republic',
	'CK' => 'Cook Islands',
	'CR' => 'Costa Rica',
	'CI' => 'Cote D\'Ivoire',
	'HR' => 'Croatia',
	'CU' => 'Cuba',
	'CY' => 'Cyprus',
	'CZ' => 'Czech Republic',
	'DK' => 'Denmark',
	'DJ' => 'Djibouti',
	'DM' => 'Dominica',
	'DO' => 'Dominican Republic',
	'EC' => 'Ecuador',
	'EG' => 'Egypt',
	'SV' => 'El Salvador',
	'GQ' => 'Equatorial Guinea',
	'ER' => 'Eritrea',
	'EE' => 'Estonia',
	'ET' => 'Ethiopia',
	'FK' => 'Falkland Islands (Malvinas)',
	'FO' => 'Faroe Islands',
	'FJ' => 'Fiji',
	'FI' => 'Finland',
	'FR' => 'France',
	'GF' => 'French Guiana',
	'PF' => 'French Polynesia',
	'TF' => 'French Southern Territories',
	'GA' => 'Gabon',
	'GM' => 'Gambia',
	'GE' => 'Georgia',
	'DE' => 'Germany',
	'GH' => 'Ghana',
	'GI' => 'Gibraltar',
	'GR' => 'Greece',
	'GL' => 'Greenland',
	'GD' => 'Grenada',
	'GP' => 'Guadeloupe',
	'GU' => 'Guam',
	'GT' => 'Guatemala',
	'GG' => 'Guernsey',
	'GN' => 'Guinea',
	'GW' => 'Guinea-Bissau',
	'GY' => 'Guyana',
	'HT' => 'Haiti',
	'HM' => 'Heard Island & Mcdonald Islands',
	'VA' => 'Holy See (Vatican City State)',
	'HN' => 'Honduras',
	'HK' => 'Hong Kong',
	'HU' => 'Hungary',
	'IS' => 'Iceland',
	'IN' => 'India',
	'ID' => 'Indonesia',
	'IR' => 'Iran, Islamic Republic Of',
	'IQ' => 'Iraq',
	'IE' => 'Ireland',
	'IM' => 'Isle Of Man',
	'IL' => 'Israel',
	'IT' => 'Italy',
	'JM' => 'Jamaica',
	'JP' => 'Japan',
	'JE' => 'Jersey',
	'JO' => 'Jordan',
	'KZ' => 'Kazakhstan',
	'KE' => 'Kenya',
	'KI' => 'Kiribati',
	'KR' => 'Korea',
	'KW' => 'Kuwait',
	'KG' => 'Kyrgyzstan',
	'LA' => 'Lao People\'s Democratic Republic',
	'LV' => 'Latvia',
	'LB' => 'Lebanon',
	'LS' => 'Lesotho',
	'LR' => 'Liberia',
	'LY' => 'Libyan Arab Jamahiriya',
	'LI' => 'Liechtenstein',
	'LT' => 'Lithuania',
	'LU' => 'Luxembourg',
	'MO' => 'Macao',
	'MK' => 'Macedonia',
	'MG' => 'Madagascar',
	'MW' => 'Malawi',
	'MY' => 'Malaysia',
	'MV' => 'Maldives',
	'ML' => 'Mali',
	'MT' => 'Malta',
	'MH' => 'Marshall Islands',
	'MQ' => 'Martinique',
	'MR' => 'Mauritania',
	'MU' => 'Mauritius',
	'YT' => 'Mayotte',
	'MX' => 'Mexico',
	'FM' => 'Micronesia, Federated States Of',
	'MD' => 'Moldova',
	'MC' => 'Monaco',
	'MN' => 'Mongolia',
	'ME' => 'Montenegro',
	'MS' => 'Montserrat',
	'MA' => 'Morocco',
	'MZ' => 'Mozambique',
	'MM' => 'Myanmar',
	'NA' => 'Namibia',
	'NR' => 'Nauru',
	'NP' => 'Nepal',
	'NL' => 'Netherlands',
	'AN' => 'Netherlands Antilles',
	'NC' => 'New Caledonia',
	'NZ' => 'New Zealand',
	'NI' => 'Nicaragua',
	'NE' => 'Niger',
	'NG' => 'Nigeria',
	'NU' => 'Niue',
	'NF' => 'Norfolk Island',
	'MP' => 'Northern Mariana Islands',
	'NO' => 'Norway',
	'OM' => 'Oman',
	'PK' => 'Pakistan',
	'PW' => 'Palau',
	'PS' => 'Palestinian Territory, Occupied',
	'PA' => 'Panama',
	'PG' => 'Papua New Guinea',
	'PY' => 'Paraguay',
	'PE' => 'Peru',
	'PH' => 'Philippines',
	'PN' => 'Pitcairn',
	'PL' => 'Poland',
	'PT' => 'Portugal',
	'PR' => 'Puerto Rico',
	'QA' => 'Qatar',
	'RE' => 'Reunion',
	'RO' => 'Romania',
	'RU' => 'Russian Federation',
	'RW' => 'Rwanda',
	'BL' => 'Saint Barthelemy',
	'SH' => 'Saint Helena',
	'KN' => 'Saint Kitts And Nevis',
	'LC' => 'Saint Lucia',
	'MF' => 'Saint Martin',
	'PM' => 'Saint Pierre And Miquelon',
	'VC' => 'Saint Vincent And Grenadines',
	'WS' => 'Samoa',
	'SM' => 'San Marino',
	'ST' => 'Sao Tome And Principe',
	'SA' => 'Saudi Arabia',
	'SN' => 'Senegal',
	'RS' => 'Serbia',
	'SC' => 'Seychelles',
	'SL' => 'Sierra Leone',
	'SG' => 'Singapore',
	'SK' => 'Slovakia',
	'SI' => 'Slovenia',
	'SB' => 'Solomon Islands',
	'SO' => 'Somalia',
	'ZA' => 'South Africa',
	'GS' => 'South Georgia And Sandwich Isl.',
	'ES' => 'Spain',
	'LK' => 'Sri Lanka',
	'SD' => 'Sudan',
	'SR' => 'Suriname',
	'SJ' => 'Svalbard And Jan Mayen',
	'SZ' => 'Swaziland',
	'SE' => 'Sweden',
	'CH' => 'Switzerland',
	'SY' => 'Syrian Arab Republic',
	'TW' => 'Taiwan',
	'TJ' => 'Tajikistan',
	'TZ' => 'Tanzania',
	'TH' => 'Thailand',
	'TL' => 'Timor-Leste',
	'TG' => 'Togo',
	'TK' => 'Tokelau',
	'TO' => 'Tonga',
	'TT' => 'Trinidad And Tobago',
	'TN' => 'Tunisia',
	'TR' => 'Turkey',
	'TM' => 'Turkmenistan',
	'TC' => 'Turks And Caicos Islands',
	'TV' => 'Tuvalu',
	'UG' => 'Uganda',
	'UA' => 'Ukraine',
	'AE' => 'United Arab Emirates',
	'GB' => 'United Kingdom',
	'US' => 'United States',
	'UM' => 'United States Outlying Islands',
	'UY' => 'Uruguay',
	'UZ' => 'Uzbekistan',
	'VU' => 'Vanuatu',
	'VE' => 'Venezuela',
	'VN' => 'Viet Nam',
	'VG' => 'Virgin Islands, British',
	'VI' => 'Virgin Islands, U.S.',
	'WF' => 'Wallis And Futuna',
	'EH' => 'Western Sahara',
	'YE' => 'Yemen',
	'ZM' => 'Zambia',
	'ZW' => 'Zimbabwe',
);

}
