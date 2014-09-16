<style>
	.wrap {font-size:14px; padding: 30px;}
	.wrap p b {font-size:17px;}
	.wrap pre { background: #2E3436; color:#fff; padding: 5px; border-radius: 5px; font-size: 18px; line-height: 30px;}
	.wrap pre b {color: #90CAEE; font-weight: bold; font-size:20px;}
</style>

<div class="wrap">

	<h2>Missing method</h2>
	<p>Method <b><?php echo $fileFunctionName; ?></b> was not found</p>
	<p>Add it to <b><?php echo $fileRelativePath;  ?></b></p>

	<h2>Example</h2>

	<pre class="code">
&lt;?php

class <b><?php echo $fileClassName; ?></b> {
	public function <b><?php echo $fileFunctionName; ?></b> ()
	{
		//Magic
	}
}
	</pre>

</div>
