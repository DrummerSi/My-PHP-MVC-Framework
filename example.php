<?php require_once 'libs/classes/FileBundler.php'; 
define("ROOT", dirname(__FILE__));
define("DS", DIRECTORY_SEPARATOR);
require_once 'libs/classes/jsmin.php';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JavaScript (and CSS) Bundler.  Example page.</title>
</head>

<body>
	<h1>Bundler</h1>
	<?php
		// create bundler for JavaScript files
		$jsBundler = new FileBundler(array(
			"type"=>"js",
                        "forceBundle" => "true"
                        //"enableBundles" => "false"
			//"debugMode"=>true,
			//"approot"=>"/",
			//"sourceDir"=>"/assets/js/",
			//"bundleDir"=>"/assets/bundles/"
		)); 

		// add single file to bundle
		$jsBundler->addFile("/assetss/js/bootstrap.js");

		// add multiple files to bundle
		$jsFiles = array("/assets/js/forms.js", "/assets/js/jquery.validate.min.js");
		$jsBundler->addFiles($jsFiles);

		// create new (or reuse if existing) bundle
		echo($jsBundler->render());
                
                
                
                $cssBundler = new FileBundler(array(
                    "type" => "css",
                    "forceBundle" => "true"
                ));
                
                $cssBundler->addFiles(array("/assets/css/bootstrap.css", "/assets/css/app.css"));
                echo($cssBundler->render());
                
	?>
	
</body>
</html>

