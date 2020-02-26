=== FloPress ===
Contributors: gillesameri
Plugin URI: http://flopress.io
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
Tags: Visual scripting, snippets, features, customization, development
Requires at least: 4.7
Tested up to: 5.3.2
Stable tag: 1.4.2
Requires PHP: 5.4.43

Flopress is a complete visual scripting system based on the use of graphical elements representing native WordPress mechanisms.

== Description ==
<strong>CUSTOMIZE WORDPRESS WITH VISUAL SCRIPTING</strong><br />

FloPress is a suite of integrated tools for WordPress developers & integrators to design and build features without writing a line of code. You can do many things with FloPress like making WordPress more secure, updating post data, creating Shortcodes or whatever you want.

* The ideal compromise between custom development and reusability​
* Reduce development time and share features across all websites
* Get full access to hooks and functions definitions
* Learn WordPress's coding in a fun way​

Visual scripting system is based on the concept of using graphical elements, which represent functions, operators or variables, and to connect them typically via lines or arrows, to forming relations and build scripts. Instead of having to write code line by line, you do everything visually.

This system is extremely flexible and powerful as it provides the ability to use virtually the full range of concepts and tools, usually only available for developers. Code is more visual and it needs less abstract thinking to be understood. Any integrator, developer can look at it and quickly grasp the flow of logic.

[youtube https://www.youtube.com/watch?v=Ilw1SP5gjAw]

## Features are composed with the following element

* **Hook scripts (actions and filters)** Hooks are used to literally hook into parts of the WordPress page to retrieve, insert, or modify data. They can allow us to take certain actions behind the scenes.
* **Shortcode scripts** Shortcodes simplify the use of features in WordPress.
* **Templates** Templates can generate any text-based content like HTML, CSS, Javascript, …
* **Data items** Data items are used to define settings fields or to share value between flow scripts. 

## Why use it ?

Because it’s much easier. By linking native WordPress mechanisms together, it is possible to deal with many situations that do not require the installation of a complete plugin to use a small part. 

* Easy to maintain
* Easy to learn
* Portable
* Rapid development
* Focus on stuff that you have to care
* Handle programming errors
* Never forget the semicolon again ;)

When activated, FloPress generates an object-oriented (OO) PHP classes.

## Prerequisites

No prior programming is required. If you have worked with WordPress before, you will discover that it is very easy to add your own custom features.

A basic understanding of mathematics (order of operations, addition, subtraction, multiplication) will facilitate comprehension of certain coding logic.

## And more... 

* **Get access to 70+ time-saving features** Get access to a great deal of ready-to-use features, directly available on your WordPress administration page.
* **Add 12 Plugins definitions** FloPress provides hook and function definitions on some of the most popular and widely used WordPress plugins.
* **Access your features anywhere** Sync your features to your own host, via FTP. Connect your own storage location, push the features in one click and share your features across all your websites.

== Installation ==

=== Method 1 : Auto ===

1. Log in to your WP install
2. From the Administration Panels, click on the Plugin Menu
3. Under Plugins, click the “Add New” sub menu
4. From the Plugins screen, click on the Upload button.
5. Select the FloPress zip file.
6. Activate the plugin.
7. Enter the license key you received after the purchase and activate it.

=== Method 2 : FTP ===

1. Download the FloPress plugin
2. Upload ‘flopress’ to the ‘/wp-content/plugins/’ directory
3. After upload, from the Plugins screen, you will find a new plugin item called “Flopress”.
4. Activate the plugin.
5. Enter the license key you received after the purchase and activate it.

== Frequently Asked Questions ==

= Support and Documentation =
Please refer to our [Support & Documentation pages](https://flopress.io/documentation/) for all the technical information and support documentation on the FloPress plugin.

== Screenshots ==

1. FloPress features/snippets management.
2. Function definitions picker
3. Hooks (actions and filters) picker
4. Dynamic data
5. Render HTML with dynamic templates (Twig)

== Change Log ==

= 1.4.3 =
* Fix: Update HTML in the time-saving feature : Show an urgent message in the admin area
= 1.4.2 =
* Fix: Multi-categories logic
* Fix: Improve PHP validation
* Fix: Nonce security
* Fix: Localize scripts
* New: Add sample feature when new install of FloPress
* New: Improve search for definitions
* Fix: Time-saving features scroll
* Fix: Uninstall keep MySQL table and remove it when plugin is deleted
= 1.4.1 =
* New: Add unset PHP function
* Fix: Comma for array when no value
* Fix: Ignore folders processing to main file
* Dev: Change ACF register block definitions
= 1.4.0 =
* Dev: Improve PHP code builder
* New: New PHP definitions
* Tweak: Bootstrap layout
* New: Improve UI
* Fix: Hooks picker autocomplete
* Fix: Feature copy
* Fix: Move components config to Javascript
* New: Functions name inside template without fp_function
* New: Add third party plugin and picker dialog
* New: Add ftp library and settings dialog
* New: 70 time-saving features
* Fix: Remove infinite scrolling for time-saving features
= 1.3.3 =
* New: Templates twig functions with fp_function
* New: Sortable features
* New: New PHP definitions
* New: Post and media picker settings
* Fix: Function name in component
= 1.3.2 =
* New: Settings and data
* New: Custom hooks with dynamics arguments
= 1.3.1 =
* Fix: Fix PHP arguments not required
= 1.3.0 =
* Tweak: Change main layout
* New: Remove front compilation