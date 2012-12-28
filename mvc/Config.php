<?php

class Config
{
	protected $conf;
	protected $route;

	public function __construct(){
		$configFile = dirname(__FILE__)."/../config/mvc.conf";
		if( file_exists($configFile) ){
			$this->conf  = parse_ini_file($configFile, true);
		}else{
			header("HTTP/1.0 404 Not Found");
			exit;
		}
		$routeFile = dirname(__FILE__)."/../config/routes.conf";
		if( file_exists($routeFile) ){
			$this->route = parse_ini_file($routeFile, true);
		}
	}
	
	public function getConfig($section = null, $key = null){
		if( $section == null ){
			return $this->conf;
		}
		if( $key == null ){
			return isset($this->conf[$section]) ? $this->conf[$section] : null;
		}
		return isset($this->conf[$section][$key]) ? $this->conf[$section][$key] : null;
	}

	public function getRoutes($section = "ROUTE", $key = null){
		if( $section == null ){
			return $this->route;
		}
		if( $key == null ){
			return isset($this->route[$section]) ? $this->route[$section] : null;
		}
		return isset($this->route[$section][$key]) ? $this->route[$section][$key] : null;
	}
}
