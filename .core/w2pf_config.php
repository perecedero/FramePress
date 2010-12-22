<?php

/*
	WordPress Framework, HTML class v1.0
	developer: Perecedero (Ivan Lansky) perecedero@gmail.com
*/


class w2pf_config_v1 {

	var $vars = null;
	var $path= null;

	function __construct($path){
		$this->path = &$path;
		require_once($this->path->Dir['CONFIG'] . $this->path->DS . 'config.php');
		$this->vars = $config;
	}

	function read ($key)
	{
		if(isset($this->vars[$key])){
			return $this->vars[$key];
		}
		return null;
	}
}

?>
