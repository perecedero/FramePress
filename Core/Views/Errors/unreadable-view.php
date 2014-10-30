<?php
	$file = $error['request']['rendering']['file'];
	$file_nice = substr( $file, strpos($file, $this->Core->status['plugin.foldername']));
?>
<div class="padd">
	<h1>Unreadable view</h1>
	<p>Theview file <b><?php echo $file_nice; ?></b> can not be opened</p>
	<p>Please change the file or parent folders permsions</p>

	<?php echo $this->Core->Elements->get('Examples/unreadable.file', array('e' =>$error, 'file' =>$file_nice)); ?>
</div>
