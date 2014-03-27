<?php

use crowdwatson\MechanicalTurkService;

use \MongoDB\Repository as Repository;
use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use \MongoDB\CrowdAgent as CrowdAgent;

class AnalyticsController extends BaseController {

    public $restful = true;

    protected $repository;

    public function __construct(Repository $repository){
        $this->repository = $repository;
    }

    public function anyView(){
        $params = Input::get('jobs');
        $job_array = explode(',', $params);
        $tags = implode(', ', $job_array);

        return View::make('analytics.jobview')->with('jobConfigurations', $params);

        $c = Input::get('collection', 'Entity');

        $collection = $this->repository->returnCollectionObjectFor($c);

        if(Input::has('field'))
        {
            $collection = $this->processFields($collection);
        }
        $jobConfigurations = $collection->get();

        return View::make('analytics.jobview')->with('jobConfigurations', $jobConfigurations);

    }

    protected function processFields($collection)
    {
        foreach(Input::get('field') as $field => $value)
        {
            if(is_array($value))
            {
                foreach($value as $operator => $subvalue)
                {
                    if(is_int($operator) || $operator == "")
                    {
                        $collection = $collection->whereIn($field, array($subvalue));
                        continue;
                    }

                    if(in_array($operator, $this->operators))
                    {
                        if(is_numeric($subvalue))
                        {
                            $subvalue = (int) $subvalue;
                        }

                        if($operator == "like")
                        {
                            $collection = $collection->where($field, $operator, "%" . $subvalue . "%");
                        }
                        else
                        {
                            $collection = $collection->where($field, $operator, $subvalue);
                        }
                    }
                }

            }
            else
            {
                if(is_numeric($value))
                {
                    $value = (int) $value;
                }

                $collection = $collection->whereIn($field, array($value));
            }

        }

        return $collection;
    }

    public function getIndex() {
        return Redirect::to('jobs/listview');
    }

    //Change JobConfiguration into JobConfiguration
    public function getListview() {
        $jobConfigurations = JobConfiguration::orderBy('annotationsPerUnit','asc')->paginate(15);
        return View::make('jobs.listview')->with('jobConfigurations', $jobConfigurations);
    }

    public function getTableview(){
        return View::make('jobs.tableview');
    }

    /** via routes this method for sorting is called
    param 1 is the sorting method and param 2 is desc/asc in string
    based on params  the result is returned in the right way **/
    public function sortModel($method, $sort){
        $jobConfigurations = JobConfiguration::orderBy($method, $sort)->paginate(15);
        return View::make('jobs.results')->with('jobConfigurations', $jobConfigurations);
    }

    public function createdBy($term){
        // TODO refine query
        return View::make('jobs.results')->with(JobConfiguration::where('createdBy', '=', $term ));
    }

}
