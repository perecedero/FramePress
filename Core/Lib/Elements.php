<?php
/**
 * Elements class for FramePress.
 *
 * This class is used to create reusable elements in the views,
 *
 * Licensed under The GPL v2 License
 * Redistributions of files must retain the above copyright notice.
 *
 * @package		FramePress
 * @subpackage	core.elements
 * @license	   	GPL v2 License
 * @creator	   	Ivan Lansky ( @Perecedero )
 *
 */

//define core class
if (!class_exists('FramePress_Elements_002')) {
class FramePress_Elements_002
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
	 * Bla Bla
	 *
	 * @access public
	 * @return String (rendered element)
	 */
	public function get($template, $args = array())
	{
		//set template vars
		if(is_array($args)){
			foreach($args as $k => $v){
				$this->Core->View->Set($k, $v, 'framepress.elements');
			}
		}

		//Gets the template
		$element = 'Elements' . DS . $template;
		return $this->Core->View->render($element, array(
			'context' =>'framepress.elements'
		));
	}

}//end class
}//end if class exists

//Export framework className
$GLOBALS["FramePressElements"] = 'FramePress_Elements_002';
$FramePressElements = 'FramePress_Elements_002';
