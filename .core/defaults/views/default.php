<div class="wrap"> 
	<p>
		<? echo $content_for_layout;?>
	</p>
</div>

<script language='javascript'>
	setTimeout('close_mesaje_box()',6000);
	function close_mesaje_box() { if(jQuery('.wpf-msg') != null) { jQuery('.wpf-msg').hide('slow'); } }
</script>
