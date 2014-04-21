<?php
use DebugBar\StandardDebugBar;

/**
 * Controller baseclass for all application classes
 */
class Controller {
    
    private $dbo; //Database object
    private $model; //Model object 
    
    protected $debugbar, $debugbarRenderer;
    
    
    /**
     * Automatically loaded with every class
     */
    public function __construct(){
        $this->dbo = DB::getInstance();
        
        //Load the debug bar (via composer)
        $this->debugbar = new StandardDebugBar();
        $this->debugbarRenderer = $this->debugbar->getJavascriptRenderer();
        $this->debugbar->addCollector(new DebugBar\DataCollector\PDO\PDOCollector($this->dbo->pdo()));
        
        //For testing
        $this->debugbar['messages']->info('Parent controller loaded');
        
    }
    
    public function render($view, $data_array = array()){
        
        //load the twig view engine
        $twig_loader = new Twig_Loader_Filesystem(Config::get("views/path"));
        $twig = new Twig_Environment($twig_loader);
        
        //Custom twig function & filters ----------------------------
        //Display flash messages
        $twig->addFunction(
            new Twig_SimpleFunction('flashMessages', function(){ 
                return Session::flashMessages();                 
            })
        );
            
        //Render bundles of css and js files
        $twig->addFunction(
            new Twig_SimpleFunction('renderBundle', function($type, $name){
                $bundle = new FileBundler(array(
                    'type'  => $type,
                ));
                
                $files = Config::getBundle($type, $name);     
                if($files){
                    $bundle->addFiles($files);
                    return $bundle->render();
                }
                
            })
        );            
        
        //Return true if a user is logged in
        $twig->addFunction(
            new Twig_SimpleFunction('isLoggedIn', function(){ 
                return (Session::exists('user_logged_in')) ? true : false;               
            })
        );
            
        //Render debugbar parts
        $twig->addFunction(
            new Twig_SimpleFunction('debugbar', function($section){ 
                if(ENVIRONMENT=='development'){
                    return $this->debugbarRenderer->$section();  
                }
                return '';
            })
        ); 
        
        //render a view and pass data to be rendered
        echo $twig->render($view . Config::get("views/file_type"), $data_array);
    }
    
    public function loadModel($model_name){
        require '/application/models/' . strtolower($model_name). '.php';
        return new $model_name($this->dbo());
    }
    
    /**
     * Returns a reference to the database object
     * @return DB
     */
    public function dbo(){
        return $this->dbo;
    }
    
    public function model(){
        return $this->model;
    }
    
}