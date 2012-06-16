<?echo $this->Html->css('framepress.default.css');?>
<div class="msgbox errori mbox_wfixed">
	<p style="float: right"><a class="msg-close" href="#">x</a></p>
	<?for($i=0;$i < count($msg); $i++){?>
		<p><? echo $msg[$i]; ?></p> 
	<?}?>
</div>

<script language='javascript'>
	setTimeout(function () { if(jQuery('.msgbox') != null) { jQuery('.msgbox').hide('slow'); } }, 30000);
	jQuery(".msg-close").click(function(){ jQuery(this).parent().parent().hide(); return false; });
</script>

