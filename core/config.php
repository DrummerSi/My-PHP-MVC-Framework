<?php

/** ENVIRONMENT Should be: development, production
 * 'developement' shows all errors & debugBar
 * 'production' logs errors to log
 */
define("ENVIRONMENT", 'development');

//Setup the configuration
$GLOBALS['config'] = array(
    //Database to connect to
    'database' => array(
        'host' => '127.0.0.1',
        'db' => 'frameworkDb',
        'username' => 'root',
        'password' => '1982'
    ),
    //List of any routes areas
    'routeAreas' => array(
        'client', 'admin'
    ),
    //VIEWS configuration
    'views' => array (
        'path' => 'application/views/',
        'file_type' => '.twig'
    )    
);

//Bundles - Seperated from main config as I can see this getting big
$GLOBALS['bundles'] = array(
    'js' => array( // JS bundles
        'main' => array(
            '/assets/js/jquery-1.11.0.min.js',
            '/assets/js/jquery.validate.min.js',
            '/assets/js/bootstrap.min.js',
            '/assets/js/forms.js'
        )
    ),
    'css' => array( // CSS bundles
        'main' => array(
            '/assets/css/bootstrap.min.css',
            '/assets/css/bootstrap-theme.min.css',
            '/assets/css/app.css'
        )
    )
);