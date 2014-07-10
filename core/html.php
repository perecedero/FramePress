<?php

//define core class
if (!class_exists('FramePressHtml_001')) {
class FramePressHtml_001
{

	/**
	 * Generate wellformed css LINK tag.
	 *
	 * @param string $file Name of the file to load
	 * @param array $args Options for the tag
	 * @return String
	*/
	public function css ($file, $args=array())
	{
		$url = $this->path['css_url'] . '/' . $file;
		return "<link href='{$url}' rel='stylesheet' type='text/css'>";
	}

	/**
	 * Generate wellformed js SCRIPT tag.
	 *
	 * @param string $file Name of the file to load
	 * @param array $args Options for the tag
	 * @return String
	*/
	public function js ($file, $args=array())
	{
		$url = $this->path['js_url'] . '/' . $file;
		return "<script type='text/javascript' language='javascript' src='{$url}'></script>";
	}

	/**
	 * Generate wellformed A tag.
	 *
	 * @param string $title Link Anchor
	 * @param mixed $url Href for the link
	 * @param array $args Options for the tag
	 * @return String
	*/
	public function link ($title, $url=array(), $args=array())
	{
		$opt ='';
		foreach($args as $key =>$value) {
			$opt .= ' '.$key.'=\''.$value.'\'';
		}

		$href = $this->router($url);

		return "<a href='{$href}' {$opt} >{$title}</a>";
	}

	/**
	 * Generate wellformed IMG tag.
	 *
	 * @param string $file Name of the file to load
	 * @param array $args Options for the tag
	 * @return String
	*/
	public function img ($file, $args=array())
	{
		$opt ='';
		foreach($args as $key =>$value) {
			$opt .= ' '.$key.'=\''.$value.'\'';
		}

		if (strpos($file, 'http') === false) {
			$url = $this->path['img_url'] . '/' . $file;
		} else {
			$url = $file;
		}
		return "<img src='{$url}' {$opt}/>";
	}

	/**
	 * Generate wellformed FORM tag.
	 *
	 * @param mixed $url - action property for the form
	 * @param array $args Options for the tag
	 * @return String
	*/
	public function form ($url, $args=array())
	{
		$defaults = array('method'=> 'post');
		$options = array_merge($defaults, $args);

		$opt ='';
		foreach($options as $key =>$value) {
			$opt .= ' '.$key.'=\''.$value.'\'';
		}

		$action = $this->router($url);

		return "<form action='{$action}' {$opt}/>";
	}


}//end class

//Export framework className
$GLOBALS["FramePressHtml"] = 'FramePressHtml_001';
$FramePress = 'FramePressHtml_001';
