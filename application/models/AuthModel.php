<?php

class AuthModel extends Model {
    
    protected $dbo;
    
    public function __construct(){
        $this->dbo = DB::getInstance();
    }
    
    /**
     * Creates a user in the database.
     * Returns uderId if successful, else FALSE
     * @param string $email
     * @param string $password
     * @return mixed false or userId
     */
    public function createUser($email, $password){
        
        //Check if this user exists
        $user = $this->dbo->select('user', '*', "WHERE `email` = " . $this->dbo->escape($email) );
        
        //If user doesn't already exist, then create the user
        if($user->count() == 0){            
            $fields = [
                'email' => $email,
                'password' => Auth::generateHash($password) //bcrypt algorythm
            ];
            $userId = $this->dbo->insert("user", $fields);
            return $userId;
        }
        
        return false;
    }    
    
    public function attemptLogin($email, $password){
        
        //Check if this user exists
        $user = $this->dbo->select('user', '*', "WHERE `email` = " . $this->dbo->escape($email) );
        
        //If user exists, check password
        if($user->count() == 1){
            if($this->verify($password, $user->results()[0]->password)){
                //Yeah.. We've logged in
                Session::put('user_logged_in', true);
                Session::put('user_details', $this->returnedUserData($user->results()[0]));
                return true;                
            } else {
                //Password incorrect
                return false;
            }           
        } else {
            //User doesn't exist
            return false;
        }
        
    }
    
    /**
     * Logs a user out of the system
     */
    public function logout(){
        Session::delete('user_logged_in');
        Session::delete('user_details');
    }
    
    /**
     * Verifies that the entered password, when hashed, equals the hased password value
     * @param string $password
     * @param string $hashedPassword
     * @return boolean 
     */
    private function verify($password, $hashedPassword){
        return crypt($password, $hashedPassword) == $hashedPassword;
    }
    
    
    /**
     * Simply returned a sub-set of user data to be placed inside a session
     */
    private function returnedUserData($user){
        //Grab a sub-set of the user data to be made available (I.E. Don't send back the hashed password)
        $data = array(
            'id'        => $user->id,
            'email'     => $user->email
        );
        
        //Query the db for any roles the user belongs to, and add that to the data array       
        $roles_raw = $this->dbo->raw("SELECT name FROM role INNER JOIN user_role ON (role.id = user_role.roleId) WHERE user_role.userId = " . $this->dbo->escape($user->id))->results();    //select('role_user', '*', "WHERE `email` = " . $this->dbo->escape($email) );
        foreach($roles_raw as $role){
            $roles[] = $role->name;
        };
        
        $data['roles'] = $roles;
        
        //Return the data
        return $data;
    }
    

}