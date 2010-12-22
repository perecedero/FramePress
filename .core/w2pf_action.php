<?php

/*
	WordPress Framework, Action class v1.0
	developer: Perecedero (Ivan Lansky) perecedero@gmail.com
*/

class w2pf_action_v1 {

	var $actions= array();
	var $view= null;
	var $path= null;

	function __construct($path, $view)
	{
		$this->path = &$path;
		$this->view = &$view;
	}

	function add ($actions = null)
	{
		$this->actions = $actions;
		
		foreach ($this->actions as $action){
			$tag = $action['tag'];
			if($action['is_ajax']){$tag='wp_ajax_'.$tag;}
			add_action($tag, array($this, $action['handler'].'AYNIL'.$action['function']));
		}

	}

	function __call($name, $args=array())
	{
		$parts = explode('AYNIL', $name);

		if(file_exists($file = $this->path->Dir['ACTIONS'] . $this->path->DS . $parts[0].'.php'))
		{
			require_once ($file);
			if(function_exists($parts[1]))
			{
				$this->view->page=$parts[0];
				$this->view->view_name=$parts[1];
				call_user_func_array($parts[1], $args);
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
