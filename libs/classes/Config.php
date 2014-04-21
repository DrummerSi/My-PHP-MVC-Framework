<?php

class Config {
    
    /**
     * Reads value from config array, based on input
     * @param string $path
     * @return boolean or string
     */
    public static function get($path = null){
        if($path){
            $config = $GLOBALS['config'];
            $path = explode("/", $path);
            
            foreach($path as $bit){
                if(isset($config[$bit])){
                    $config = $config[$bit];
                }
            }            
            return $config;
        }
        return false;
    } 
    
    /**
     * Returns an array of files for bundle $name of $type
     * @param string $type
     * @param string $name
     */
    public static function getBundle($type, $name){
        $bundles = $GLOBALS['bundles'];
        
        try {
            $bundle = $bundles[$type][$name];
        } catch(Exception $e){
            $bundle = '';
        }
        
        return $bundle;

    }
    
}