<?php

/*
	WordPress Framework, View class v1.2
	developer: Perecedero (Ivan Lansky) perecedero@gmail.com
*/

class w2pf_view_test {

	var $vars = array();
	var $css= array();
	var $js= array();
	var $layout= 'default';

	var $page =null;
	var $view_name =null;

	var $path = null;
	var $msg = null;
	var $html = null;

	function __construct($path, $msg, $html_class_name){
		$this->path = &$path;
		$this->msg = &$msg;
		$this->html =new $html_class_name(&$path);
	}

	function set ($varName, $value)
	{
		$this->vars[$varName] = $value;
	}

	function css ($cssname)
	{
		$this->css[] = $cssname;
	}

	function js ($jsname, $at_top=false)
	{
		$this->js[] = array($jsname, $at_top);
	}

	function layout ($layout_name)
	{
		$this->layout = $layout_name;
	}

	function draw ()
	{
		ob_start();

			//import variables
			if ($this->vars)
			{
				foreach ($this->vars as $key=>$value) {$$key = $value; }$this->vars = array();
			}

			//load css's
			if($this->css)
			{
				echo '<style type=\'text/css\'>'."\n";
					for($i=0; $i<count($this->css); $i++) { if(file_exists($cssfile=$this->path->Dir['CSS'] . $this->path->DS . $this->css[$i] . ".css")){require_once ($cssfile); echo "\n";} }
				echo '</style>'."\n";
			}

			//load js's
			if($this->js)
			{
				echo '<script type=\'text/javascript\'>'."\n";
					for($i=0; $i<count($this->js); $i++) { if($this->js[$i][1] && file_exists($jsfile=$this->path->Dir['JS'] . $this->path->DS . $this->js[$i][0] . ".js")){require_once ($jsfile); echo "\n";} }
				echo '</script>'."\n";
			}

			// load view
			$view_name_1234 = $this->view_name;
			$view_name_1234_not_found = false;
			if(file_exists($view_page_to_dsiplay_1234 = $this->path->Dir['VIEW'] . $this->path->DS . $this->page. $this->path->DS .  $view_name_1234 . ".php")){
				require_once ($view_page_to_dsiplay_1234);
			}
			else
			{
				require_once ($this->path->Dir['CORE'] . $this->path->DS . 'defaults' . $this->path->DS . 'views' . $this->path->DS . "missing_view.php");
				$view_name_1234_not_found = true;
			}

			//load js's
			if($this->js)
			{
				echo '<script type=\'text/javascript\'>'."\n";
					for($i=0; $i<count($this->js); $i++) { if(!$this->js[$i][1] && file_exists($jsfile=$this->path->Dir['JS'] . $this->path->DS . $this->js[$i][0] . ".js")){require_once ($jsfile); echo "\n";} }
				echo '</script>'."\n";
			}

		//save all
		$content_for_layout = ob_get_contents();
		ob_end_clean();

		ob_start();

		//load layout's
		if(!$view_name_1234_not_found && file_exists($layout_page_to_dsiplay_1234 = $this->path->Dir['VIEW'] . $this->path->DS . 'layouts' . $this->path->DS . $this->layout . ".php")){
			require_once ($layout_page_to_dsiplay_1234);
		}
		else
		{
			require_once ($this->path->Dir['CORE'] . $this->path->DS . 'defaults' . $this->path->DS . 'views' . $this->path->DS . "default.php");
		}

		//save all
		$buffer = ob_get_contents();

		ob_end_clean();

		echo $buffer;exit;
	}

	function draw_error ($view_name_1234 = null)
	{
		ob_start();

		//import variables
		if ($this->vars)
		{
			foreach ($this->vars as $key=>$value) {$$key = $value; }$this->vars = array();
		}

		// load view
		require_once ($this->path->Dir['CORE'] . $this->path->DS . 'defaults' . $this->path->DS . 'views' . $this->path->DS . $view_name_1234.".php");
		//save all
		$content_for_layout = ob_get_contents();

		ob_end_clean();


		ob_start();

		//load layout's
		require_once ($this->path->Dir['CORE'] . $this->path->DS . 'defaults' . $this->path->DS . 'views' . $this->path->DS . "default.php");
		//save all
		$buffer = ob_get_contents();

		ob_end_clean();

		echo $buffer; exit;
		return true;
	}
}

?>
