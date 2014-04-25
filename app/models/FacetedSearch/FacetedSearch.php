<?php

use \MongoDB\Entity as Entity;

class FacetedSearch {

	public function getDistinctFieldAndCount($field)
	{
    	$distinctFieldValues = array_flatten(Entity::distinct($field)->get()->toArray());

    	$distinctFieldValuesAndCount = array();

    	foreach($distinctFieldValues as $distinctFieldValue)
    	{
    		$distinctFieldValuesAndCount[$distinctFieldValue]['count'] = Entity::where($field, $distinctFieldValue)->count();
    	}

        return $distinctFieldValuesAndCount;
	}

    public function getMainSearchFilters($page = "media")
    {
        if($mainSearchFilters = \MongoDB\Temp::where('_id', 'mainSearchFilters')->where('created_at', '>', new \DateTime('-1 hour'))->first()){
            $mainSearchFilters = $mainSearchFilters->toArray()['filters'];
        } else {
            $mainSearchFilters['formats'] = $this->getDistinctFieldAndCount('format');
            $mainSearchFilters['domains'] = $this->getDistinctFieldAndCount('domain');
            $mainSearchFilters['documentTypes'] = $this->getDistinctFieldAndCount('documentType');

            if($page == "media")
            {
                $mainSearchFilters['documentTypes'] = array_only($mainSearchFilters['documentTypes'], array('twrex-structured-sentence', 'job'));
            } elseif ($page == "job")
            {
                $mainSearchFilters['documentTypes'] = array_only($mainSearchFilters['documentTypes'], array('job'));
            }

            foreach($mainSearchFilters['domains'] as $domain => $domainVal)
            {
                $mainSearchFilters['domains'][$domain]['formats'] = array_flatten(Entity::where('domain', $domain)->distinct('format')->get()->toArray());
            }        

            foreach($mainSearchFilters['documentTypes'] as $documentType => $docVal)
            {
                $mainSearchFilters['documentTypes'][$documentType]['domains'] = array_flatten(Entity::where('documentType', $documentType)->distinct('domain')->get()->toArray());
                $mainSearchFilters['documentTypes'][$documentType]['formats'] = array_flatten(Entity::where('documentType', $documentType)->distinct('format')->get()->toArray());
            }

            $mainSearchFilters['documentTypes']["crowdagents"] = array("count" => 0);

            $mainSearchFilters['useragents'] = \User::all()->toArray();

            if($oldSearchFilters = \MongoDB\Temp::where('_id', 'mainSearchFilters')->first())
            {
                $oldSearchFilters->forceDelete();
            }

            $entity = new \MongoDB\Temp;
            $entity->_id = "mainSearchFilters";
            $entity->filters = $mainSearchFilters;
            $entity->save();
        }

        if(!\MongoDB\Temp::where('_id', 'jobCache')->where('created_at', '>', new \DateTime('-1 hour'))->first())
        {            
            \Session::flash('rawArray', 1);
            $db = \DB::getMongoDB();
            $db = $db->temp;
            
            $result = \MongoDB\Entity::where('documentType', 'job')->with('hasConfiguration')->get()->toArray();

            array_push($result, [
                "_id" => "jobCache",
                "created_at" => new \MongoDate(time()),
            ]);

            try {
                $db->batchInsert(
                    $result,
                    array('continueOnError' => true)
                );             
            } catch (Exception $e) {
            // ContinueOnError will still throw an exception on duplication, even though it continues, so we just move on.
            }


            \Session::forget('rawArray');
        }

        // dd($mainSearchFilters);

        return $mainSearchFilters;
    }
}