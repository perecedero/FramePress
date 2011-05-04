<div class="wrap">
	<p>
		<h2>Missing Controller</h2>
		<p>Controller <b><?echo ucfirst($this->path->config->read('prefix')); ?><?echo $this->controller_name; ?></b> not found</p>
		<p>Create it on <b><?echo $this->controller_file; ?></b></p>
	</p>

	<p>
		<h2>Example</h2>
		<p>
			<pre>
&lt;?php<br/>
	Class <?echo ucfirst($this->path->config->read('prefix')); ?><?echo $this->controller_name; ?> { <br/>
		function index () { }
	}<br/>
?&gt;
			</pre>
		</p>
	</p>

</div>