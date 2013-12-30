<style>
	.wrap p {font-size:14px; margin: 0 auto; with 960;}
	.wrap p b {font-size:17px;}
	.wrap pre { width:15px; background: #969696; color:#fff; font-family: Consolas,Monaco,monospace; padding: 5px; border-radius: 5px 0px 0px 5px; font-size: 18px; line-height: 30px;}
	.wrap pre.code {width:600px;  border-radius: 0px 5px 5px 0px; background: #2E3436;}
	.wrap pre b {color: #90EE90; font-weight: bold; font-size:20px;}
</style>

<div class="wrap">

	<h2>Missing file</h2>
	<p>Controller file <b><?php echo strtolower($fileName); ?>.php</b> not found</p>
	<p>Create it on <b><?php echo $fileRelativePath; ?></b></p>

	<h2>Example</h2>

	<pre class="code">
	&lt;?php
		Class <b><?php echo $fileClassName; ?></b> {
			function index () {
				//Magic
			}
		}
	?&gt;
	</pre>

	<p>
		Note: prefix <b><?php echo ucfirst($this->config['prefix']); ?></b> must be added to your class's name to get unique names in the ecosystem
	<p>
</div>
