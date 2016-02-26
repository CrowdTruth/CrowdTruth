<?php

/**
*   The unit class is used to create unit entities. This can be both raw and structured documents.
*/

namespace Entities;

use \Entity as Entity;
use \SoftwareAgent as SoftwareAgent;
use \Activity as Activity;

class Unit extends Entity { 

	protected $attributes = array('type' => 'unit');
	
    /**
    *   Override the standard query to include documenttype.
    */
    public function newQuery($excludeDeleted = true)
    {
        $query = parent::newQuery($excludeDeleted = true);
        $query->where('type', 'unit');
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
			if(!SoftwareAgent::find('unitcreator'))
			{
				$softwareAgent = new SoftwareAgent;
				$softwareAgent->_id = "unitcreator";
				$softwareAgent->label = "This component is used for creating units in the database";
				$softwareAgent->save();
			}
			
			if(!isset($unit->activity)){
				$activity = new Activity;
				$activity->label = "Unit added to the platform";
				$activity->softwareAgent_id = 'unitcreator';
				$activity->save();
				$unit->activity_id = $activity->_id;
			}

		});
	}
				
}
?>
