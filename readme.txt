=== Guten Free Options ===
Contributors: majick
Donate link: http://wpmedic.tech/guten-free-options/
Tags: gutenberg, block editor, classic editor, options
Author URI: http://wpmedic.tech
Plugin URI: http://wpmedic.tech/guten-free-options/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 5.2.4
Requires at least: 4.9.0
Tested up to: 5.0.2
Stable tag: trunk

Gutenberg Free Options for your WordPressed Burger err I mean Editor

== Description ==

Want to use and/or not use the new Block Editor (Gutenberg) according to *your needs*?

Guten Free Options allows you to smoothly transition your writing experience to WordPress 5+ by giving you full control over when a post should be edited with the Classic Editor or Block Editor... plus have a button to easily switch between them like other page builders.

With this plugin you can set the default editor to be used at ANY level, and more specific levels will override the previous ones - but only if they are set (similar to how cascading stylesheet inherit rules.) 

This means complete flexibility - defaults for multisite networks, single sites, any user role, author preference, as well as being able to easily set default requirements for different custom post types and post templates, and then also switch over or override for individual posts as needed. Here is the list of possible options (in order):

* Network Default - for Multisite if plugin network activated
* Site Editor Default - set the base editor default for the site
* User Role Defaults - set a default editor for each role
* User Selection - optional default editor per user via profile
* Post Type - set a default editor to use for each post type
* Post Type Locking - lock any post type at this filtering level
* Existing Blocks - use block editor if post has existing blocks
* Post Template - set a default editor for each post template
* Metabox Override - allows selection on the post edit screen
* Querystring Button - easily switch between the editors any time
* Admin Override - post IDs or slugs specified on plugin screen

Compatible with both WordPress 4.9.x with Gutenberg plugin and WordPress 5 beta+ Block Editor, and includes *filters everywhere* for further custom use case control.

Also features some extra tweaks:

* Awesome button on either editor page to switch between editors
* Disable the Gutenberg install plugin nag (for WP 4.9 only)
* Remove the Gutenberg menu (for Gutenberg plugin only)
* Prevent update disabling the Gutenberg plugin (on update to WP 5 only)
(so you can continue to update Gutenberg plugin separately to core.)

Forget your personal editor allergies and install this plugin to just use what you need when you need it! :-)

[Guten Free Options Home](http://wpmedic.tech/guten-free-options/)


== Installation ==

1. Upload `guten-free-options.zip` via the Wordpress plugin installer.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Access the Plugin Settings via the Admin -> Settings menu -> Guten Free submenu

Multisite Usage
1. Network Activate the plugin from the Network Plugins page.
2. Access Network plugin settings via Network Admin -> Settings menu -> Guten Free submenu
3. Optionally set a default Editor for the Network and Save settings.


== Frequently Asked Questions ==


== Screenshots ==


== Upgrade Notice ==


== Changelog ==

= 0.9.4 =
* use simplified plugins setup array and loading method
* keep track of last filter that changed the default editor
* add constant for settings and/or network settings locking
* added post block checking for frontend Gutenberg loading
* added reset settings confirmation message box
* added settings updated/reset message via flag
* added unset of non-default plugin settings keys on save
* improved user object check to prevent errors
* fix to filepath variable on forced Gutenberg loading

= 0.9.3 = 
* added post type locking options (prevents further filtering)
* added querystring usage explanation to plugin admin screen
* added option to prevent disable of Gutenberg plugin on WP5 update
* added plugin images to plugin admin screen
* moved add_filters to init to prevent duplicate filtering
* fix to post type level selection filtering
* fix to match default post type for new posts
* fix value matching for multisite default
* fix to only apply multisite default when network activated
* fix to reset overriding plugin info values on submit
* check added alternate submenu link for each post type

= 0.9.2 =
* hotfix for post_id in gfo_get_post_content_only function

= 0.9.1 =
* fix to add submenu link for Block Editor in Classic mode
* fix for Gutenberg plugin as no is_block_editor() method exists
* add settings links to plugins and network plugins pages

= 0.9.0 =
* beta Working Public Test Version (on Github)

= 0.8.0 =
* alpha Development Version


== Other Notes ==

