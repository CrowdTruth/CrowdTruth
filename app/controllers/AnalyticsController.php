<?php

use crowdwatson\MechanicalTurkService;

class AnalyticsController extends BaseController {

    public $restful = true;
    public static $colorList = array( '#FF7F50','#659CEF', '#FFDAB9 ', '#8FBC8F', '#FFA07A', '#B0C4DE', '#CD5C5C', '#9ACD32',  '#DAA520');
    protected $repository;

    public function __construct(Repository $repository){
        $this->repository = $repository;
    }

    public function anyView(){

        $jobArray = explode(',', Input::get('jobs'));
        $colors = array();
        $jobsInfo = array();

        for ($iter = 0; $iter < count($jobArray); ++$iter) {

            $jobID = $jobArray[$iter];
            $jobsInfo[$jobID] = Entity::find($jobID);
            $color = AnalyticsController::$colorList[$iter%count(AnalyticsController::$colorList)];
            $jobsInfo[$jobID]['color'] = $color;
            $colors[$jobID] = $color;
        }


        return View::make('analytics.jobview')->with('jobConfigurations', $jobsInfo)
            ->with('jobIDs', $jobArray)
            ->with('jobColors',$colors);
    }
    public function getIndex() {
        return Redirect::to('jobs/listview');
    }
}
