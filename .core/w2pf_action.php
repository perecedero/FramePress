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

 * IMPORTANT NOTE: class name will be rewrited as w2pf_action_[something] (see w2pf_init.php file), to get unique class names between plugins.
 */

class w2pf_action_test {

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
	var $view= null;

	/**
	 * local instance of Path Class
	 *
	 * @var Object
	 * @access public
	 */
	var $path= null;

	/**
	 * local instance of Config Class
	 *
	 * @var Object
	 * @access public
	 */
	var $config= null;

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
	function __construct(&$path, &$view, &$config) {
		$this->path = $path;
		$this->view = $view;
		$this->config = $config;
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

		foreach ($this->actions as $action){
			$tag = $action['tag'];
			if($action['is_ajax']){$tag='wp_ajax_'.$tag;}
			add_action($tag, array($this, $action['handler'].'AYNIL'.$action['function']));
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

		$this->view->controller_file = $this->controller_file = $this->path->Dir['ACTIONS'] . DS . $parts[0].'.php';
		$this->view->controller_name = $this->controller_class = ucfirst ($parts[0]);
		$this->controller_class =  ucfirst ($this->config->read('prefix')).$this->controller_class;
		$this->view->function_name = $this->controller_method = $parts[1];

		if(!file_exists($this->controller_file))
		{
			$this->view->draw_error('missing_file');
		}

		require_once ($this->controller_file);

		if (!class_exists($this->controller_class))
		{
			$this->view->draw_error('missing_controller');
		}

		$this->controller_object = new $this->controller_class();

		if(!method_exists($this->controller_object, $this->controller_method))
		{
			$this->view->draw_error('missing_function');
		}

		if(method_exists($this->controller_object, 'before_filter')) { call_user_func(array($this->controller_object, 'before_filter')); }
		call_user_func_array(array($this->controller_object, $this->controller_method) , $args);
		if(method_exists($this->controller_object, 'after_filter')) { call_user_func(array($this->controller_object, 'after_filter')); }

	}
}

?>
