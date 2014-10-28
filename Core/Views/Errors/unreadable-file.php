<?php
	$type = $error['core.status']['loading']['type'];
	$file = $error['core.status']['loading']['file'];
	$file_nice = substr( $file, strpos($file, $error['core.status']['plugin.foldername']));
?>
<div class="padd">
	<h1>Unreadable <?php echo $type?> file</h1>
	<p>The <?php echo $type?> file <b><?php echo $file_nice; ?></b> can not be opened</p>
	<p>Please change the file or parent folders permsions</p>

	<?php echo $this->Core->Elements->get('Examples/unreadable.file', array('e' =>$error)); ?>
</div>
