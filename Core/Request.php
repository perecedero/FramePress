<?php

//define core class
if (!class_exists('FramePress_Request_002')) {
class FramePress_Request_002
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
		$this->queue[] = array(
			'call' => $req,
			'call.type' => $type,
			'controller.file' => $this->Core->paths['controller'] . DS . $controller_requested . '.php',
			'controller.name' => $controller_requested,
			'controller.class' => ucfirst($this->Core->config['prefix']) . ucfirst($controller_requested),
			'controller.method' => $function_requested,
			'controller.method.args' => $args,
			'controller.object' => null,
			'response.code' => 200,
			'response.type' => null,
			'response.body' => null,
		);

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
			trigger_error('Missing Method | FramePress' );
			return false;
		}

		return true;
	}

	public function current($key = null, $value= null)
	{
		//pr($this->queue);

		if(!$key){

			//call to current() and no queue;
			if(!$this->queue){ return array(); }

			//return the last request with all the values
			else { return end($this->queue); }

		} else if ($key && $value === null) {

			//call to current($key) and no queue;
			if(!$this->queue){ return null; }
			else{
				$last = end($this->queue);
				return $last[$key];
			}
		} else {

			//call to current($key, $value) and no queue;
			if(!$this->queue){ return null; }
			else {
				end($this->queue);
				$i = key($this->queue);
				return $this->queue[$i][$key] = $value;
			}
		}
	}
	public function finish($name = null, $value= null)
	{
		return array_pop ($this->queue);
	}

 }//end class
}//end if class exist

//Export framework className
$GLOBALS["FramePressRequest"] = 'FramePress_Request_002';
$FramePressRequest = 'FramePress_Request_002';
