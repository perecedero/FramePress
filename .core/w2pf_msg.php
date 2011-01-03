<?php

/*
	WordPress Framework, View class v1.2
	developer: Perecedero (Ivan Lansky) perecedero@gmail.com
*/

class w2pf_msg_test {

	var $messages_error = array();
	var $messages_general = array();


	function error ($msg)
	{
		$this->messages_error[] = $msg;
	}

	function general ($msg)
	{
		$this->messages_general[] = $msg;
	}

	function clear ($type = 'All')
	{
		switch ($type)
		{
			case 'error': $this->messages_error = array(); break;
			case 'general': $this->messages_general = array(); break;
			default:  $this->messages_error = $this->messages_general = array(); break;
		}
	}

	function show ($type = 'error', $options=array()){

		$defaults = array('class'=>"updated fade");
		$opts = array_merge($defaults, $options);

		return $this->msg_html($type, $opts); break;
	}

	private function msg_html ($type, $opt)
	{
		if($type == 'error' )
		{
			$to_show = $this->messages_error;
			$html_plus =  "<p><strong>Important:</strong></p>";
		}
		else
		{
			$to_show = $this->messages_general;
			$html_plus = "";
		}

		//exit if nothing to show
		if (!$to_show){return "";}

		// make html structure
		$html = "<div class=\"{$opt['class']} wpf-msg\">";
		$html .= $html_plus;
		for($i=0;$i < count($to_show); $i++){ $html .= "<p>{$to_show[$i]}</p>"; }
		$html .= "</div>";

		return $html;
	}

}

?>
