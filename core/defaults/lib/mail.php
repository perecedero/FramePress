<?php
/**
 * Mail class for FramePress.
 *
 * This class is responsable for sending mails from the framework,
 *
 * Licensed under The GPL v2 License
 * Redistributions of files must retain the above copyright notice.
 *
 * @package		FramePress
 * @subpackage	core.mail
 * @since		 	beta 3
 * @license	   	GPL v2 License
 * @creator	   	Ausberto Huanca Vila, Ivan Lansky ( @perecedero )
 * 
 */

//define core class
if (!class_exists('FramePress_Email_001')) {
class FramePress_Email_001
{
	public $config = array(
		'from' => null,
		'to' => array(),
		'cc' => array(),
		'bcc' => array(),
		'reply' => null,
		'subject' => null,
		'type'=> 'html',
		'template' => null,
		'attachment'=> null,
		//Max length for the lines of the message (Don't apply to html messages)
		'lineLength' => 0
	);

	/**
	 * Local Reference to the framework core object
	 * 
	 * @var object
	 */
	private $fp = null;

	/**
	 * Constructor.
	 *
	 * @param string $FramePress Core Class
	*/
	public function __construct( &$FramePress ){
		$this->fp = $FramePress;
		$this->fp->mergePaths(array(
			'mail' => $this->fp->path['view'] . DS . 'mail'
		));
	}

	public function clean()
	{
		$this->config = array(
			'from' => null,
			'to' => array(),
			'cc' => array(),
			'bcc' => array(),
			'reply' => null,
			'subject' => null,
			'type'=> null,
			'template' => null,
			'attachment'=> null,
			'lineLength' => 0
		);
	}

	public function config( $conf )
	{
		$this->config = array_merge($this->config, $conf);
	}

	/**
	 * Save a variable to pass it to the template.
	 * 
	 * @param string $varName
	 * @param mixed $value
	 * @access public
	 * @return void
	 */
	public function viewSet($varName, $value)
	{
		$this->fp->viewSet($varName, $value, 'mail');
	}

	/**
	 * Sends the mail and prepare the headers according to the values set before.
	 *
	 * @access public
	 * @return boolean
	 */
	public function send()
	{
		$headers = '';
		$body = '';

		//Gets the template with the merged vars
		$msg = $this->fp->drawView($this->fp->path['mail'] . DS . $this->config['template'] . '.php' , false, 'mail');

		//Makes the wordwrap only for plain text
		if ($this->config['lineLength'] > 0 && strtolower($this->config['type']) == 'text') {
			$msg = wordwrap($msg, $this->config['lineLength']);
		}

		//Start to build the headers
		if ($this->config['from'] != "") {
			$headers.='From: ' . $this->config['from'] . "\r\n";
		} else {
			trigger_error('Missing from address');
		}
	
		if (count($this->config['cc']) > 0) {
			$headers.='Cc: ' . join(', ', $this->config['cc']) . "\r\n";
		}

		if (count($this->config['bcc']) > 0) {
			$headers.='Bcc: ' . join(', ', $this->config['bcc']) . "\r\n";
		}

		if ($this->config['reply'] != "") {
			$headers.='Reply-To: ' . $this->config['reply'] . "\r\n";
		}

		$headers.='X-Mailer: FramePress/Mail'. "\r\n";

		//Make an unique id for the boundaries
		$uid = md5(uniqid(time()));
		$headers .= "MIME-Version: 1.0\r\n";

		//Special headers for an attachment
		if ($this->config['attachment'] ) {
			$headers .= "Content-Type: multipart/mixed; boundary=\"mixed-" . $uid . "\"\r\n\r\n";
			$body .= "This is a multi-part message in MIME format.\r\n";
			$body .= "--mixed-" . $uid . "\r\n";
			$body .= "Content-Type: multipart/alternative;  boundary=\"alt-" . $uid . "\"\r\n";
			$body .= "--alt-" . $uid . "\r\n";
		}

		//Type of content for the mail message and its body
		$content_type = (strtolower($this->config['type']) == 'html')? 'html' : 'plain';
		$content_type = 'Content-type: text/'.$content_type.'; charset=iso-8859-1' . "\r\n".'Content-Transfer-Encoding: 7bit'. "\r\n\r\n";
		if ($this->config['attachment']) {
			$body .= $content_type;
		} else {
			$headers .= $content_type;
		}
		
		//put the body
		$body .= $msg . "\r\n\r\n";

		//put the atachament
		if ($this->config['attachment']) {
			//Gets the file content to attach to the mail
			$content = file_get_contents( $this->config['attachment'] );
			$content = chunk_split(base64_encode($content));

			//Prepare the headers for the file
			$body .= '--alt-' . $uid . "--\r\n\r\n";
			$body .= '--mixed-' . $uid . "\r\n";
			$body .= 'Content-Type: application/octet-stream; name="' . basename($this->config['attachment']) . "\"\r\n";
			$body .= 'Content-Description: ' . basename($this->config['attachment']) . "\r\n";
			$body .= 'Content-Disposition: attachment; filename="' . basename($this->config['attachment']) . '"; size="' . filesize($this->config['attachment']) . "\";\r\n";
			$body .= 'Content-Transfer-Encoding: base64'."\r\n\r\n";
			$body .= $content . "\r\n";
			$body .= '--mixed-' . $uid . '--';
		}

		
		//echo '<pre>';echo $headers . $body; echo '</pre><br><br>'; 
		if (is_array($this->config['to'])){
			$to = join(', ', $this->config['to']);
		} else {
			$to = $this->config['to'];
		}
		
		return mail($to,  $this->config['subject'], $body, $headers);
	}

}//end class

//Export framework className
$GLOBALS["FramePressEmail"] = 'FramePress_Email_001';
$FramePressEmail = 'FramePress_Email_001';

}//end if class exists
