# What is FramePress

A simple framework for WordPress's plugins.
This framework will help you to develop a plugin quickly and easily.
It work as a VC (view-controller) Framework: handle your short codes, metaboxes, admin pages and hooks in the right way!

## Requirements

 PHP 5, WordPress +3

## Features

* Object Oriented
* View-Controller infrastructure
* Predefined folder structure for better file organization
* Load objects on demand, one instance per framework**
* Built-in features to easily develop controllers
* Built-in error handling and debug options
* Built-in internationalization (i18n)
* Super configurable (in a easy/fast way)

## Change Log

#### Version 010
Build from the scratch
improved performance
improved stability


## Documentation
######[Full documentation / Examples](https://github.com/perecedero/FramePress/wiki)
===


#### Creating and configuring your instance of FramePress

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


#### Adding hooks

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

#### Adding admin pages

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
				'page.title' => 'My own admin menu',
				'menu.title' => 'My Menu',
				'capability' => 'administrator',
				'controller' => 'Test',
				'function' => 'myMenu',
			),
		),
	));
```


#### Adding shortCodes

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
the short codes will be replaced with the string returned by your function


#### Adding metaboxes

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

