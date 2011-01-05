<?php

/*
	WordPress Framework, HTML class v1.0
	developer: Perecedero (Ivan Lansky) perecedero@gmail.com
*/


class w2pf_config_test {

	var $vars = null;
	var $path= null;

	function __construct($path){
		global $W2PF_CONFIG;
		$this->path = &$path;
		$this->vars =$W2PF_CONFIG;
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
