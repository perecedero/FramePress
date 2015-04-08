<?php

//define core class
if (!class_exists('FramePress_WordPress_002')) {
class FramePress_WordPress_002
{

	public $pages = array();

	public $hooks = array();

	public $shortcodes = array();

	public $metaboxes = array();

	public $Core = null;

	public function __construct(&$fp)
	{
		$this->Core = $fp;

		add_action('admin_menu', array($this, 'addAdminPagesReal'));
		add_action('add_meta_boxes', array($this, 'addMetaboxesReal'));
	}


	public function addAdminPage($type, $page = array())
	{
		$this->adminPages(array($type => array($page)));
	}

	public function adminPages($pages = array())
	{
		foreach ($pages as $type => $childs) {
			for ($i=0; $i<count($childs); $i++) {
				$page = $this->populatePage($childs[$i]);
				$this->pages[$type][] = $page;
			}
		}
	}

	public function addAdminPagesReal()
	{
		foreach ($this->pages as $type => $pages){
			for($i=0; $i<count($pages); $i++){

				$page = $pages[$i];

				switch($type) {
					case 'menu':
						add_menu_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $page['callback'], $page['icon'], $page['position'] );
					break;
					case 'submenu':
						add_submenu_page( $page['parent.slug'], $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $page['callback']);
					break;
					case 'dashboard':
						add_dashboard_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $page['callback']);
					break;
					case 'posts':
						add_posts_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $page['callback']);
					break;
					case 'media':
						add_media_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $page['callback']);
					break;
					case 'links':
						add_links_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $page['callback']);
					break;
					case 'pages':
						add_pages_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $page['callback']);
					break;
					case 'comments':
						add_comments_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $page['callback']);
					break;
					case 'appearance':
						add_theme_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $page['callback']);
					break;
					case 'plugins':
						add_plugins_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $page['callback']);
					break;
					case 'users':
						add_users_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $page['callback']);
					break;
					case 'tools':
						add_management_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $page['callback']);
					break;
					case 'settings':
						add_options_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $page['callback']);
					break;
					default:break;
				}
			}
		}
	}

//-----------------------------------------------------------------------------------------------------

	public function addShortcode($shortcode)
	{
		$this->shortcodes(array($shortcode));
	}

	public function shortcodes( $shortcodes=array() )
	{
		foreach ($shortcodes as $sc){
			$this->shortcodes[] = $sc = $this->populateShortcode($sc);
			add_shortcode($sc['tag'] , $sc['callback']);
		}
	}

//-----------------------------------------------------------------------------------------------------

	public function addAction($action)
	{
		$this->hooks(array($action));
	}

	public function addFilter($filter)
	{
		$this->hooks(array($filter));
	}

	public function hooks( $hooks=array() )
	{
		foreach ($hooks as $hook) {

			$this->hooks[] = $hook = $this->populateHook($hook);
			foreach ($hook['callback'] as $tag => $callback) {

				if (!$hook['is_ajax']) {
					add_action($tag, $callback, $hook['priority'], $hook['accepted_args']);
				} else {

					if (in_array($hook['is_ajax'], array(true, 'both', 'private'))) {
						add_action('wp_ajax_' . $tag, $callback, $hook['priority'], $hook['accepted_args']);
					}

					if (in_array($hook['is_ajax'], array(true, 'both', 'public'))) {
						add_action('wp_ajax_nopriv_' . $tag, $callback, $hook['priority'], $hook['accepted_args']);
					}
				}
			}
		}
	}

//-----------------------------------------------------------------------------------------------------

	public function addMetabox($metabox)
	{
		$this->metaboxes(array($metabox));
	}

	public function metaboxes ($metaboxes = array())
	{
		$metab_defaults = array('id'=> null, 'title'=> null, 'post_type'=> null, 'context'=> 'advanced', 'priority'=>null, 'callback_args' => null, 'controller'=> null, 'function'=> null);

		foreach ($metaboxes as $mb){
			$mb = $this->populateMetabox($mb);
			$this->metaboxes[] = $mb;
		}
	}

	public function addMetaboxesReal ($postType)
	{
		foreach ($this->metaboxes as $mb){
			if(in_array($postType, (array)$mb['post_type'])){
				add_meta_box( $mb['id'], $mb['title'], $mb['callback'], $postType, $mb['context'], $mb['priority'], $mb['callback_args'] );
			}
		}
	}

	//---------------------------------------

	public function router ($url=array())
	{
		//string pased, nothing to do
		if (!is_array($url)){ return $url; }

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

	private function populateHook($hook)
	{
		//populate missing/default info
		$hook_defaults = array('tag'=> null, 'controller' => null, 'function' => null, 'is_ajax'=> false, 'priority' => 10, 'accepted_args' => 1);
		$hook = array_merge($hook_defaults, $hook);

		if (!is_array($hook['tag'])) {
			$hook['tag'] = (array)$hook['tag'];
		}

		foreach ($hook['tag'] as $t) {
			$hook['callback'][$t] = array($this->Core->Dispatcher, 'hook' . '__AYNIL__' . $hook['controller'] . '__AYNIL__' . $hook['function'] . '__AYNIL__' . $t);
		}

		return $hook;
	}

	private function populateMetabox($mb)
	{
		$metabox_defaults = array('id'=> null, 'title'=> null, 'post_type'=> null, 'context'=> 'advanced', 'priority'=>null, 'callback_args' => null, 'controller'=> null, 'function'=> null);
		$mb = array_merge($metabox_defaults, $mb);
		$mb['callback'] = array($this->Core->Dispatcher, 'metabox' . '__AYNIL__' . $mb['controller'] . '__AYNIL__' . $mb['function'] . '__AYNIL__' . $mb['id']);
		return $mb;
	}

	private function populateShortcode($sc)
	{
		$short_defaults = array('tag'=> null, 'controller'=> null, 'function'=> null);
		$sc = array_merge($short_defaults, $sc);
		$sc['callback'] =  array($this->Core->Dispatcher, 'shortcode' . '__AYNIL__' . $sc['controller'] . '__AYNIL__' . $sc['function'] . '__AYNIL__' . $sc['tag']);
		return $sc;
	}

	private function populatePage($page= array())
	{
		//populate missing/default info
		$page_defaults = array('page.title'=> null, 'menu.title'=> null, 'capability'=> null, 'controller'=> null, 'function'=>'index', 'parent'=> null, 'icon'=> null, 'position'=> null);
		$page = array_merge($page_defaults, $page);

		//generate url for image selected
		if ( $page['icon'] && file_exists( $this->Core->paths['img'] . DS . $page['icon'])) {
			$page['icon'] =  $this->Core->paths['img.url'] . DS . $page['icon'];
		}

		//generate slug for this menu
		$page['menu.slug'] = sanitize_title($this->Core->config['prefix'] . ' ' . $page['menu.title']);

		//find parent menu
		if ($page['parent']){
			$menus = (isset($this->pages['menu']))?$this->pages['menu']:array();
			for ($p=0; $p < count($menus); $p++){
				if( $menus[$p]['menu.title'] == $page['parent'] ) {
					$page['parent.slug'] = sanitize_title($this->Core->config['prefix'] . ' ' . $menus[$p]['menu.title']);
					break;
				}
			}
			if(!isset($page['parent.slug'] )){ $page['parent.slug'] = $page['parent']; }
		}

		//callback
		$page['callback'] =  array($this->Core->Dispatcher, 'adminpage' . '__AYNIL__' . $page['controller'] . '__AYNIL__' . $page['function'] . '__AYNIL__' . $page['menu.slug']);

		return $page;
	}


}//end class
}//end if class exist

//Export framework className
$GLOBALS["FramePressWordPress"] = 'FramePress_WordPress_002';
$FramePressWordPress = 'FramePress_WordPress_002';
