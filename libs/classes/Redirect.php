<?php

class Redirect {
    
    public static function to($location = null){
        if($location){
            
            if(is_numeric($location)){
                
                //load the twig view engine
                $twig_loader = new Twig_Loader_Filesystem(Config::get("views/path"));
                $twig = new Twig_Environment($twig_loader);
                
                switch($location){
                    case 401:
                        header('HTTP/1.0 401 Unauthorized');
                        $view = '_errors/401';
                        $title = 'Unauthorised';
                        break;
                    case 404:
                        header('HTTP/1.0 404 Not Found');
                        $view = '_errors/404';
                        $title = 'Page not found';
                        break;
                    case 500:
                        header('HTTP/1.0 500 Internal Server Error');
                        $view = '_errors/500';
                        $title = 'Error';
                        break;
                }
                
                //render a view and pass data to be rendered
                echo $twig->render($view . Config::get("views/file_type"), array('page_title' => $title));
                die();
                
            } else {
                header("location: {$location}");
                exit();
            }
            
        }
    }
    
}