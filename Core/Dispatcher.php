<?php

//define core class
if (!class_exists('FramePress_Dispatcher_001')) {
class FramePress_Dispatcher_001
{
	public $Core = null;

	public function __construct(&$fp)
	{
		$this->Core = $fp;
	}

	public function __call($name, $fargs=array())
	{
		$this->Core->Request->init($name, $fargs);

		if( !$this->Core->Request->check()) {
			return false;
		}

		//before_filter
		if(method_exists($this->Core->status['controller.object'], 'beforeFilter')) {
			call_user_func(array($this->Core->status['controller.object'], 'beforeFilter'));
		}

		//make the call
		$this->Core->Response->body = call_user_func_array(
			array($this->Core->status['controller.object'], $this->Core->status['controller.method']),
			$this->Core->status['controller.method.args']
		);

		//after_filter
		if(method_exists($this->Core->status['controller.object'], 'afterFilter')) {
			call_user_func(array($this->Core->status['controller.object'], 'afterFilter'));
		}

		return $this->Core->Response->parse();
	}


}//end class
}//end if class exist

//Export framework className
$GLOBALS["FramePressDispatcher"] = 'FramePress_Dispatcher_001';
$FramePressDispatcher = 'FramePress_Dispatcher_001';
