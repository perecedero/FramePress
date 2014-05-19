<style>
	.wrap {font-size:14px; padding: 30px;}
	.wrap p b {font-size:17px;}
	.wrap pre { background: #2E3436; color:#fff; padding: 5px; border-radius: 5px; font-size: 18px; line-height: 30px;}
	.wrap pre b {color: #90CAEE; font-weight: bold; font-size:20px;}
</style>

<div class="wrap">

	<h2>Missing view</h2>
	<p>The view for <b><?php echo $fileFunctionName; ?></b> was not found</p>
	<p>Create it at <b><?php echo $fileRelativePath;  ?></b></p>

	<h2>Example</h2>

	<pre class="code">

<b>&lt;h1&gt;</b>Hello World!<b>&lt;/h1&gt;</b>
<b>&lt;p&gt;</b>This is my first view file<b>&lt;/p&gt;</b>
<b>&lt;p&gt;</b>FramePress Rocks!<b>&lt;/p&gt;</b>
	</pre>

</div>
