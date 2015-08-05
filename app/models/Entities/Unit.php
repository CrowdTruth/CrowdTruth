<?php

/**
*   The unit class is used to create unit entities. This can be both raw and structured documents.
*/

namespace Entities;

use \Entity as Entity;
use \SoftwareAgent as SoftwareAgent;
use \Activity as Activity;

class Unit extends Entity { 

	protected $attributes = array('documentType' => 'unit');
	
    /**
    *   Override the standard query to include documenttype.
    */
    public function newQuery($excludeDeleted = true)
    {
        $query = parent::newQuery($excludeDeleted = true);
        $query->where('documentType', 'unit');
        return $query;
    }

	public static function boot ()
    {
        parent::boot();

        static::creating(function ( $unit )
        {
		
			/**
			 * Store a new unit to the database. Construct all entity information for such file.
			 * 
			 */
		
			// Create the SoftwareAgent if it doesnt exist
			SoftwareAgent::store('filecreator', 'Unit creation');
			
			if(!isset($unit->activity)){
				$activity = new Activity;
				$activity->label = "Unit added to the platform";
				$activity->softwareAgent_id = 'filecreator';
				$activity->save();
				$unit->activity_id = $activity->_id;
			}
		});
	}
			

				
}
?>
