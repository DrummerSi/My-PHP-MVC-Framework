<?php

class Input {
    
    /**
     * Input::get
     * Returns the value of $_POST & $_GET values, if they exist
     * 
     * @param string    $item   Name of the item value you want to return
     * @param string    $type   Type of item you wish to return : 'both', 'get', 'post'
     */
    public static function get($item, $type = "both"){
        if(isset($_POST[$item]) && ($type=="both" || $type="post")){
            return $_POST[$item];
        } else if(isset($_GET[$item]) && ($type=="both" || $type="get")){
            return $_GET[$item];
        }
        return '';
    }
    
    /**
     * Returns true if the form item exists (Similar to isset)
     * @param string    $item   Name of the item value you want to return
     * @param string    $type   Type of item you wish to return : 'both', 'get', 'post'
     * @return boolean 
     */
    public static function exists($item, $type = "both"){
        if(($type=="both" || $type="post")){
            return isset($_POST[$item]);
        } else if(($type=="both" || $type="get")){
            return isset($_GET[$item]);
        }
        return false;
    }
    
    /**
     * Returns true if the form item has a value (similar to !empty)
     * @param string    $item   Name of the item value you want to return
     * @param string    $type   Type of item you wish to return : 'both', 'get', 'post'
     * @return boolean 
     */
    public static function entered($item, $type = "both"){
        if(isset($_POST[$item]) && ($type=="both" || $type="post")){
            return !empty($_POST[$item]);
        } else if(isset($_GET[$item]) && ($type=="both" || $type="get")){
            return !empty($_GET[$item]);
        }
        return false;
    }
    
}
