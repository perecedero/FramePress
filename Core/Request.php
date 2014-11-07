<?php

//define core class
if (!class_exists('FramePress_Request_003')) {
class FramePress_Request_003
{
	public $Core = null;

	public $queue = array();

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
		$this->create($req, $type, array(
			'controller.file' => $this->Core->paths['controller'] . DS . $controller_requested . '.php',
			'controller.name' => $controller_requested,
			'controller.class' => ucfirst($this->Core->config['prefix']) . ucfirst($controller_requested),
			'controller.method' => $function_requested,
			'controller.method.args' => $args,
			'controller.object' => null,
		));

		return $this->check();
	}

	public function check()
	{
		$req =  $this->current();

		$name = $req['controller.name'];

		//create the controller object && check method
		$obj =  $this->Core->load('Controller', $name);
		if(!$obj){
			return false;
		}

		$this->current('controller.object', $obj);

		if(!method_exists($obj, $req['controller.method'])){
			$this->Core->Error->set('Missing Method');
			return false;
		}

		return true;
	}

	public function current($key = null, $value= null)
	{
		//call to current() and no queue;
		//ex: called for first time from the template
		if(!$this->queue){
			$this->create();
		}

		if(!$key){
			//return the last request with all the values
			return end($this->queue);
		} else if ($key && $value === null) {
			$last = end($this->queue);
			return $last[$key];
		} else {
			end($this->queue);
			$i = key($this->queue);
			return $this->queue[$i][$key] = $value;
		}
	}

	public function finish($name = null, $value= null)
	{
		return array_pop ($this->queue);
	}

	public function create($request = null, $type= null, $extra = array())
	{
		if(!$request) {
			$request = 'custom';
			if( did_action( 'wp' )) {
				$type = 'from-template';
			} else {
				$type = 'before-template';
			}
		}

		$this->queue[] = array_merge( array(
			'call' => $request,
			'call.type' => $type,
			'response.code' => 200,
			'response.type' => null,
			'response.body' => null,
		), $extra);
	}


}//end class
}//end if class exist

//Export framework className
$GLOBALS["FramePressRequest"] = 'FramePress_Request_003';
$FramePressRequest = 'FramePress_Request_003';
