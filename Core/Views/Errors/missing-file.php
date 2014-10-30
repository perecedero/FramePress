<?php
	$type = $error['request']['loading']['type'];
	$file = $error['request']['loading']['file'];
	$file_nice = substr( $file, strpos($file, $this->Core->status['plugin.foldername']));
?>
<div class="padd">
	<h1>Missing <?php echo $type?> file</h1>
	<p><?php echo $type?> file <b><?php echo basename($file); ?></b> was not found</p>
	<p>Create it at <b><?php echo $file_nice; ?></b></p>

	<?php if($type == 'Controller'){ echo $this->Core->Elements->get('Examples/controller.missing.file', array('e' =>$error)); }?>
</div>

