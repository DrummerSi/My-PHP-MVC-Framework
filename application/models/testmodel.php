<?php

class TestModel extends Model {

    function helloWorld(){
        return "string returned from helloWorld() function inside TestModel, which extends Model";
    }
    
    function getNames(){
        return $this->dbo->select('testTable', '*', 'LIMIT 10');
    }
    
    

}