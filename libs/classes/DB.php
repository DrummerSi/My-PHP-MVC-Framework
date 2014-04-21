<?php

class DB {
    
    private static $instance = null;
    private $pdo; //Hold instance to PDO
    private $error = false; //Error in current query
    
    private $results;   //Holds results of current query
    private $count;     //Holds number of records of current resultset
    private $recordId;  //Holds the last inserted/ updated record Id
    
    private function __construct(){
        try {
            //Create a new DB connection
            $this->pdo = new DebugBar\DataCollector\PDO\TraceablePDO(
                    new PDO('mysql:host='.Config::get('database/host').';dbname='.Config::get('database/db'), 
                    Config::get('database/username'), 
                    Config::get('database/password') 
                    //array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
                )
            );
            
        } catch (Exception $ex) {
            die("Database connection error: " . $ex->getMessage());
        }
    }
    
    public static function getInstance() {
        if(!isset(self::$instance)){
            self::$instance = new DB();
        }
        return self::$instance;
    }
    
    
    //insert, update, get functions
    
    /**
     * Inserts data to a specified database $table
     * @param string $table
     * @param array $fields
     * @return int (recordID, if successful) or false
     */
    public function insert($table, $fields = array()){
        $keys   = implode('`,`', array_keys($fields));
        $values = '';
        foreach(array_values($fields) as $value){
            $values .= ($values=='' ? '' :', ') . $this->escape($value);
        }
        //$values = left($values, strlen($values) - 2);        
        
        $sql = "INSERT INTO {$table} (`{$keys}`) VALUES ({$values});";

        if(!$this->query($sql)->error() ){
            //Success: Return id of item
            return $this->pdo->lastInsertId();
        }
        //Failed: Return FALSE!
        return false;
    }
    
    /**
     * Updates a field in the database.
     * 
     * $condition1 can be full WHERE string or $id_identifier
     * $condition2 is optional id value for $id_identifier
     * 
     * @param string $table
     * @param array $fields
     * @param string $condition1
     * @param string $condition2
     * @return boolean
     */
    public function update($table, $fields = array(), $condition1, $condition2 = null){        
        $values = '';
        
        //if condition2 is filled in, then create a WHERE statement
        if($condition2 != null){
            $condition1 = " WHERE {$condition1} = " . $this->escape($condition2);
        }
        
        foreach($fields as $field=>$value){
            $values .= ($values=='' ? '' :', ') . "{$field} = " . $this->escape($value);
        }
        
        $sql = "UPDATE {$table} SET {$values} {$condition1}";
        if(!$this->query($sql)->error()){
            return $this;
        }
        return false;
        
    }
    
    
    /**
     * Runs a SELECT statement on the database
     * @param string $table
     * @param array $fields
     * @param string $condition1
     * @param string $condition2
     * @return \DB|boolean
     */
    public function select($table, $fields, $condition1, $condition2 = null){  
        
        //if condition2 is filled in, then create a WHERE statement
        if($condition2 != null){
            $condition1 = " WHERE {$condition1} = " . $this->escape($condition2);
        }
        
        $sql = "SELECT {$fields} FROM ${table} {$condition1}";
        if(!$this->query($sql)->error()){
            return $this;
        }
        return false;
    }
    
    public function selectPaged($table, $fields, $condition, $limit, $offset){
        
        $sql = "SELECT SQL_CALC_FOUND_ROWS {$fields} FROM {$table} {$condition} LIMIT {$limit} OFFSET {$offset}";
        
        if(!$this->query($sql, true)->error()){
            return $this;
        }
        return false;
        
    }
    
    /**
     * Delete s field from database
     * 
     * $condition1 can be full WHERE string or $id_identifier
     * $condition2 is optional id value for $id_identifier
     * 
     * @param string $table
     * @param string $condition1
     * @param string $condition2
     * @return boolean
     */
    public function delete($table, $condition1, $condition2 = null){
        
        //if condition2 is filled in, then create a WHERE statement
        if($condition2 != null){
            $condition1 = " WHERE {$condition1} = " . $this->escape($condition2);
        }
        
        $sql = "DELETE FROM {$table} {$condition1}";
        if(!$this->query($sql)->error()){
            return $this;
        }
        return false;
        
    }
    
    /**
     * Runs a raw SQL statment on the database
     * @param string $sql
     * @return \DB|boolean
     */
    public function raw($sql){
        if(!$this->query($sql)->error()){
            return $this;
        }
        return false;
    }

    /**
     * Runs query in the database
     * @param string $sql
     * @return \DB
     */
    private function query($sql, $paged = false){
        $this->error = false;
        $this->count = 0;
        $this->results = null;
        
        if( $sqlQuery = $this->pdo->query($sql) ){
            $this->results = $sqlQuery->fetchAll(PDO::FETCH_OBJ);            
            $this->recordId = $this->pdo->lastInsertId();
            if($paged == false){
                //Standard query count
                $this->count = $sqlQuery->rowcount();
            } else {
                //If we're in pagination mode, return FOUND_ROWS() as number of records
                $this->count = $this->pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
            }
        } else {
            $this->error = true;
            if( ENVIRONMENT == "development" ){
                print_r($this->pdo->errorInfo()); //Print errors in debug mode
                die();
            }
        }
        return $this;
    }
    
    
    public function test(){
        return "lol";
    }
    
    public function escape($value){
        return $this->pdo->quote($value);
    }
    
    public function error(){
        return $this->error;
    }
    
    public function results(){
        return $this->results;
    }
    
    public function count(){
        return $this->count;
    }
    
    public function recordId(){
        return $this->recordId;
    }
    
    public function pdo(){
        return $this->pdo;
    }
    
    
    
}