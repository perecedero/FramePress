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

 * IMPORTANT NOTE: class name will be rewrited as page_[prefix] (see init.php file), to get unique class names between plugins.
 */
class page_test1 {

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
	 * @param object $config Reference to Config class instance created on Core class
	 * @access public
	 */
	function __construct( &$path, &$view, &$config ) {

		$this->Path = $path;
		$this->View = $view;
		$this->Config = $config;
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

		foreach ($this->pages as $type => $pages){
			for($i=0; $i<count($pages); $i++){

				$page_defaults = array('page.title'=> null, 'menu.title'=> null, 'capability'=> null, 'controller'=> null, 'function'=>'index', 'parent'=> null, 'icon'=> null, 'position'=> null);
				$page = array_merge($page_defaults, $pages[$i]);

				//generate url for image selected
				if ( $page['icon'] ) {
					$page['icon'] = $this->Path->Dir['IMG_URL'] . DS . $page['icon'];
				}

				//magic!
				$page['menu.slug'] = $this->Config->read('prefix') . '_' . $page['controller'];
				if ($page['parent']){
					$menus = $this->pages['menu'];
					for ($p=0; $p < count($menus); $p++){
						if( $menus[$p]['menu.title'] == $page['parent'] ) {
							$page['parent.slug'] = $this->Config->read('prefix') . '_' . $menus[$p]['controller'];
							break;
						}
					}
				}

				switch($type)
				{
					case 'menu':
						add_menu_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'call_'.$page['function']), $page['icon'], $page['position'] );
					break;
					case 'submenu':
						add_submenu_page( $page['parent.slug'], $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'call_'.$page['function']));
					break;
					case 'dashboard':
						add_dashboard_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'call_'.$page['function']));
					break;
					case 'posts':
						add_posts_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'call_'.$page['function']));
					break;
					case 'media':
						add_media_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'call_'.$page['function']));
					break;
					case 'links':
						add_links_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'call_'.$page['function']));
					break;
					case 'pages':
						add_pages_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'call_'.$page['function']));
					break;
					case 'comments':
						add_comments_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'call_'.$page['function']));
					break;
					case 'appearance':
						add_theme_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'call_'.$page['function']));
					break;
					case 'plugins':
						add_plugins_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'call_'.$page['function']));
					break;
					case 'users':
						add_users_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'call_'.$page['function']));
					break;
					case 'tools':
						add_management_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'call_'.$page['function']));
					break;
					case 'settings':
						add_options_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'call_'.$page['function']));
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
	function __call($name ,$args=array()) {

		$default_function = explode ('_', $name);
		$page = str_replace($this->Config->read('prefix') . '_','', $_GET['page']);

		$this->View->controller_file = $this->controller_file = $this->Path->Dir['PAGES'] . DS . $page.'.php';
		$this->View->controller_name = $this->controller_class = ucfirst ($page);
		$this->controller_class =  ucfirst ($this->Config->read('prefix')).$this->controller_class;
		$this->View->function_name = $this->controller_method =  (isset($_GET['function']))?$_GET['function']:$default_function[1];
		$args = (isset($_GET['fargs']))?$_GET['fargs']:array();

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
			exit;
		}

		$aux = $this->controller_class;
		$this->controller_object = new $aux();

		if(!method_exists($this->controller_object, $this->controller_method))
		{
			$fileRelativePath = substr( $this->controller_file, strpos($this->controller_file, $this->Path->Dir['N_ROOT']), strlen($this->controller_file));
			$this->View->set( 'fileRelativePath', $fileRelativePath );
			$this->View->set( 'fileClassName', $this->controller_class );
			$this->View->draw_error('missing_function');
			restore_error_handler();
			@ini_set('display_errors', false);
			return false;
		}

		if(method_exists($this->controller_object, 'before_filter')) { call_user_func(array($this->controller_object, 'before_filter')); }
		call_user_func_array(array($this->controller_object, $this->controller_method) , $args);
		if(method_exists($this->controller_object, 'after_filter')) { call_user_func(array($this->controller_object, 'after_filter')); }

		$this->View->draw();

		restore_error_handler();
		@ini_set('display_errors', false);

		@ob_end_flush();
	}
}
?>
