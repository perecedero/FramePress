<?php echo $this->css('framepress.default.css');?>
<style>
	.wrap {padding: 30px;}
	.wrap p  {font-size:14px;}
	.wrap pre { background: #2E3436; color:#fff; padding: 15px; border-radius: 5px; font-size: 16px; line-height: 15px;}
	.wrap pre b {color: #90CAEE; font-weight: bold; font-size:20px;}
	.wrap input[type="text"], .wrap textarea { width: 500px; margin-bottom: 30px; box-shadow: 0 0 15px #999; border-width:2px; border-color: #f0f0f0; height:40px; }
</style>


<div class="wrap">

	<h2>Example of Admin menu page</h2>

	<p>This view is using FramePress helpers, making the whole markup proccess faster and easier</p>

	<p>One of the named helpers is the method from, that create a wellformed html form tag pointing to the correct action</p>

<pre>
<b>function form(array $path [, array $attr ])</b>

&lt;?php echo $this->form(array('function'=> 'testEmailSend'));?&gt;

&lt;?php echo $this->form(array('controller'=> 'test', 'function'=> 'testEmailSend'));?&gt;

&lt;?php echo $this->form(array('function'=> 'testEmailSend'), array('class' => 'form email');?&gt;

</pre>

	<br><hr><br>

	<p>In this example after submitting the form, a FramePress generic lib will send an email to the given address</p>

	<p>Generic libs give us the ability to create code that run on different FramePress instances without having naming problems</p>

	<br><hr><br>


	<h2>Send and email</h2>


	<?php echo $this->form(array('function'=> 'testEmailSend'));?>
		<input type="text" name="data[to]" placeholder="Recipient email address"><br>
		<input type="text" name="data[subject]" placeholder="Subject"><br>
		<textarea name="data[body]" placeholder="Body"></textarea><br>
		<input type="submit">
	</form>
</div>


