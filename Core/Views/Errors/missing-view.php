<?php //pr($view);?>
<?php $file = substr( $view['file'], strpos($view['file'], $error['core.status']['plugin.foldername'])); ?>
<h1>Missing view</h1>
<p>The file <b><?php echo basename($view['file']); ?></b> was not found</p>
<p>Create it  at <b><?php echo $file; ?></b></p>

<br><br>
<h2>Example</h2>
<pre>

<b>&lt;h1&gt;</b>Hello World!<b>&lt;/h1&gt;</b>
<b>&lt;p&gt;</b>This is my first view file<b>&lt;/p&gt;</b>
<b>&lt;p&gt;</b>FramePress Rocks!<b>&lt;/p&gt;</b>

</pre>
