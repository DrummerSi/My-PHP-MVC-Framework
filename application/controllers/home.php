<?php

class Home extends Controller {
    
    public function index() {
        $this->render('home/index');
    }
    
    public function auth(){
        Auth::secure(array("Admin"));
        
        $data = array(
            'loginDetails' => var_export(Auth::userDetails(),true),
            'title'        => 'You are authenticated!'
        );
        
        $this->render('home/authed', $data);
    }
    
    /**
     * Test function to create a user
     */
    public function auth_createUser(){
        if($userId = $auth->createUser("blink_si@hotmail.com", "test")){
            echo("Created user: " . $userId);
        } else {
            echo("NOT created");
        }
    }
    
    public function config(){
        
        //Load configuration into view array
        $data = array(
            'host' => Config::get('database/host'),
            'db' => Config::get('database/db'),
            'username' => Config::get('database/username'),
            'password' => Config::get('database/password'),
            'db_config' => Config::get('database'),
            'db_array' => array(
                'host' => Config::get('database/host'),
                'db' => Config::get('database/db'),
                'username' => Config::get('database/username'),
                'password' => Config::get('database/password')
            )
        );
        
        $this->render('home/config', $data);
        
    }
    
    public function db(){
        
        $debugLog = "";
        
        $table = "testTable";
        $fields = array(
            'name' => "simon 'o tool",
            'number' => 5
        );
        
        //Insert into DB
        $debugLog .= ("Trying to insert into DB:<br />");        
        $id = $this->dbo()->insert($table, $fields);
        $debugLog .= ("--- Inserted record ID = {$id}<br />");
        
        //Now update the DB
        $debugLog .= ("<br/>Updating record in database<br />");
        $fields["name"] = "Simon Hopwood";
        $fields["number"] = 007;
        $result = $this->dbo()->update($table, $fields, 'id', $id);
        //$result = $this->dbo()->update($table, $fields, 'WHERE id = 5');
        if($result == true){
            $debugLog .= ("Update SUCCESSFUL!<br />");
        } else {
            $debugLog .= ("Update FAILED!<br />");
        }
        
        //Select from the database
        $debugLog .= ("<br/>Select from the database");
        $items = $this->dbo()->select($table, '*', 'LIMIT 5');
        $debugLog .=  '<br />Returned' . $items->count() . 'items<br /><ul>';
        foreach($items->results() as $item){
            $debugLog .= ("<li><b>" . $item->id . "</b> " . $item->name . "</li>");
        }
        $debugLog .= ("</ul>");
        
        //Now delete the record
        $debugLog .= ("<br />Trying to delete record<br />");
        $this->dbo()->delete($table, 'id', $id);   
        //$this->dbo()->delete($table, 'WHERE id = 8');
        if($result == true){
            $debugLog .= ("Delete SUCCESSFUL!<br />");
        } else {
            $debugLog .= ("Delete FAILED!<br />");
        }
        
        //Insert raw commands
        $debugLog .= ("<br />Try issing a raw INSERT command<br />");
        $result = $this->dbo()->raw("INSERT INTO {$table} (`name`, `number`) VALUES ('Simon', 9001);");
        if(!$result->error()){
            $debugLog .= ("Successfully ran query<br />");
            $debugLog .= ($result->count() . " records affected.<br />");
            $debugLog .= ("Inserted id: " . $result->recordId() . ".<br />");
        }
        $raw_insertId = $result->recordId();
        
        $debugLog .= ("<br />Try issing a raw SELECT command<br />");
        $result = $this->dbo()->raw("SELECT * FROM {$table} ORDER BY id DESC LIMIT 4;");
        if(!$result->error()){
            $debugLog .= ("Successfully ran query<br />");
            $debugLog .= ($result->count() . " records returned.<br /><ul>");
            foreach($result->results() as $item){
                $debugLog .= ("<li><b>{$item->id}</b> {$item->name} ({$item->number})</li>");
            }
            $debugLog .= ("</ul>");
        }
        
        $debugLog .= ("<br />Try issing a raw DELETE command<br />");
        $result = $this->dbo()->raw("DELETE FROM {$table} WHERE id = {$raw_insertId};");
        if(!$result->error()){
            $debugLog .= ("Successfully ran query<br />");
            $debugLog .= ($result->count() . " records deleted.<br />");
        }       
        
        
        //Send the logs over to the template
        $data = array(
            'debugLog' => $debugLog,
        );
        
        $this->render('home/db', $data);        
        
    }
    
    public function email(){
        
        $email = new Email();
        $email->to("Simon Hopwood <blink_si@hotmail.com>")
              ->from("blink_si@hotmail.com")
              ->subject("This is a test")
              ->body("This is the email body");
        //$email->loadBodyTemplate("templateName", $_POST);
        
        //$email = new Email()->
        
        //die();
        
        if($email->send() != true){
            //Email failed
            die( $email->status() );            
        } else {
            die("Email sent fine");
        }
    }
    
    public function image(){
        
        $data = array(
            'images' => array(
                array(
                    'name'  => 'Simple image, resized as a gif',
                    'image' => base64_encode(WideImage::load(ROOT.'/assets/img/testImage.jpg')
                                ->resize(400,300)->asString(".gif")),
                    'type'  => 'gif'
                ),
                array(
                    'name'  => 'Resize, rotate 20 & round the corners. Save as png',
                    'image' => base64_encode(WideImage::load(ROOT.'/assets/img/testImage.jpg')
                                ->resize(400,300)->rotate(20)->roundCorners(20)->asString(".png")),
                    'type'  => 'png'
                ),
                array(
                    'name'  => 'Crop center, convert to greyscale & save as jpg',
                    'image' => base64_encode(WideImage::load(ROOT.'/assets/img/testImage.jpg')
                                ->crop('center', 'center', 400, 300)->asGrayscale()->asString(".jpg")),
                    'type'  => 'jpg'
                )
            )            
        );
        
        $this->render('home/image', $data);   
        
    }
    
    public function models(){
        
        //Load a model 
        $testModel = $this->loadModel('TestModel');        
        $dbUsers = $testModel->getNames();
        
        
        //Run a function inside a model
        $data = array(
            'helloWorld'    => $testModel->helloWorld(),
            'db'            => $dbUsers->results(),
            'dbCount'       => $dbUsers->count()                
        );
        
        $this->render('home/model', $data);

    }
    
    public function pagination($page = 1, $dbPage = 1){
        
        //Fixed pagination
        $config = array(
            'url'           => "/home/pagination/{page}/" . $dbPage . "?hello=world",
            'current_page'  => $page,
            'per_page'      => 20,
            'total_items'   => 100
        );
        
        //DB Pagination
        $test = $this->dbo()->selectPaged("testTable", "*", "", 20, ($dbPage*20)-20);
        $config2 = array(
            'url'           => "/home/pagination/" . $page . "/{page}",
            'current_page'  => $dbPage,
            'per_page'      => 20,
            'total_items'   => $test->count()
        );
        
        $data = array(
            'pagination'    => Pagination::create($config),
            'paginationDb'  => Pagination::create($config2),
            'test'          => $test->results()
        );

        $this->render('home/pagination', $data);
    }
    
    public function validation(){
        
        $validate = new Validate();
        //$validate->useSessionFlash(false);
        $validate->clientSide(false);
        $validate->customClientSideErrors(true);
        $validate->rules(array(
            'requiredField' => array('required' => "true"),
            'emailField' => array(
                'required' => 'true',
                'email' => 'true'
            ),
            'numberField' => array(
                'required' => 'true',
                'number' => 'true',
                'minlength' => 2,
                'maxlength' => 4,
                'min' => 1,
                'max' => 20
            ),
            'remoteField' => array('remote' => 'http://localhost:81/home/remote_val'), // 'http://localhost:8000/ajax/valtest.php'
            'numberRangeField' => array('range' => '1,100'),
            'rangeLengthField' => array('rangelength' => '2,6'),
            'dateField' => array('date' => 'true'),
            'passwordField' => array('required' => 'true'),
            'passwordField2' => array('equalto' => 'passwordField'),
            'urlField' => array('required' => 'true', 'url' => 'true'),
            'selectField' => array('required' => 'true'),
            'agree' => array('required' => 'true'),
            'optionsRadios' => array('required' => 'true'),
            'textarea' => array()
        ));

        //Optionally set custom field names
        $validate->customNames(array(
            'requiredField' => 'First field',
            'passwordField' => 'Password',
            'passwordField2' => 'Another password'
        ));

        //Optionally set custom error messages
        $validate->customErrors(array(
            'emailField' => array(
                'required' => 'Fill the email in!',
                'email' => 'The email field isn\'t valid'
            ),
            'remoteField' => array(
                'remote' => 'You must enter \'simon\' to validate'
            )
        ));

        $validate->defaultValues(array(
            'numberField' => '3',
            'selectField' => 4,
            'optionsRadios' => 'option2',
            'textarea' => 'test<br />A new line'
        ));
        
        if(isPostBack()){

            if($validate->passed()){
                echo "Form validated: OK";
                die();
            } else {
                //output here if not using session flashes
                //echo $validate->formattedErrors();
            }

        }
        
        //Select list items
        $selectList[] = array('value' => '', 'name' => '- Please select -');
        $selectList[] = array('value' => '1', 'name' => 'one');
        $selectList[] = array('value' => '2', 'name' => 'two');
        $selectList[] = array('value' => '3', 'name' => 'three');
        $selectList[] = array('value' => '4', 'name' => 'four');
        $selectList[] = array('value' => '5', 'name' => 'five');
        
        
        $data = array(
            'form' => $validate->templateStrings(),
            'page_title' => 'Validation',
            'selectList' => $selectList
        );
        
        //print_r($data);
        
        $this->render('home/validation', $data);
    }
    
    public function redirect(){   
        
        if(isPostBack()){
            switch(Input::get('type')){
                case 'home':
                    Redirect::to('/');
                case '401':
                    Redirect::to(401);
                case '404':
                    Redirect::to(404);
                case '500':
                    Redirect::to(500);
            }
            
        } else {        
            $this->render('home/redirect');
        }
    }
    
    public function remote_val(){
        die("false");
    }
    
    
    public function session_flash(){
        
        if(isPostBack()){
            Session::addFlash("A flash message was added. Refresh to remove");
            Redirect::to('/');
        } else {
        
            Session::addFlash("Success by default");
            Session::addFlash("Success with title", "success", "Title here");
            Session::addFlash("Danger alert", "danger");
            Session::addFlash("Info alert", "info");
            Session::addFlash("Warning alert with html<br /><strong>bold</strong> <u>Underlined</u>", "warning");
            Session::addFlash("A blank 'class' will render an alert with no styling", "");

            $this->render('home/session_flash');
        }
        
    }
    
    

    
}