<?php

//define core class
if (!class_exists('FramePress_Error_001')) {
class FramePress_Error_001
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
			//is shutdown and  there is a captured error
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
			'core.status' => $this->Core->status
		);

		$this->takeAction();
		return;
	}

	//handle error depending on type
	public function takeAction ()
	{
		//dont take action if debug is deactivated
		if( !$this->Core->config['debug']) {
			return true;
		}

		//shutdown response
		if( $this->shutdown ) {
			$this->printDebug(); return true;
		}

		//hook errors are shown on shutdown
		$rq = (isset($this->Core->status['request.type']))?$this->Core->status['request.type']:null;
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

		//this two types are shown on shutdown
		if ( in_array($e_type[0], array('Missing File', 'Unreadable File'))  ) {
			return;
		}

		// show/print errors trigged by framepress (missing controller, missing method, etc)
		//for printable elements (metaboxes, shorcodes, admin pages)

		//set info to the view
		$this->Core->View->set('error', $this->lastError(), $this->viewContext);
		$viewRequest = (isset($this->Core->View->contexts[$e_type[1]]['request']))?$this->Core->View->contexts[$e_type[1]]['request']: null;
		if($viewRequest){
			$this->Core->View->set('view', $viewRequest, $this->viewContext);
		}

		//parse response
		$view = 'Errors/'.sanitize_title($e_type[0]);
		$this->Core->View->layout('error', $this->viewContext);
		return $this->Core->Response->parseError($view, $this->viewContext);
	}

	public function printDebug()
	{
		//if is ajax  return a json error
		if(defined('DOING_AJAX') && DOING_AJAX && !empty( $_REQUEST['action'] )){
			$this->Core->Response->type = 'application/json';
			$this->Core->Response->body =  $this->errors;
			$this->Core->Response->sendResponse();
		} else {
			$this->Core->View->set('errors', $this->errors, $this->viewContext);
			if(ob_get_length() > 0 || headers_sent()) {
				$this->Core->View->layout('error', $this->viewContext);
				$this->Core->Response->parseError('Errors/shutdown', $this->viewContext);
			} else {
				$this->Core->View->layout('default', $this->viewContext);
				$this->Core->Response->parseError('Errors/shutdown.complete', $this->viewContext);
			}
		}
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
		return $this->errors[count($this->errors) -1];
	}



 }//end class
} //end if class exists;


//Export framework className
$GLOBALS["FramePressError"] = 'FramePress_Error_001';
$FramePressError = 'FramePress_Error_001';
