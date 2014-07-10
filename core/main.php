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
if (!class_exists('FramePress_010')) {
class FramePress_010
{
	public $config = array(
		'prefix' => null,
		'use.tmp' => false,
		'use.i18n' => true,
		'use.session' => true,
		'performance.log' => false,
		'debug' => false,
	);

	public $status = array (
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

	public $shortcodes = array();

	public $errorlog = array();

	/**
	 * Constructor.
	 *
	 * @param string $main_file Name of the main file
	 * @param string $config user configuration
	*/
	public function __construct($main_file, $config = array() )
	{
		$fullpath = dirname($main_file);
		$foldername = basename(dirname($main_file));

		//set paths
		$this->paths = array (
			'plugin' => $fullpath,
			'core' => $fullpath . DS . 'core',
			'controllers' => $fullpath . DS . 'controllers',
			//'view' => $fullpath . DS . 'views',
			//'d_view' => $fullpath . DS . 'core' . DS . 'defaults' . DS . 'views',
			//'layout' => $fullpath . DS . 'views' . DS . 'layouts',
			'lib' => $fullpath . DS . 'lib',
			'lang' => $fpl_foldername . DS . 'languages',
			'tmp' => $fullpath . DS . 'tmp',
			'resources' => $fullpath . DS . 'resources',
			'img' => $fullpath . DS . 'resources' . DS . 'img',
			'img_url' => get_bloginfo( 'wpurl' ) . '/wp-content/plugins/' . $fpl_foldername . '/resources/img',
			'css' => $fullpath . DS . 'resources' . DS . 'css',
			'css_url' => get_bloginfo( 'wpurl' ) . '/wp-content/plugins/' . $fpl_foldername . '/resources/css',
			'js' => $fullpath . DS . 'resources' . DS . 'js',
			'js_url' => get_bloginfo( 'wpurl' ). '/wp-content/plugins/' . $fpl_foldername . '/resources/js',
		);


		//~ $load_path = array(
			//~ 'core' => $this->path['core'] . DS,
			//~ 'controllers' => $this->path['controllers'] . DS,
			//~ 'lib' => $this->path['lib'] . DS,
		//~ )
//~
		//~ set_include_path(get_include_path().PATH_SEPARATOR.'path/to/my/directory/');
		//~ spl_autoload_extensions('.fp.php,.php,.inc');
		//~ spl_autoload_register();

		register_shutdown_function (array($this, 'errorhandler'));


		//set partial status
		$this->status = array_merge($this->status, array(
			'plugin.fullpath' => $fullpath,
			'plugin.foldername' => $fpl_foldername,
			'plugin.mainfile' => basename($main_file),
		));



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
				$tempPath = $this->path['tmp'];
				if(!is_writable( $this->path['tmp'] ) ){
					trigger_error("Can&#39;t write on <b>" . $this->path['tmp'] . "</b> folder, please change it's permissions to 777", E_USER_WARNING);
				}
			}
			//set TMP folder to use
			$this->path['systmp'] = ($tempPath)?$tempPath:$this->path['tmp'];
		}

		if ($this->config['performance.log']) {
			$this->config['use.session'] = true;
		}

		//Configure sessions
		if ($this->config['use.session']) {

			//get session ID from cookies
			$id = null;
			if(!isset($_COOKIE)) {$_COOKIE = array();}
			foreach ($_COOKIE as $key => $value) {
				if(preg_match("/^framepress_session_id_(.)*$/", $key)) { $id = md5($value); break; }
				elseif(preg_match("/^wordpress_logged_in_(.)*$/", $key)) { $id = md5($value); break; }
			}
			if (!$id){
				$name = uniqid('framepress_session_id_', true);
				$value = uniqid(base64_encode(time() . rand() ), true);
				setcookie ($name, $value, time()+$this->session['time'], '/', null, false, true );
				$id = md5($value);
			}

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

		if ($this->config['performance.log']){
			if(!$this->sessionCheck('performance.log')) {
				$this->sessionWrite('performance.log', array());
			}
			add_action('in_admin_footer', array($this, 'showPerformanceLog'));
			add_action('wp_footer', array($this, 'showPerformanceLog'));
		}

		//Load languages
		if ($this->config['use.i18n']) {
			add_action('init', array($this, 'load_languages'));
		}

		//Register activation and deactivation functions
		register_activation_hook($this->status['plugin.foldername'] . DS . $this->status['plugin.mainfile'], array($this,'activation'));
		register_deactivation_hook($this->status['plugin.foldername'] . DS . $this->status['plugin.mainfile'], array($this, 'deactivation'));

		//Capture output
		add_action('init', array($this, 'capture_output'));

		//user defined actions
		do_action($this->config['prefix'] . '_framepress_creation' );
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
		do_action($this->config['prefix'] . '_activation' );
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

		do_action($this->config['prefix'] . '_deactivation' );
	}

	/**
	 * Call deactivation function
	 *
	 * @return void
	*/
	public function uninstall ()
	{
		if($this->session['name']){
			delete_option($this->session['name']);
		}

		do_action($this->config['prefix'] . '_uninstall' );
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
	public function adminPages( $pages=array() )
	{
		$this->pages = $pages;

		//add/calculate default/missing info
		foreach ($this->pages as $type => $pages){
			for($i=0; $i<count($pages); $i++){

				$page_defaults = array('page.title'=> null, 'menu.title'=> null, 'capability'=> null, 'controller'=> null, 'function'=>'index', 'parent'=> null, 'icon'=> null, 'position'=> null);
				$page = array_merge($page_defaults, $pages[$i]);

				//generate url for image selected
				if ( $page['icon'] && file_exists( $this->path['img'] . DS . $page['icon'])) {
					$page['icon'] =  $this->path['img_url'] . DS . $page['icon'];
				}

				//magic!
				$page['menu.slug'] = $this->config['prefix'] . '-' . $page['controller'] . '-' . $page['function'];
				if ($page['parent']){
					$menus = (isset($this->pages['menu']))?$this->pages['menu']:array();
					for ($p=0; $p < count($menus); $p++){
						if( $menus[$p]['menu.title'] == $page['parent'] ) {
							$page['parent.slug'] = $this->config['prefix'] . '-' . $menus[$p]['controller']. '-' . $menus[$p]['function'];
							break;
						}
					}
					if(!isset($page['parent.slug'] )){ $page['parent.slug'] = $page['parent']; }
				}
				$this->pages[$type][$i] = $page;
			}
		}

		add_action('admin_menu', array($this, 'addAdminPagesReal'));
	}

	/**
	 * Create plugin admin pages
	 *
	 * @param array $pages
	 * @return void
	*/
	public function addAdminPagesReal ()
	{
		foreach ($this->pages as $type => $pages){
			for($i=0; $i<count($pages); $i++){

				$page = $pages[$i];

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
	 * Register plugin short codes
	 *
	 * @param array $actions
	 * @return void
	*/
	public function shortcodes( $shortcodes=array() )
	{
		$this->shortcodes = $shortcodes;
		foreach ($this->shortcodes as $sc){

			$short_defaults = array('tag'=> null, 'controller'=> null, 'function'=> null, 'recursion'=> null);
			$sc = array_merge($short_defaults, $sc);

			$recursion = ($sc['recursion'])? 'recursion' : '';

			add_shortcode($sc['tag'] , array($this, 'shortcode' . '__AYNIL__' . $sc['controller'] . '__AYNIL__' . $sc['function'] . '__AYNIL__' . $recursion));
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

			if (!$action['is_ajax']) {
				add_action($tag, array($this, 'action' . '__AYNIL__' . $action['controller'] . '__AYNIL__' . $action['function']), $action['priority'], $action['accepted_args']);
			}

			if( in_array($action['is_ajax'], array('both', 'private')) ){
				add_action( 'wp_ajax_' . $tag, array($this, 'action' . '__AYNIL__' . $action['controller'] . '__AYNIL__' . $action['function']), $action['priority'], $action['accepted_args']);
			}

			if( in_array($action['is_ajax'], array('both', 'public')) ){
				add_action( 'wp_ajax_nopriv_' . $tag, array($this, 'action' . '__AYNIL__' . $action['controller'] . '__AYNIL__' . $action['function']), $action['priority'], $action['accepted_args']);
			}
		}
		return true;
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
		if($this->config['performance.log']){
			$time = microtime(true);
			$memA = memory_get_peak_usage(true);
		}

		//parse info
		$info = explode ('__AYNIL__', $name);

		//check call type
		$type = $info[0];

		//get needed info
		if($type == 'page' ){
			$controller_requested = $info[1];
			$function_requested = (isset($_GET['function']))? $_GET['function'] : $info[2];
			$args = (isset($_GET['fargs']))? $_GET['fargs'] : $fargs;
		}else{
			$controller_requested = $info[1];
			$function_requested = $info[2];
			$args = $fargs;
		}

		//set call status
		$this->status['controller.file'] = $this->path['controllers'] . DS . $controller_requested . '.php';
		$this->status['controller.name'] = $controller_requested;
		$this->status['controller.class'] = ucfirst($this->config['prefix']) . ucfirst($controller_requested);
		$this->status['controller.method'] = $function_requested;
		$this->status['controller.method.args'] = $args;
		$this->status['view.file'] = $this->path['view'] . DS . strtolower($controller_requested) . DS . $function_requested . '.php';
		$this->status['view.layout.file'] = $this->path['d_view'] . DS . 'fpl_default_layout.php';

		@ini_set('display_errors', false);
		@set_error_handler(array($this, 'errorhandler'));

		//check controller file
		if(!file_exists($this->status['controller.file']) || !is_readable($this->status['controller.file'])) {
			return $this->callErrorHandler($type, 'fpl_missing_file');
		}

		//import controller
		require_once($this->status['controller.file']);
		if (!class_exists($this->status['controller.class'])){
			return $this->callErrorHandler($type, 'fpl_missing_controller');
		}

		//create the controller object
		$fpl_controllerclass = $this->status['controller.class'];
		$this->status['controller.object'] = new $fpl_controllerclass();
		if(!method_exists($this->status['controller.object'], $this->status['controller.method'])){
			return $this->callErrorHandler($type, 'fpl_missing_function');
		}

		//set controller's selected layout to the view
		if(isset($this->status['controller.object']->layout)) {
			$this->status['view.layout.file'] =$this->path['layout'] . DS . $this->status['controller.object']->layout . '.php';
		}

		//make the call
		if(method_exists($this->status['controller.object'], 'before_filter')) { call_user_func(array($this->status['controller.object'], 'before_filter')); }
		$call_return = call_user_func_array(array($this->status['controller.object'], $this->status['controller.method']) , $this->status['controller.method.args']);
		if(method_exists($this->status['controller.object'], 'after_filter')) { call_user_func(array($this->status['controller.object'], 'after_filter')); }

		if ($type == 'page'){
			$this->drawView();
			@ob_end_flush();
		}

		@restore_error_handler();

		if($this->config['performance.log']){
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

		if($type == 'shortcode' ){

			//fix for wpautop, that add <p> and <br> to the shortcode content
			global $wp_filter;
			foreach($wp_filter['the_content'] as $priority => $value ){
				if(isset($value['wpautop'])){
					unset($wp_filter['the_content'][$priority]['wpautop']);
				}
			}
			//recursion
			if($info[3] == 'recursion' ){
				$call_return = do_shortcode($call_return);
			}
			return $call_return;
		}

		//filters can return things
		if($type == 'action' ){
			return $call_return;
		}
	}



	/**
	 * Perform a import of a file on lib folder
	 *
	 * @param string $name the place for redirect
	 * @return void
	*/
	public function import ($name)
	{
		$file =  $this->path['lib'] . DS . $name;
		$default_file =  $this->path['d_lib'] . DS . $name;

		if(file_exists ($file) ) {
			return require_once($file);
		}elseif(file_exists ($default_file)){
			return require_once($default_file);
		}

		return false;
	}

	/**
	 * Perform a import of a file on lib folder
	 *
	 * @param string $name the place for redirect
	 * @return void
	*/
	public function load ($name, $args = null)
	{
		$trimedName =  rtrim($name, '.php');
		$globalClassName = ucfirst(basename($trimedName));

		$file =  $this->path['lib'] . DS . $trimedName . '.php';
		$default_file =  $this->path['d_lib'] . DS . $trimedName . '.php';


		if(file_exists ($file)) {
			require_once($file);
		} elseif (file_exists ($default_file)){
			require_once($default_file);
		} else {
			return false;
		}


		global $$globalClassName;
		$className = $$globalClassName;

		if(method_exists($className, 'fpGetInstance')){
			return $className::fpGetInstance($this, $args);
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

		if($this->config['performance.log']){
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
						if(!$url['menu_type']){ $url['menu_type'] = $type; }
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

	private function callErrorHandler ($type, $view = null, $fileRelativePath = null, $print_now = true )
	{
		if(!$fileRelativePath) {
			$fileRelativePath = substr( $this->status['controller.file'], strpos($this->status['controller.file'], $this->status['plugin.foldername']));
		}

		$this->errorlog[]= array(
			'level' => $this->mapErrorCode(E_USER_WARNING),
			'message' =>$view. ' - controller name: '. $this->status['controller.name'] . ' class: ' . $this->status['controller.class'] . ' method: ' .  $this->status['controller.method'],
			'file'=>$fileRelativePath,
			'line' => 0
		);

		if($this->config['debug'] ) {

			if(in_array($type , array('action', 'shortcode'))) {

					add_action('wp_after_admin_bar_render', array($this, 'showErrorLog'));
					add_action('the_content', array($this, 'showErrorLog'));

			} else {

				$this->viewSet('fileRelativePath', $fileRelativePath );
				$this->viewSet('fileName', $this->status['controller.name'] );
				$this->viewSet('fileClassName', $this->status['controller.class'] );
				$this->viewSet('fileFunctionName', $this->status['controller.method'] );

				$view =  $this->drawView($view, $print_now);

				@restore_error_handler();
				@ini_set('display_errors', false);
				return $view;
			}
		}

		@restore_error_handler();
		@ini_set('display_errors', false);
		return false;
	}

	public function errorhandler($level = null, $message=null, $file= null, $line = null, $context=null)
	{
		$e = error_get_last();
		$print_now = false;

		if (!$level && !$e) { //shotdown running and nothing found
			return true;
		} else if (!$level && $e) {
			$print_now = true;
			$level = $e['type']; $message=$e['message']; $file= $e['file']; $line = $e['line'];
		}

		//escape reports with @
		if( 0 == ini_get( "error_reporting" ) || 0 == error_reporting() ){
			return;
		}

		//solo informar error si  $file se encuentra en el path de este plugin
		if(strpos($file, $this->status['plugin.fullpath']) === false){
			return;
		}
		$this->errorlog[]= array('level' => $this->mapErrorCode($level),  'message' =>$message, 'file'=> $file, 'line' => $line);

		if ($print_now) {
			$this->showErrorLog();
		} else {
			add_action('wp_after_admin_bar_render', array($this, 'showErrorLog'));
			add_action('wp_footer', array($this, 'showErrorLog'));
		}
		return true;
	}

	public function mapErrorCode($code) {
		$error =  null;
		switch ($code) {
			case E_PARSE:
			case E_ERROR:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
				$error = 'Error';
				break;
			case E_WARNING:
			case E_USER_WARNING:
			case E_COMPILE_WARNING:
			case E_RECOVERABLE_ERROR:
				$error = 'Warning';
				break;
			case E_NOTICE:
			case E_USER_NOTICE:
				$error = 'Notice';
				break;
			case E_STRICT:
				$error = 'Strict';
				break;
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				$error = 'Deprecated';
				break;
		}
		return $error;
	}

	public function showErrorLog()
	{
		if(!$this->config['debug'] || !$this->errorlog) {
			return;
		}

		echo '<div style="margin:0 auto; width:960px; padding-top: 50px;">';
		foreach ($this->errorlog as $e ){

			if ($e['level'] == 'Error') { $color = '#FFF3F7' ; }
			elseif ($e['level'] == 'Warning') { $color = '#FFFFF3' ; }
			elseif ($e['level'] == 'Notice') { $color = '#F4F3FF' ; }
			elseif ($e['level'] == 'Strict') { $color = '#F9F9F9' ; }
			elseif ($e['level'] == 'Deprecated') { $color = '#F9F9F9' ; }

			echo '<div style="padding:10px; margin:15px; color:565656; border-left:solid 3px #1E90FF; background-color: '.$color.';">' .
			'<div style="font-size:16px; margin-bottom:10px;">' .$e['message'] . '</div>' .
			'<div style="font-size:14px;"> In <b>' . $e['file'] . '</b></div>' .
			'<div style="font-size:14px;"> On line <b>' . $e['line'] . '</b></div>' .
			'</div>';
		}
		echo '</div>';
		$this->errorlog = array();

	}

	public function showPerformanceLog ()
	{
		$log = $this->sessionRead('performance.log');
		$this->sessionWrite('performance.log', array());
		echo '<script>jQuery("#wpfooter").css("position", "relative")</script>';
		foreach($log as $l){ echo '<div style="margin: 10px 0; font: 16px bold;">'.join(' -- ', $l).'</div>'; }
		echo '<br>';
	}

}//end class

}//end if class exists

//Export framework className
$GLOBALS["FramePress"] = 'FramePress_010';
$FramePress = 'FramePress_010';
