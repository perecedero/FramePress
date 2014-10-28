<?php $file = substr( $error['core.status']['controller.file'], strpos($error['core.status']['controller.file'], $error['core.status']['plugin.foldername'])); ?>

<div class="padd">
	<h1>Missing method</h1>
	<p>The method <b><?php echo $error['core.status']['controller.method']; ?></b> was not found on the class <b><?php echo $error['core.status']['controller.class']; ?></b></p>
	<p>Add it to <b><?php echo $file;  ?></b></p>

	<pre>
	&lt;?<b>php</b>

	<b>class</b> <?php echo $error['core.status']['controller.class']; ?> {

		<i>// some code</i>

		<b>public function</b> <?php echo $error['core.status']['controller.method']; ?>()
		{
			<i>// Magic</i>
		}

		<i>// more code over here</i>
	}

	</pre>
</div>
