<?php

//define core class
if (!class_exists('FramePress_View_001')) {
class FramePress_View_001
{

	public $Core = null;

	public $contexts = null;

	public $context = 'FramePress';

	public function __construct(&$fp)
	{
		$this->Core = $fp;
	}


	/**
	 * Save a variable to pass it to the view.
	 *
	 * @param string $varName tag for the variable
	 * @param mixed $value value of the variable passed to the view
	 * @return void

	*/
	public function set ($name, $value, $context = null)
	{
		$context  = ($context)? $context : $this->context;
		$this->contexts[$context]['vars'][$name] = $value;
	}

	/**
	 * Set the name of the layout that will be used for draw the view
	 *
	 * @param string $layout_name name of the layout file
	 * @return void
	*/
	public function layout ($layout = null, $context = null)
	{
		$context  = ($context)? $context : $this->context;
		$this->contexts[$context]['layout'] = $this->Core->paths['layouts'] . DS . $layout. '.php';
	}

	/**
	 * Draw the view with the layout
	 *
	 * @return mixed: false on failure, string on $print false, void in $print true
	*/
	public function render ($file = null, $args = array())
	{
		$fpr_defaults = array('print' => true, 'context'=>$this->context);
		$fpr_args = array_merge($fpr_defaults, $args);

		$fpr_info = $this->aaa($file, $fpr_args);

		//Check the file
		if(!file_exists($fpr_info['file'])){
			trigger_error('Missing View | '.$fpr_args['context'], E_USER_WARNING);
			return false;
		}
		if(!is_readable($fpr_info['file'])){
			trigger_error('Unreadable View | '.$fpr_args['context'], E_USER_WARNING);
			return false;
		}

		@ob_start();
			//import variables
			if (isset($this->contexts[$fpr_args['context']]['vars'])) {
				foreach ($this->contexts[$fpr_args['context']]['vars'] as $key => $value) { $$key = $value; }
			}

			//load view
			require $fpr_info['file'];

			//save all
			$content_for_layout = @ob_get_contents();
		@ob_end_clean();

		@ob_start();
			//load layout's
			require $fpr_info['layout'];

			//save all
			$fpl_buffer = @ob_get_contents();
		@ob_end_clean();

		if ($fpr_args['print']){
			echo $fpl_buffer;
			return true;
		}else{
			return $fpl_buffer;
		}
	}

	public function aaa($file= null, $args)
	{
		$viewFolder =  @$this->Core->status['controller.name'];
		$viewName =  @$this->Core->status['controller.method'];
		$path = @$this->Core->paths['views'];
		$corepath = @$this->Core->paths['core.views'];

		//Find The view
		if($file){

			$name = rtrim($file, '.php') . '.php';

			if( is_file($file) ){
				$info = $this->bbb($file);
			} elseif( is_file($path . DS . $name) ){
				$info = $this->bbb($path . DS . $name);
			} elseif( is_file($path . DS . $viewFolder . DS . $name) ){
				$info = $this->bbb($viewPath . DS . $viewFolder . DS . $name);
			} elseif( is_file($corepath . DS . $name) ){
				$info = $this->bbb($corepath . DS . $name);
			} else {
				$info = $this->bbb($name);
			}
		} else {
			$info = $this->bbb($path . DS . $viewFolder . DS . $viewName . '.php');
		}

		$info['layout'] = $this->ccc($args);

		$info['print'] = $args['print'];

		$this->contexts[$args['context']]['request'] = $info;

		return $info;
	}

	public function bbb ($path)
	{
		$view = basename(rtrim($path,'.php') );
		$viewsPaths = array($this->Core->paths['plugin'], DS . $view . '.php');

		return array(
			'view' => $view,
			'view.path' => str_replace($viewsPaths, '', $path),
			'file' => $path,
		);
	}

	public function ccc ($args)
	{
		$layout = (isset($this->contexts[$args['context']]['layout']))?$this->contexts[$args['context']]['layout']: '';
		$path =$this->Core->paths['layouts'];
		$corepath =$this->Core->paths['core.views.layouts'];

		if(!$layout){
			$layout = $this->Core->paths['core.views.layouts'] . DS . 'default.php';
		} else if( !file_exists($layout)) {
			$core_layout =str_replace( $path, $corepath , $layout);
			if( file_exists($core_layout) ){
				$layout = $core_layout;
			} else {
				$layout = $this->Core->paths['core.views.layouts'] . DS . 'default.php';
				trigger_error('Missing Layout | '.$args['context'] );
			}
		}

		return $layout;
	}


}//end class
}//end if class exist

//Export framework className
$GLOBALS["FramePressView"] = 'FramePress_View_001';
$FramePress = 'FramePress_View_001';
