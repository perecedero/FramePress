<?php

/**
 * Msg class for FramePress.
 *
 * Help Class to display info messages on views
 *
 * Licensed under The GPL v2 License
 * Redistributions of files must retain the above copyright notice.
 *
 * @link				none yet
 * @package		core
 * @subpackage	core.pages
 * @since			0.1
 * @license			GPL v2 License

 * IMPORTANT NOTE: class name will be rewrited as msg_[prefix] (see init.php file), to get unique class names between plugins.
 */
class msg_test1 {

	/**
	 * List of messages
	 *
	 * @var array
	 * @access public
	 */
	var $messages = array(
		'error' => array(),
		'success' => array(),
		'warning' => array(),
		'info' => array(),
	);

	/**
	 * local instance of Path Class
	 *
	 * @var Object
	 * @access public
	*/
	var $Path = null;

	/**
	 * local instance of Config Class
	 *
	 * @var Object
	 * @access public
	 */
	var $Config= null;

	/**
	 * local instance of Html Class
	 *
	 * @var Object
	 * @access public
	*/
	var $Html = null;

	/**
	 * Constructor.
	 *
	 * @param object $path Reference to Path class instance created on Core class
	 * @param object $html Reference to Html class instance created on Core class
	 * @access public
	 */
	function __construct( &$path, &$config, &$html ){

		$this->Path = $path;
		$this->Config = $config;
		$this->Html = $html;
	}


	/**
	 * Store the error message
	 *
	 * @param string $msg Message to store
	 * @return void
	 * @access public
	 */
	function set ($type='info', $msg = null)
	{
		if(!$msg){ return false; }

		switch ($type)
		{
			case 'error': $this->messages['error'][] = $msg; break;
			case 'success': $this->messages['success'][] = $msg; break;
			case 'warning': $this->messages['warning'][] = $msg; break;
			case 'info': $this->messages['info'][] = $msg; break;
		}
		return true;
	}


	/**
	 * Clear the messages previously stored messages
	 *
	 * @param string $type what type of messages to clear
	 * @return void
	 * @access public
	 */
	function clear ($type = 'All')
	{
		switch ($type)
		{
			case 'error': $this->messages['error'] = array(); break;
			case 'success': $this->messages['success'] = array(); break;
			case 'warning': $this->messages['warning'] = array(); break;
			case 'info': $this->messages['info'] = array(); break;
			default:
				$this->messages = array(
					'error' => array(),
					'success' => array(),
					'warning' => array(),
					'info' => array(),
				);
			break;
		}
	}

	/**
	 * Display the messages as HTML
	 *
	 * @param string $type what type of messages to Display
	 * @param array $options what type of messages to Display
	 * @return void
	 * @access public
	 */
	function show ($type = 'error', $options=array()){

		$x_defaults = array('count' => 3 );
		$options = array_merge($x_defaults, $options);

		$x_def_path = $this->Path->Dir['D_VIEW'] . DS;
		$x_usr_path = $this->Path->Dir['VIEW'] . DS . 'elements' . DS;

		switch ($type)
		{
			case 'error':
				$x_viewFile = 'msg_error.php';
				$msg = $this->messages['error'];
			break;
			case 'success': 
				$x_viewFile = 'msg_success.php';
				$msg = $this->messages['success'];
			break;
			case 'warning':
				$x_viewFile = 'msg_warning.php';
				$msg = $this->messages['warning'];
			break;
			case 'info': default:
				$x_viewFile = 'msg_info.php';
				$msg = $this->messages['info'];
			break;
		}

		if (!file_exists( $x_view = $x_usr_path . $x_viewFile )) {
			$x_view = $x_def_path . $x_viewFile;
		}

		ob_start();
			//load layout's
			require_once ( $x_view );

			//save all
			$buffer = ob_get_contents();
		ob_end_clean();

		return $buffer;
	}

}

?>
