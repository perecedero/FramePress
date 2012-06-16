<?php echo $this->Html->css("styles.css");?>
<?php echo $this->Html->js("my.js");?>


<form method="post" action="<?php echo $this->Path->router(array('controller'=>'main', 'function'=>'save'));?>">

	<?echo $this->Msg->show('error');?>
	<?echo $this->Msg->show('info');?>
	<?echo $this->Msg->show('success');?>
	<?echo $this->Msg->show('warning');?>

	<p>
		<?if(isset($bar)){?>
			$bar is set with  <b><?echo $bar?></b>
		<?} else {?>
			$bar is not set
		<?}?>
	</p>

	<input type="submit" value="Set $bar" class="button"/>
	<?echo $this->Html->link('Go to framepress tools link', array('menu_type'=>'tools', 'controller'=>'main', 'function'=>'index'), array('class'=>'button'));?>
	<?echo $this->Html->link('Go to framepress menu', array('menu_type'=>'menu', 'controller'=>'main', 'function'=>'index'), array('class'=>'button'));?>
</form>



