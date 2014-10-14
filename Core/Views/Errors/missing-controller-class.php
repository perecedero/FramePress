<?php $file = substr( $error['core.status']['controller.file'], strpos($error['core.status']['controller.file'], $error['core.status']['plugin.foldername'])); ?>
<h1>Missing controller file</h1>
<p>Class <b><?php echo $error['core.status']['controller.class']; ?></b> was not found</p>
<p>Create it at <b><?php echo $file; ?></b></p>

<h2>Example</h2>

<pre class="code">
&lt;?<b>php</b>

<b>class</b> <?php echo $error['core.status']['controller.class']; ?> {

    <b>public function</b> index()
    {
        <i>// Magic</i>
    }
}

</pre>

<p>
	Note: prefix <b><?php echo ucfirst($this->Core->config['prefix']); ?></b> must be added to your class's name to get unique names in the wordpress ecosystem
</p>
