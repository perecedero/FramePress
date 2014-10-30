<!doctype html>
<html lang="en">
	<head>

		<script src="<?php echo includes_url();?>js/jquery/jquery.js" type="text/javascript"></script>
		<script src="<?php echo $this->Core->paths['core.js.url'] . DS . 'error.js'; ?>" type="text/javascript"></script>
	    <link href="<?php echo $this->Core->paths['core.css.url'] . DS . 'error.css'; ?>" rel="stylesheet" type="text/css">
	    <link href="http://fonts.googleapis.com/css?family=Exo+2:300,100italic,300italic,100" rel="stylesheet" type="text/css">

	</head>
	<body>

		<div class="framepress-errors content">
				<h1>Something seems to not be working properly</h1>
				<img width="40%" src="<?php echo $this->Core->paths['core.img.url'] . DS . '500.png';?>">
		</div>

		<div id="framepress-errors">
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
		</div>

	</body>
</html>



