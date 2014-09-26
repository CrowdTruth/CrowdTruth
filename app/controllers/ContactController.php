<?php

class ContactController extends BaseController {

//Server Contact view:: we will create view in next step
 public function contact(){

            return View::make('contact');
        }

        //Contact Form
        public function contactForm(){

            //Get all the data and store it inside Store Variable
            $data = Input::all();

            //Validation rules
            $rules = array (
                'name' => 'required|alpha',
                'email' => 'required|email',
                'message' => 'required|min:10'
            );

            //Validate data
            $validator  = Validator::make ($data, $rules);

            //If everything is correct than run passes.
            if ($validator -> passes()){

                //Send email using Laravel send function
                Mail::send('emails.contact', $data, function($message) use ($data)
                {
				$message->from($data['email'] , $data['name']);                 
				$message->to('crowdwatson@gmail.com', 'my name')->subject('CrowdTruth.org contact');

                });

				Session::flash('flashSuccess', 'Your e-mail has been sent successfully. We will contact you soon.');
                return Redirect::to('/home'); 
            }else{
				
				//return contact form with errors
				Session::flash('flashNotice', 'Please fill in all the fields');
                return Redirect::to('/contact');
            }
        }
}