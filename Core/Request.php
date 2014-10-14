<?php

//define core class
if (!class_exists('FramePress_Request_001')) {
class FramePress_Request_001
{
	public $Core = null;

	public function __construct(&$fp)
	{
		$this->Core = $fp;
	}

	public function Init ( $request, $fargs )
	{
		//parse info
		$info = explode ('__AYNIL__', $request);

		//check call type: 'adminpage', 'shortcode', 'metabox', 'hook'
		$type = $info[0];

		//get needed info
		if($type == 'adminpage' ){
			$controller_requested = $info[1];
			$function_requested = (isset($_GET['function']))? $_GET['function'] : $info[2];
			$args = (isset($_GET['fargs']))? $_GET['fargs'] : $fargs;
			$req = $info[3];
		}else{
			$controller_requested = $info[1];
			$function_requested = $info[2];
			$args = $fargs;
			$req = $info[3];
		}

		//set call status
		$this->Core->status = array_merge( $this->Core->status, array(
			'request' => $req,
			'request.type' => $type,
			'controller.name' => $controller_requested,
			'controller.class' => ucfirst($this->Core->config['prefix']) . ucfirst($controller_requested),
			'controller.method' => $function_requested,
			'controller.method.args' => $args,
			'controller.file' => $this->Core->paths['controller'] . DS . $controller_requested . '.php'
		));

	}

	public function check()
	{
		$name = $this->Core->status['controller.name'];
		if( !$this->Core->isLoaded('Controller', $name)){
			//check controller file
			if(!file_exists($this->Core->status['controller.file'])) {
				trigger_error('Missing Controller File | FramePress' );
				return false;
			}
			if(!is_readable($this->Core->status['controller.file'])) {
				trigger_error('Unreadable Controller File | FramePress' );
				return false;
			}

			//import controller && check class
			require_once($this->Core->status['controller.file']);
			if (!class_exists($this->Core->status['controller.class'])){
				trigger_error('Missing Controller Class | FramePress' );
				return false;
			}
		}

		//create the controller object && check method
		$this->Core->status['controller.object'] =  $this->Core->load('Controller', $name);

		if(!method_exists($this->Core->status['controller.object'], $this->Core->status['controller.method'])){
			trigger_error('Missing Method | FramePress' );
			return false;
		}

		return true;
	}

 }//end class
}//end if class exist

//Export framework className
$GLOBALS["FramePressRequest"] = 'FramePress_Request_001';
$FramePressRequest = 'FramePress_Request_001';
