<?php

//define core class
if (!class_exists('FramePress_Error_003')) {
class FramePress_Error_003
{
	public $errors = array();
	public $shutdown = false;
	public $viewContext =  'framepress.error';


	public $Core = null;

	public function __construct(&$fp)
	{
		$this->Core = $fp;
	}

	public function set($message, $level= null, $file=null, $line =null, $fromframepress = true)
	{
		if(!$level) {$level = E_USER_WARNING;}

        ob_start();
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $trace = ob_get_contents();
        ob_end_clean();

		$this->errors[] = array(
			'level' => $this->mapErrorCode($level),
			'message' =>$message,
			'file' =>$file,
			'line' =>$line,
			'trace' =>  $trace,
			'request' => $this->Core->Request->current(),
			'from.framepress' => $fromframepress
		);

		$this->takeAction();
		return;
	}

	public function capture($level = null, $message=null, $file = null, $line = null)
	{
		//escape reports with @
		if( 0 == ini_get( "error_reporting" ) || 0 == error_reporting() ){
			return;
		}

		$this->set($message, $level, $file, $line, false);

		return;
	}

	public function shutdown()
	{
		//shutdown
		$e = error_get_last();

		if ($e) {
			//is shutdown bacause error ocurred
			$this->shutdown =  true;
			$this->set($e['message'], $e['type'], $e['file'], $e['line'], false);

		} elseif ( !$e && $this->errors) {
			//is shutdown and there are erros stored
			$this->shutdown =  true;
			$this->takeAction();

		} elseif (!$e && !$this->errors) {
			//is shutdown and there are not erros stored
			return false;
		}
	}

	//handle error depending on type
	public function takeAction()
	{
		if (!$this->Core->config['debug']) {
			return true;
		}

		//shutdown response
		if ( $this->shutdown) {
			$this->Core->Response->printDebug(); return true;
		}

		//get error
		$error = $this->lastError();

		//any error not trigged by FramePress will be shown at shutdown
		if ( !$error['from.framepress'] ) {
			return;
		}

		//hook errors are shown at shutdown
		if ( isset( $error['request']['call.type']) &&  $error['request']['call.type'] == 'hook') {
			return true;
		}

		//load errors not related with controllers are shown in shutdown
		if ( isset( $error['request']['loading']['type']) &&  $error['request']['loading']['type'] != 'Controller') {
			return;
		}

		// show/print errors trigged by framepress (missing controller, missing method, etc)
		//for printable elements (metaboxes, shorcodes, admin pages)

		return $this->Core->Response->error(sanitize_title($error['message']));
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


	/**
	 * Enque script and styles to display errors
	 *
	 * This function is registered on FramePress constructor only if debug is activated
	 *
	 * @return void
	*/
	public function _addScripts ()
	{
		if (!$this->Core->config['debug']) {
			return true;
		}

		wp_enqueue_script('FramePressErrors');
		wp_enqueue_style('FramePressErrors');
	}


 }//end class
} //end if class exists;


//Export framework className
$GLOBALS["FramePressError"] = 'FramePress_Error_003';
$FramePressError = 'FramePress_Error_003';
