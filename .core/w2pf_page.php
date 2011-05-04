<?php
/**
 * Page class for FramePress.
 *
 * FramePress abstracts the handling of WP admin pages.
 * This class is responsable of add previusly declared admin pages to WP and handle its call
 *
 * Licensed under The GPL v2 License
 * Redistributions of files must retain the above copyright notice.
 *
 * @link				none yet
 * @package       core
 * @subpackage    core.pages
 * @since         0.1
 * @license       GPL v2 License

 * IMPORTANT NOTE: class name will be rewrited as w2pf_page_[something] (see w2pf_init.php file), to get unique class names between plugins.
 */
class w2pf_page_test {

	/**
	 * List of admin pages to add
	 *
	 * @var array
	 * @access public
	 */
	var $pages= array();

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
	 * @param object $config Reference to Config class instance created on Core class
	 * @access public
	 */
	function __construct(&$path, &$view, &$config) {

		$this->path = $path;
		$this->view = $view;
		$this->config = $config;
	}

	/**
	 * Store the admin pages for WP and create an action tha will create them on WP
	 *
	 * @param array $pages List of actions to add
	 * @return void
	 * @access public
	 */
	function add ($pages = array()) {

		$this->pages = $pages;
		add_action('admin_menu', array($this, 'wpf_page_add_pages_real'));
	}

	/**
	 * Create stored admin pages on WP
	 *
	 * @return void
	 * @access public
	 */
	function wpf_page_add_pages_real () {

		foreach ($this->pages as $type => $page){
			for($i=0; $i<count($page); $i++){

				$icon_url = $position = null;
				foreach ($page[$i] as $key => $value){$$key = $value;}

				//this fix the controller name conflict between plugins
				if($this->config->read('prefix')){
					$menu_slug = $this->config->read('prefix') . '_' . $menu_slug;
					if (isset($parent_slug)){
						$parent_slug = $this->config->read('prefix') . '_' . $parent_slug;
					}
				}

				switch($type)
				{
					case 'menu':
						add_menu_page( $page_title, $menu_title, $capability, $menu_slug, array($this, 'call'), $icon_url, $position );
					break;
					case 'submenu':
						add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, array($this, 'call'));
					break;
					case 'dashboard':
						add_dashboard_page( $page_title, $menu_title, $capability, $menu_slug, array($this, 'call'));
					break;
					case 'posts':
						add_posts_page( $page_title, $menu_title, $capability, $menu_slug, array($this, 'call'));
					break;
					case 'media':
						add_media_page( $page_title, $menu_title, $capability, $menu_slug, array($this, 'call'));
					break;
					case 'links':
						add_links_page( $page_title, $menu_title, $capability, $menu_slug, array($this, 'call'));
					break;
					case 'pages':
						add_pages_page( $page_title, $menu_title, $capability, $menu_slug, array($this, 'call'));
					break;
					case 'comments':
						add_comments_page( $page_title, $menu_title, $capability, $menu_slug, array($this, 'call'));
					break;
					case 'appearance':
						add_theme_page( $page_title, $menu_title, $capability, $menu_slug, array($this, 'call'));
					break;
					case 'plugins':
						add_plugins_page( $page_title, $menu_title, $capability, $menu_slug, array($this, 'call'));
					break;
					case 'users':
						add_users_page( $page_title, $menu_title, $capability, $menu_slug, array($this, 'call'));
					break;
					case 'tools':
						add_management_page( $page_title, $menu_title, $capability, $menu_slug, array($this, 'call'));
					break;
					case 'settings':
						add_options_page( $page_title, $menu_title, $capability, $menu_slug, array($this, 'call'));
					break;
					default:break;
				}

			}
		}
	}

	/**
	 * Handler for admin pages calls.
	 * It will call the correct function on the correct controller
	 * All data for this come from $_GET superglobal
	 *
	 * @return void
	 * @access public
	 */
	function call() {

		$page = str_replace($this->config->read('prefix') . '_','', $_GET['page']);

		$this->view->controller_file = $this->controller_file = $this->path->Dir['PAGES'] . DS . $page.'.php';
		$this->view->controller_name = $this->controller_class = ucfirst ($page);
		$this->controller_class =  ucfirst ($this->config->read('prefix')).$this->controller_class;
		$this->view->function_name = $this->controller_method =  (isset($_GET['function']))?$_GET['function']:'index';
		$args = (isset($_GET['fargs']))?$_GET['fargs']:array();

		if(!file_exists($this->controller_file))
		{
			$this->view->draw_error('missing_file');
		}

		require_once ($this->controller_file);

		if (!class_exists($this->controller_class))
		{
			$this->view->draw_error('missing_controller');
		}

		$aux = $this->controller_class;
		$this->controller_object = new $aux();

		if(!method_exists($this->controller_object, $this->controller_method))
		{
			$this->view->draw_error('missing_function');
		}

		if(method_exists($this->controller_object, 'before_filter')) { call_user_func(array($this->controller_object, 'before_filter')); }
		call_user_func_array(array($this->controller_object, $this->controller_method) , $args);
		if(method_exists($this->controller_object, 'after_filter')) { call_user_func(array($this->controller_object, 'after_filter')); }

		$this->view->draw();

		ob_end_flush();
	}
}
?>
