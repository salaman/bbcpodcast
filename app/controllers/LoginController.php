<?php

class LoginController extends \BaseController {

    protected $layout = 'layouts.master';

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));
    }

    public function getIndex()
    {
        $this->layout->nav = "login";
        $this->layout->content = View::make('login');
    }

    public function postIndex()
    {
        $rules = array(
            'username' => 'required',
            'password' => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);

        $response = null;

        if ($validator->fails()) {
            $response = Redirect::to('login')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        }
        else {
            $userdata = array(
                'username' 	=> Input::get('username'),
                'password' 	=> Input::get('password')
            );

            if (!Auth::attempt($userdata)) {
                $response = Redirect::to('login')
                    ->withErrors(array('Invalid username or password.'))
                    ->withInput(Input::except('password'));
            }
            else {
                $response = Redirect::intended('programmes');
            }
        }

        return $response;
    }

    public function getLogout()
    {
        Auth::logout();
        return Redirect::to('login');
    }

}