<?php
	$file = $error['request']['controller.file'];
	$file_nice = substr( $file, strpos($file, $this->Core->status['plugin.foldername']));
	$class = $error['request']['controller.class'];
	$method = $error['request']['controller.method'];
?>

<div class="padd">
	<h1>Missing Method</h1>
	<p>The method <b><?php echo $method; ?></b>  <b><?php echo $method; ?></b> was not foundon the class <b><?php echo $class; ?></b></p>
	<p>Add it to <b><?php echo $file_nice; ?></b></p>

	<?php echo $this->Core->Elements->get('Examples/controller.missing.method', array('e' =>$error));?>
</div>
