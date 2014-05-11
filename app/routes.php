<?php

// Event::listen('illuminate.query', function($query){
// 	var_dump($query);
// });


Route::group(array('before' => 'auth'), function()
{


	Route::controller('media/preprocess/fullvideo', 'preprocess\FullvideoController');
	Route::controller('media/preprocess/twrex', 'preprocess\TwrexController');
	Route::controller('media/preprocess/CSVresultController', 'preprocess\CSVresultController');
	Route::controller('media', 'MediaController');

	Route::controller('selection', 'SelectionController');
	Route::controller('process', 'ProcessController');
	Route::controller('jobs', 'JobsController');
	Route::controller('workers', 'WorkersController');
    Route::controller('analyze','AnalyticsController');
    Route::controller('onlinesource', 'OnlineSourceController');
	


});

Route::get('/', function()
{
    /*set_time_limit(12000);
    //dd(array_flatten(\MongoDB\Entity::where('documentType','annotation')->where('unit_id', "entity/text/medical/twrex-structured-sentence/1128")->distinct('job_id')->get()->toArray()));
    $units = \MongoDB\Entity::where('tags', 'unit')->get(['_id'])->toArray();
   // $units = \MongoDB\Entity::where('tags', 'unit')->where('_id',"entity/text/medical/twrex-structured-sentence/1128")->get(['_id'])->toArray();
    foreach($units as $unit)
    {
        $jobField = array();
        $jobField['count'] = count(array_flatten(\MongoDB\Entity::where('documentType','annotation')->where('unit_id', $unit['_id'])->distinct('job_id')->get()->toArray()));
        $jobTypes = array_flatten(\MongoDB\Entity::where('documentType','annotation')->where('unit_id', $unit['_id'])->distinct('type')->get()->toArray());
        $jobField['distinct'] = count($jobTypes);
        $jobField['types'] = array();
        foreach($jobTypes as $type) {
            $jobField['types'][$type] = array();
            $jobField['types'][$type]['count'] = count(array_flatten(\MongoDB\Entity::where('documentType','annotation')->where('unit_id', $unit['_id'])->where('type',$type)->distinct('job_id')->get()->toArray()));
            $unitJobs = array_flatten(\MongoDB\Entity::where('documentType','annotation')->where('type',$type)->where('unit_id', $unit['_id'])->distinct('job_id')->get()->toArray());
            $jobTemplates = array_flatten(\MongoDB\Entity::whereIn('_id', $unitJobs)->distinct('template')->get()->toArray());
            $jobField['types'][$type]['distinct'] = count($jobTemplates);

            $jobField['types'][$type]['templates'] = array();
            foreach($jobTemplates as $template){
                    $countTemplateJobs = count(\MongoDB\Entity::whereIn('_id', $unitJobs)->where('template',$template)->lists('template'));
                    $jobField['types'][$type]['templates'][$template] = $countTemplateJobs;
            }
        }

        $annotationField = array();
        $annotationField['count'] = count(\MongoDB\Entity::where('unit_id', $unit['_id'])->get()->toArray());
        $annotationField['spam'] = count(\MongoDB\Entity::where('unit_id', $unit['_id'])->where('spam', true)->get()->toArray());
        $annotationField['nonSpam'] = count(\MongoDB\Entity::where('unit_id', $unit['_id'])->where('spam', false)->get()->toArray());

        $workerField = array();
        $workerField['count'] = count(array_flatten(\MongoDB\Entity::where('unit_id', $unit['_id'])->distinct('crowdAgent_id')->get()->toArray()));

        $spamWorkers = array_flatten(\MongoDB\Entity::where('unit_id', $unit['_id'])->where('spam', true)->distinct('crowdAgent_id')->get()->toArray());
        $nonSpamWorkers = array_flatten(\MongoDB\Entity::where('unit_id', $unit['_id'])->where('spam', false)->distinct('crowdAgent_id')->get()->toArray());
        $potentialSpam = array_intersect($spamWorkers, $nonSpamWorkers);
        $workerField['spam'] = count($spamWorkers) - count($potentialSpam);
        $workerField['nonSpam'] = count($nonSpamWorkers) - count($potentialSpam);
        $workerField['potentialSpam'] = count($potentialSpam);

        //filtered
        $filteredField = array();
        $filteredField['job_ids'] = array_flatten(\MongoDB\Entity::where('documentType', 'job')->where('metrics.filteredUnits.list','all', array($unit['_id']))->get(['_id'])->toArray());
        $filteredField['count'] = count($filteredField['job_ids']);

        //batches
        $batchesField = array();
        $batchesField['count'] = count(\MongoDB\Entity::where('documentType', 'batch')->where('parents', 'all', array($unit['_id']))->get()->toArray());

        $mongoUnit = \MongoDB\Entity::where('_id',$unit['_id'])->first();
        $mongoUnit->cache = ["jobs" => $jobField,
            "workers" => $workerField,
            "annotations" => $annotationField,
            "filtered" => $filteredField,
            "batches" => $batchesField];
        $mongoUnit->update();

        $avg_clarity = \MongoDB\Entity::where('metrics.units.withoutSpam.'.$unit['_id'], 'exists', 'true')->avg('metrics.units.withoutSpam.'.$unit['_id'].'.max_relation_Cos.avg');
        $mongoUnit->avg_clarity = $avg_clarity;
        $mongoUnit->update();

    }*/

      /*  $batch['count'] = count(\MongoDB\Entity::where('documentType', 'batch')->where('parents', 'all', array($unit->_id))->get()->toArray());
        $annotation['count'] = count(\MongoDB\Entity::where('unit_id', $unit->_id)->get()->toArray());
        $annotation['spamCount'] = count(\MongoDB\Entity::where('unit_id', $unit->_id)->where('spam', true)->get()->toArray());
        $annotation['nonSpamCount'] = count(\MongoDB\Entity::where('unit_id', $unit->_id)->where('spam', false)->get()->toArray());
        $workers['count'] = count(array_flatten(\MongoDB\Entity::where('unit_id', $unit->_id)->distinct('crowdAgent_id')->get()->toArray()));
        $workers['spamCount'] = count(\MongoDB\Entity::where('unit_id', $unit->_id)->where('spam', true)->distinct('crowdAgent_id')->get()->toArray());
        $workers['nonSpamCount'] = count(\MongoDB\Entity::where('unit_id', $unit->_id)->where('spam', false)->distinct('crowdAgent_id')->get()->toArray());


        foreach ($jobTypes as $type){
            $job["types"][$type] = count(array_flatten(\MongoDB\Entity::where('unit_id', $unit->_id)->whereIn('job_id', $jobIdsPerType[$type])->distinct('job_id')->get()->toArray()));
        }
        $unit->cache = ["jobs" => $job,
            "workers" => $workers,
            "annotations" => $annotation,
            "batches" => $batch];

        $unit->update();
    }

    $jobTypes = array_flatten(\MongoDB\Entity::where('documentType','job')->distinct('type')->get()->toArray());
    $job = array_flatten(\MongoDB\Entity::where('documentType','job')->distinct('type')->get()->toArray());
    //get job ids per type
    $jobIdsPerType = array();
    foreach ($jobTypes as $type){
        $jobIdsPerType[$type] = array_flatten(\MongoDB\Entity::where('documentType', 'job')->where('type',$type)->distinct('_id')->get()->toArray());
    }*/
    /*return
    //return count(\MongoDB\Entity::where('unit_id', 'entity/text/medical/twrex-structured-sentence/0')->whereNotIn ('crowdAgent_id', $spammers)->distinct('crowdAgent_id')->get()->toArray());
    return count(\MongoDB\Entity::where('unit_id', 'entity/text/medical/twrex-structured-sentence/0')->whereIn('crowdAgent_id', $spammers)->distinct('crowdAgent_id')->get()->toArray());*/

    return Redirect::to('home');
});

Route::get('home', 'PagesController@index');
Route::get('info', 'PagesController@info');
Route::get('papers', 'PagesController@papers');

Route::controller('api/v1', '\Api\v1\apiController');
Route::controller('api/media', '\Api\media\apiController');
Route::controller('api/search', '\Api\search\apiController');
Route::controller('api/actions', '\Api\actions\apiController');
Route::controller('api/analytics', '\Api\analytics\apiController');

Route::resource('api/v3/', '\Api\v3\apiController', array('only' => array('index', 'show')));
Route::resource('api/v4', '\Api\v4\apiController', array('only' => array('index', 'show')));

Route::controller('user', 'UserController');