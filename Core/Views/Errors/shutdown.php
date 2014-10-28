	<script type="text/javascript">   var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>" </script>

	<style>
		body {margin:0; padding:0;}
		#hook-errors	 {position: fixed; bottom:0; width:100%; border-top: 5px solid #565656; color: white; z-index:99999;}
		#hook-errors	 a {color: tomato; font-weight: bold;}
		#hook-errors	 .error-item {padding: 10px;}
		#hook-errors	 .error-item b {color: #e6e6e6;}
		#hook-errors	 .error-item:nth-child(even) { background: #4F4F4F; }
		#hook-errors	 .error-item:nth-child(odd) { background: #3D3D3D; }
		#hook-errors	 .error-item {  border-top: 1px solid tomato }
		#hook-errors	 .error-item .panel {display: none; border-left: 2px solid tomato; background: #2E3436; color:#fff; margin: 10px 0; padding: 5px; border-radius: 5px; font-size: 12px; max-height: 300px; overflow-y: scroll;}
	</style>

	<div id="hook-errors">
		<div class="error-list">
		<?php foreach($errors as $e){?>
			<div class="error-item">
				<?php $msg = explode(' | ', $e['message']); ?>
				<?php $id = md5(serialize($e)); ?>
				<?php if (isset($msg[1])) { //error trigged by framepress  ?>
					<?php echo ucfirst($this->Core->config['prefix']); ?> <?php echo $e['level'];?> on <?php echo $e['core.status']['call.type']; ?> <b><?php echo $e['core.status']['call']; ?></b>: <a href="" onclick="jQuery('#<?php echo $id;?>').toggle(); return false;"><?php echo $msg[0]; ?></a>
				<?php } else { ?>
					<?php echo ucfirst($this->Core->config['prefix']); ?> <?php echo $e['level'];?> on file <b><?php echo $e['file']; ?></b> (line <?php echo $e['line']; ?>): <a href="" onclick="jQuery('#<?php echo $id;?>').toggle(); return false;"><?php echo $msg[0]; ?></a>
				<?php }?>
				<div id="<?php echo $id;?>" class="panel" ><?php pr($e['core.status']); ?></div>
			</div>
		<?php }?>
		</div>
	</div>

	<script>
		jQuery(document).ready(function($){
			$('body').append($('#hook-errors').detach());
		});
	</script>



