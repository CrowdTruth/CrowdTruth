<?php

use \MongoDB\Entity as Entity;
use \MongoDB\Activity as Activity;

class UserController extends BaseController {

	public function __construct() {
	   $this->beforeFilter('csrf', array('on'=>'post'));
	}

	public function getIndex(){
		return Redirect::to('user/login');
	}

	public function getLogin(){
		if(Auth::check())
			return Redirect::to('/');

		return View::make('user/login');
	}

	public function getRegister(){
		if(Auth::check())
			return Redirect::to('/');
					
		return View::make('user/register');
	}

	public function getLogout(){
		Cart::destroy();
		Auth::logout();
		return Redirect::to('');
	}
	    
	/**
     * Display current user activity
     */
	public function getActivity() {
	
		// redirect if user is not logged in
		if(!Auth::check())
			return Redirect::to('/');

		$activities = Activity::getActivitiesForUser(Auth::user()->_id);
        return View::make('user/activity')->with('activities', $activities);
    }

	public function postLogin(){
	    $userdata = array(
	        'username' => Input::get('username_or_email'),
	        'email' => Input::get('username_or_email'),
	        'password' => Input::get('password'),
	    );

	    if($user = User::where('_id', '=', strtolower($userdata['username']))->orWhere('email', '=', strtolower($userdata['email']))->first())
	    	if(Auth::attempt(array('email' => $user['email'], 'password' => $userdata['password'])))
	    		return Redirect::intended('/');

    	Session::flash('flashError', 'Invalid credentials');
    	return Redirect::back();
	}

	public function postRegister(){
		$role = 'user';
		// Check if demo account
		if (Hash::check(Input::get('invitation'), Config::get('config.demoInvitationCode'))) {
			$role = 'demo';
		// Check if normal account	
		} elseif (!Hash::check(Input::get('invitation'), Config::get('config.invitationCode')) && Config::get('config.invitationCode') != ''){
			Session::flash('flashError', 'Wrong invite code : )');
			return Redirect::back()->withInput(Input::except('password', 'confirm_password'));
		}

	    $userdata = array(
	    	'_id' => strtolower(Input::get('username')),
	        'firstname' => ucfirst(strtolower(Input::get('firstname'))),
	        'lastname' => ucfirst(strtolower(Input::get('lastname'))),
	        'email' => strtolower(Input::get('email')),
	        'password' => Input::get('password'),
	        'confirm_password' => Input::get('confirm_password'),
	        'role' => $role
	    );

        $rules = array(
        	'_id' => 'required|min:3|unique:useragents',        	
            'firstname' => 'required|min:3',
            'lastname' => 'required|min:1',
            'email' => 'required|email|unique:useragents',
            'password' => 'required|min:5',
            'confirm_password' => 'required|same:password'
        );

	    $validation = Validator::make($userdata, $rules);

	    if($validation->fails()){

	    	$msg = '<ul>';
			foreach ($validation->messages()->all() as $message)
				$msg .= "<li>$message</li>";

	    	Session::flash('flashError', "$msg</ul>");
	        return Redirect::back()->withInput(Input::except('password', 'confirm_password'));
	    }

	    unset($userdata['confirm_password']);
	    $userdata['password'] = Hash::make($userdata['password']);
	    $user = new User($userdata); 

	    try {
		    $this->createCrowdWatsonUserAgent();
		    $user->save();
	    } catch (Exception $e) {
	    	return Redirect::back()->with('flashError', $e->getMessage())->withInput(Input::except('password', 'confirm_password'));
	    }

	    Auth::login($user);
	    return Redirect::to('/');
	}

	public function createCrowdWatsonUserAgent(){
		if(!User::find('crowdwatson'))
		{
			$softwareAgent = new User;
			$softwareAgent->_id = "crowdwatson";
			$softwareAgent->firstname = "Crowd";
			$softwareAgent->lastname = "Watson";
			$softwareAgent->email = "crowdwatson@gmail.com";
			$softwareAgent->save();
		}
	}
}

?>