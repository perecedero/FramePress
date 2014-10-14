<?php

//define core class
if (!class_exists('FramePress_WordPress_001')) {
class FramePress_WordPress_001
{

	public $pages = array();

	public $hooks = array();

	public $shortcodes = array();

	public $metaboxes = array();

	public $Core = null;

	public function __construct(&$fp)
	{
		$this->Core = $fp;
	}

	/**
	 * Register admin pages
	 *
	 * @param array $pages
	 * @return void
	 * @link http://codex.wordpress.org/Administration_Menus
	*/
	public function adminPages( $pages=array() )
	{
		$this->pages = $pages;

		//Populate page with default/missing/computed info
		foreach ($this->pages as $type => $pages){
			for($i=0; $i<count($pages); $i++){

				//populate missing/default info
				$page_defaults = array('page.title'=> null, 'menu.title'=> null, 'capability'=> null, 'controller'=> null, 'function'=>'index', 'parent'=> null, 'icon'=> null, 'position'=> null);
				$page = array_merge($page_defaults, $pages[$i]);

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
							$page['parent.slug'] = sanitize_title($this->Core->config['prefix'] . ' ' . $menus[$p]['page.title']);
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
	 * Add admin pages
	 *
	 * @param array $pages
	 * @return void
	 * @link http://codex.wordpress.org/Administration_Menus
	*/
	public function addAdminPagesReal ()
	{
		foreach ($this->pages as $type => $pages){
			for($i=0; $i<count($pages); $i++){

				$page = $pages[$i];
				$callback =  array($this->Core->Dispatcher, 'adminpage' . '__AYNIL__' . $page['controller'] . '__AYNIL__' . $page['function'] . '__AYNIL__' . $page['menu.slug']);

				switch($type) {
					case 'menu':
						add_menu_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $callback, $page['icon'], $page['position'] );
					break;
					case 'submenu':
						add_submenu_page( $page['parent.slug'], $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $callback);
					break;
					case 'dashboard':
						add_dashboard_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $callback);
					break;
					case 'posts':
						add_posts_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $callback);
					break;
					case 'media':
						add_media_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $callback);
					break;
					case 'links':
						add_links_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $callback);
					break;
					case 'pages':
						add_pages_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $callback);
					break;
					case 'comments':
						add_comments_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $callback);
					break;
					case 'appearance':
						add_theme_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $callback);
					break;
					case 'plugins':
						add_plugins_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $callback);
					break;
					case 'users':
						add_users_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $callback);
					break;
					case 'tools':
						add_management_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $callback);
					break;
					case 'settings':
						add_options_page( $page['page.title'], $page['menu.title'], $page['capability'], $page['menu.slug'], $callback);
					break;
					default:break;
				}
			}
		}
	}

//-----------------------------------------------------------------------------------------------------

	/**
	 * Register short codes
	 *
	 * @param array $actions
	 * @return void
	 * @link http://codex.wordpress.org/Function_Reference/add_shortcode
	*/
	public function shortcodes( $shortcodes=array() )
	{
		$this->shortcodes = $shortcodes;
		$short_defaults = array('tag'=> null, 'controller'=> null, 'function'=> null);

		foreach ($this->shortcodes as $sc){

			//populate missing/default info
			$sc = array_merge($short_defaults, $sc);

			// the name will give dispacher the info to know the responsable
			// of handle the request: type | controller | function
			$callback =  array($this->Core->Dispatcher, 'shortcode' . '__AYNIL__' . $sc['controller'] . '__AYNIL__' . $sc['function'] . '__AYNIL__' . $sc['tag']);

			add_shortcode($sc['tag'] , $callback);
		}
	}

	/**
	 * Register plugin hooks
	 *
	 * @param array $hooks
	 * @return void
	 * @link http://codex.wordpress.org/Function_Reference/add_action
	 * @link http://codex.wordpress.org/Function_Reference/add_filter
	*/
	public function hooks( $hooks=array() )
	{
		$this->hooks = $hooks;
		$hook_defaults =array('is_ajax'=> false, 'priority' => 10, 'accepted_args' => 1);

		foreach ($this->hooks as $hook){

			//populate missing/default info
			$hook = array_merge($hook_defaults, $hook);

			$tag = $hook['tag'];

			// the name will give dispacher the info to know the responsable
			// of handle the request: type | controller | function
			$callback =  array($this->Core->Dispatcher, 'hook' . '__AYNIL__' . $hook['controller'] . '__AYNIL__' . $hook['function'] . '__AYNIL__' . $hook['tag']);

			if (!$hook['is_ajax']) {
				add_action($tag, $callback, $hook['priority'], $hook['accepted_args']);
			} else {

				if( in_array($hook['is_ajax'], array(true, 'both', 'private')) ){
					add_action( 'wp_ajax_' . $tag, $callback, $hook['priority'], $hook['accepted_args']);
				}

				if( in_array($hook['is_ajax'], array(true, 'both', 'public')) ){
					add_action( 'wp_ajax_nopriv_' . $tag, $callback, $hook['priority'], $hook['accepted_args']);
				}
			}
		}
		return true;
	}


	/**
	 * Register metaboxes
	 *
	 * @param array $metaboxes
	 * @return void
	 * @link http://codex.wordpress.org/Function_Reference/add_meta_box
	*/
	public function metaboxes ($metaboxes = array())
	{
		$metab_defaults = array('id'=> null, 'title'=> null, 'post_type'=> null, 'context'=> 'advanced', 'priority'=>null, 'callback_args' => null, 'controller'=> null, 'function'=> null);

		foreach ($metaboxes as $mb){
			//populate missing/default info
			$mb = array_merge($metab_defaults, $mb);
			$this->metaboxes[] = $mb;
		}

		add_action('add_meta_boxes', array($this, 'addMetaboxesReal'));
	}

	/**
	 * Add metaboxes
	 *
	 * @param string $posttype: posttype begin edited
	 * @return void
	 * @link http://codex.wordpress.org/Function_Reference/add_meta_box
	*/
	public function addMetaboxesReal ($postType)
	{
		foreach ($this->metaboxes as $mb){
			$callback =  array($this->Core->Dispatcher, 'metabox' . '__AYNIL__' . $mb['controller'] . '__AYNIL__' . $mb['function'] . '__AYNIL__' . $mb['id']);
			if(in_array($postType, (array)$mb['post_type'])){
				add_meta_box( $mb['id'], $mb['title'], $callback, $postType, $mb['context'], $mb['priority'], $mb['callback_args'] );
			}
		}
	}

}//end class
}//end if class exist

//Export framework className
$GLOBALS["FramePressWordPress"] = 'FramePress_WordPress_001';
$FramePressWordPress = 'FramePress_WordPress_001';
