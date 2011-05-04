<?php

/**
 * Html class for FramePress.
 *
 * This class provide functions for views like load css and js files.
 *
 * Licensed under The GPL v2 License
 * Redistributions of files must retain the above copyright notice.
 *
 * @link				none yet
 * @package		core
 * @subpackage	core.view
 * @since			0.1
 * @license			GPL v2 License

 * IMPORTANT NOTE: class name will be rewrited as w2pf_html_[something] (see w2pf_init.php file), to get unique class names between plugins.
 */

class w2pf_html_test {

	/**
	 * local instance of Path Class
	 *
	 * @var Object
	 * @access public
	*/
	var $path = null;

	/**
	 * Constructor.
	 *
	 * @param object $path Reference to Path class instance created on Core class
	 * @access public
	 */
	function __construct(&$path) {
		$this->path = $path;
	}

	/**
	 * Generate wellformed css LINK tag.
	 *
	 * @param string $file Name of the file to load
	 * @param array $args Options for the tag
	 * @return String
	 * @access public
	*/
	function css ($file, $args=array()) {

		$url=$this->path->Dir['CSS_URL'].'/'.$file;
		return "<link href='{$url}' rel='stylesheet' type='text/css'>";
	}

	/**
	 * Generate wellformed js SCRIPT tag.
	 *
	 * @param string $file Name of the file to load
	 * @param array $args Options for the tag
	 * @return String
	 * @access public
	*/
	function js ($file, $args=array()) {

		$url=$this->path->Dir['JS_URL'].'/'.$file;
		return "<script type='text/javascript' language='javascript' src='{$url}'></script>";
	}

	/**
	 * Generate wellformed A tag.
	 *
	 * @param string $title Link Anchor
	 * @param mixed $url Href for the link
	 * @param array $args Options for the tag
	 * @return String
	 * @access public
	*/
	function link ($title, $url=array(), $args=array()) {

		$opt ='';
		foreach($args as $key =>$value)
		{
			$opt .= ' '.$key.'=\''.$value.'\'';
		}

		$href = $this->path->router($url);

		return "<a href='{$href}' {$opt}/>{$title}</a>";
	}

	/**
	 * Generate wellformed IMG tag.
	 *
	 * @param string $file Name of the file to load
	 * @param array $args Options for the tag
	 * @return String
	 * @access public
	*/
	function img ($file, $args=array()) {

		$opt ='';
		foreach($args as $key =>$value)
		{
			$opt .= ' '.$key.'=\''.$value.'\'';
		}
		$url=$this->path->Dir['IMG_URL'].'/'.$file;
		return "<img src='{$url}' {$opt}/>";
	}

}

?>
