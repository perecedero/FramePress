<?php

//define core class
if (!class_exists('FramePress_Dispatcher_002')) {
class FramePress_Dispatcher_002
{
	public $Core = null;

	public function __construct(&$fp)
	{
		$this->Core = $fp;
	}

	public function __call($name, $fargs=array())
	{
		if( !$this->Core->Request->init($name, $fargs)) {
			return false;
		}

		$req = $this->Core->Request->current();

		//before_filter
		if(method_exists($req['controller.object'], 'beforeFilter')) {
			call_user_func(array($req['controller.object'], 'beforeFilter'));
		}

		//make the call
		$this->Core->Request->current('response.body', call_user_func_array(
			array($req['controller.object'], $req['controller.method']),
			$req['controller.method.args']
		));

		//after_filter
		if(method_exists($req['controller.object'], 'afterFilter')) {
			call_user_func(array($req['controller.object'], 'afterFilter'));
		}

		return $this->Core->Response->_echo();
	}


}//end class
}//end if class exist

//Export framework className
$GLOBALS["FramePressDispatcher"] = 'FramePress_Dispatcher_002';
$FramePressDispatcher = 'FramePress_Dispatcher_002';
