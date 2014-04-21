<?php

/*
 * Basic Templating features 
 */
class Template {
    
    private $templateVars;
    
    //Set/ get the title of the page
    public function title($title = null){
        if($title){
            $this->title = $title;
        } else {
            return $this->title;
        }
    }
    
    //Magic method to set template variables
    public function __set($name, $value){
        $this->templateVars[$name] = $value;
    }
    
    //Magic method to get template variables
    public function __get($name){
        if(array_key_exists($name, $this->templateVars)){
            return $this->templateVars[$name];
        } else {
            return '';
        }
    }
    
    //Setup some defaults on creation of the class
    function __construct(){
        $this->page = "admin";
        $this->head = '';
        $this->title = '';
        $this->content = '';
        $this->styles = '';
        $this->scripts = '';
    }
    
    //Renders the template to the page
    public function render(){
        include $this->templateName();     
    }
    
    //Returns the full template name
    public function templateName(){
        return "/templates/main/" . $this->page . ".php";
    }
    
}