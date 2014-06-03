# What is FramePress

A simple framework for WordPress's plugins
This framework will help you to develop a plugin quickly and easily.
It work as a VC (view-controller) Framework: handle your short codes, metaboxes, admin menus/pages, actions and fliters in the right way!

## Requirements

 PHP 5, WordPress +3

## Features

* View-Controller like workflow
* Predefined folder structure for better file organization
* Built-in features to easily develop controllers:  Sessions API, View API, Paths API
* Built-in features to easily add content to views: js, css, links, images, forms
* Built-in error and performance log
* Built-in internationalization (i18n)
* Super configurable (in a easy/fast way)

## Change Log

#### Version 005
Changed activation/deactivation handlers

#### Version 004
Fixed examples on test controller
Changed the way admin pages/menus are registered

#### Version 003
improved shortcodes
improved error handler and reporting
included debug mode
Fixed minor bugs

#### Version 002
added shortcodes compatibility!
modified action is\_ajax to can add wp\_ajax\_nopriv\_
Fixed some bugs on the core action
added more examples to the test controller


##Creating and configuring your instance of framepress

file main.php

```PHP
require_once( 'core/FPL.php' );
global $FramePress;

global $test;
$test = new $FramePress(__FILE__, array(
	'prefix' => 'testprefix',
));
```

You must give a __unique__ name to your instance variable.
We use __$test__ for a obvious reason.

In this step you can setup predefined options and add custom ones.
The most important and the only required, is the __prefix__ value. this value
will be used to give unique names to controllers classes and other features
like i18n


#### Complete list of predefined options

>__prefix__:
>_Used to give unique names to controllers classes and others_
> * type string
> * default null
>
>__use.tmp__:
>_Permmit the use of TMP system folder_
> * type bool
> * default false
>
>__use.i18n__:
>_Enable internationalization for this plugin_
>_Use the value of prefix as dictionary selector_
> * type bool
> * default true
>
>__use.session__:
>_Enable the use of emulated sessions for this plugin_
> * type bool
> * default true
>
>__performance.log__:
>_Enable the log of time and memory usage for this plugin_
> * type bool
> * default false
>
>__debug__:
>_Enable error reporting_
> * type bool
> * default true

###Plugin activation and deactivation

Framepress will fire \[prefix\]_activation and \[prefix\]_deactivation actions on plugin activation and deactivation. That means that, internaly, Framepress register activation and deactivation hooks
check out actions section to see how to handle actions

##Adding custom Paths

file main.php

```PHP
$test->mergePaths(array(
	'superlibs' => $test->path['lib'] . DS . 'super';
	'duperlibs' => $test->path['lib'] . DS . 'super' . DS . 'duper';
));
```

The __mergePaths__ function give you the possibility of define new path for your
plugin structure, and to modify predefined ones.

#### Complete list of predefined paths

>__core__: path to core folder
>
>__controllers__: path to controllers folder
>
>__view__: path to view folder
>
>__d_view__: path to  core default views folder
>
>__layout__: path to layout folders folder
>
>__lib__: path to lib folder
>
>__d_lib__: path to core default lib folder
>
>__lang__: path to languages folder
>
>__tmp__: path to tmp folder
>
>__systmp__: path to system tmp folder (only if use.tmp is true)
>
>__resources__: path to resources folder
>
>__img__: path to img folder
>
>__img_url__: URL for img folder
>
>__css__: path to css folder
>
>__css_url__: URL for css folder
>
>__js__: path to js folder
>
>__js_url__: URL for js folder


##Adding actions / filters handlers

file main.php

```PHP
// action / filters
$my_actions = array (
	array(
		'tag' => 'init',
		'controller' => 'super',
		'function' => 'actionA',
	),
);
$test->actions($my_actions);
```

In this way the  function actionA  on super controller will be called on init
tag is a predefined wordpress action or a custom tag.
See [Action Reference](http://http://codex.wordpress.org/Plugin_API/Action_Reference "") for more information about predefined tags

#### Complete list of options for an action

>__tag__:
>__(required)__ _predefined wordpress action or a custom tag_
>
>__controller__:
>__(required, string)__ _The controller used to handle the action_
>
>__function__:
>__(required, string)__ _The function used to handle the action_
>
>__is\_ajax__:
>__(optional, string)__ _Especify if this action will be called from the client side_
>__(Values)__ _private (wp\_ajax\_), public (wp\_ajax\_nopriv\_), both (add 2 acctions)_
>
>
>__priority__:
>__(required, int)__ _Specify the order in which the function associated with the action is executed_
>
>__accepted_args__:
>__(required)__ _define how many arguments your function can accept_

##Adding admin pages

file main.php

```PHP
$wp_pages = array (
	'menu' => array (
		array (
			'page.title' => 'framepress, easier impossibru',
			'menu.title' => 'Test menu',
			'capability' => 'administrator',
			'controller' => 'super',
			'function' => 'index',
			'icon' => 'logo16.png',
		),
	),
);
$test->pages($wp_pages);
```

As easy as that, you can add multiple admin pages.

#### The posibles admin page types are:

>* menu
>* submenu
>* dashboard
>* posts
>* media
>* links
>* pages
>* comments
>* appearance
>* plugins
>* users
>* tools
>* settings

For each type you can define multiples admin pages

file main.php

```PHP
$my_pages = array (
	'menu' => array (
		array (
			'page.title' => 'framepress, easier impossibru',
			'menu.title' => 'Test menu',
			'capability' => 'administrator',
			'controller' => 'super',
			'function' => 'index',
			'icon' => 'logo16.png',
		),
		array (
			'page.title' => 'framepress, easier impossibru PRO',
			'menu.title' => 'Test menu PRO',
			'capability' => 'administrator',
			'controller' => 'super',
			'function' => 'indexPRO',
			'icon' => 'logo16.png',
		),
	),
);
$test->pages($my_pages);
```

#### Complete list of options for an admin page

>__page.title__:
>__(required)__ _The title for the admin page_
>
>__menu.title__:
>__(required)__ _The title for the admin menu/submenu_
>
>__capability__:
>__(required)__ _The user role able to use this menu_
>
>__controller__:
>__(required)__ _The controller used to handle the click on this menu_
>
>__function__:
>__(optional)__ _The function used to handle the click on this menu_
>
>__parent__:
>__(required for submenus )__ _The menu.title of the parent menu_
>
>__icon__:
>__(optional, only for menus)__ _The image file name under img folder to use as menu icon_
>
>__position__:
>__(optional, only for menus)__ _The menu position in the admin bar_


##Adding shortCodes handlers

file main.php

```PHP
//adding short codes
$my_shortcodes = array (
	array(
		'tag' => 'shortcode_name',
		'controller' => 'main',
		'function' => 'hendle_shortcode',
	),
);
$test->shortcodes($my_shortcodes);
```

Now you can handle shortcodes in your post or page.
the short codes will be reaplaced with the string returned by your function.

#### Complete list of options for an action

>__tag__:
>__(required)__ _name of the shortcode to use_
>
>__controller__:
>__(required, string)__ _The controller used to handle the shortcode_
>
>__function__:
>__(required, string)__ _The function used to handle the shortcode_
>
>__recursion__:
>__(optional, bool)__ _on true will parse shortcodes inside the shortcode_


## FUN (Examples)

### Metaboxes

file main.php

```PHP
//declare the actions handlers

$my_actions = array (
	array(
		'tag' => 'add_meta_boxes',
		'controller' => 'metaboxes',
		'function' => 'location',
	),
	array(
		'tag' => 'save_post',
		'controller' => 'metaboxes',
		'function' => 'saveLocation',
	),
);
$test->actions($my_actions);
```

file controllers/metaboxes.php

```PHP
class TestMetaboxes
{
	public function location ()
	{
		// Tell to wordpress to add the metabox on posts
		add_meta_box('location_selector_mbox', __( 'Location selector', 'test' ), array( $this, 'printLocation' ), 'post', 'normal', 'default');
	}

	public function printLocation ($post, $metabox)
	{
		global $test;

		$previous_value = get_post_meta($post->ID, 'location', true);

		// Set the value to the view
		$test->viewSet('selected', $previous_value);

		// Tell FramePress to draw the especific view
		$test->drawView('metaboxes/locationSelector');
	}

	public function saveLocation($post_id, $post)
	{
		update_post_meta($post_id, 'location', $_POST['location']);
		return $post;
	}

}
```

file views/metaboxes/locationSelector.php

```HTML+PHP
<!-- complex view -->

<input name="location" value="<?php echo $selected ?>">

<!--  complex view end -->
```

### AJAX

file main.php

```PHP
//declare the actions handlers

$my_actions = array (
	array(
		'tag' => 'test_event_tick',
		'controller' => 'events',
		'function' => 'onTick',
		is_ajax => public
	),
);
$test->actions($my_actions);
```

file controllers/events.php

```PHP
class TestEvents
{
	public function onTick ()
	{
		global $test;

		/*do some magic*/

		//return response as json

		return json_encode (array('status'=>'ok', 'msg' => 'tick saved'));

		//or return a view
		// null: draw default view (views/events/onTick.php)
		// false: do not echo the view, return it instead

		return $test->drawView(null, false);
	}
}
```

in some view ...

```JavaScript
jQuery.post(ajaxurl, {action:"test_event_tick", 'cookie': encodeURIComponent(document.cookie)}, function(data) {

	//do some magic with the response

});
```
