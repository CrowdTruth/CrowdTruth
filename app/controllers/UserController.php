<?php

class UserController extends BaseController {

	protected $invitationCode = '$2y$10$3CDexLP1GQ.HMmU8YG0eHOBUJclK.HGXzt56fCQ/D2GSMlUqM8OOe';
	// Hashed because it might end up on github :-)

	public function __construct() {
	   $this->beforeFilter('csrf', array('on'=>'post'));
	}

	protected function createSchema(){
		Schema::create('users', function($collection)
		{
		    $collection->unique('username');
		    $collection->unique('email');
		});
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

	    if($user = User::where('username', '=', $userdata['username'])->orWhere('email', '=', $userdata['email'])->first()){
	    	if(Auth::attempt(array('email' => $user['email'], 'password' => $userdata['password']))){
	    		return Redirect::intended('/');
	    	} else {
	    		Session::flash('flashError', 'Invalid credentials');
	    		return Redirect::back();	    		
	    	}
	    } else {
	    	Session::flash('flashError', 'Invalid credentials');
	    	return Redirect::back();
	    }
	}

	public function postRegister(){
		if (!Hash::check(Input::get('invitation'), $this->invitationCode)){
			Session::flash('flashError', 'Wrong invite code : )');
			return Redirect::back();
		}

	    $userdata = array(
	        'firstname' => Input::get('firstname'),
	        'lastname' => Input::get('lastname'),
	        'username' => Input::get('username'),
	        'email' => Input::get('email'),
	        'password' => Input::get('password'),
	        'confirm_password' => Input::get('confirm_password'),
	    );

        $rules = array(
            'firstname' => 'required|min:3',
            'lastname' => 'required|min:1',
            'username' => 'required|min:3|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5',
            'confirm_password' => 'required|same:password'
        );

	    $validation = Validator::make($userdata, $rules);

	    if($validation->fails()){
	    	Session::flash('flashError', $validation->messages()->toJson());
	        return Redirect::back();
	    }

	    unset($userdata['confirm_password']);
	    $userdata['password'] = Hash::make($userdata['password']);

		if(!Schema::hasCollection('users'))
			$this->createSchema();

	    $user = new User($userdata);
	    $user->save();
	    Auth::login($user);

	    return Redirect::to('/');
	}
}

?>