<?php

/**
 * View class for FramePress.
 *
 * This class is responsable of draw the views for controllers functions.
 * It also handle de views for controller missing errors
 *
 * Licensed under The GPL v2 License
 * Redistributions of files must retain the above copyright notice.
 *
 * @link				none yet
 * @package		core
 * @subpackage	core.view
 * @since			0.1
 * @license			GPL v2 License

 * IMPORTANT NOTE: class name will be rewrited as w2pf_view_[something] (see w2pf_init.php file), to get unique class names between plugins.
 */

class w2pf_view_test {

	/**
	 * List of variables to pass to the view
	 *
	 * @var array
	 * @access public
	*/
	var $vars = array();

	/**
	 * name of layout page to insert the view
	 *
	 * @var String
	 * @access public
	*/
	var $layout= 'default';

	/**
	 * Name of controller drawind the view
	 * neede to now where to find the view file
	 *
	 * @var String
	 * @access public
	*/
	var $controller_name =null;

	/**
	 * Name of controller function drawind the view
	 * neede to now where to find the view file
	 *
	 * @var String
	 * @access public
	*/
	var $function_name =null;

	/**
	 * Path for view file
	 *
	 * @var String
	 * @access public
	*/
	var $controller_file =null;

	/**
	 * Path for view file
	 *
	 * @var String
	 * @access public
	*/
	var $view_file =null;

	/**
	 * Path for layout file
	 *
	 * @var String
	 * @access public
	*/
	var $layout_file =null;

	/**
	 * local instance of Path Class
	 *
	 * @var Object
	 * @access public
	*/
	var $path = null;

	/**
	 * local instance of Msg Class
	 *
	 * @var Object
	 * @access public
	*/
	var $msg = null;

	/**
	 * local instance of Html Class
	 *
	 * @var Object
	 * @access public
	*/
	var $html = null;

	/**
	 * Constructor.
	 *
	 * @param object $path Reference to Path class instance created on Core class
	 * @param object $msg Reference to Msg class instance created on Core class
	 * @param object $html Reference to Html class instance created on Core class
	 * @access public
	 */
	function __construct(&$path, &$msg, &$html){

		$this->path = $path;
		$this->msg = $msg;
		$this->html = $html;
	}

	/**
	 * Save a variable to pass it to the view.
	 *
	 * @param string $varName tag for the variable
	 * @param mixed $value value of the variable passed to the view
	 * @return void
	 * @access public
	*/
	function set ($varName, $value) {

		$this->vars[$varName] = $value;
	}

	/**
	 * Set the name of the layout that will be used for draw the view
	 *
	 * @param string $layout_name name of the layout file
	 * @return void
	 * @access public
	*/
	function layout ($layout_name) {

		$this->layout = $layout_name;
	}

	/**
	 * Draw the view with the layout
	 *
	 * @return void
	 * @access public
	*/
	function draw () {

		$this->view_file = $this->path->Dir['VIEW'] . DS . strtolower($this->controller_name) . DS . $this->function_name . ".php";
		if(!file_exists($this->view_file)){
			$this->draw_error('missing_view'); exit;
		}

		$this->layout_file = $this->path->Dir['VIEW'] . DS . 'layouts' . DS . $this->layout . ".php";
		if(!file_exists($this->path->Dir['VIEW'] . DS . 'layouts' . DS . $this->layout . ".php")) {
			$this->layout_file = $this->path->Dir['CORE'] . DS . 'defaults' . DS . 'views' . DS . "default.php";
		}

		ob_start();
			//import variables
			if ($this->vars)
			{
				foreach ($this->vars as $key=>$value) {$$key = $value; }$this->vars = array();
			}

			//load view
			require_once ($this->view_file);

			//save all
			$content_for_layout = ob_get_contents();
		ob_end_clean();


		ob_start();
			//load layout's
			require_once ($this->layout_file);

			//save all
			$buffer = ob_get_contents();
		ob_end_clean();

		echo $buffer;
	}

	/**
	 * Draw a error view with the layout
	 *
	 * @return void
	 * @access public
	*/
	function draw_error ($type = null) {

		ob_start();
			// load view
			$file = $this->path->Dir['CORE'] . DS . 'defaults' . DS . 'views' . DS . $type.".php";
			require_once ($file);

			//save all
			$content_for_layout = ob_get_contents();
		ob_end_clean();

		ob_start();
			//load layout's
			$file = $this->path->Dir['CORE'] . DS . 'defaults' . DS . 'views' . DS . "default.php";
			require_once ($file);

			//save all
			$buffer = ob_get_contents();
		ob_end_clean();

		echo $buffer;

		exit;
	}
}

?>
