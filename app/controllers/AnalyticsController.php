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
        if( Input::has('jobs' )) {
            $params = Input::get('jobs');
            $job_array = explode(',', $params);
            $tags = implode(', ', $job_array);

            return View::make('analytics.jobview')->with('jobConfigurations', $params);
        }

        if( Input::has('annotations')) {
            //etc.
        }

        
        // $c = Input::get('collection', 'Entity');

        // $collection = $this->repository->returnCollectionObjectFor($c);

        // if(Input::has('field'))
        // {
        //     $collection = $this->processFields($collection);
        // }
        // $jobConfigurations = $collection->get();

        // return View::make('analytics.jobview')->with('jobConfigurations', $jobConfigurations);

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



}
