<?php

//define core class
if (!class_exists('FramePress_Response_002')) {
class FramePress_Response_002
{
	public $Core = null;

	public function __construct(&$fp)
	{
		$this->Core = $fp;
	}

	public function type($type)
	{
		$this->Core->Request->current('response.type', $type);
	}

	public function code($code)
	{
		$this->Core->Request->current('response.code', $code);
	}

	public function body($body)
	{
		$this->Core->Request->current('response.body', $body);
	}

	public function _echo()
	{
		$req = $this->Core->Request->current();
		$output = '';

		if ( in_array($req['call.type'], array('adminpage', 'metabox')) && is_null($req['response.body'])){
			//print default view
			$this->Core->View->render(null, array('print' => true));
		}

		elseif($req['call.type'] == 'shortcode' ){

			//fix for wpautop, that add <p> and <br> to the shortcode content
			global $wp_filter;
			foreach($wp_filter['the_content'] as $priority => $value ){
				if(isset($value['wpautop'])){
					unset($wp_filter['the_content'][$priority]['wpautop']);
				}
			}

			if(is_null($req['response.body'])) {
				//render default view
				$req['response.body'] = $this->Core->View->render();
			}

			//parse posible shortcodes inside this short code
			$output = do_shortcode($req['response.body']);
		}


		elseif($req['call.type'] == 'hook' ){
			if($req['response.type']){
				//Response must be sent
				$this->send();
			} else {
				//filters can return things
				$output = $req['response.body'];
			}
		}

		$this->Core->Request->finish();
		return $output;
	}

	public function error($error = null)
	{
		$req = $this->Core->Request->current();
		$type = (isset($req['call.type']))? $req['call.type'] : null;

		if ( in_array($type, array('adminpage', 'metabox', 'shortcode')) ){

			$context =  $this->Core->Error->viewContext;

			$this->Core->View->layout('error', $context);
			$this->Core->View->set('error', $this->Core->Error->lastError(), $context);

			//parse response
			$this->Core->View->render('Errors/'.$error, array('print' => true, 'context' =>$context));
		}
	}

	public function printDebug()
	{
		//if is ajax  return a json error
		if(defined('DOING_AJAX') && DOING_AJAX && !empty( $_REQUEST['action'] )){
			$this->type( 'application/json');
			$this->body( $this->Core->Error->errors );
			$this->send();
		} else {

			if(ob_get_length() > 0 || headers_sent()) {
				$view ='Errors/shutdown';
			} else {
				$view = 'Errors/shutdown.complete';
			}

			$this->Core->View->set('errors', $this->Core->Error->errors, $this->Core->Error->viewContext);
			$this->Core->View->layout('default', $this->Core->Error->viewContext);
			$this->Core->View->render($view, array('print' => true, 'context' =>$this->Core->Error->viewContext));
		}
	}

	public function send()
	{
		$req = $this->Core->Request->current();

		if(!headers_sent()){
			header('HTTP/1.1 '.$req['response.code'], true);
			header('Status: '.$req['response.code'], true);
			header('Content-type: '.$req['response.type'], true);
		}

		if($req['response.type'] == 'application/json' && !is_string($req['response.body'])) {
			echo json_encode($req['response.body']);
		} else {
			echo $req['response.body'];
		}
		exit;
	}

}//end class
}//end if class exist

//Export framework className
$GLOBALS["FramePressResponse"] = 'FramePress_Response_002';
$FramePressResponse = 'FramePress_Response_002';
