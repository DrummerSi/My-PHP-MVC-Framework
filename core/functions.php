<?php

//Returns a string safe for rendering on a web page
function encode($string){
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

//Returns true if the server request method = POST
function isPostBack(){
    return ($_SERVER['REQUEST_METHOD'] == 'POST');
}

/**
 * Returns lefthand x characters in string
 * @param string $string
 * @param int $count
 * @return string 
 */
function left($string, $count){
    return substr($string, 0, $count);
}

/**
 * Returns righthand x characters in string
 * @param string $string
 * @param int $count
 * @return string
 */
function right($string, $count){
    return substr($string, (strlen($string) - $count), strlen($string));
}

