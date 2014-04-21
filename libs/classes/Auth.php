<?php

class Auth {
    
    public static function secure($allowedRoles = null){
        
        if(!Session::exists('user_logged_in')){
            //User is NOT logged in.. Reject
            Redirect::to('/account/login');
        }
        
        //if no roles are provided, exit
        if($allowedRoles == null)
            return;
        
        //Roles may be passed over. .Could be string or array
        if(is_array($allowedRoles)){
            $roles = $allowedRoles;
        } else {
            $roles[] = $allowedRoles;
        }
        
        if( !array_intersect($roles, Auth::userDetails()['roles']) ){
            Redirect::to('/account/login');
        }
        
    }
    
    public static function generateHash($password){
        if(defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH){
            $salt = '$2y$11$' . substr(md5(uniqid(rand(), true)), 0, 22);
            return crypt($password, $salt);
        }
    }
    
    public static function userDetails(){
        return Session::get('user_details');
    }
 
    
}