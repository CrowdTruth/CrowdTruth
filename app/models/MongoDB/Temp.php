<?php

namespace MongoDB;

use Moloquent, Input;


class Temp extends Moloquent {

	protected $collection = 'temp';
	protected static $unguarded = true;
    public static $snakeAttributes = false;

    public function __construct()
    {
        $this->filterResults();
        parent::__construct();
    }

    protected $hidden = ['metrics.pivotTables'];

    public function filterResults()
    {
        $input = Input::all();
        if(array_key_exists('wasDerivedFrom', $input))    array_push($this->appends, 'wasDerivedFrom');
        if(array_key_exists('wasGeneratedBy', $input))    array_push($this->with, 'wasGeneratedBy');
        if(array_key_exists('wasAttributedTo', $input))    $this->with = array_merge($this->with, array('wasAttributedToUserAgent', 'wasAttributedToCrowdAgent'));
        if(array_key_exists('wasAttributedToUserAgent', $input))    array_push($this->with, 'wasAttributedToUserAgent');
        if(array_key_exists('wasAttributedToCrowdAgent', $input))    array_push($this->with, 'wasAttributedToCrowdAgent');
        if(array_key_exists('hasConfiguration', $input))    array_push($this->with, 'hasConfiguration');
        if(isset($input['wasDerivedFrom']['without'])) $this->hidden = array_merge($this->hidden, array_flatten(array($input['wasDerivedFrom']['without'])));
        if(isset($input['without'])) $this->hidden = array_merge($this->hidden, array_flatten(array($input['without'])));
    }       

	public static function createImageCache()
	{
        \Session::flash('rawArray', 1);
        $db = \DB::getMongoDB();
        $db = $db->temp;

        \Queue::push('Queues\UpdateUnits', \MongoDB\Entity::whereIn('documentType', ['painting', 'drawing', 'picture'])->lists('_id'));

		$result = \MongoDB\Entity::whereIn('documentType', ['painting', 'drawing', 'picture'])->get()->toArray();

		if(count($result) > 0)
		{
            foreach($result as &$parent)
            {
                $children = \MongoDB\Entity::whereIn('parents', [$parent['_id']])->get(['recognizedFeatures', 'content.features'])->toArray();

                $parent['content']['features'] = [];
                $parent['totalRelevantFeatures'] = 0;

                foreach($children as $child){

                    if(isset($child['recognizedFeatures']))
                    {
                        $parent['totalRelevantFeatures'] = $parent['totalRelevantFeatures'] + count($child['recognizedFeatures']);
                    }

                    $featureKey = key($child['content']['features']);

                    if(!isset($parent['content']['features'][$featureKey]))
                    {
                        $parent['content']['features'][$featureKey] = [];

                        if(is_array($child['content']['features'][$featureKey]))
                        {
                            foreach($child['content']['features'][$featureKey] as $k => $v)
                            {
                                $parent['content']['features'][$featureKey][$k] = $v;
                            }                           
                        } 

                    }
                    else {
                        if(is_array($child['content']['features'][$featureKey]))
                        {
                            foreach($child['content']['features'][$featureKey] as $k => $v)
                            {
                                $parent['content']['features'][$featureKey][$k] = $v;
                            }
                        } 
                        else 
                        {
                            $parent['content']['features'][$featureKey] = $child['content']['features'][$featureKey];
                        }
                    }
                }
            }

	        try {
                \MongoDB\Temp::whereIn('documentType', ['painting', 'drawing', 'picture'])->forceDelete();

	            $db->batchInsert(
	                $result,
	                array('continueOnError' => true)
	            );             
	        } catch (Exception $e) {
	        // ContinueOnError will still throw an exception on duplication, even though it continues, so we just move on.
	        }
			
		}

        \Session::forget('rawArray');
    }

	public static function getDistinctFieldLabelAndCount($field, $tags = null)
	{
        if($tags == null)
        {
            $distinctFieldValues = array_flatten(Entity::distinct($field)->get()->toArray());
        }
        else
        {
            $distinctFieldValues = array_flatten(Entity::whereIn('tags', $tags)->distinct($field)->get()->toArray());
        }

    	$distinctFieldValuesAndCount = array();

    	foreach($distinctFieldValues as $distinctFieldValue)
    	{
            $distinctFieldValuesAndCount[$distinctFieldValue]['label'] = $distinctFieldValue;

            if(isset(\MongoDB\Entity::getKeyLabelMapping()[strtolower($distinctFieldValue)])) {
                $distinctFieldValuesAndCount[$distinctFieldValue]['label'] = \MongoDB\Entity::getKeyLabelMapping()[$distinctFieldValue];
            }

    		$distinctFieldValuesAndCount[$distinctFieldValue]['count'] = Entity::where($field, $distinctFieldValue)->count();
    	}

        return $distinctFieldValuesAndCount;
	}    

    public static function createMainSearchFiltersCache()
    {
        // $mainSearchFilters['media']['formats'] = $this->getDistinctFieldAndCount('format', ['unit']);
        // $mainSearchFilters['media']['domains'] = $this->getDistinctFieldAndCount('domain', ['unit']);
        $mainSearchFilters['media']['documentTypes'] = static::getDistinctFieldLabelAndCount('documentType', ['unit']);
        $mainSearchFilters['media']['documentTypes']['all'] = ["count" => \MongoDB\Entity::whereIn('tags', ['unit'])->count(),
                                                                "label" => "All units"
                                                                ];
        
        unset($mainSearchFilters['media']['documentTypes']['twrex']);

        $mainSearchFilters['job']['count'] = Entity::where('documentType', 'job')->count();
        $mainSearchFilters['workers']['count'] = \MongoDB\CrowdAgent::all()->count();

        ksort($mainSearchFilters['media']['documentTypes']);

	    $entity = new \MongoDB\Temp;
	    $entity->_id = "mainSearchFilters";
	    $entity->filters = $mainSearchFilters;
	    $entity->save();

	    return $entity->toArray();
    }

    public static function getMainSearchFiltersCache()
    {
    	if(!\MongoDB\Temp::where('_id', 'mainSearchFilters')->first())
    	{
            static::createImageCache();
            static::createJobCache();
    		return static::createMainSearchFiltersCache();
    	}

    	return \MongoDB\Temp::where('_id', 'mainSearchFilters')->first()->toArray();
    }

    public static function createJobCache()
    {        
        \Session::flash('rawArray', 1);
        $db = \DB::getMongoDB();
        $db = $db->temp;

        $result = \MongoDB\Entity::where('documentType', 'job')->with('hasConfiguration')->get()->toArray();

        if(count($result) > 0)
        {
            try {
                \MongoDB\Temp::where('documentType', 'job')->forceDelete();

                $db->batchInsert(
                    $result,
                    array('continueOnError' => true)
                );             
            } catch (Exception $e) {
            // ContinueOnError will still throw an exception on duplication, even though it continues, so we just move on.
            }
        }

        \Session::forget('rawArray');
    }
}
