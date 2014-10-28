<?php
	$type = $error['core.status']['loading']['type'];
	$file = $error['core.status']['loading']['file'];
	$file_nice = substr( $file, strpos($file, $error['core.status']['plugin.foldername']));
	$class = $error['core.status']['loading']['class_name'];
?>

<div class="padd">
	<h1>Missing <?php echo $type?> class</h1>
	<p>Class <b><?php echo $class; ?></b> was not found</p>
	<p>Create it at <b><?php echo $file_nice; ?></b></p>

	<?php if($type == 'Controller') { echo $this->Core->Elements->get('Examples/controller.missing.class', array('e' =>$error)); }?>
</div>
