<?php

//define core class
if (!class_exists('FramePress_Error_002')) {
class FramePress_Error_002
{
	public $errors = array();
	public $shutdown = false;
	public $viewContext =  'framepress.error';


	public $Core = null;

	public function __construct(&$fp)
	{
		$this->Core = $fp;
	}

	public function capture($level = null, $message=null, $file= null, $line = null, $context=null)
	{
		//escape reports with @
		if( 0 == ini_get( "error_reporting" ) || 0 == error_reporting() ){
			return;
		}

		//shutdown
		$e = error_get_last();
		if (!$level && $e) {
			//is shutdown bacause error ocurred
			$level = $e['type']; $message=$e['message']; $file= $e['file']; $line = $e['line'];
			$this->shutdown =  true;
		} elseif (!$level && !$e && $this->errors) {
			//is shutdown and there are erros stored
			$this->shutdown =  true;
			return $this->takeAction();
		} elseif (!$level && !$e && !$this->errors) {
			//is shutdown and there are not erros stored
			return false;
		}

		//solo informar error si  $file se encuentra en el path de este plugin
		if(strpos($file, $this->Core->status['plugin.fullpath']) === false){
			return false;
		}

		$this->errors[] = array(
			'level' => $this->mapErrorCode($level),
			'message' =>$message,
			'file'=> $file,
			'line' => $line,
			'core.status' => array_merge($this->Core->status, $this->Core->Request->current())
		);

		$this->takeAction();
		return;
	}

	//handle error depending on type
	public function takeAction ()
	{
		//shutdown response
		if( $this->shutdown ) {
			$this->Core->Response->printDebug(); return true;
		}

		//hook errors are shown on shutdown
		$req = $this->Core->Request->current();
		$rq = (isset($req['call.type']))?$req['call.type']:null;
		if ( $rq == 'hook' ) {
			return true;
		}

		//get error
		$error = $this->lastError();
		$e_type = explode(' | ', $error['message']);
		$is_trigged_by_framepress = isset($e_type[1]);

		//any error not trigged by FramePress will be shown at shutdown
		if ( !$is_trigged_by_framepress ) {
			return;
		}

		//load errors not related with controllers are shown in shutdown
		if ( isset( $error['core.status']['loading']['type']) &&  $error['core.status']['loading']['type'] != 'Controller') {
			return;
		}

		// show/print errors trigged by framepress (missing controller, missing method, etc)
		//for printable elements (metaboxes, shorcodes, admin pages)

		return $this->Core->Response->error(sanitize_title($e_type[0]));
	}

	public function mapErrorCode($code)
	{
		$error =  null;
		switch ($code) {
			case E_PARSE:
			case E_ERROR:
			case E_CORE_ERROR:
			case E_USER_ERROR:
			case E_COMPILE_ERROR:
				$error = 'Error';
				break;
			case E_WARNING:
			case E_USER_WARNING:
			case E_COMPILE_WARNING:
			case E_RECOVERABLE_ERROR:
				$error = 'Warning';
				break;
			case E_NOTICE:
			case E_USER_NOTICE:
				$error = 'Notice';
				break;
			case E_STRICT:
				$error = 'Strict';
				break;
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				$error = 'Deprecated';
				break;
		}
		return $error;
	}

	public function lastError()
	{
		return end($this->errors);
	}



 }//end class
} //end if class exists;


//Export framework className
$GLOBALS["FramePressError"] = 'FramePress_Error_002';
$FramePressError = 'FramePress_Error_002';
