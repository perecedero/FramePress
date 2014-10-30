<?php
	$type = $error['request']['loading']['type'];
	$file = $error['request']['loading']['file'];
	$file_nice = substr( $file, strpos($file, $this->Core->status['plugin.foldername']));
	$class = $error['request']['loading']['class_name'];
?>

<div class="padd">
	<h1>Missing <?php echo $type?> class</h1>
	<p>Class <b><?php echo $class; ?></b> was not found</p>
	<p>Create it at <b><?php echo $file_nice; ?></b></p>

	<?php if($type == 'Controller') { echo $this->Core->Elements->get('Examples/controller.missing.class', array('e' =>$error)); }?>
</div>
