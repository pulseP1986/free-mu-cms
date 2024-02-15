<?php
    define('MIN_PHP_VERS', '7.2.0');
	
	use Tracy\Debugger;

    Debugger::enable((ENVIRONMENT == 'development') ? Debugger::DEVELOPMENT : Debugger::PRODUCTION, APP_PATH . DS . 'logs' . DS . 'Tracy');
	
	// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
	function initDmN(){
	   $security = load_class('security');
		if($security->isIPBanned()){
			header("HTTP/1.1 403 Forbidden");
			exit('Your ip is black listed!');
		}

		set_exception_handler(function($e){
			print_exception($e);
		});

		if(!version_compare(MIN_PHP_VERS, PHP_VERSION, '<=')){
			throw new Exception('You must be using PHP ' . MIN_PHP_VERS . ' or better. You are currently using: ' . PHP_VERSION);
		}

		$request = load_class('request');
		require(SYSTEM_PATH . DS . 'controller.class.php');
		$router = load_class('router');
		$router->route($request);
	}
   
	initDmN();
