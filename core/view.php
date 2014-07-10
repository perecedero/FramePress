<?php

//define core class
if (!class_exists('FramePressView_001')) {
class FramePressView_001
{

	public $FramePress = null;

	public $status = null;

	public $paths = null;

	public $context = 'FramePress';

	public function __construct(&$fp)
	{
		$this->FramePress = $fp;

		//	Paths ---------------------

		$fullpath = $this->FramePress->paths['plugin'] ;

		$this->paths = array(
			'core_views' => $fullpath . DS . 'core' . DS . 'views',
			'views' => $fullpath . DS . 'views',
			'layouts' => $fullpath . DS . 'views' . DS . 'layouts',
		);

		//do not overwrite user defined path for views
		$this->paths = array_intersect_key(array_merge($this->paths, $this->FramePress->paths), $this->paths);

		$this->FramePress->mergePaths($this->paths);

	}


	/**
	 * Save a variable to pass it to the view.
	 *
	 * @param string $varName tag for the variable
	 * @param mixed $value value of the variable passed to the view
	 * @return void

	*/
	public function set ($varName, $value)
	{
		$this->status['view.vars'][$this->context][$varName] = $value;
	}

	/**
	 * Set the name of the layout that will be used for draw the view
	 *
	 * @param string $layout_name name of the layout file
	 * @return void
	*/
	public function setLayout ($layout_name = null)
	{
		$this->status['view.layout.file'] = $this->paths['layouts'] . DS . $layout_name. '.php';
	}

	/**
	 * Draw the view with the layout
	 *
	 * @return mixed: false on failure, string on $print false, void in $print true
	*/
	public function draw ($file = null, $flushoutput = true)
	{
		if($file){

			$fileDefExt = rtrim($file, '.php') . '.php';

			if(is_file($file)){
				$this->status['view.file'] = $file;
			}elseif(is_file($this->path['views'] . DS . $fileDefExt)){
				$this->status['view.file'] = $this->path['views'] . DS . $fileDefExt;
			}elseif(is_file($this->path['views'] . DS . strtolower($this->FramePress->status['controller.name']) . DS . $fileDefExt)){
				$this->status['view.file'] = $this->path['views'] . DS . strtolower($this->FramePress->status['controller.name']) . DS . $fileDefExt;
			}elseif(is_file($this->path['core_views'] . DS . $fileDefExt)){
				$this->status['view.file'] = $this->path['core_views'] . DS . $fileDefExt;
			} else {
				$this->status['view.file'] = $fileDefExt;
			}
		}

		if(!file_exists($this->status['view.file'])){
			$fileRelativePath = substr( $this->status['view.file'], strpos($this->status['view.file'], $this->FramePress->status['plugin.foldername']));
			return $this->callErrorHandler('view',  'fpl_missing_view', $fileRelativePath, $flushoutput);
		}

		if(!file_exists($this->status['view.layout.file'])) {
			$this->status['view.layout.file'] = $this->path['core_views'] . DS . 'fpl_default_layout.php';
		}

		@ob_start();
			//import variables
			if (isset($this->status['view.vars'][$context])) {
				foreach ($this->status['view.vars'][$context] as $key => $value) { $$key = $value; }
			}

			//load view
			require ($this->status['view.file']);

			//save all
			$content_for_layout = @ob_get_contents();
		@ob_end_clean();

		@ob_start();
			//load layout's
			require ($this->status['view.layout.file']);

			//save all
			$fpl_buffer = @ob_get_contents();
		@ob_end_clean();

		if ($flushoutput){
			echo $fpl_buffer;
		}else{
			return $fpl_buffer;
		}
	}


}//end class

//Export framework className
$GLOBALS["FramePressView"] = 'FramePressView_001';
$FramePress = 'FramePressView_001';
