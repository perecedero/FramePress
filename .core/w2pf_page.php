<?php

/*
	WordPress Framework, Page class v1.0
	developer: Perecedero (Ivan Lansky) perecedero@gmail.com
*/

class w2pf_page_test {

	var $pages= array();
	var $view= null;
	var $path= null;
	var $config= null;

	function __construct($path, $view, $config)
	{
		$this->path = &$path;
		$this->view = &$view;
		$this->config = &$config;
	}

	function add ($pages = null)
	{
		$this->pages = $pages;
		add_action('admin_menu', array($this, 'wpf_page_add_pages_real'));
	}

	function wpf_page_add_pages_real ()
	{
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

	function call($name)
	{
		$page = str_replace($this->config->read('prefix') . '_','', $_GET['page']);
		$function = (isset($_GET['function']))?$_GET['function']:'index';
		$args = (isset($_GET['fargs']))?$_GET['fargs']:array();

		if(file_exists($file = $this->path->Dir['PAGES'] . $this->path->DS . $page.'.php'))
		{
			require_once ($file);
			if(function_exists($function))
			{
				$this->view->page=$page;
				$this->view->view_name=$function;
				call_user_func_array($function, $args);
				$this->view->draw();
			}
			else
			{
				$this->view->vars=array('path'=> $file, 'file'=>$page.'.php', 'function'=> $function);
				$this->view->draw_error('missing_function');
			}
		}
		else
		{
			$this->view->vars=array('path'=> $file, 'file'=>$page.'.php');
			$this->view->draw_error('missing_page');
		}
	}

}

?>
