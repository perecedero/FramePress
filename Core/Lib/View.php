<?php

//define core class
if (!class_exists('FramePress_View_003')) {
class FramePress_View_003
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
		$this->contexts[$context]['layout'] = $this->Core->paths['views'] . DS . 'Layouts' . DS . $layout. '.php';
	}

	/**
	 * Draw the view with the layout
	 *
	 * @return mixed: false on failure, string on $print false, void in $print true
	*/
	public function render ($file = null, $args = array())
	{
		$fpr_defaults = array('print' => false, 'context'=>$this->context);
		$fpr_args = array_merge($fpr_defaults, $args);

		$fpr_info = $this->aaa($file, $fpr_args);

		$this->Core->Request->current('rendering', $fpr_info);

		//Check the file
		if(!file_exists($fpr_info['file'])){
			$this->Core->Error->set('Missing View');
			return false;
		}

		if(!is_readable($fpr_info['file'])){
			$this->Core->Error->set('Unreadable View');
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

		$this->Core->Request->current('rendering', false);

		if ($fpr_args['print']){
			echo $fpl_buffer;
			return true;
		}else{
			return $fpl_buffer;
		}
	}

	public function aaa($file= null, $args)
	{
		$req = $this->Core->Request->current();

		$viewFolder =  @$req['controller.name'];
		$viewName =  @$req['controller.method'];
		$path = @$this->Core->paths['views'];
		$corepath = @$this->Core->paths['core.views'];

		//Find The view
		if($file){

			$name = preg_replace('/.php$/s', '', $file) . '.php';

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
		$req = $this->Core->Request->current();

		$layout = (isset($this->contexts[$args['context']]['layout']))?$this->contexts[$args['context']]['layout']: '';
		$path =$this->Core->paths['views'] . DS . 'Layouts';
		$corepath =$this->Core->paths['core.views']. DS . 'Layouts';

		if(!$layout){
			if(isset($req['controller.object']->layout)){
				$layout = $path . DS . $req['controller.object']->layout . '.php';
			} else {
				$layout = $corepath . DS .'default.php';
			}
		} else if( !file_exists($layout)) {
			$core_layout =str_replace( $path, $corepath , $layout);
			if( file_exists($core_layout) ){
				$layout = $core_layout;
			} else {
				$layout = $corepath . DS .'default.php';
				$this->Core->Error->set('Missing Layout');
			}
		}

		return $layout;
	}


}//end class
}//end if class exist

//Export framework className
$GLOBALS["FramePressView"] = 'FramePress_View_003';
$FramePress = 'FramePress_View_003';
