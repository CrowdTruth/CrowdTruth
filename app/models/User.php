<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Moloquent implements UserInterface, RemindableInterface {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $connection = 'mongodb_app';
	protected $collection = 'users';
	protected static $unguarded = true;
	
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

    public function associatedTextActivities(){
    	return $this->hasMany('\mongo\text\Activity', 'user_id', '_id');
    }

    public function associatedImageActivities(){
    	return $this->hasMany('\mongo\image\Activity', 'user_id', '_id');
    }

    public function associatedVideoActivities(){
    	return $this->hasMany('\mongo\video\Activity', 'user_id', '_id');
    }

    public function associatedTextEntities(){
    	return $this->hasMany('\mongo\text\Entity', 'user_id', '_id');
    }

    public function associatedImageEntities(){
    	return $this->hasMany('\mongo\image\Entity', 'user_id', '_id');
    }

    public function associatedVideoEntities(){
    	return $this->hasMany('\mongo\video\Entity', 'user_id', '_id');
    }
}