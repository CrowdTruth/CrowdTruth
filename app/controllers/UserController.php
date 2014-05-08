<?php

class UserController extends BaseController {

	protected $invitationCode = '$2y$10$3CDexLP1GQ.HMmU8YG0eHOBUJclK.HGXzt56fCQ/D2GSMlUqM8OOe';
	protected $demoInvitationCode = '$2y$10$gHVt4pkDOMkPuE1YSSdtv.DIm6MuHIB2Xv6VYsCYx6Pkuop7weT96';
	// Hashed because it might end up on github :-)

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
		if (Hash::check(Input::get('invitation'), $this->demoInvitationCode)) {
			$role = 'demo';
		// Check if normal account	
		} elseif (!Hash::check(Input::get('invitation'), $this->invitationCode)){
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