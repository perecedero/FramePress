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
if (!class_exists('FramePress_Elements_001')) {
class FramePress_Elements_001
{
    private static $instance;

	/**
	 * Local Reference to the framework core object
	 *
	 * @var object
	 */
	private $fp = null;

	/**
	 * Constructor.
	 *
	 * @param string $FramePress Core Class
	*/
	public function __construct( &$FramePress ){
		$this->fp = $FramePress;
		$this->fp->mergePaths(array(
			'elements' => $this->fp->path['view'] . DS . 'elements'
		));
	}

	/**
	 * Save a variable to pass it to the template.
	 *
	 * @param string $varName
	 * @param mixed $value
	 * @access public
	 * @return void
	 */
	private function viewSet($varName, $value)
	{
		$this->fp->viewSet($varName, $value, 'framepress.elements');
	}

	/**
	 * Bla Bla
	 *
	 * @access public
	 * @return String (rendered element)
	 */
	private function drawView($template)
	{
		return $this->fp->drawView($this->fp->path['elements'] . DS . rtrim($template, '.php') . '.php'  , false, 'framepress.elements');
	}

	/**
	 * Bla Bla
	 *
	 * @access public
	 * @return String (rendered element)
	 */
	public function get($template, $args)
	{
		//set template vars
		if(is_array($args)){
			foreach($args as $k => $v){
				$this->viewSet($k, $v);
			}
		}

		//Gets the template
		return $this->drawView($template);
	}


    public static function fpGetInstance(&$framepress)
    {
        if (!self::$instance) {
            $className = __CLASS__;
            self::$instance = new $className($framepress);
        }
        return self::$instance;
    }

}//end class

//Export framework className
$GLOBALS["FramePressElements"] = 'FramePress_Elements_001';
$FramePressElements = 'FramePress_Elements_001';

}//end if class exists
