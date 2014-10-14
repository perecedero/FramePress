<?php $file = substr( $error['core.status']['controller.file'], strpos($error['core.status']['controller.file'], $error['core.status']['plugin.foldername'])); ?>
<h1>Unreadable controller file</h1>
<p>The controller file <b><?php echo $file; ?></b> can not be opened</p>
<p>Please change the file or parent folders permsions</p>

<pre>

$ cd <?php echo dirname($error['core.status']['plugin.fullpath'])."\n"; ?>
$ chmod <b>0644</b> <?php echo $file; ?>

</pre>

or to be sure, change the permissions all over your plugin

<pre>

$ cd <?php echo $error['core.status']['plugin.fullpath']."\n"; ?>
$ find . -type f -exec chmod <b>0644</b> {} \;
$ find . -type d -exec chmod <b>0755</b> {} \;

</pre>
