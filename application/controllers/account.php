<?php

class Account extends Controller {
    
    private $auth;
    
    //Auto-load the auth model
    public function __construct(){
        parent::__construct();
        //$this->auth = $this->loadModel("AuthModel");
        $this->auth = new AuthModel();
    }
    
    public function index() {
        $this->login();
    }
    
    public function login(){
        
        if(isPostBack() ){
            if($user = $this->auth->attemptLogin(Input::get("email"), Input::get("password")) ){
                Session::addFlash("You have been logged in");
                Redirect::to("/home/auth");
            } else {
                Session::addFlash("username or password incorrect", "danger");
            }
        }
        
        $this->render("account/login");
    }
    
    public function logout(){
        $this->auth->logout();
        Redirect::to("/");
    }
  
    

    
}