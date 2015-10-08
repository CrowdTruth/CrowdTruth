<?php

/**
*   The file class is used to create a new file entity.
*	If the file already exists, it will return the existing file
*/
namespace Entities;

use \Entity as Entity;
use \SoftwareAgent as SoftwareAgent;
use \Activity as Activity;
use \Exception as Exception;

class File extends Entity { 
    
	protected $attributes = array('documentType' => 'file');
	
	/**
    *   Override the standard query to include documenttype.
    */
    public function newQuery($excludeDeleted = true)
    {
        $query = parent::newQuery($excludeDeleted = true);
        $query->where('documentType', 'file');
        return $query;
    }

	protected $types = [
				'text/plain',
				'text/anytext',
				'application/txt',
				'application/octet-stream',
				'text/x-c',
				'text/x-asm',
				'text/x-pascal',
				'text/x-c++'];
	
    public static function boot ()
    {
        parent::boot();

        static::creating(function ( $file )
        {
		
			/**
			 * Store a new file to the database. Construct all entity information for such file.
			 * 
			 */
		
			// Create the SoftwareAgent if it doesnt exist
			SoftwareAgent::store('filecreator', 'File creation');
			
			if(!isset($file->activity)){
				$activity = new Activity;
				$activity->label = "File added to the platform";
				$activity->softwareAgent_id = 'filecreator';
				$activity->save();
				$file->activity_id = $activity->_id;
			}
		});
	}

	/**
	 * Process a file and store it
	**/
	public function store( $file )
	{
	
		// Throw error if no file
		if(empty($file)) {
			throw new Exception("No file was selected");
		}
		
		$this->title = strtolower($file->getClientOriginalName());
		$this->content = \File::get($file->getRealPath());
		$this->hash = md5(serialize([$this->content]));
		$this->size = $file->getSize();
		$this->filetype = $file->getMimeType();
		
		// see if there are any crowdsourcing results in the file
		if(preg_match("/^\"HITId\",\"HITTypeId\"/", $this->content) || preg_match("/^_unit_id,_created_at/", $this->content)) {
			$this->results = true;
		}
		
		// Throw error if file is of wrong type
		if(!in_array($this->filetype, $this->types)) {
			throw new Exception($this->title . " is not of an accepted file type:" . $this->filetype);
		}
		
		// Throw error if file is too large
		if($this->size > 90000000) {
			throw new Exception($this->title . " is too large");
		}

	}
}
?>
