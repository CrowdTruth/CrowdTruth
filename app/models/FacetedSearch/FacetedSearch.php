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

    public function getMainSearchFilters()
    {
        $mainSearchFilters['formats'] = $this->getDistinctFieldAndCount('format');
        $mainSearchFilters['domains'] = $this->getDistinctFieldAndCount('domain');
        $mainSearchFilters['documentTypes'] = $this->getDistinctFieldAndCount('documentType');

        // dd($mainSearchFilters);

        return $mainSearchFilters;
    }
}