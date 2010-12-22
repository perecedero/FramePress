<?php

/*
	WordPress Framework, HTML class v1.0
	developer: Perecedero (Ivan Lansky) perecedero@gmail.com
*/

class w2pf_html_v1 {

	var $path = null;


	function __construct($path){
		$this->path = &$path;
	}

	function img ($file, $args=array())
	{
		$opt ='';
		foreach($args as $key =>$value)
		{
			$opt .= ' '.$key.'=\''.$value.'\'';
		}
		$url=$this->path->Dir['IMG_URL'].'/'.$file;
		return "<img src='{$url}' {$opt}/>";
	}

	function link ($title, $url=array(), $args=array())
	{
		$opt ='';
		foreach($args as $key =>$value)
		{
			$opt .= ' '.$key.'=\''.$value.'\'';
		}

		$href = $this->path->router($url);

		return "<a href='{$href}' {$opt}/>{$title}</a>";
	}

}

?>
