<?php
	$file = $error['request']['rendering']['file'];
	$file_nice = substr( $file, strpos($file, $this->Core->status['plugin.foldername']));
?>

<div class="padd">
	<h1>Missing view</h1>
	<p>The file <b><?php echo basename($file_nice); ?></b> was not found</p>
	<p>Create it  at <b><?php echo $file_nice; ?></b></p>

	<?php echo $this->Core->Elements->get('Examples/view.missing.file', array('e' =>$error)); ?>
</div>
