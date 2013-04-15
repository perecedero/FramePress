<?php

 /**
 * Core class for FramePress Lite.
 *
 * DESCRIPTION NEEDED
 *
 * Licensed under The GPL v2 License
 * Redistributions of files must retain the above copyright notice.
 *
 * @link			https://github.com/perecedero/FramePressLite
 * @package		FramePress
 * @subpackage	core
 * @license		GPL v2 License
 * @author		Ivan Lansky (@perecedero)
 */


//Use the DS to separate the directories
if(!defined('DIRECTORY_SEPARATOR')){define('DIRECTORY_SEPARATOR', '/');}
if(!defined('DS')){define('DS', DIRECTORY_SEPARATOR);}


//define core class
if (!class_exists('FramePress_001')) {
class FramePress_001 
{
	public $config = array(
		'prefix' => null,
		'use.tmp' => false,
		'use.i18n' => true,
		'use.session' => true,
		'use.performance.log' => false,
	);

	public $status = array(
		'plugin.mainfile' => null,
		'plugin.fullpath' => null,
		'plugin.foldername' => null,
		'controller.file' => null,
		'controller.name' => null,
		'controller.class' => null,
		'controller.method' => null,
		'controller.object' => null,
		'view.file' => null,
		'view.layout.file' => null,
		'view.vars' => array()
	);

	public $session = array(
		'id' => null,
		'name' => null,
		'time' => 14400, // 3600 * 4  // 4 hours
	);

	public $path = array();

	public $pages = array();

	public $actions = array();

	public $errorlog = array();

	/**
	 * Constructor. It wil create all the objects necesaries for FramePress
	 *
	 * @param string $main_file Name of the main file
	 * @param string $config user defined configuration
	*/
	public function __construct($main_file, $config = array() )
	{
		$fpl_fullpath = dirname($main_file);
		$fpl_foldername = basename(dirname($main_file));

		//set partial status
		$this->status = array_merge($this->status, array(
			'plugin.fullpath' => $fpl_fullpath,
			'plugin.foldername' => $fpl_foldername,
			'plugin.mainfile' => basename($main_file),
		));

		//set paths
		$this->path = array (
			'core' => $fpl_fullpath . DS . 'core',
			'config' => $fpl_fullpath . DS . 'config',
			'controllers' => $fpl_fullpath . DS . 'controllers',
			'view' => $fpl_fullpath . DS . 'views',
			'd_view' => $fpl_fullpath . DS . 'core' . DS . 'defaults' . DS . 'views',
			'layout' => $fpl_fullpath . DS . 'views' . DS . 'layouts',
			'mail_view' => $fpl_fullpath . DS . 'views' . DS . 'emails',
			'lib' => $fpl_fullpath . DS . 'lib',
			'd_lib' => $fpl_fullpath . DS . 'core' . DS . 'defaults' . DS . 'lib',
			'lang' => $fpl_foldername . DS . 'languages',
			'tmp' => $fpl_foldername . DS . 'tmp',
			'resources' => $fpl_fullpath . DS . 'resources',
			'img' => $fpl_fullpath . DS . 'resources' . DS . 'img',
			'img_url' => get_bloginfo( 'wpurl' ) . '/wp-content/plugins/' . $fpl_foldername . '/resources/img',
			'css' => $fpl_fullpath . DS . 'resources' . DS . 'css',
			'css_url' => get_bloginfo( 'wpurl' ) . '/wp-content/plugins/' . $fpl_foldername . '/resources/css',
			'js' => $fpl_fullpath . DS . 'resources' . DS . 'js',
			'js_url' => get_bloginfo( 'wpurl' ). '/wp-content/plugins/' . $fpl_foldername . '/resources/js',
		);

		//Merge configurations
		$this->config = array_merge($this->config, $config);

		//Configure TMP folder
		if ($this->config['use.tmp']) {
			//try to get sys tmp folder path
			if ( function_exists('sys_get_temp_dir')) {
				$tempPath=@realpath(sys_get_temp_dir());
			}else{
				$tempPath_env = array('TMP', 'TEMP', 'TMPDIR');
				for($i = 0; $i < count($tempPath_env); $i++){
					if( $tempPath = getenv($tempPath_env[$i]) ){ break; }
				}
			}
			//if we can use sys tmp folder, the must use our TMP folder
			if(!@is_writable($tempPath) || !@is_readable($tempPath)){
				if(!is_writable( $this->path['tmp'] ) ){
					trigger_error("Can&#39;t write on <b>" . $this->path['tmp'] . "</b> folder, please change it's permissions to 777", E_USER_WARNING);
				}
			}
			//set TMP folder to use
			$this->path['systmp'] = ($tempPath)?$tempPath:$this->path['tmp'];
		}

		if ($this->config['use.performance.log']) {
			$this->config['use.session'] = true;
		}

		//Configure sessions
		if ($this->config['use.session']) {

			//get session ID from cookies
			$id = null;
			if(!isset($_COOKIE)) {$_COOKIE = array();}
			foreach ($_COOKIE as $key => $value) {
				if(preg_match("/^wordpress_logged_in_(.)*$/", $key)) { $id = md5($value); break; }
			}
			if (!$id){ $id = 'Global'; }

			$this->session['id'] = $id;

			//session_name
			$this->session['name'] = 'fpl_session_' . strtolower($this->config['prefix']);

			//create a global session if not exist
			if ( !$session = get_option($this->session['name']) ) {
				$session = array (
					$this->session['id'] => array (
						'time' => strtotime('now'),
						'data' => array(),
					),
					'Global' => array (
						'time' => strtotime('now'),
						'data' => array(),
					),
				);
			}

			//remove old sessions
			foreach ($session as $key => $value ) {
				if ( ( $value['time'] + $this->session['time'] ) < strtotime('now') ) {
					unset( $session[ $key ] );
				}
			}

			//add user session
			if(!isset($session[$this->session['id']])){
				$session[$this->session['id']] = array (
					'time' => strtotime('now'),
					'data' => array(),
				);
			}

			update_option ($this->session['name'],$session);
		}

		if ($this->config['use.performance.log']){
			if(!$this->sessionCheck('performance.log')) {
				$this->sessionWrite('performance.log', array());
			}
			add_action('in_admin_footer', array($this, 'showPerformanceLog'));
		}

		//Load languages
		if ($this->config['use.i18n']) {
			add_action('init', array($this, 'load_languages'));
		}

		//Register activation and deactivation functions
		register_activation_hook($this->status['plugin.foldername'] . DS . $this->status['plugin.mainfile'], array($this,'activation'));
		register_deactivation_hook($this->status['plugin.foldername'] . DS . $this->status['plugin.mainfile'], array($this, 'deactivation'));

		//Capture output
		add_action('admin_init', array($this, 'capture_output'));
	}

	//------------------------------------------------------------------------------------------------------------------

	/**
	 * Start the output capture to can use headers on the plugin
	 *
	 * @return void
	*/
	public function capture_output ()
	{
		@ob_start();
	}

	/**
	 * Load lenguaje dictionary
	 *
	 * @return void
	*/
	public function load_languages ()
	{
		load_plugin_textdomain( strtolower($this->config['prefix']), false, $this->path['lang'] );
	}

	/**
	 * Call activation function
	 *
	 * @return void
	*/
	public function activation ()
	{
		require_once ($this->path['config'] . DS . 'activation.php');
		$function_name = strtolower($this->config['prefix']) . '_' . 'on_activation';
		if (function_exists($function_name)){
			call_user_func($function_name);
		}
	}

	/**
	 * Call deactivation function
	 *
	 * @return void
	*/
	public function deactivation ()
	{
		if($this->session['name']){
			delete_option($this->session['name']);
		}

		require_once ($this->path['config'] . DS . 'activation.php');
		$function_name = strtolower($this->config['prefix']) . '_' . 'on_deactivation';
		if (function_exists($function_name)){
			call_user_func($function_name);
		}
	}

	public function showPerformanceLog ()
	{
		$log = $this->sessionRead('performance.log');
		$this->sessionWrite('performance.log', array());
		echo '<script>jQuery("#wpfooter").css("position", "relative")</script>';
		foreach($log as $l){ echo '<div style="margin: 10px 0; font: 16px bold;">'.join(' -- ', $l).'</div>'; }
		echo '<br>'; 
	}

	//------------------------------------------------------------------------------------------------------------------

	/**
	 * Merge default path with user defined one  
	 *
	 * @param array $custom_path user defined path to use with the FramePress
	 * @return void
	*/
	public function mergePaths( $custom_path=array() )
	{
		//merge configurations
		$this->path = array_merge($this->path, $custom_path);
	}

	//------------------------------------------------------------------------------------------------------------------

	/**
	 * Register plugin admin pages
	 *
	 * @param array $pages
	 * @return void
	*/
	public function pages( $pages=array() )
	{
		$this->pages = $pages;
		add_action('admin_menu', array($this, 'addPagesReal'));
	}

	/**
	 * Create plugin admin pages
	 *
	 * @param array $pages
	 * @return void
	*/
	public function addPagesReal ()
	{
		foreach ($this->pages as $type => $pages){
			for($i=0; $i<count($pages); $i++){

				$page_defaults = array('page.title'=> null, 'menu.title'=> null, 'capability'=> null, 'controller'=> null, 'function'=>'index', 'parent'=> null, 'icon'=> null, 'position'=> null);
				$page = array_merge($page_defaults, $pages[$i]);

				//generate url for image selected
				if ( $page['icon'] ) {
					$page['icon'] = $this->path['img_url'] . DS . $page['icon'];
				}

				//magic!
				$page['menu.slug'] = $this->config['prefix'] . '-' . $page['controller'] . '-' . $page['function'];
				$this->pages[$type][$i]['menu.slug'] = $page['menu.slug'];
				if ($page['parent']){
					$menus = $this->pages['menu'];
					for ($p=0; $p < count($menus); $p++){
						if( $menus[$p]['menu.title'] == $page['parent'] ) {
							$page['parent.slug'] = $this->config['prefix'] . '-' . $menus[$p]['controller']. '-' . $menus[$p]['function'];
							break;
						}
					}
				}

				switch($type) {
					case 'menu':
						add_menu_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'page' . '__AYNIL__' . $page['controller'] . '__AYNIL__' . $page['function']), $page['icon'], $page['position'] );
					break;
					case 'submenu':
						add_submenu_page( $page['parent.slug'], $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'page' . '__AYNIL__' . $page['controller'] . '__AYNIL__' . $page['function']));
					break;
					case 'dashboard':
						add_dashboard_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'page' . '__AYNIL__' . $page['controller'] . '__AYNIL__' . $page['function']));
					break;
					case 'posts':
						add_posts_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'page' . '__AYNIL__' . $page['controller'] . '__AYNIL__' . $page['function']));
					break;
					case 'media':
						add_media_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'page' . '__AYNIL__' . $page['controller'] . '__AYNIL__' . $page['function']));
					break;
					case 'links':
						add_links_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'page' . '__AYNIL__' . $page['controller'] . '__AYNIL__' . $page['function']));
					break;
					case 'pages':
						add_pages_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'page' . '__AYNIL__' . $page['controller'] . '__AYNIL__' . $page['function']));
					break;
					case 'comments':
						add_comments_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'page' . '__AYNIL__' . $page['controller'] . '__AYNIL__' . $page['function']));
					break;
					case 'appearance':
						add_theme_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'page' . '__AYNIL__' . $page['controller'] . '__AYNIL__' . $page['function']));
					break;
					case 'plugins':
						add_plugins_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'page' . '__AYNIL__' . $page['controller'] . '__AYNIL__' . $page['function']));
					break;
					case 'users':
						add_users_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'page' . '__AYNIL__' . $page['controller'] . '__AYNIL__' . $page['function']));
					break;
					case 'tools':
						add_management_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'page' . '__AYNIL__' . $page['controller'] . '__AYNIL__' . $page['function']));
					break;
					case 'settings':
						add_options_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], array($this, 'page' . '__AYNIL__' . $page['controller'] . '__AYNIL__' . $page['function']));
					break;
					default:break;
				}
			}
		}
	}

	/**
	 * Register plugin actions/filters
	 *
	 * @param array $actions
	 * @return void
	*/
	public function actions( $actions=array() )
	{
		$this->actions = $actions;
		$default =array('is_ajax'=> false, 'priority' => 10, 'accepted_args' => 1);

		foreach ($this->actions as $action){

			$action = array_merge($default, $action);

			$tag = $action['tag'];
			if($action['is_ajax']){$tag='wp_ajax_'.$tag;}

			add_action($tag, array($this, 'action' . '__AYNIL__' . $action['controller'] . '__AYNIL__' . $action['function']), $action['priority'], $action['accepted_args']);
		}
	}

	/**
	 * Handler for  pages / actions calls.
	 * It will call the correct function on the correct controller
	 * All data for this come from $_GET superglobal
	 *
	 * @return void
	 * @access public
	 */
	public function __call($name, $fargs=array())
	{
		if($this->config['use.performance.log']){
			$time = microtime(true);
			$memA = memory_get_peak_usage(true);
		}
	
		//check call type
		$type = 'page';
		if(strpos($name, 'action__AYNIL__') !== false) {
			$type = 'action';
		}

		//get needed info
		$info = explode ('__AYNIL__', $name);
		if($type == 'page' ){
			$controller_requested = $info[1];
			$function_requested = (isset($_GET['function']))? $_GET['function'] : $info[2];
			$args = (isset($_GET['fargs']))? $_GET['fargs'] : $fargs;
		}else{
			$controller_requested = $info[1];
			$function_requested = $info[2];
			$args = $fargs;
		}

		$this->status['controller.file'] = $this->path['controllers'] . DS . $controller_requested . '.php';
		$this->status['controller.name'] = $controller_requested;
		$this->status['controller.class'] = ucfirst($this->config['prefix']) . ucfirst($controller_requested);
		$this->status['controller.method'] = $function_requested;
		$this->status['controller.method.args'] = $args;
		$this->status['view.file'] = $this->path['view'] . DS . strtolower($controller_requested) . DS . $function_requested . '.php';
		$this->status['view.layout.file'] = $this->path['d_view'] . DS . 'fpl_default_layout.php';

		@ini_set('display_errors', true);
		@set_error_handler(array($this, 'errorhandler'));

		if(!file_exists($this->status['controller.file']) || !is_readable($this->status['controller.file'])) {
			$fileRelativePath = substr( $this->status['controller.file'], strpos($this->status['controller.file'], $this->status['plugin.foldername']), strlen($this->status['controller.file']));
			$this->viewSet('fileRelativePath', $fileRelativePath );
			$this->viewSet('fileName', $this->status['controller.name'] );
			$this->viewSet('fileClassName', $this->status['controller.class'] );
			$res = $this->drawView('fpl_missing_file', ($type != 'action'));
			if($type == 'action'){
				$this->errorlog[] = $res;
				add_action('wp_after_admin_bar_render', array($this, 'showErrorLog'));
			}
			@restore_error_handler();
			@ini_set('display_errors', false);
			return false;
		}

		require_once($this->status['controller.file']);

		if (!class_exists($this->status['controller.class'])){
			$fileRelativePath = substr( $this->status['controller.file'], strpos($this->status['controller.file'], $this->status['plugin.foldername']), strlen($this->status['controller.file']));
			$this->viewSet('fileRelativePath', $fileRelativePath );
			$this->viewSet('fileClassName', $this->status['controller.class'] );
			$res = $this->drawView('fpl_missing_controller', ($type != 'action'));
			if($type == 'action'){
				$this->errorlog[] = $res;
				add_action('wp_after_admin_bar_render', array($this, 'showErrorLog'));
			}
			@restore_error_handler();
			@ini_set('display_errors', false);
			return false;
		}
 
		$fpl_controllerclass = $this->status['controller.class'];
		$this->status['controller.object'] = new $fpl_controllerclass();

		if(!method_exists($this->status['controller.object'], $this->status['controller.method'])){
			$fileRelativePath = substr( $this->status['controller.file'], strpos($this->status['controller.file'], $this->status['plugin.foldername']), strlen($this->status['controller.file']));
			$this->viewSet('fileRelativePath', $fileRelativePath );
			$this->viewSet('fileClassName', $this->status['controller.class'] );
			$this->viewSet('fileFunctionName', $this->status['controller.method'] );
			$res = $this->drawView('fpl_missing_function', ($type != 'action'));
			if($type == 'action'){
				$this->errorlog[] = $res;
				add_action('wp_after_admin_bar_render', array($this, 'showErrorLog'));
			}			
			@restore_error_handler();
			@ini_set('display_errors', false);
			return false;
		}

		//set controller selected layout to de view
		if(isset($this->status['controller.object']->layout)) {
			$this->status['view.layout.file'] =$this->path['layout'] . DS . $this->status['controller.object']->layout . '.php';
		}

		if(method_exists($this->status['controller.object'], 'before_filter')) { call_user_func(array($this->status['controller.object'], 'before_filter')); }
		call_user_func_array(array($this->status['controller.object'], $this->status['controller.method']) , $this->status['controller.method.args']);
		if(method_exists($this->status['controller.object'], 'after_filter')) { call_user_func(array($this->status['controller.object'], 'after_filter')); }

		if ($type != 'action'){
			$this->drawView();
			@ob_end_flush();
		}

		@restore_error_handler();
		@ini_set('display_errors', false);


		if($this->config['use.performance.log']){
			$endtime = microtime(true);
			$memB = memory_get_peak_usage(true);

			$log = $this->sessionRead('performance.log');
			$log[]=array(
				'request' => ucfirst($type). ': ' . $this->status['controller.class'] . '/' . $this->status['controller.method'],
				'time' => round($endtime - $time, 4). ' s',
				'memory' => (($memB - $memA) / 1024) . ' Kb'
			);
			$this->sessionWrite('performance.log', $log);
		}
	}

	//------------------------------------------------------------------------------------------------------------------

	/**
	 * Save a variable to pass it to the view.
	 *
	 * @param string $varName tag for the variable
	 * @param mixed $value value of the variable passed to the view
	 * @return void

	*/
	public function viewSet ($varName, $value, $context = 'FPL')
	{
		$this->status['view.vars'][$context][$varName] = $value;
	}

	/**
	 * Set the name of the layout that will be used for draw the view
	 *
	 * @param string $layout_name name of the layout file
	 * @return void
	*/
	public function viewSetLayout ($layout_name = null)
	{
		$this->status['view.layout.file'] = $this->path['layout'] . DS . $layout_name. '.php';
	}

	/**
	 * Draw the view with the layout
	 *
	 * @return mixed: false on failure, string on $print false, void in $print true
	*/
	public function drawView ($file = null, $print = true, $context = 'FPL')
	{
		if($file){
			if(is_file($file)){
				$this->status['view.file'] = $file;
			}else{
				$this->status['view.file'] = $this->path['d_view'] . DS . $file . '.php';
			}
		}

		if(!file_exists($this->status['view.file'])){
			$fileRelativePath = substr( $this->status['view.file'], strpos($this->status['view.file'], $this->status['plugin.foldername']), strlen($this->status['view.file']));
			$this->viewSet('fileRelativePath', $fileRelativePath );
			$this->viewSet('fileFunctionName', $this->status['controller.method'] );
			$this->drawView('fpl_missing_view');
			$res = $this->drawView('fpl_missing_view', $print);
			if(!$print){
				$this->errorlog[] = $res;
				add_action('wp_after_admin_bar_render', array($this, 'showErrorLog'));
			}
			return false;
		}

		if(!file_exists($this->status['view.layout.file'])) {
			$this->status['view.layout.file'] = $this->path['d_view'] . DS . 'fpl_default_layout.php';
		}

		@ob_start();
			//import variables
			if ($this->status['view.vars'][$context]) {
				foreach ($this->status['view.vars'][$context] as $key=>$value) { $$key = $value; }
			}

			//load view
			@require_once ($this->status['view.file']);

			//save all
			$content_for_layout = @ob_get_contents();
		@ob_end_clean();


		@ob_start();
			//load layout's
			@require_once ($this->status['view.layout.file']);

			//save all
			$fpl_buffer = @ob_get_contents();
		@ob_end_clean();

		if ($print){
			echo $fpl_buffer;
		}else{
			return $fpl_buffer;
		}
	}

	//------------------------------------------------------------------------------------------------------------------

	public function sessionRead ($key, $global = null)
	{
		$id = $this->session['id']; if ($global) {$id = 'Global';}
		$session =get_option($this->session['name'] );
		$session[$id]['time'] = strtotime('now');
		update_option ($this->session['name'], $session);
		return ( isset($session[$id]['data'][$key]) )? $session[$id]['data'][$key] : null ;
	}

	public function sessionCheck ($key, $global = null)
	{
		$id = $this->session['id']; if ($global) {$id = 'Global';}
		$session = get_option($this->session['name']);
		$session[$id]['time'] = strtotime('now');
		update_option ($this->session['name'],$session);
		return  isset($session[$id]['data'][$key]);
	}

	public function sessionDelete ($key, $global = null)
	{
		$id = $this->session['id']; if ($global) {$id = 'Global';}
		$session = get_option($this->session['name']);
		$session[$id]['time'] = strtotime('now');
		unset($session[$id]['data'][$key]);
		$ses = $session;
		update_option ($this->session['name'], $ses );
	}

	public function sessionDestroy ()
	{
		$id = $this->session['id'];
		$session = get_option($this->session['name']);
		$session[$id]['time'] = strtotime('now');
		$session[$id]['data'] = array();
		update_option ($this->session['name'], $session);
		return true;
	}

	public function sessionWrite ($key, $value, $global = null)
	{
		$id = $this->session['id']; if ($global) {$id = 'Global';}
		$session = get_option($this->session['name']);
		$session[$id]['time'] = strtotime('now');
		$session[$id]['data'][$key] = $value;
		update_option ($this->session['name'], $session );
	}

	//------------------------------------------------------------------------------------------------------------------

	/**
	 * Generate wellformed css LINK tag.
	 *
	 * @param string $file Name of the file to load
	 * @param array $args Options for the tag
	 * @return String
	*/
	public function css ($file, $args=array())
	{
		$url = $this->path['css_url'] . '/' . $file;
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
		$url = $this->path['js_url'] . '/' . $file;
		return "<script type='text/javascript' language='javascript' src='{$url}'></script>";
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
		$opt ='';
		foreach($args as $key =>$value) {
			$opt .= ' '.$key.'=\''.$value.'\'';
		}

		$href = $this->router($url);

		return "<a href='{$href}' {$opt} >{$title}</a>";
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
		$opt ='';
		foreach($args as $key =>$value) {
			$opt .= ' '.$key.'=\''.$value.'\'';
		}

		$url = $this->path['img_url'] . '/' . $file;
		return "<img src='{$url}' {$opt}/>";
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

		$opt ='';
		foreach($options as $key =>$value) {
			$opt .= ' '.$key.'=\''.$value.'\'';
		}

		$action = $this->router($url);

		return "<form action='{$action}' {$opt}/>";
	}

	//------------------------------------------------------------------------------------------------------------------

	/**
	 * Perform a import of a file on lib folder
	 *
	 * @param string $name the place for redirect
	 * @return void
	*/
	public function import ($name, $return_path=null)
	{
		$file =  $this->path['lib'] . DS . $name;
		$default_file =  $this->path['d_lib'] . DS . $name;

		if(file_exists ($file) ) {
			if ($return_path) { return $file; }
			return require_once($file);
		}elseif(file_exists ($default_file)){
			if ($return_path) { return $default_file; }
			return require_once($default_file);
		}

		return false;
	}

	/**
	 * Perform a redirect using headers
	 *
	 * @param array $url The place for redirect
	 * @return void
	*/
	public function redirect ($url=array())
	{
		@ob_end_clean();

		$url = $this->router($url);

		if($this->config['use.performance.log']){
			$log = $this->sessionRead('performance.log');
			$log[]=array(
				'request' => 'redirect'. ': ' . $url,
				'time' => 'x',
				'memory' => 'x',
			);
			$this->sessionWrite('performance.log', $log);
		}	

		$href = html_entity_decode($url);

		header('HTTP/1.1 302 Found', true);
		header('Status: 302 Found', true);
		header('Location: ' . $href);
		exit;
	}

	/**
	 * Create an URL to a controller or resource using a "place" array
	 *
	 * @param array $url place for the href
	 * @return string
	*/
	public function router ($url=array())
	{
		//string pased, nothing to do
		if (!is_array($url)){
			return $url;
		}

		//complete $url
		$defaults = array('menu_type' => null, 'controller' => null, 'function'=> null, 'params'=> '');
		$url = array_merge($defaults, $url);

		//search menu slug
		if($url['controller']){
			$aux_slug = $this->config['prefix'] . '-' . $url['controller'];
			foreach ($this->pages as $type => $pages){
				for($i=0; $i<count($pages); $i++){
					if (strpos($pages[$i]['menu.slug'], $aux_slug) !== false) {
						$url['menu.slug'] = $pages[$i]['menu.slug'];
					}
				}
			}
		}

		//correct values
		$url['controller'] = ($url['controller'])? $url['menu.slug'] : $_GET['page'];
		$url['function'] = ($url['function'])?'&amp;function='.$url['function']:'';

		//parameter to the funcion
		foreach($url as $key =>$value) {
			if(preg_match("/^[[:digit:]]+$/", $key)) { $url['params'].='&amp;fargs[]='.urlencode($value); }
		}

		$wpurl = get_bloginfo('wpurl');
		switch ($url['menu_type']){
			case 'menu':		$base = $wpurl.'/wp-admin/admin.php?'; break;
			case 'dashboard':	$base = $wpurl.'/wp-admin/index.php?'; break;
			case 'posts':		$base = $wpurl.'/wp-admin/edit.php?'; break;
			case 'media':		$base = $wpurl.'/wp-admin/upload.php?'; break;
			case 'links':		$base = $wpurl.'/wp-admin/link-manager.php?'; break;
			case 'pages':		$base = $wpurl.'/wp-admin/edit.php?post_type=page&'; break;
			case 'comments':	$base = $wpurl.'/wp-admin/edit-comments.php?'; break;
			case 'appearance':	$base = $wpurl.'/wp-admin/themes.php?'; break;
			case 'plugins':		$base = $wpurl.'/wp-admin/plugins.php?'; break;
			case 'users':		$base = $wpurl.'/wp-admin/users.php?'; break;
			case 'tools':		$base = $wpurl.'/wp-admin/tools.php?'; break;
			case 'settings':	$base = $wpurl.'/wp-admin/options-general.php?'; break;
			default: 			$base = $_SERVER['PHP_SELF'].'?'; break;
		}

		return $base . 'page=' . $url['controller'] . $url['function'] . $url['params'];
	}

	public function errorhandler($level, $message, $file, $line, $context)
	{
		//escape reports with @
		if( 0 == ini_get( "error_reporting" ) || 0 == error_reporting() ){
			return;
		}

		//solo informar error si  $file se encuentra en el path de este plugin
		if(strpos($file, $this->status['plugin.fullpath']) === false){
			return;
		}

		$this->errorlog[]=
		'<div style="padding:10px; margin:5px; width: 600px; color:565656; border:solid 1px #b6b6b6; background-color: #FFFFE0;">' .
		'<div style="font-size:16px; margin-bottom:10px;">' .$message . '</div>' .
		'<div style="font-size:14px;"> In <b>' . $file . '</b></div>' .
		'<div style="font-size:14px;"> On line <b>' . $line . '</b></div>' .
		'</div>';

		add_action('wp_after_admin_bar_render', array($this, 'showErrorLog'));
		return true; 
	}

	public function showErrorLog()
	{
		$log = join("\n", $this->errorlog);
		echo $log;
		$this->errorlog = array();
	}

}//end class

//Export framework className
$GLOBALS["FramePress"] = 'FramePress_001';
$FramePress = 'FramePress_001';

}//end if class exists
