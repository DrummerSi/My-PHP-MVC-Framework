<?php

class Hello extends Controller {
    
    public function index($one, $two, $three = null){
        
        $data = array(
            "message" => "index method from <strong>Hello</strong> controller",
            "one" => "one: $one",
            "two" => "two: {$two}",
            "three" => "three: {$three}"
        );
        $this->render('hello/index', $data);
    }
    
}