<?php

Class Filebundler {
    
    private $type; //Bundle type: js or css
    private $bundleDir;
    private $enableBundles;
    private $compress;
    private $version;
    private $name;
    private $forceBundle;
    
    private $files;
    
    
    public function __construct($props){
        $this->type         = isset($props['type'])          ? $props['type']           : 'js';
        $this->bundleDir    = isset($props['bundleDir'])     ? $props['bundleDir']      : '/assets/bundles';
        $this->enableBundles= isset($props['enableBundles']) ? $props['enableBundles']  : true;
        $this->compress     = isset($props['compress'])      ? $props['compress']       : true;
        $this->version      = isset($props['version'])       ? $props['version']        : 1;
        $this->name         = isset($props['name'])          ? $props['name']           : '';
        $this->forceBundle  = isset($props['forceBundle'])   ? $props['forceBundle']    : false;        
    }
    
    public function addFiles($files){
        foreach($files as $file){
            $this->addFile($file);
        }
    }
    
    public function addFile($file){
        $this->files[] = $file;
    }
    
    public function render(){
        
        $errorFlagged = false;
        $overwriteBundle = false;
        $tag = '';
                
        if($this->enableBundles === true){
            
            if($this->name!=''){
                $bundleName = $this->name . '.' . $this->type;
            } else {
                $bundleName = $this->generateBundleName();
            }
            
            $bundleSysPath = realpath(ROOT . $this->bundleDir) . DS . $bundleName;
            
            $bundleDate = $this->getBundleLastUpdate($bundleSysPath);
            $filesDate  = $this->getFilesLastUpdate();  
            
            if( $filesDate > $bundleDate){
                $overwriteBundle = true;
                $version = md5($filesDate);
            } else {
                $version = md5($bundleDate);
            }
            
            if(!file_exists($bundleSysPath) or $overwriteBundle == true or $this->forceBundle == true ){
                
                $code = '';
                foreach($this->files as $file){
                    $filePath = realpath(ROOT . DS . $file);
                    $code .= (file_exists($filePath)) ? file_get_contents($filePath) : '';
                }
                
                if($this->compress == true){
                    switch($this->type){
                        case 'js':
                            $code = $this->compressJS($code);
                            break;
                        case 'css':
                            $code = $this->compressCSS($code);
                            break;
                    }
                }
                
                try {
                    file_put_contents($bundleSysPath, $code);                
                } catch(Exception $e) {
                    //Can't write to file
                    $errorFlagged = true;                  
                }
                
            }
            
            if($errorFlagged == true){
                $tag = $this->renderClassicScripts();
            } else {
                $bundleLink = $this->bundleDir . '/' . $bundleName . '?v=' . $version;
                switch($this->type){
                    case 'js':
                        $tag = '<script type="text/javascript" src="' . $bundleLink . '"></script>';
                        break;
                    case 'css':
                        $tag = '<link type="text/css" rel="stylesheet" href="' . $bundleLink . '" />';
                        break;
                }
            }

            
        } else {
            //Bundles not enabled
            echo("Bundles not enabled");
            $tag = $this->renderClassicScripts();
        }
        
        return $tag;        
        //die($tag);
        
    }
    
    private function generateBundleName(){
        $sortedArray = sort($this->files);
        return md5(implode('.', $this->files)) . '.' . $this->type;        
    }
    
    private function getBundleLastUpdate($file){
        if(file_exists($file)){
            return filemtime($file);
        }
        return 0;
    }
    
    private function getFilesLastUpdate(){
        
        $latestDate = '';        
        foreach($this->files as $file){
            $filePath = realpath(ROOT . DS . $file);
            if(file_exists($filePath)){
                if(filemtime($filePath) > $latestDate) {
                    $latestDate = filemtime($filePath);
                }
            }
        }
        return $latestDate;
    }
    
    private function renderClassicScripts(){
        $tag = '';
        foreach($this->files as $file){
            switch($this->type){
                case 'js':
                    $tag .= '<script type="text/javascript" src="' . $file . '?v=' . $this->version . '"></script>';
                    break;
                case 'css':
                    $tag .= '<link type="text/css" rel="stylesheet" href="' . $file . '?v=' . $this->version . '"/>';
                    break;
            }
        }
        return $tag;
    }
    
    private function compressJS($code){
        try {
            return JSMin::minify($code);
        } catch(Exception $e){
            return $code;
        }
    }
    
    private function compressCSS($code){
        $code = preg_replace('/[\r\n\t\s]+/s', ' ', $code);           // new lines, multiple spaces/tabs/newlines
        $code = preg_replace('#/\*.*?\*/#', '', $code);               // comments
        $code = preg_replace('/[\s]*([\{\},;:])[\s]*/', '\1', $code); // spaces before and after marks
        $code = preg_replace('/^\s+/', '', $code);                    // spaces on the beginning
        return $code;
    }
    
}