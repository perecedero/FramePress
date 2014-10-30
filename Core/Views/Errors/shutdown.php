	<div class="error-list">
	<?php foreach($errors as $e){ $id = md5(microtime()); ?>
		<div class="error-item">

			<?php if ($e['from.framepress']) { //error trigged by framepress  ?>
				<?php echo ucfirst($this->Core->config['prefix']); ?> <?php echo $e['level'];?>
				on <?php echo $e['request']['call.type']; ?> <b><?php echo $e['request']['call']; ?></b>:
			<?php } else {  //other errors?>
				<?php echo $e['level'];?> on file <b><?php echo $e['file']; ?></b> (line <?php echo $e['line']; ?>):
			<?php }?>

			<b class="msg"><?php echo $e['message']; ?></b>

			<div id="<?php echo $id;?>" class="panel" >
				<h3>Request</h3>
				<div class="request"><?php pr($e['request']); ?></div>
				<h3>Trace</h3>
				<div class="trace"><?php pr($e['trace']); ?></div>
			</div>

		</div>
	<?php }?>
	</div>


