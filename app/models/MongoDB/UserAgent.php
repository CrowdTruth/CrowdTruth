<?php

namespace MongoDB;

use Jenssegers\Mongodb\Sentry\User as SentryUser;
use Schema;

use Illuminate\Auth\UserInterface;

class UserAgent extends SentryUser implements UserInterface {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $collection = 'useragents';
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

    public static function boot()
    {
        parent::boot();

        static::saving(function($activity)
        {
            if(!Schema::hasCollection('useragents'))
            {
                static::createSchema();
            }
        });
    }

    public static function createSchema() {
		Schema::create('useragents', function($collection)
		{
		    $collection->unique('email');
		});
    }

    /**
     * Get list of all users
     */
    public static function getUserlist()
    {
    	return UserAgent::orderBy('_id', 'asc')->get();
    }
    
    public function associatedActivities(){
    	return $this->hasMany('\MongoDB\Activity', 'user_id', '_id');
    }

    public function associatedEntities(){
    	return $this->hasMany('\MongoDB\Entity', 'user_id', '_id');
    }

	public function getRememberToken()
	{
	    return $this->remember_token;
	}

	public function setRememberToken($value)
	{
	    $this->remember_token = $value;
	}

	public function getRememberTokenName()
	{
	    return 'remember_token';
	}
	
	// Override Jenssegers\Mongodb\Sentry\User
	public function isActivated()
	{
		return true;
	}
	
	public function getLoginName()
	{
		return '_id';
	}
}
