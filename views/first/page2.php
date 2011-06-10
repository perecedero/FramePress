<?php echo $this->html->css("styles.css");?>
<?php echo $this->html->js("my.js");?>

<form method="post" action="<?php echo $this->path->router(array('controller'=>'first', 'function'=>'save'));?>">

   <?echo $this->msg->show('general');?>
   <?echo $this->msg->show('error');?>

   <p><?if(isset($bar)){?> $bar was set <?}?></p>

</form>

   <?echo $this->html->link('Back to dashboard', array('menu_type'=>'tools', 'controller'=>'second', 'function'=>'index'), array('class'=>'button'));?>
   <?echo $this->html->img('img.png');?>
   <?echo $this->html->img('foo/img2.png');?>