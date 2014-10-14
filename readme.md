# What is FramePress

A simple framework for WordPress's plugins.
This framework will help you to develop a plugin quickly and easily.
It work as a VC (view-controller) Framework: handle your short codes, metaboxes, admin pages and hooks in the right way!

## Requirements

 PHP 5, WordPress +3

## Features

* Object Oriented
* View-Controller infraestructure
* Predefined folder structure for better file organization
* Load objects on demand, one instance per framework**
* Built-in features to easily develop controllers
* Built-in error handling and debug options
* Built-in internationalization (i18n)
* Super configurable (in a easy/fast way)

## Change Log

#### Version 010
Build from the scrach
improved performance
improved stability


# Easy how to

Check wiki for better documentation

##Creating and configuring your instance of framepress

file main.php

```PHP
	//init framework
	require_once( 'Core/FramePress.php' );

	global $test;
	$test  =  framePressGet(array(
		'prefix' => 'pfx',
		'debug' => true,
	));
```

You must give a __unique__ name to your instance variable.
We use __$test__ for a obvious reason.

In this step you can setup predefined options and add custom ones.
The most important and the only required, is the __prefix__ value. this value
will be used to give unique names to controllers classes and other features
like i18n



###Plugin activation and deactivation

FramePress will fire \[prefix\]_activation and \[prefix\]_deactivation actions on plugin activation and deactivation. That means that, internaly, Framepress register activation and deactivation hooks
check out actions section to see how to handle actions

##Add/modify Paths

file main.php

```PHP
$test->mergePaths(array(
	'controller' => $test->path['core'] . DS . 'my_controllers';
	'superlibs' => $test->path['lib'] . DS . 'super';
	'duperlibs' => $test->path['lib'] . DS . 'super' . DS . 'duper';
));
```

The __mergePaths__ function give you the possibility of define new path for your
plugin structure, and to modify predefined ones.


##Adding hooks

file main.php

```PHP
	// action / filters
	$test->WordPress->hooks(array(
		array(
			'tag' => 'init',
			'controller' => 'Test',
			'function' => 'init',
		),
		array(
			'tag' => 'my_action',
			'controller' => 'Test',
			'function' => 'foo',
		)
	));
```

In this way the method init on Test controller will be called on init action hook
tag is a predefined wordpress action or a custom tag.
See [Action Reference](http://http://codex.wordpress.org/Plugin_API/Action_Reference "") for more information about predefined tags

##Adding admin pages

file main.php

```PHP
	$test->WordPress->adminPages(array(
		'settings' => array (
			array (
				'page.title' => 'option under settings menu',
				'menu.title' => 'My Settings',
				'capability' => 'administrator',
				'controller' => 'Test',
				'function' => 'settingsMenu',
			),
		),
		'menu' => array (
			array (
				'page.title' => 'My ownadmin menu',
				'menu.title' => 'My Menu',
				'capability' => 'administrator',
				'controller' => 'Test',
				'function' => 'myMenu',
			),
		),
	));
```


##Adding shortCodes

file main.php

```PHP
	$test->WordPress->shortcodes(array(
		array(
			'tag' => 'test_error', //will handle shortcode [test_error]
			'controller' => 'Test',
			'function' => 'MyShortcode',
		)
	));
```

Now you can handle shortcodes in your post or page.
the short codes will be reaplaced with the string returned by your function


##Adding metaboxes

file main.php

```PHP
	$test->WordPress->metaboxes( array(
		array(
			'controller' => 'Test',
			'function' => 'myMetabox',
			'id' => 'mymetabox_fields',
			'title'=> 'Testing metaboxes',
			'post_type'=> array('post', 'page'),
			'context'=> 'advanced',
		),
	));
```


## FUN (Examples)

### Metaboxes Complete

file main.php

```PHP
	//init framework
	require_once( 'Core/FramePress.php' );

	global $test;
	$test  =  framePressGet(array(
		'prefix' => 'pfx',
		'debug' => true,
	));

	//add the metabox
	$test->WordPress->metaboxes( array(
		array(
			'controller' => 'Metabox',
			'function' => 'print',
			'id' => 'mymetabox_fields',
			'title'=> 'Testing metaboxes',
			'post_type'=> array('post', 'page'),
			'context'=> 'advanced',
		),
	));

	$test->WordPress->hooks(array(
		array(
			'tag' => 'save_post',
			'controller' => 'Metabox',
			'function' => 'saveFields',
		),
	));
```

file Controllers/Metabox.php

```PHP
class PfxMetabox
{
	public function print($post, $args)
	{
		global $test;

		$previous_value = get_post_meta($post->ID, 'location', true);

		// Set the value to the view
		$test->View->Set('selected', $previous_value);

		//FramePress will automatically render the view Views/Metabox/print.php
		// you can renderany other with:
		// $test->View->render('folder/viewname', array('print' =>false));
	}

	public function saveFields($post_id, $args)
	{
		//don't forget to check nonce here

		update_post_meta($post_id, 'location', $_POST['location']);
		return $post;
	}

}
```

file Views/Metabox/print.php

```HTML+PHP
<!-- complex view -->

<input name="location" value="<?php echo $selected ?>">

<!--  complex view end -->
```

### AJAX

file main.php

```PHP
	//init framework
	require_once( 'Core/FramePress.php' );

	global $test;
	$test  =  framePressGet(array(
		'prefix' => 'pfx',
		'debug' => true,
	));

	$test->WordPress->hooks(array(
		array(
			'tag' => 'my_ajax_action',
			'controller' => 'Test',
			'function' => 'ajax',
			'is_ajax' => true
		),
	));
```

file Controllers/Test.php

```PHP
class PfxTest
{
	public function ajax()
	{
		global $test;

		/*do some magic*/

		//return response as json
		$test->Response->type = 'application/json';
		return array('status'=>'ok', 'msg' => 'some msg');
	}
}
```

in some view ...

```JavaScript
jQuery.post(ajaxurl, {action:"my_ajax_action", function(data) {

	//do some magic with the response

});
```
