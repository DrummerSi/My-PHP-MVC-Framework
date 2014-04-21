<?php
//Define application constants

require_once('config.php');

//load application classes
require_once '/libs/application.php';
require_once '/libs/controller.php';
require_once '/libs/model.php';

//Load the composor autoload system
require 'vendor/autoload.php';

//load additional functions as required
require_once '/core/functions.php';

//Auto load other classes when required
spl_autoload_register(function($class){    
    
    /*if($class="WideImage"){
        echo(ROOT.DS.'libs'.DS.$class.DS.$class.'.php');
        die();
    };*/
    
    //Try and load a model first -- As these have the 'Model' suffix - /application/models/
    if( file_exists(ROOT.DS.'application'.DS.'models'.DS.$class.'.php') ){
        require_once ROOT.DS.'application'.DS.'models'.DS.$class.'.php';
    
    //Attempt to load framework classes - /libs/classes/
    } elseif( file_exists(ROOT.DS.'libs'.DS.'classes'.DS.$class.'.php') ){
        require_once ROOT.DS.'libs'.DS.'classes'.DS.$class.'.php';
        
    //Else, attempt to load custom classes - /applications/classes/
    } elseif( file_exists(ROOT.DS.'application'.DS.'classes'.DS.$class.'.php') ){
        require_once ROOT.DS.'application'.DS.'classes'.DS.$class.'.php';
    
    //Else, attempt to load 3rd party frameworks classes  -- /libs/{classname}/
    } elseif( file_exists(ROOT.DS.'libs'.DS.$class.DS.$class.'.php') ){
        require_once ROOT.DS.'libs'.DS.$class.DS.$class.'.php';
    }
});


/**
 * Function to display errors on production settings
 */
function productionError($errno, $errstr, $errfile, $errline){
    Redirect::to(500);
}

//Setup error logging/ reporting
switch (ENVIRONMENT) {
    case 'development':
        //In dev mode, do NOT show errors; log them instead
        error_reporting(E_ALL | E_STRICT);    
        
        //Setup developer errors
        $whoops = new Whoops\Run();
        $errorPage = new Whoops\Handler\PrettyPageHandler();
        $whoops->pushHandler($errorPage);
        $whoops->register();
        
        break;
    case 'production':
        //Show ALL errors in production mode
        error_reporting(E_ALL);    
        set_error_handler("productionError"); //Show custom error for production use
        break;
    default:
        die("Application environment not set correctly.");
        break;    
}

//start the application
$app = new Application();

