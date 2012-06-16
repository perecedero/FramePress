<?php
/**
 * Actions class for FramePress.
 *
 * FramePress abstracts the handling of WP actions.
 * This class is responsable of add previusly declared actions to WP and handle its call
 *
 * Licensed under The GPL v2 License
 * Redistributions of files must retain the above copyright notice.
 *
 * @link				none yet
 * @package       core
 * @subpackage    core.actions
 * @since         0.1
 * @license       GPL v2 License

 * IMPORTANT NOTE: class name will be rewrited as action_[prefix] (see init.php file), to get unique class names between plugins.
 */

class action_test1 {

	/**
	 * List of actions to add
	 *
	 * @var array
	 * @access public
	 */
	var $actions= array();

	/**
	 * local instance of View Class
	 *
	 * @var Object
	 * @access public
	 */
	var $View= null;

	/**
	 * local instance of Path Class
	 *
	 * @var Object
	 * @access public
	 */
	var $Path= null;

	/**
	 * local instance of Config Class
	 *
	 * @var Object
	 * @access public
	 */
	var $Config= null;

	/**
	 * Name of controller that handles a specific call
	 *
	 * @var String
	 * @access private
	 */
	private  $controller_file= null;

	/**
	 * Name of class that handles a specific call
	 *
	 * @var String
	 * @access private
	 */
	private $controller_class= null;

	/**
	 * Name of method that handles a specific call
	 *
	 * @var String
	 * @access private
	 */
	private $controller_method= null;

	/**
	 * Controller Object that handles a specific call
	 *
	 * @var Object
	 * @access private
	 */
	private $controller_object= null;

	/**
	 * Constructor.
	 *
	 * @param object $path Reference to Path class instance created on Core class
	 * @param object $view Reference to View class instance created on Core class
	 * @access public
	 */
	function __construct( &$path, &$view, &$config ) {
		$this->Path = $path;
		$this->View = $view;
		$this->Config = $config;
	}

	/**
	 * Add the actions to WP
	 *
	 * @param array $actions List of actions to add
	 * @return void
	 * @access public
	 */
	function add ($actions = null) {

		$this->actions = $actions;
		$default =array('is_ajax'=> false, 'priority' => 10, 'accepted_args' => 1);

		foreach ($this->actions as $action){

			$action = array_merge($default, $action);

			$tag = $action['tag'];
			if($action['is_ajax']){$tag='wp_ajax_'.$tag;}

			add_action($tag, array($this, $action['controller'].'AYNIL'.$action['function']), $action['priority'], $action['accepted_args']);
		}
	}

	/**
	 * Handler for actions calls.
	 * It will call the correct function on the correct controller
	 *
	 * @param string $name String with controller and function to call
	 * @param array $args List of params for the function
	 * @return void
	 * @access public
	 */
	function __call($name, $args=array()) {

		$parts = explode('AYNIL', $name);

		$this->View->controller_file = $this->controller_file = $this->Path->Dir['ACTIONS'] . DS . $parts[0].'.php';
		$this->View->controller_name = $this->controller_class = ucfirst ($parts[0]);
		$this->controller_class =  ucfirst ($this->Config->read('prefix')).$this->controller_class;
		$this->View->function_name = $this->controller_method = $parts[1];

		@ini_set('display_errors', true);
		set_error_handler('framePress_1234567_eh');

		if(!file_exists($this->controller_file))
		{
			$fileRelativePath = substr( $this->controller_file, strpos($this->controller_file, $this->Path->Dir['N_ROOT']), strlen($this->controller_file));
			$this->View->set( 'fileRelativePath', $fileRelativePath );
			$this->View->set( 'fileClassName', $this->controller_class );
			$this->View->draw_error('missing_file');
			restore_error_handler();
			@ini_set('display_errors', false);
			return false;
		}

		require_once ($this->controller_file);

		if (!class_exists($this->controller_class))
		{
			$fileRelativePath = substr( $this->controller_file, strpos($this->controller_file, $this->Path->Dir['N_ROOT']), strlen($this->controller_file));
			$this->View->set( 'fileRelativePath', $fileRelativePath );
			$this->View->set( 'fileClassName', $this->controller_class );
			$this->View->draw_error('missing_controller');

			restore_error_handler();
			@ini_set('display_errors', false);
			return false;
		}

		$this->controller_object = new $this->controller_class();

		if(!method_exists($this->controller_object, $this->controller_method))
		{
			$fileRelativePath = substr( $this->controller_file, strpos($this->controller_file, $this->Path->Dir['N_ROOT']), strlen($this->controller_file));
			$this->View->set( 'fileRelativePath', $fileRelativePath );
			$this->View->set( 'fileClassName', $this->controller_class );
			$error = $this->View->draw_error('missing_function', true); 
			$this->View->draw_error('missing_function', true);

			restore_error_handler();
			@ini_set('display_errors', false);
			return false;
		}

		if(method_exists($this->controller_object, 'before_filter')) { call_user_func(array($this->controller_object, 'before_filter')); }
		call_user_func_array(array($this->controller_object, $this->controller_method) , $args);
		if(method_exists($this->controller_object, 'after_filter')) { call_user_func(array($this->controller_object, 'after_filter')); }

		//$this->View->draw();

		restore_error_handler();
		@ini_set('display_errors', false);

		//@ob_end_flush();

	}
}

?>
