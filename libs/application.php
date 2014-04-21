<?php
class Application {
    
    private $area = null;
    private $controller = 'home';    
    private $action = 'index';
    private $params = array();
    
    //Start the application
    //Analyze the URl elements and calls the correct controller & action
    public function __construct(){
        
        //Setup sessions
        Session::start();
        
        //Get controller, action & params from user
        $this->splitUrl();
        
        // check for the controller.. Does it exist?
        $controllerFile = './application/controllers/' . (($this->area) ? $this->area . '/' : '') . $this->controller . '.php';
        
        if(file_exists($controllerFile)){
            //if so, load the controller and create a new object
            require($controllerFile);
            $this->controller = new $this->controller();
            
            //Now check for an action
            if(method_exists($this->controller, $this->action)){
                call_user_func_array(array($this->controller, $this->action), $this->params);
            } else {
                //Fallback to the default index action
                //$this->controller->index();
                //die("Invalid URL (1)");
                Redirect::to(404);
            }
        } else {
            //Invalid URL.. Show 404?        
            //die("Invalid URL (2)");
            Redirect::to(404);
        }
        
    }
    
    //Get and split the url
    private function splitUrl(){
        if(isset($_GET['url'])){
            
            //Split the URL
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            
            //Load any areas from the config
            $areas = Config::get("routeAreas");
            
            if(isset($url[0])){
                if(array_search($url[0], $areas) !== false){
                    $this->area = $url[0];
                    array_splice($url, 0, 1);
                }            
            }
            
            //load the remaining parts
            $this->controller = (isset($url[0]) ? $url[0] : null);
            $this->action = (isset($url[1]) ? $url[1] : 'index'); //Default action to index method
            
            for($r=2; $r<count($url); $r++){
                $this->params[] = $url[$r];
            }            
            
            //For debugging only
            /*echo 'Area: ' . $this->area . '<br />';
            echo 'Controller: ' . $this->controller . '<br />';
            echo 'Action: ' . $this->action . '<br />';
            echo 'Params: ' . print_r($this->params) . '<br />';*/
            
            
        }
    }
    
}