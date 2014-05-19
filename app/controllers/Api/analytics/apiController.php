<?php namespace Api\analytics;

use \BaseController as BaseController;
use \Input as Input;
use \URL as URL;
use \Response as Response;

use \MongoDB\Repository as Repository;
use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;
use \MongoDB\SoftwareAgent as SoftwareAgent;
use \MongoDB\CrowdAgent as CrowdAgent;
use \Job;
use \Exception;

class apiController extends BaseController
{
    protected $operators = array(
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'not like', 'between', 'ilike',
        '&', '|', '^', '<<', '>>',
        'exists', 'type', 'mod', 'where', 'all', 'size', 'regex',
    );

    /**
     * Operator conversion.
     *
     * @var array
     */
    protected $conversion = array(
        '=' => '=',
        '!=' => '$ne',
        '<>' => '$ne',
        '<' => '$lt',
        '<=' => '$lte',
        '>' => '$gt',
        '>=' => '$gte',
    );
    protected $pipelineList = array('$match','$sort','$project','$group');

    public $restful = true;
    private $content_type = 'application/json';

    protected $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    private function processAggregateInput($input)
    {

        $aggregateOperators = array();

        if (Input::has('match')) {
            foreach (input::get('match') as $field => $value) {

                if ((array)$value !== $value) {
                    if ($value == 'true') $value = true;
                    else if ($value == 'false') $value = false;
                    if (is_numeric($value)) {
                        $aggregateOperators['$match'][$field] = $value + 0;
                    } else {
                        $aggregateOperators['$match'][$field] = $value;
                    }

                    continue;
                }

                foreach (array_keys($value) as $k => $v) {
                    //is associative array
                    if ($k !== $v) {
                        //change to numbers the values
                        foreach ($value as $key => $val) {

                            if (isset($this->conversion[$key])) {
                                $key = $this->conversion[$key];
                            }

                            if ($key == 'like') {
                                $aggregateOperators['$match'][$field] = new \MongoRegex('/' . $val . '/i');
                            } else {
                                $aggregateOperators['$match'][$field][$key] = floatval($val);
                            }
                        }
                    } else {
                        $aggregateOperators['$match'][$field]['$in'] = $value;
                    }
                    break;
                }

            }
        }
        if (Input::has('sort')) {
            foreach (Input::get('sort') as $field => $value) {
                $aggregateOperators['$sort'][$field] = intval($value);
            }
        }
        if (Input::has('project')) {
            foreach (Input::get('project') as $field => $value) {
                $aggregateOperators['$project'][$field] = array('$ifNull' => array('$' . $value, 0));
            }
        }
        if (Input::has('push')) {
            //dd(Input::get());
            foreach (Input::get('push') as $field => $value) {
                $aggregateOperators['$group'][$field] = array('$push' => '$' . $value);
            }
        }
        if (Input::has('addToSet')) {
            $aggregateOperators['$group']['content'] = array('$addToSet' => "$" . Input::get('addToSet'));
        }

        return $aggregateOperators;
    }

    public function getWorker()
    {
        $result = array();
        $aggregateOperators = $this->processAggregateInput(Input::all());
        $crowdAgentID = Input::get('agent');
        $result['infoStat'] = \MongoDB\CrowdAgent::where('_id', $crowdAgentID)->get()->toArray()[0];
        $result['infoStat']['avgAgreementAcrossJobs'] = \MongoDB\CrowdAgent::avg('avg_agreement');
        $result['infoStat']['avgCosineAcrossJobs'] = \MongoDB\CrowdAgent::avg('avg_cosine');


        $selection = \MongoDB\Entity::raw(function ($collection) use ($aggregateOperators, $crowdAgentID) {
            $aggregateOperators['$match']['crowdAgent_id'] = $crowdAgentID;
            $aggregateOperators['$match']['documentType'] = 'annotation';
            $aggregateOperators['$project']['job_id'] = array('$ifNull' => array('$' . 'job_id', 0));
            $aggregateOperators['$project']['unit_id'] = array('$ifNull' => array('$' . 'unit_id', 0));
            $aggregateOperators['$project']['type'] = array('$ifNull' => array('$' . 'type', 0));
            $aggregateOperators['$project']['annotation'] = array('$ifNull' => array('$' . 'dictionary', 0));

            $aggregateOperators['$group']['_id'] = '$unit_id';
            $aggregateOperators['$group']['count'] = array('$sum' => 1);
            $aggregateOperators['$group']['job_id'] = array('$push' => ('$job_id'));
            $aggregateOperators['$group']['type'] = array('$push' => ('$type'));
            $aggregateOperators['$group']['annotation'] = array('$push' => ('$annotation'));

            return $collection->aggregate(array
            (array('$match' => $aggregateOperators['$match']),
                array('$project' => $aggregateOperators['$project']),
                array('$group' => $aggregateOperators['$group'])));
        });
        $response = $selection['result'];
        $unitIDs = array();
        $jobIDs = array();

        foreach ($response as $unit => $value) {
            $result['annotationContent'][$value['_id']] = $value;
            array_push($unitIDs, $value['_id']);
            $annotationType = array();
            foreach ($value['job_id'] as $index => $type) {
                array_push($jobIDs, $value['job_id'][$index]);
                if (!array_key_exists($type, $annotationType)) {
                    $annotationType[$type] = $value['annotation'][$index];
                } else {
                    $annInfo = $value['annotation'][$index];
                    foreach ($annInfo as $k => $v) {
                        if (is_numeric($v)) {
                            $annotationType[$type][$k] += $v;
                        } else {
                            foreach ($v as $embeddedK => $embeddedV) {
                                $annotationType[$type][$k][$embeddedK] += $embeddedV;
                            }
                        }
                    }
                }
            }
     //       $result['annotationContent'][$value['_id']]['annotationType'] = $annotationType;
        $result['annotationContent'][$value['_id']]['annotationType'] = array();
            foreach ($annotationType as $job => $annotation) {
                $annotationInfo = array('job_id' => $job, 'annotation' => $annotation);
                $result['annotationContent'][$value['_id']]['annotationType'][$job] = $annotationInfo;
            }
        }

        $unitIDs = array_unique($unitIDs);
        $units = \MongoDB\Entity::whereIn('_id', $unitIDs)->get(array('content.sentence.formatted',
                                                                    'content.sentence.formatted',
                                    'documentType',
                                    'domain',
                                                                    'content.relation.noPrefix',
                                                                    'content.terms.first.formatted',
                                                                    'content.terms.second.formatted'))->toArray();
        foreach($units as $index =>$value) {
            $result['annotationContent'][$value['_id']]['unitContent'] = $value;
        }

        $jobIDs = array_unique($jobIDs);
        $jobs = \MongoDB\Entity::whereIn('_id', $jobIDs)->get(array('metrics.workers.withFilter.'.$crowdAgentID,
                                                                     'metrics.aggWorkers', 'type', 'jobConf_id', 'template', 'platformJobId', 'metrics.units', 'results'))->toArray();
    foreach($jobs as $index =>$value) {
        $result['jobContent'][$value['_id']] = $value;
        $jobConfID = \MongoDB\Entity::where('_id', '=', $value['_id'])->lists('jobConf_id');
        $jobTitle = \MongoDB\Entity::whereIn('_id', $jobConfID)->get(array('content.title'))->toArray();
        $result['jobContent'][$value['_id']]['jobConf'] = $jobTitle[0];
    }
    
    foreach($result['annotationContent'] as $id => $annInfo ) {
            foreach ($result['annotationContent'][$id]['annotationType'] as $index => $value) {
                $job_id = $value['job_id'];
                $result['annotationContent'][$id]['annotationType'][$index]['job_info'] =  $result['jobContent'][$job_id];
            }
        }
        return $result;
    }

    public function getJob()
    {
        $result = array();
     //   $aggregateOperators = $this->processAggregateInput(Input::all());
        $jobID = Input::get('job');
        $result['infoStat'] = \MongoDB\Entity::where('_id', $jobID)->get()->toArray()[0];
	$jobConfID = \MongoDB\Entity::where('_id', $jobID)->lists('jobConf_id');
        $jobConf = \MongoDB\Entity::whereIn('_id', $jobConfID)->get()->toArray();
        $result['infoStat']['jobConf'] = $jobConf[0];
	if(isset($result['infoStat']['metrics'])) {
		foreach ($result['infoStat']['metrics']['workers']['withoutFilter'] as $workerId => $value) {
			$result['infoStat']['workers'][$workerId] = \MongoDB\CrowdAgent::where('_id', $workerId)->get()->toArray()[0];
		
		}
		foreach ($result['infoStat']['results']['withSpam'] as $unitId => $value) {
			$result['infoStat']['units'][$unitId] = \MongoDB\Entity::where('_id', $unitId)->get()->toArray()[0];
		
		}
    	}
        return $result;

    }

    public function getAnnotation()
    {
        //http://crowdtruth.org/api/analytics/piegraph/?match[documentType][]=annotation&match[crowdAgent_id][]=crowdagent/cf/21832469&
        //match[crowdAgent_id][]=crowdagent/cf/19107746&match[crowdAgent_id][]=crowdagent/cf/19619472&match[crowdAgent_id][]=crowdagent/cf/19887374&
        //match[crowdAgent_id][]=crowdagent/cf/9333400&match[crowdAgent_id][]=crowdagent/cf/19817298&match[crowdAgent_id][]=crowdagent/cf/22080912&
        //match[crowdAgent_id][]=crowdagent/amt/A1O1DTYWRD9NIZ&match[crowdAgent_id][]=crowdagent/amt/A1EDP2JFUPZOEQ&match[softwareAgent_id][]=cf&match[type][]=FactSpan
        //&project[dictionary]=dictionary&project[unit_id]=unit_id&project[job_id]=job_id&project[id]=_id&project[crowdAgent_id]=crowdAgent_id&group=id
        //&push[dictionary]=dictionary&push[unit_id]=unit_id&push[job_id]=job_id&push[crowdAgent_id]=crowdAgent_id
        $result = array();
        $aggregateOperators = $this->processAggregateInput(Input::all());

        $projectList = array('id' => '_id','dictionary' => 'dictionary', 'job_id'=>'job_id', 'unit_id'=>'unit_id', 'crowdAgent_id' => 'crowdAgent_id');

        $selection = \MongoDB\Entity::raw(function ($collection) use ($aggregateOperators, $projectList) {
            foreach ($projectList as $k => $v) {
                $aggregateOperators['$project'][$k] = array('$ifNull' => array('$' . $v, 0));
                $aggregateOperators['$group'][$k] = array('$push' => ('$'.$k));
            }
            $aggregateOperators['$project']['constField'] = array('$const' => 0);
            $aggregateOperators['$group']['count'] = array('$sum' => 1);
            $aggregateOperators['$group']['_id'] = '$constField';

            return $collection->aggregate(array
            (array('$match' => $aggregateOperators['$match']),
                array('$project' => $aggregateOperators['$project']),
                array('$group' => $aggregateOperators['$group'])));
        });
        $response = $selection['result'][0];

        $units = \MongoDB\Entity::whereIn('_id', $response['unit_id'])->get(array('avg_clarity', 'content.sentence'))->toArray();
        $unitsDict = array();
        foreach($units as $index =>$value) {
            $unitsDict[$value['_id']] = $value;
        }

        $c = Input::get('collection', 'temp');
        $collection = $this->repository->returnCollectionObjectFor($c);
        $jobs = $collection->whereIn('_id', $response['job_id'])->get(array('metrics.annotations', 'hasConfiguration.content.title', 'hasConfiguration.content.type'))->toArray();
        $jobsDict = array();
        foreach($jobs as $index =>$value) {
            $jobsDict[$value['_id']] = $value;
        }

        $agents = \MongoDB\CrowdAgent::whereIn('_id', $response['crowdAgent_id'])->get(array('softwareAgent_id', 'avg_agreement', 'avg_cosine', 'flagged'))->toArray();
        $agentsDict = array();
        foreach($agents as $index =>$value) {
            $agentsDict[$value['_id']] = $value;
        }

        $results =  array();
        foreach($response['id'] as $index => $value){
        //    $results[$index] = array();
        //    $results[$value]['id'] = ;
            $results[$value]['dictionary'] = $response['dictionary'][$index];
            $results[$value]['unit'] = $unitsDict[$response['unit_id'][$index]];
            $results[$value]['job'] = $jobsDict[$response['job_id'][$index]];
            $results[$value]['agent'] = $agentsDict[$response['crowdAgent_id'][$index]];
        }

        return $results;
    }


    public function getUnit()
    {
        $result = array();
        $aggregateOperators = $this->processAggregateInput(Input::all());
        $unitID = Input::get('unit');
        $result['infoStat'] = \MongoDB\Entity::where('_id', $unitID)->get()->toArray()[0];

        $selection = \MongoDB\Entity::raw(function ($collection) use ($aggregateOperators, $unitID) {
            $aggregateOperators['$match']['unit_id'] = $unitID;
            $aggregateOperators['$match']['documentType'] = 'annotation';
            $aggregateOperators['$project']['job_id'] = array('$ifNull' => array('$' . 'job_id', 0));
            $aggregateOperators['$project']['crowdAgent_id'] = array('$ifNull' => array('$' . 'crowdAgent_id', 0));
            $aggregateOperators['$project']['type'] = array('$ifNull' => array('$' . 'type', 0));
            $aggregateOperators['$project']['annotation'] = array('$ifNull' => array('$' . 'dictionary', 0));

            $aggregateOperators['$group']['_id'] = '$crowdAgent_id';
            $aggregateOperators['$group']['count'] = array('$sum' => 1);
            $aggregateOperators['$group']['job_id'] = array('$push' => ('$job_id'));
            $aggregateOperators['$group']['type'] = array('$push' => ('$type'));
            $aggregateOperators['$group']['annotation'] = array('$push' => ('$annotation'));

            return $collection->aggregate(array
            (array('$match' => $aggregateOperators['$match']),
                array('$project' => $aggregateOperators['$project']),
                array('$group' => $aggregateOperators['$group'])));
        });
        $response = $selection['result'];
        $crowdAgentIDs = array();
        $jobIDs = array();
    $result['annotationContent'] = array();
    $result['jobContent'] = array();
    $result['agentContent'] = array();
        foreach ($response as $agent => $value) {
            $result['annotationContent'][$value['_id']] = $value;
            array_push($crowdAgentIDs, $value['_id']);
            $annotationType = array();
            foreach ($value['job_id'] as $index => $type) {
                array_push($jobIDs, $value['job_id'][$index]);
                if (!array_key_exists($type, $annotationType)) {
                    $annotationType[$type] = $value['annotation'][$index];
                } else {
                    $annInfo = $value['annotation'][$index];
                    foreach ($annInfo as $k => $v) {
                        if (is_numeric($v)) {
                            $annotationType[$type][$k] += $v;
                        } else {
                            foreach ($v as $embeddedK => $embeddedV) {
                                $annotationType[$type][$k][$embeddedK] += $embeddedV;
                            }
                        }
                    }
                }
            }
            $result['annotationContent'][$value['_id']]['annotationType'] = array();
            foreach ($annotationType as $job => $annotation) {
                $annotationInfo = array('job_id' => $job, 'annotation' => $annotation);
                $result['annotationContent'][$value['_id']]['annotationType'][$job] = $annotationInfo;
            }

        }

        $crowdAgentIDs = array_unique($crowdAgentIDs);
        $agents = \MongoDB\CrowdAgent::whereIn('_id', $crowdAgentIDs)->get(array('cache',
            'cfWorkerTrust',
            'softwareAgent_id'))->toArray();
        foreach($agents as $index =>$value) {
        $result['annotationContent'][$value['_id']]["valuesWorker"] = $value;
    //        $result['agentContent'][$value['_id']] = $value;
        }

        $jobIDs = array_unique($jobIDs);
        $jobs = \MongoDB\Entity::whereIn('_id', $jobIDs)->get(array('results.withoutSpam.'.$unitID,
            'results.withSpam.'.$unitID,
            'metrics.units.withoutSpam.'.$unitID,
            'metrics.aggUnits',
            'metrics.filteredUnits',
            'metrics.workers.withFilter', 
            'sofwareAgent_id', 
        'platformJobId'))->toArray();

        foreach($jobs as $index =>$value) {
            $result['jobContent'][$value['_id']] = $value;
        $jobConfID = \MongoDB\Entity::where('_id', '=', $value['_id'])->lists('jobConf_id');
        $jobTitle = \MongoDB\Entity::whereIn('_id', $jobConfID)->get(array('content.title'))->toArray();
        $result['jobContent'][$value['_id']]['jobConf'] = $jobTitle[0];
        }
        
        foreach($result['annotationContent'] as $id => $annInfo ) {
            foreach ($result['annotationContent'][$id]['annotationType'] as $index => $value) {
                $job_id = $value['job_id'];
                $result['annotationContent'][$id]['annotationType'][$index]['job_info'] =  $result['jobContent'][$job_id];
            }
        }
        return $result;
    }

   public function getUnitworkerpie()
    {
        $selection = \MongoDB\Entity::raw(function ($collection) {

            $aggregateOperators = $this->processAggregateInput(Input::all());

            $aggregateOperators['$group'] = array();
            $aggregateOperators['$group']['_id'] = '$spam';
            $aggregateOperators['$group']['count'] = array('$sum' => 1);
            $aggregateOperators['$group']['content'] = array('$addToSet' => array("platform" => '$softwareAgent_id', "crowdAgent_id" => '$crowdAgent_id'));

            return $collection->aggregate(array
            (array('$match' => $aggregateOperators['$match']),
                array('$group' => $aggregateOperators['$group'])));
        });
        $results = $selection['result'];
        return $results;
    }

    public function getAggregate()
    {
        $input = Input::all();
        $c = Input::get('collection', 'Entity');

        $collection = $this->repository->returnCollectionObjectFor($c);

        $selection = $collection->raw(function ($collection) use ($input) {

            $aggregateOperators = $this->processAggregateInput($input);
            //a constField field to create the lists
            $aggregateOperators['$project']['constField'] = array('$const' => 0);
            $aggregateOperators['$group']['count'] = array('$sum' => 1);
            $aggregateOperators['$group']['_id'] = '$constField';
            //dd($aggregateOperators);
            return $collection->aggregate(array
            (array('$match' => $aggregateOperators['$match']),
                array('$sort' => $aggregateOperators['$sort']),
                array('$project' => $aggregateOperators['$project']),
                array('$group' => $aggregateOperators['$group'])));
        });
        if (count($selection['result']) == 0) {
            foreach (Input::get('project') as $field => $value) {
                $results[$field] = array();
            }
            $results['query'] = Input::get('match');
            return $results;
        }
        //dd($selection);
        $results = $selection['result'][0];
        $results['query'] = Input::get('match');
        return $results;
    }
    public function getMetrics(){

        $IDsQuery = \MongoDB\Entity::raw(function ($collection) {
            $input = Input::all();
            $aggregateOperators = $this->processAggregateInput($input);
            $aggregateOperators['$group']['_id'] = 0;
            $aggregateQuery = array();
            foreach($this->pipelineList as $operator){
                if(array_key_exists($operator, $aggregateOperators)) {
                    array_push( $aggregateQuery, array($operator=>$aggregateOperators[$operator]));
                }
            }

            return $collection->aggregate($aggregateQuery);
        });

        $c = Input::get('collection', 'Entity');

        $collection = $this->repository->returnCollectionObjectFor($c);
        $targetField = key(Input::get('push'));

        if (count($IDsQuery['result']) == 0) {
            $results = array();
            foreach (Input::get('metrics') as $field => $value) {
                $results[$value] = array();
            }
            return $results;
        }

        $IDsList = $IDsQuery['result'][0][$targetField];
        $metricListNames = Input::get('metrics');
        $metrics = $collection::whereIn('_id', $IDsList)->get($metricListNames)->toArray();
        return $metrics;
    }

    public function getSpammers()
    {
        $spammersSet = \MongoDB\Entity::raw(function ($collection) {
            $match = array('documentType' => 'job', 'metrics' => array('$exists' => true));
            $project = array('_id' => 0, 'spammers' => '$metrics.spammers.list', 'index' => array('$const' => 0));
            $unwind = '$spammers';
            $group = array('_id' => '$index', 'spammers' => array('$addToSet' => '$spammers'));

            return $collection->aggregate(array
            (array('$match' => $match),
                array('$project' => $project),
                array('$unwind' => $unwind),
                array('$group' => $group)));

        });
        return $spammersSet['result'][0]['spammers'];
    }

    public function getUnitvector() {
        $template = Input::get('template');
        $aggregateOperators = $this->processAggregateInput(Input::all());


    }

    public function getPiegraph()
    {
        if (!(Input::has('group'))) {
            return 'No Data';
        }

        $c = Input::get('collection', 'Entity');

        $collection = $this->repository->returnCollectionObjectFor($c);

        $selection = $collection->raw(function ($collection) {
            $groupValue = Input::get('group');
            $aggregateOperators = $this->processAggregateInput(Input::all());
            $aggregateOperators['$project'][$groupValue] = "$" . $groupValue;

            //:{ 'jobs': {$cond: [{ $gt: [{$ifNull: [ '$cache.jobs.count', 0 ] }, 0]}, 'inJobs', 'notInJobs']}}}
            $aggregateOperators['$project']['jobs'] = array('$cond' =>
                array( array('$gt'=>array( array('$ifNull' => array ('$cache.jobs.count', 0)),0)), 'used in Jobs', 'not used in Jobs'));
            $aggregateOperators['$group']['_id'] = "$" . $groupValue;
            $aggregateOperators['$group']['count'] = array('$sum' => 1);

            $aggregateQuery = array();
            foreach($this->pipelineList as $operator){
                if(array_key_exists($operator, $aggregateOperators)) {
                    array_push( $aggregateQuery, array($operator=>$aggregateOperators[$operator]));
                }
            }
            return $collection->aggregate($aggregateQuery);
        });

        $results = $selection['result'];

        return $results;
    }
    public function getWorkergraph()
    {
        $c = Input::get('collection', 'Entity');

        $collection = $this->repository->returnCollectionObjectFor($c);

        $selection = $collection->raw(function ($collection) {

            $aggregateOperators = $this->processAggregateInput(Input::all());

            //additionally project
            //a constField field to create the lists
            $aggregateOperators['$project']['constField'] = array('$const' => 0);

            //project the id as well
            $aggregateOperators['$project']['_id'] = 1;

            //push all the values in a list
            foreach (Input::get('project') as $field => $value) {
                $aggregateOperators['$group'][$field] = array('$push' => ('$' . $field));
            }
            $aggregateOperators['$project']['workers'] = array('$ifNull' => array(array('$subtract' => array('$cache.jobTypes.count', '$cache.spammer.count')), 0));
            $aggregateOperators['$project']['jobsCount'] = array('$ifNull' => array('$' . 'cache.jobTypes.count', 0));
            $aggregateOperators['$project']['annotationsCount'] = array('$ifNull' => array('$' . 'cache.annotations.count', 0));
            $aggregateOperators['$group']['id'] = array('$push' => ('$_id'));
            $aggregateOperators['$group']['workers'] = array('$push' => ('$workers'));

            //group by id to create the lists and compute average of workers, units, annotatations
            $aggregateOperators['$group']['_id'] = '$constField';
            $aggregateOperators['$group']['avgAnnotations'] = array('$avg' => '$annotationsCount');
            $aggregateOperators['$group']['avgJobs'] = array('$avg' => '$jobsCount');


            $aggregateQuery = array();
            foreach($this->pipelineList as $operator){
                if(array_key_exists($operator, $aggregateOperators)) {
                    array_push( $aggregateQuery, array($operator=>$aggregateOperators[$operator]));
                }
            }
            return $collection->aggregate($aggregateQuery);

        });
        if (count($selection['result']) == 0) {
            foreach (Input::get('project') as $field => $value) {
                $results[$field] = array();
            }
            $results['jobsCount'] = array();
            $results['annotationsCount'] = array();
            $results['id'] = array();
            $results['workers'] = array();
            $results['avgAnnotations'] = array();
            $results['avgJobs'] = array();
            return $results;
        }
        $results = $selection['result'][0];

        //get the workers found as spammers in other jobs
        $ids = $selection['result'][0]['id'];
        $sizeIDs = count($ids);
        $results['avgAnnotations'] = array_fill(0, $sizeIDs, $results['avgAnnotations']);
        $results['avgWorkers'] = array_fill(0, $sizeIDs, $results['avgJobs']);

        $results['query'] = Input::get('match');
        return $results;
    }

    public function getUnitgraph()
    {
        $c = Input::get('collection', 'Entity');

        $collection = $this->repository->returnCollectionObjectFor($c);

        $selection = $collection->raw(function ($collection) {

            $aggregateOperators = $this->processAggregateInput(Input::all());

            //additionally project
            //a constField field to create the lists
            $aggregateOperators['$project']['constField'] = array('$const' => 0);

            //project the id as well
            $aggregateOperators['$project']['_id'] = 1;

            //push all the values in a list
            foreach (Input::get('project') as $field => $value) {
                $aggregateOperators['$group'][$field] = array('$push' => ('$' . $field));
            }
            $aggregateOperators['$project']['workersCount'] = array('$ifNull' => array('$' . 'cache.workers.count', 0));
            $aggregateOperators['$project']['annotationsCount'] = array('$ifNull' => array('$' . 'cache.annotations.count', 0));
            $aggregateOperators['$project']['jobsCount'] = array('$ifNull' => array('$' . 'cache.jobTypes.count', 0));
            $aggregateOperators['$group']['id'] = array('$push' => ('$_id'));

            //group by id to create the lists and compute average of workers, units, annotatations
            $aggregateOperators['$group']['_id'] = '$constField';
            $aggregateOperators['$group']['avgWorkers'] = array('$avg' => '$workersCount');
            $aggregateOperators['$group']['avgAnnotations'] = array('$avg' => '$annotationsCount');
            $aggregateOperators['$group']['avgJobs'] = array('$avg' => '$jobsCount');


            return $collection->aggregate(array
            (array('$match' => $aggregateOperators['$match']),
                array('$sort' => $aggregateOperators['$sort']),
                array('$project' => $aggregateOperators['$project']),
                array('$group' => $aggregateOperators['$group'])));

        });
        if (count($selection['result']) == 0) {
            foreach (Input::get('project') as $field => $value) {
                $results[$field] = array();
            }
            $results['id'] = array();
            $results['workersCount'] = array();
            $results['annotationsCount'] = array();
            $results['jobsCount'] = array();
            $results['id'] = array();
            $results['avgWorkers'] = array();
            $results['avgAnnotations'] = array();
            $results['avgJobs'] = array();
            $results['query'] = Input::get('match');
            return $results;
        }
        $results = $selection['result'][0];

        //get the workers found as spammers in other jobs
        $ids = $selection['result'][0]['id'];
        $sizeIDs = count($ids);
        $results['avgWorkers'] = array_fill(0, $sizeIDs, $results['avgWorkers']);
        $results['avgAnnotations'] = array_fill(0, $sizeIDs, $results['avgAnnotations']);
        $results['avgJobs'] = array_fill(0, $sizeIDs, $results['avgJobs']);

        $results['query'] = Input::get('match');
        return $results;
    }


    public function getJobgraph()
    {

        $c = Input::get('collection', 'Entity');

        $collection = $this->repository->returnCollectionObjectFor($c);

        $selection = $collection->raw(function ($collection) {

            $aggregateOperators = $this->processAggregateInput(Input::all());

            //additionally project
            //a constField field to create the lists
            $aggregateOperators['$project']['constField'] = array('$const' => 0);
            //project the id as well
            $aggregateOperators['$project']['_id'] = 1;

            //push all the values in a list
            foreach (Input::get('project') as $field => $value) {
                $aggregateOperators['$group'][$field] = array('$push' => ('$' . $field));
            }
            $aggregateOperators['$group']['id'] = array('$push' => ('$_id'));

            //group by id to create the lists and compute average of workers, units, annotatations
            $aggregateOperators['$group']['_id'] = '$constField';
            $aggregateOperators['$group']['avgWorkers'] = array('$avg' => '$workers');
            $aggregateOperators['$group']['avgAnnotations'] = array('$avg' => '$annotations');
            $aggregateOperators['$group']['avgUnits'] = array('$avg' => '$units');


            return $collection->aggregate(array
            (array('$match' => $aggregateOperators['$match']),
                array('$sort' => $aggregateOperators['$sort']),
                array('$project' => $aggregateOperators['$project']),
                array('$group' => $aggregateOperators['$group'])));

        });
        $results = $selection['result'][0];

        //create an array for time values
        $time = array();

        //get all the spammers in the database
        $spammersSet = $this->getSpammers();

        //get the workers found as spammers in other jobs
        $ids = $selection['result'][0]['id'];
        $sizeIDs = count($ids);
        $potentialSpammersCount = array();

        //use a for to insure the same order is preserved in the arrays
        for ($iter = 0; $iter < $sizeIDs; $iter++) {
            //get the workers of the job
            $workersOfJob = \MongoDB\Entity::where('documentType', 'annotation')->where('job_id', $ids[$iter])->lists('crowdAgent_id');
            $workersOfJob = array_unique($workersOfJob);

            //check if there are spammers
            $spammersOfJob = \MongoDB\Entity::where('_id', $ids[$iter])->select('metrics.spammers.list')->get();

            if (isset($spammersOfJob[0]['metrics'])) {
                $spammersOfJob = $spammersOfJob[0]['metrics']['spammers']['list'];
                //remove the spammers
                $workersOfJob = array_diff($workersOfJob, $spammersOfJob);
            }
            //check if in the list of workers there are workers marked as spamm in other jobs
            $potentialSpammers = array_intersect($workersOfJob, $spammersSet);
            $potentialSpammersCount[$iter] = count($potentialSpammers);
            $results['workers'][$iter] = count($workersOfJob) - count($potentialSpammers);

            //add the time value


        }

        $results['avgWorkers'] = array_fill(0, $sizeIDs, $results['avgWorkers']);
        $results['avgAnnotations'] = array_fill(0, $sizeIDs, $results['avgAnnotations']);
        $results['avgUnits'] = array_fill(0, $sizeIDs, $results['avgUnits']);
        $results['potentialSpamWorkers'] = $potentialSpammersCount;
        $results['query'] = Input::get('match');
        return $results;
    }

    public function getJobtypes()
    {
        return array_flatten(\MongoDB\Entity::where('documentType', 'job')->distinct('type')->get()->toArray());
    }

    public function getIndex()
    {


    }

    private function returnJson($return)
    {
        return Response::json($return);
    }

}

?>
