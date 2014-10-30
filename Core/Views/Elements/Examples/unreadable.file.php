<h2>Example</h2>

<pre>

$ cd <?php echo dirname($this->Core->status['plugin.fullpath'])."\n"; ?>
$ chmod <b>0644</b> <?php echo $file; ?>

</pre>

or to be sure, change the permissions all over your plugin

<pre>

$ cd <?php echo $this->Core->status['plugin.fullpath']."\n"; ?>
$ find . -type f -exec chmod <b>0644</b> {} \;
$ find . -type d -exec chmod <b>0755</b> {} \;

</pre>
