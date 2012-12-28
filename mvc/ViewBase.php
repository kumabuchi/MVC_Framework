<?php

class ViewBase
{

	protected $variables;

	public function __construct(){
		$variables = array();
	}

	public function getVariable($key){
		if( isset($this->variables[$key]) ){
			return $this->variables[$key];
		}
		return null;
	}

	public function setVariable($key, $val){
		$this->variables[$key] = $val;
	}

}
