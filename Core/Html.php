<?php

//define core class
if (!class_exists('FramePress_Html_001')) {
class FramePress_Html_001
{

	/**
	 * Local Reference to the framework core object
	 *
	 * @var object
	 */
	private $Core = null;

	/**
	 * Constructor.
	 *
	 * @param string $fp Core Class
	*/
	public function __construct( &$fp ){
		$this->Core = $fp;
	}

	/**
	 * Generate wellformed css LINK tag.
	 *
	 * @param string $file Name of the file to load
	 * @param array $args Options for the tag
	 * @return String
	*/
	public function css ($file, $args=array())
	{
		$url = $this->Core->paths['css.url'] . '/' . $file;
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
		if(!isset($args['inline'])){
			$url = $this->Core->paths['js.url'] . '/' . $file;
			return "<script type='text/javascript' language='javascript' src='{$url}'></script>";
		} else {
			$file = $this->Core->paths['js'] . DS . $file;
			return "<script type='text/javascript' language='javascript'>" . file_get_contents($file) . "</script>";
		}
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
		$href = $this->Core->router($url);
		$prop = $this->tagPropeties($options);
		return "<a href='{$href}' {$prop} >{$title}</a>";
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
		$prop = $this->tagPropeties($options);

		if (strpos($file, 'http') === false) {
			$url = $this->Core->paths['img.url'] . DS . $file;
		} else {
			$url = $file;
		}
		return "<img src='{$url}' {$prop} >";
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

		$action = $this->Core->router($url);
		$prop = $this->tagPropeties($options);

		return "<form action='{$action}' {$prop} >";
	}


	public function tagPropeties ($args)
	{
		$escape = (isset($args['escape'])) ? $args['escape'] : true;
		$prop ='';
		foreach($args as $key =>$value) {
			if($key == 'escape') { continue; }
			$prop .= ' '. sprintf('%s="%s"', $key, ($escape ? $this->h($value) : $value));
		}
		return  $prop;
	}


	/**
	 * Convenience method for htmlspecialchars.
	 *
	 * @param string|array|object $text Text to wrap through htmlspecialchars. Also works with arrays, and objects.
	 *    Arrays will be mapped and have all their elements escaped. Objects will be string cast if they
	 *    implement a `__toString` method. Otherwise the class name will be used.
	 * @param boolean $double Encode existing html entities
	 * @param string $charset Character set to use when escaping. Defaults to config value in 'App.encoding' or 'UTF-8'
	 * @return string Wrapped text
	 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#h
	 */
	public function h($text, $double = true, $charset = 'UTF-8')
	{
		if (is_array($text)) {
			$texts = array();
			foreach ($text as $k => $t) {
				$texts[$k] = h($t, $double, $charset);
			}
			return $texts;
		} elseif (is_object($text)) {
			if (method_exists($text, '__toString')) {
				$text = (string)$text;
			} else {
				$text = '(object)' . get_class($text);
			}
		} elseif (is_bool($text)) {
			return $text;
		}

		if (is_string($double)) {
			$charset = $double;
		}
		return htmlspecialchars($text, ENT_QUOTES, ($charset) ? $charset : $defaultCharset, $double);
	}


}//end class
}//end if class exists

//Export framework className
$GLOBALS["FramePressHtml"] = 'FramePress_Html_001';
$FramePress = 'FramePress_Html_001';
