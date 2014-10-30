<h2>Example</h2>

<pre class="code">
&lt;?<b>php</b>

<b>class</b> <?php echo $e['request']['controller.class']; ?> {

    <b>public function</b> index()
    {
        <i>// Magic</i>
    }
}

</pre>

<p>
	Note: prefix <b><?php echo ucfirst($this->Core->config['prefix']); ?></b> must be added to your class's name to get unique names in the wordpress ecosystem
</p>
