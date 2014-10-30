<h2>Example</h2>

	<pre>
	&lt;?<b>php</b>

	<b>class</b> <?php echo $e['request']['controller.class']; ?> {

		<i>// some code</i>

		<b>public function</b> <?php echo $e['request']['controller.method']; ?>()
		{
			<i>// Magic</i>
		}

		<i>// more code over here</i>
	}

	</pre>

<p>
	Note: prefix <b><?php echo ucfirst($this->Core->config['prefix']); ?></b> must be added to your class's name to get unique names in the wordpress ecosystem
</p>
