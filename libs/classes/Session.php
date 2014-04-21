<?php

class Session {
    
    /**
     * Start a session, if one is not already started
     */
    public static function start(){
        if(session_id() == ''){
            session_start();
        }
    }
    
    /**
     * Sets a $named session to $value. Returns true if successful.
     * @param string $name
     * @param string $value
     * @return boolean
     */
    public static function put($name, $value){
        return $_SESSION[$name] = $value;
    }
    
    /**
     * Returns the $named session variable
     * @param string $name
     * @return string
     */
    public static function get($name){
        return (self::exists($name)) ? $_SESSION[$name] : null;
    }
    
    /**
     * Returns true if $named session exists
     * @param string $name
     * @return boolean
     */
    public static function exists($name){
        return (isset($_SESSION[$name])) ? true : false;
    }
    
    /**
     * Deletes the $named session, if it exists
     * @param string $name
     */
    public static function delete($name){
        if(self::exists($name)){
            unset($_SESSION[$name]);
            return true;
        }
        return false;
    }
    
    /**
     * Adds a flash messages to the messages array
     * @param string $message
     * @param string $class
     */
    public static function addFlash($message, $class='success', $title = ''){   
        
        $existingMessages = (self::exists('flashMessage')) ? self::get('flashMessage') : '';
        $existingMessages[] = array("message" => $message, "class" => $class, "title" => $title);  
        
        self::put('flashMessage', $existingMessages);        
    }
    
    public static function flashMessages(){
        
        //The string to return with all the flash messages in it
        $flashMessages = '';
        
        if(self::exists('flashMessage')){
            $existingMessages = self::get('flashMessage');
            foreach($existingMessages as $flashMessage){
                $flashMessages .= '<div class="alert alert-' . $flashMessage['class'] .'">';
                $flashMessages .= ($flashMessage['title'] != '') ? '<strong>' . $flashMessage['title'] . '</strong><br />' : '';
                $flashMessages .= $flashMessage['message'];
                $flashMessages .= '</div>';
            }    
        }
        
        self::delete('flashMessage');  
        return $flashMessages;
    }
    
}