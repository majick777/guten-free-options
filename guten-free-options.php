<?php
/*
Plugin Name: Guten Free Options
Plugin URI: http://wpmedic.tech/guten-free-options/
Author: Tony Hayes
Description: Gutenberg Free Options for your WordPressed Burger err I mean Editor
Version: 0.9.2
Author URI: http://wpmedic.tech
GitHub Plugin URI: majick777/guten-free-options
*/

// ============
// PLUGIN SETUP
// ============

// -----------------
// Set Plugin Values
// -----------------
global $wordquestplugins, $gutenfree; $gutenfree = array();
$slug = $gutenfree['slug'] = 'guten-free-options';
$gutenfree['network-slug'] = 'guten-free-network-options';
$wordquestplugins[$slug]['version'] = $gutenfree['version'] = '0.9.2';
$wordquestplugins[$slug]['title'] = $gutenfree['title'] = 'Guten Free Options';
$wordquestplugins[$slug]['namespace'] = $gutenfree['namespace'] = 'gfo';
$wordquestplugins[$slug]['settings'] = $gutenfree['settings'] = 'gfo';
$wordquestplugins[$slug]['hasplans'] = $gutenfree['hasplans'] = false;
$wordquestplugins[$slug]['textdomain'] = $gutenfree['textdomain'] = 'guten-free-options';
// $wordquestplugins[$slug]['wporgslug'] = $gutenfree['wporgslug'] = 'guten-free-options';

// ------------------------
// Check for Update Checker
// ------------------------
// note: lack of updatechecker.php file indicates WordPress.Org SVN version
// presence of updatechecker.php indicates site download or GitHub version
$updatechecker = dirname(__FILE__).'/updatechecker.php';
if (!file_exists($updatechecker)) {$wordquestplugins[$slug]['wporg'] = true;}
else {include($updatechecker); $wordquestplugins[$slug]['wporg'] = false;}

// ---------------------------
// maybe Load WordQuest Helper
// ---------------------------
if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
	$wordquest = dirname(__FILE__).'/wordquest.php';
	if (file_exists($wordquest) && is_admin()) {include($wordquest);}
}

// --------------
// Add Admin Page
// --------------
add_action('admin_menu', 'gfo_add_admin_page');
function gfo_add_admin_page() {
	global $gutenfree;
	// to allow programmatic locking of plugin settings page
	$lock_settings = apply_filters('gfo_lock_settings', get_option('gfo_lock_settings'));
	if ($lock_settings) {return;}
	$page_title = apply_filters('gfo_settings_page_title', __('Guten Free Options','guten-free-options'));
	$menu_title = apply_filters('gfo_settings_menu_title', __('Guten Free','guten-free-options'));
	$capability = apply_filters('gfo_manage_options_capability', 'manage_options');
	add_options_page($page_title, $menu_title, $capability, $gutenfree['slug'], 'gfo_settings_page');
}

// ----------------------
// Add Network Admin Page
// ----------------------
add_action('network_admin_menu', 'gfo_add_network_page');
function gfo_add_network_page() {
	global $gutenfree;
	// to allow programmatic locking of network settings page
	$lock_settings = apply_filters('gfo_lock_network_settings', get_option('gfo_lock_network_settings'));
	if ($lock_settings) {return;}

	// only add network menu if plugin is network activated
	$plugins = get_site_option('active_sitewide_plugins');
	$plugin = plugin_basename(__FILE__);
	if (array_key_exists($plugin, $plugins)) {
		$page_title = apply_filters('gfo_network_page_title', __('Guten Free Network Options','guten-free-options'));
		$menu_title = apply_filters('gfo_network_menu_title', __('Guten Free','guten-free-options'));
		$capability = apply_filters('gfo_manage_network_options_capability', 'manage_network_options');
		add_submenu_page('settings.php', $page_title, $menu_title, $capability, $gutenfree['network-slug'], 'gfo_network_settings_page');
	}
}

// -----------------------------
// Add Plugin Page Settings Link
// -----------------------------
// 0.9.1: added plugin page settings link
add_filter('plugin_action_links', 'gfo_plugin_action_links', 10, 2);
function gfo_plugin_action_links($links, $file) {
	global $gutenfree;
	if ($file == plugin_basename(__FILE__)) {
		$settings_url = add_query_arg('page', $gutenfree['slug'], admin_url('admin.php'));
		$settings_link = "<a href='".$settings_url."'>".__('Settings','guten-free-options')."</a>";
		$newlink['settings'] = $settings_link;
		$links = array_merge($newlink, $links);
	}
	return $links;
}

// -------------------------------------
// Add Network Plugin Page Settings Link
// -------------------------------------
// 0.9.1: added network plugin page settings link
add_filter('network_admin_plugin_action_links', 'gfo_network_plugin_action_links', 10, 4);
function gfo_network_plugin_action_links($links, $file, $plugin_data, $context) {
	global $gutenfree;
	if ($file == plugin_basename(__FILE__)) {
	 	$settings_url = add_query_arg('page', $gutenfree['network-slug'], network_admin_url('settings.php'));
	 	$settings_link = "<a href='".$settings_url."'>".__('Network Settings','guten-free-options')."</a>";
	 	$newlink['settings'] = $settings_link;
	 	$links = array_merge($newlink, $links);
	}
	return $links;
}


// ========
// SETTINGS
// ========

// -------------------
// Get Plugin Settings
// -------------------
$plugin_options = get_option('guten_free_options');
if ($plugin_options && is_array($plugin_options)) {$gutenfree = array_merge($gutenfree, $plugin_options);}

// ----------------------------
// Check Debug Switch Overrides
// ----------------------------
if (defined('GUTEN_FREE_DEBUG') && GUTEN_FREE_DEBUG) {$gutenfree['debug'] = true;}
elseif (isset($_REQUEST['gfo_debug']) && ($_REQUEST['gfo_debug'] == '1')) {$gutenfree['debug'] = true;}
elseif (isset($_REQUEST['debug']) && ($_REQUEST['debug'] == '1')) {$gutenfree['debug'] = true;}

// --------------------
// Get Default Settings
// --------------------
function gfo_get_defaults() {

	// set site default for a multisite site or single site
	if (is_multisite()) {$default = '';} else {$default = 'classic';}

	$defaults = array(

		/* Switches */
		'default_editor'		=> $default,
		'switch_buttons'		=> 'yes',
		'disable_nag'			=> 'yes',
		'remove_menu'			=> 'yes',
		'check_blocks'			=> 'yes',
		'user_default'			=> '',
		'editor_metabox'		=> 'yes',
		// 'no_override'		=> '',

		/* Post Types */
		'classic_types'			=> array(),
		'block_types'			=> array(),

		/* User Roles */
		'classic_roles'			=> array(),
		'block_roles'			=> array(),

		/* Page Templates */
		'classic_templates'		=> array(),
		'block_templates'		=> array(),

		/* Post IDs */
		'classic_ids'			=> '',
		'block_ids'				=> '',

		/* Debug */
		'debug'					=> '',

	);
	$defaults = apply_filters('gfo_default_settings', $defaults);
	return $defaults;
}

// ------------------
// Get Plugin Setting
// ------------------
// note: any setting can be easily filtered using gfo_ prefix to the setting key eg.
// add_filter('gfo_default_editor', 'my_custom_filter');
function gfo_get_setting($key, $filter=true) {
	global $gutenfree;
	if (isset($gutenfree[$key])) {$value = $gutenfree[$key];}
	else {
		if (!isset($gutenfree['defaults'])) {$gutenfree['defaults'] = gfo_get_defaults();}
		if (isset($gutenfree['defaults'][$key])) {$value = $gutenfree['defaults'][$key];}
		else {$value = null;}
	}
	if ($filter) {$value = apply_filters('gfo_'.$key, $value);}
	return $value;
}

// -------------------
// Add Plugin Settings
// -------------------
// register_activation_hook(__FILE__, 'gfo_add_settings');
$plugin_file = plugin_basename(__FILE__);
add_action('activate_'.$plugin_file, 'gfo_add_settings', 10, 1);
function gfo_add_settings($network_wide) {
	global $gutenfree; $gutenfree = gfo_get_defaults();
	add_option('guten_free_options', $gutenfree);
	// if (is_multisite() && $network_wide) {add_site_option('network_default_editor', '');}
}

// ----------------------
// Update Plugin Settings
// ----------------------
add_action('admin_init', 'gfo_update_settings');
function gfo_update_settings() {

	global $gutenfree;
	if (!isset($_POST['gfo_update_settings']) || ($_POST['gfo_update_settings'] != 'yes')) {return;}
	$capability = apply_filters('gfo_manage_options_capability', 'manage_options');
	if (!current_user_can($capability)) {return;}
	check_admin_referer('guten-free-options');

	$options = array(

		/* Switches */
		'default_editor'		=> 'classic/inherit/block',
		'switch_buttons'		=> 'checkbox',
		'disable_nag'			=> 'checkbox',
		'remove_menu'			=> 'checkbox',
		'check_blocks'			=> 'checkbox',
		'user_default'			=> 'checkbox',
		'editor_metabox'		=> 'checkbox',

		/* No Override Metabox */
		// 'no_override'		=> 'csv',

		/* Post Overrides */
		'classic_ids'			=> 'csv',
		'block_ids'				=> 'csv',

		/* Debug */
		'debug'					=> 'checkbox',

	);

	foreach ($options as $key => $type) {
		if (!isset($_POST['gfo_'.$key])) {$posted = '';}
		else {$posted = $_POST['gfo_'.$key];}

		if (strstr($type, '/')) {
			$valid = explode('/', $type);
			if (in_array($posted, $valid)) {$gutenfree[$key] = $posted;}
		} elseif ($type == 'checkbox') {
			if ( ($posted == '') || ($posted == 'yes') ) {$gutenfree[$key] = $posted;}
		} elseif ($type == 'numeric') {
			$posted = absint($posted);
			if (is_numeric($posted)) {$gutenfree[$key] = $posted;}
		} elseif ($type == 'alphanumeric') {
			$checkposted = preg_match('/^[a-zA-Z0-9_]+$/', $posted);
			if ($checkposted) {$gutenfree[$key] = $posted;}
		} elseif ($type == 'text') {
			$posted = sanitize_text_field($posted);
			$gutenfree[$key] = $posted;
		} elseif ($type == 'textarea') {
			$posted = stripslashes(wp_kses_post($posted));
			$gutenfree[$key] = $posted;
		} elseif ($type == 'csv') {
			$cleaned = array();
			if (strstr($posted, ',')) {$values = explode(',', $posted);} else {$values[0] = $posted;}
			foreach ($values as $i => $value) {
				$value = sanitize_text_field(trim($value));
				if ( ($value != '') && !in_array($value, $cleaned)) {$cleaned[] = $value;}
			}
			$gutenfree[$key] = $cleaned;
		}
	}
	// print_r($gutenfree); exit; // debug point

	// get post type settings
	$classic_types = $gutenfree['classic_types'];
	if (!is_array($classic_types)) {$classic_types = array();}
	$block_types = $gutenfree['block_types'];
	if (!is_array($block_types)) {$block_types = array();}
	$post_types = gfo_get_post_types();

	// get user role settings
	$classic_roles = $gutenfree['classic_roles'];
	if (!is_array($classic_roles)) {$classic_roles = array();}
	$block_roles = $gutenfree['block_roles'];
	if (!is_array($block_roles)) {$block_roles = array();}
	$role_types = gfo_get_user_roles();

	// get post template settings
	$classic_templates = $gutenfree['classic_templates'];
	if (!is_array($classic_templates)) {$classic_templates = array();}
	$block_templates = $gutenfree['block_templates'];
	if (!is_array($block_templates)) {$block_templates = array();}
	$templates = gfo_get_post_templates();

	// loop all post values to check keys
	foreach ($_POST as $key => $value) {

		if (strpos($key, 'gfo_post_type_') === 0) {

			// === Post Types ===
			// ------------------
			$key = substr($key, strlen('gfo_post_type_'), strlen($key));
			foreach ($post_types as $post_type_key => $post_type_label) {
				if ($key == $post_type_key) {$label = $post_type_label; break;}
			}

			if ($value == '') {
				if (array_key_exists($key, $block_types)) {unset($block_types[$key]);}
				if (array_key_exists($key, $classic_types)) {unset($classic_types[$key]);}
			} elseif ($value == 'classic') {
				if (!array_key_exists($key, $classic_types)) {$classic_types[$key] = $label;}
				if (array_key_exists($key, $block_types)) {unset($block_types[$key]);}
			} elseif ($value == 'block') {
				if (!array_key_exists($key, $block_types)) {$block_types[$key] = $label;}
				if (array_key_exists($key, $classic_types)) {unset($classic_types[$key]);}
			}

		} elseif (strpos($key, 'gfo_user_role_') === 0) {

			// === User Roles ===
			// ------------------
			$key = substr($key, strlen('gfo_user_role_'), strlen($key));
			foreach ($role_types as $role_type_key => $role_type_label) {
				if ($key == $role_type_key) {$label = $role_type_label; break;}
			}

			if ($value == '') {
				if (array_key_exists($key, $block_roles)) {unset($block_roles[$key]);}
				if (array_key_exists($key, $classic_roles)) {unset($classic_roles[$key]);}
			} elseif ($value == 'classic') {
				if (!array_key_exists($key, $classic_roles)) {$classic_roles[$key] = $label;}
				if (array_key_exists($key, $block_roles)) {unset($block_roles[$key]);}
			} elseif ($value == 'block') {
				if (!array_key_exists($key, $block_roles)) {$block_roles[$key] = $label;}
				if (array_key_exists($key, $classic_roles)) {unset($classic_roles[$key]);}
			}

		} elseif (strpos($key, 'gfo_template_') === 0) {

			// === Post Templates ===
			// ----------------------
			$key = substr($key, strlen('gfo_template_'), strlen($key));
			foreach ($templates as $type => $template) {
				foreach ($template as $file => $label) {
					if (!isset($post_templates[$file])) {$post_templates[$file] = $label;}
				}
			}

			foreach ($post_templates as $file => $label) {
				if ($key.'.php' == $file) {
					$label = $post_templates[$file];
					if ($value == '') {
						if (array_key_exists($key, $block_templates)) {unset($block_templates[$key]);}
						if (array_key_exists($key, $classic_templates)) {unset($classic_templates[$key]);}
					} elseif ($value == 'classic') {
						if (!array_key_exists($key, $classic_templates)) {$classic_templates[$key] = $label;}
						if (array_key_exists($key, $block_templates)) {unset($block_templates[$key]);}
					} elseif ($value == 'block') {
						if (!array_key_exists($key, $block_templates)) {$block_templates[$key] = $label;}
						if (array_key_exists($key, $classic_templates)) {unset($classic_templates[$key]);}
					}
				}
			}
		}
	}

	// update post type, role and template settings
	$gutenfree['classic_types'] = $classic_types;
	$gutenfree['block_types'] = $block_types;
	$gutenfree['classic_roles'] = $classic_roles;
	$gutenfree['block_roles'] = $block_roles;
	$gutenfree['classic_templates'] = $classic_templates;
	$gutenfree['block_templates'] = $block_templates;

	update_option('guten_free_options', $gutenfree);
}

// ---------------------
// Reset Plugin Settings
// ---------------------
add_action('admin_init', 'gfo_reset_settings');
function gfo_reset_settings() {
	if ( (!isset($_POST['gfo_update_settings'])) || ($_POST['gfo_update_settings'] != 'reset') ) {return;}
	$capability = apply_filters('gfo_manage_options_capability', 'manage_options');
	if (!current_user_can($capability)) {return;}
	check_admin_referer('guten-free-options');

	// reset plugin settings to defaults
	global $gutenfree; $gutenfree = gfo_get_defaults();
	update_option('guten_free_options', $gutenfree);

	// settings reset admin message
	add_action('admin_notice', 'gfo_reset_notice');
	function gfo_reset_notice() {
    	echo '<div class="notice notice-success is-dismissible">';
        echo '<p>'.__('Guten Free Option Settings Reset!', 'guten-free-options').'</p></div>';
	}
}

// ------------------------------
// Update Network Plugin Settings
// ------------------------------
add_action('admin_init', 'gfo_update_network_settings');
function gfo_update_network_settings() {
	if (!is_multisite() || !is_network_admin()) {return;}
	if ( (!isset($_POST['gfo_update_settings'])) || ($_POST['gfo_update_settings'] != 'network') ) {return;}
	$capability = apply_filters('gfo_manage_network_options_capability', 'manage_network_options');
	if (!current_user_can($capability)) {return;}
	check_admin_referer('guten-free-options');

	if (isset($_POST['gfo_network_default_editor'])) {
		$default_editor = $_POST['gfo_network_default_editor'];
		$valid = array('classic', 'block');
		if (in_array($default_editor, $valid)) {update_site_option('network_default_editor', $default_editor);}
	}
}

// ------------------
// Plugin Page Header
// ------------------
function gfo_plugin_page_header($network=false) {
	global $gutenfree;
	$icon_url = plugins_url('images/guten-free-options.png', __FILE__);
	echo '<table><tr><td><!-- <img src="'.$icon_url.'"> --></td>';
	echo '<td width="20"></td><td>';
		echo "<table><tr><td><h3 style='font-size:20px;'>".$gutenfree['title']."</h3></td><td width='20'></td>";
		echo "<td><h3>v".$gutenfree['version']."</h3></td></tr>";
		echo "<tr><td colspan='3' align='center'>".__('by','guten-free-options');
		echo " <a href='http://wpmedic.tech/guten-free-options/' style='text-decoration:none;' target=_blank><b>WP Medic</b></a>";
		echo "</td></tr></table>";
	echo '</td><td width="50"></td>';
	if ( (isset($_REQUEST['updated'])) && ($_REQUEST['updated'] == 'yes') ) {
		echo "<td><table style='background-color: lightYellow; border-style:solid; border-width:1px; border-color: #E6DB55; text-align:center;'>";
		echo "<tr><td><div class='message' style='margin:0.25em;'><font style='font-weight:bold;'>";
		if ($network) {echo __('Network','guten-free-options')." ";}
		echo __('Settings Updated.','guten-free-options')."</font></div></td></tr></table></td>";
		echo '<td width="50"></td>';
	}
	echo '</tr></table><br>';
}

// --------------------
// Plugin Settings Page
// --------------------
function gfo_settings_page() {

	$capability = apply_filters('gfo_manage_options_capability', 'manage_options');
	if (!current_user_can($capability)) {return;}

	global $gutenfree;

	// plugin settings page styles
	$styles = ".label-cell {min-width:200px;} .post-input {width:400px;}
	.yes-no-cell {width:65px; text-align:center;}";
	$styles = apply_filters('gfo_settings_page_styles', $styles);
	if ($styles != '') {echo "<style>".$styles."</style>";}

	// plugin settings page header
	gfo_plugin_page_header();

	// cascading settings / usage note
	echo __('This plugin sets the Default Editor to be used by checking these conditions in order:','guten-free-options')."<br>";
	if (is_multisite()) {
		$network_options_url = add_query_arg('page', 'guten-free-network-options', network_admin_url());
		echo "<a href='".$network_options_url."'><i>".__('Network Default','guten-free-option')."</i></a> -> ";
	} else {
		echo "(<i>".__('Network Default','guten-free-options')."</i> ";
		echo __('which optionally sets the default editor on Multisite installations.','guten-free-options');
		echo ")<br>";
	}
	echo "<i>".__('Site Default','guten-free-options')."</i> -> ";
	echo "<i>".__('User Filtering')."</i> -> ";
	echo "<i>".__('Post Type Filtering','guten-free-options')."</i> -> ";
	echo "<i>".__('Post Filtering','guten-free-options')."</i> -> ";
	echo "<i>".__('Querystring','guten-free-options')."</i><br>";

	echo "<i>".__('Admin Post Overrides','guten-free-options')."</i> ";
	echo __('set here will override all these settings for specified posts.','guten-free-options')."<br><br>";

	echo __('If any of the conditions set match for the pageload, they will override the previous level(s).','guten-free-options')."<br>";
	echo __('This makes for the most flexible options, while still keeping all settings completely optional!','guten-free-options')."<br>";
	echo __('(Similar to how cascading stylesheet rules work for styling page elements.)','guten-free-options')."<br><br>";

	// shorthand settings note
	echo "<table><tr><td><i>".__('Classic','guten-free-options')."</i> = <i>".__('Classic TinyMCE Editor','guten-free-options')."</i></td>";
	echo "<td width='10'></td><td> | </td><td width='10'></td>";
	echo "<td><i>".__('Block','guten-free-options')."</i> = <i>".__('Gutenberg Block Editor','guten-free-options')."</i></td></tr></table>";

	// open settings form
	echo "<form id='gfo-settings-form' method='post'>";
	echo "<input type='hidden' name='page' value='guten-free-options'>";
	echo "<input type='hidden' id='gfo-update-action' name='gfo_update_settings' value='yes'>";
	wp_nonce_field('guten-free-options');

	// === Default Editor Settings ===
	// -------------------------------
	echo "<h3>".__('Default Editor Settings','guten-free-options')."</h3>";
	echo "<table>";

		// --- Site Default Editor ---
		$default_editor = gfo_get_setting('default_editor', false);
		echo "<tr><td class='label-cell' style='vertical-align:top;'><b>".__('Site Default Editor','guten-free-options')."</b></td>";
		echo "<td width='10'></td>";
		echo "<td><input type='radio' name='gfo_default_editor' value='classic'";
			if ($default_editor == 'classic') {echo " checked";}
		echo "> ".__('Classic','guten-free-options')."</td><td width='10'></td>";

		echo "<td><input type='radio' name='gfo_default_editor' value='block'";
			if ($default_editor == 'block') {echo " checked";}
		echo "> ".__('Block','guten-free-options')."</td><td width='10'></td>";

		echo "<td>";
			// optional inherit from multisite setting
			if (is_multisite()) {
				echo "<table><tr><td>";
				echo "<input type='radio' name='gfo_default_editor' value='inherit'";
					if ($default_editor == '') {echo " checked";}
				echo "> ".__('Inherit','guten-free-options')."</td>";
				echo "<td width='20'></td><td>";
			}
			echo __('Sets the Site Default Editor.','guten-free-options');
			if (is_multisite()) {echo "</td></tr></table>";}
		echo "</td></tr>";

		// --- Switch Editor Buttons? ---
		$guten_free = gfo_get_setting('switch_buttons', false);
		echo "<tr><td class='label-cell' style='vertical-align:top;'><b>".__('Switch Editor Buttons?','guten-free-options')."</b></td>";
		echo "<td width='10'></td>";
		echo "<td class='yes-no-cell'><input type='radio' name='gfo_switch_buttons' value='yes'";
			if ($guten_free == 'yes') {echo " checked";}
		echo "> ".__('Yes')."</td><td width='10'></td>";
		echo "<td class='yes-no-cell'><input type='radio' name='gfo_switch_buttons' value=''";
			if ($guten_free != 'yes') {echo " checked";}
		echo "> ".__('No')."</td><td width='10'></td>";
		echo "<td>".__('Add a Switch Editor Buttons to both Editors.','guten-free-options')."</td></tr>";

		echo "<tr height='10'><td> </td></tr>";

		// --- Disable Try Gutenberg Nag? ---
		$disable_nag = gfo_get_setting('disable_nag', false);
		echo "<tr><td class='label-cell'><b>".__('Disable Try Gutenberg Panel?','guten-free-options')."</b></td>";
		echo "<td width='10'></td>";
		echo "<td class='yes-no-cell'><input type='radio' name='gfo_disable_nag' value='yes'";
			if ($disable_nag == 'yes') {echo " checked";}
		echo "> ".__('Yes')."</td><td width='10'></td>";
		echo "<td class='yes-no-cell'><input type='radio' name='gfo_disable_nag' value=''";
			if ($disable_nag != 'yes') {echo " checked";}
		echo "> ".__('No')."</td><td width='10'></td>";
		echo "<td>".__('Affects pre-5.0 WordPress only.','guten-free-options')."</td></tr>";

		// --- Remove Gutenberg Admin Menu? ---
		$remove_menu = gfo_get_setting('remove_menu', false);
		echo "<tr><td class='label-cell'><b>".__('Remove Gutenberg Menu?','guten-free-options')."</b></td>";
		echo "<td width='10'></td>";
		echo "<td class='yes-no-cell'><input type='radio' name='gfo_remove_menu' value='yes'";
			if ($remove_menu == 'yes') {echo " checked";}
		echo "> ".__('Yes')."</td><td width='10'></td>";
		echo "<td class='yes-no-cell'><input type='radio' name='gfo_remove_menu' value=''";
			if ($remove_menu != 'yes') {echo " checked";}
		echo "> ".__('No')."</td><td width='10'></td>";
		echo "<td>".__('Affects active Gutenberg plugin only.','guten-free-options')."</td></tr>";

	echo "</table>";

	// ----------------------
	// === USER FILTERING ===
	// ----------------------
	echo "<h3>".__('User Filtering','guten-free-options')."</h3>";

	// --- User Role Defaults ---
	// --------------------------
	echo "<h4>".__('User Role Defaults','guten-free-options')."</h4>";

	// get all user role settings
	$role_types = gfo_get_user_roles();
	$classic_roles = gfo_get_setting('classic_roles');
	$block_roles = gfo_get_setting('block_roles');

	// merge roles to preserve existing settings...
	$all_role_types = array();
	if (is_array($classic_roles)) {
		foreach ($classic_roles as $key => $label) {$all_role_types[$key] = $label;}
	}
	if (is_array($block_roles)) {
		foreach ($block_roles as $key => $label) {$all_role_types[$key] = $label;}
	}
	foreach ($role_types as $key => $label) {
		if (!array_key_exists($key, $all_role_types)) {$all_role_types[$key] = $label;}
	}
	// print_r($all_role_types);

	// role type table headers
	echo "<table><tr><td class='label-cell'><b>".__('User Role','guten-free-options')."</b></td><td width='10'></td>";
	echo "<td align='center'><b>".__('Classic','guten-free-options')."</b></td><td width='5'></td>";
	echo "<td align='center'><b>".__('Inherit','guten-free-options')."</b></td><td width='5'></td>";
	echo "<td align='center'><b>".__('Block','guten-free-options')."</b></td></tr>";

		// loop role types to display options
		foreach ($role_types as $key => $label) {
			echo "<tr><td class='label-cell'>".$label."</td><td width='10'></td>";
			echo "<td align='center'><input type='radio' name='gfo_user_role_".$key."' value='classic'";
				if (array_key_exists($key, $classic_roles)) {echo " checked";}
			echo "></td><td width='5'></td>";
			echo "<td align='center'><input type='radio' name='gfo_user_role_".$key."' value=''";
				if (!array_key_exists($key, $classic_roles) && !array_key_exists($key, $block_roles)) {echo " checked";}
			echo "></td><td width='5'></td>";
			echo "<td align='center'><input type='radio' name='gfo_user_role_".$key."' value='block'";
				if (array_key_exists($key, $block_roles)) {echo " checked";}
			echo "></td></tr>";
		}
	echo "</table>";

	// --- User Default Editor? ---
	echo "<table>";
		$user_default = gfo_get_setting('user_default', false);
		echo "<tr><td class='label-cell' style='vertical-align:top;'><b>".__('User Editor Selection?','guten-free-options')."</b></td>";
		echo "<td width='10'></td>";
		echo "<td class='yes-no-cell' style='vertical-align:top;'><input type='radio' name='gfo_user_default' value='yes'";
			if ($user_default == 'yes') {echo " checked";}
		echo "> ".__('Yes')."</td><td width='10'></td>";
		echo "<td class='yes-no-cell' style='vertical-align:top;'><input type='radio' name='gfo_user_default' value=''";
			if ($user_default != 'yes') {echo " checked";}
		echo "> ".__('No')."</td><td width='10'></td>";
		echo "<td>".__('Adds default editor selection to user profile.','guten-free-options')."<br>";
		echo __('(if disabled, existing user selections are ignored.)','guten-free-option')."</td></tr>";
	echo "</table>";

	// ---------------------------
	// === POST TYPE FILTERING ===
	// ---------------------------
	echo "<h3>".__('Post Type Filtering','guten-free-options')."</h3>";

	// --- Post Type Defaults ---
	// --------------------------
	echo "<h4>".__('Post Type Defaults','guten-free-options')."</h4>";

	// get all post type settings
	$post_types = gfo_get_post_types();
	$classic_types = gfo_get_setting('classic_types', false);
	$block_types = gfo_get_setting('block_types', false);

	// merge all post types (to preserve existing settings)
	$all_post_types = array();
	if (is_array($classic_types)) {
		foreach ($classic_types as $key => $label) {$all_post_types[$key] = $label;}
	}
	if (is_array($block_types)) {
		foreach ($block_types as $key => $label) {$all_post_types[$key] = $label;}
	}
	foreach ($post_types as $key => $label) {
		if (!array_key_exists($key, $all_post_types)) {$all_post_types[$key] = $label;}
	}
	// print_r($all_post_types);

	// post types table headings
	echo "<table><tr><td class='label-cell'><b>".__('Post Type','guten-free-options')."</b></td><td width='10'></td>";
	echo "<td align='center'><b>".__('Classic','guten-free-options')."</b></td><td width='5'></td>";
	echo "<td align='center'><b>".__('Inherit','guten-free-options')."</b></td><td width='5'></td>";
	echo "<td align='center'><b>".__('Block','guten-free-options')."</b></td></tr>";

		// loop all post types to display options
		foreach ($all_post_types as $key => $label) {
			echo "<tr><td>".$label."</td><td width='10'></td>";
			echo "<td align='center'><input type='radio' name='gfo_post_type_".$key."' value='classic'";
				if (array_key_exists($key, $classic_types)) {echo " checked";}
			echo "></td><td width='5'></td>";
			echo "<td align='center'><input type='radio' name='gfo_post_type_".$key."' value=''";
				if (!array_key_exists($key, $classic_types) && !array_key_exists($key, $block_types)) {echo " checked";}
			echo "></td><td width='5'></td>";
			echo "<td align='center'><input type='radio' name='gfo_post_type_".$key."' value='block'";
				if (array_key_exists($key, $block_types)) {echo " checked";}
			echo "></td></tr>";
		}

	echo "</table>";

	// ----------------------------
	// === POST LEVEL FILTERING ===
	// ----------------------------
	echo "<h3>".__('Post Level Filtering','guten-free-options')."</h3>";

	echo "<table>";
		// --- Check for Blocks? ---
		$check_blocks = gfo_get_setting('check_blocks', false);
		echo "<tr><td class='label-cell'><b>".__('Check Content for Blocks?','guten-free-options')."</b></td><td width='10'></td>";
		echo "<td class='yes-no-cell'><input type='radio' name='gfo_check_blocks' value='yes'";
			if ($check_blocks == 'yes') {echo " checked";}
		echo "> ".__('Yes')."</td><td width='10'></td>";
		echo "<td class='yes-no-cell'><input type='radio' name='gfo_check_blocks' value=''";
			if ($check_blocks != 'yes') {echo " checked";}
		echo "> ".__('No')."</td><td width='10'></td>";
		echo "<td>".__('Defaults to Block Editor (Gutenberg) if blocks found.','guten-free-options')."</td></tr>";
	echo "</table>";

	// --- Post Templates ---
	// ----------------------
	echo "<h4>".__('Post Template Defaults','guten-free-options')."</h4>";

	$templates = gfo_get_post_templates();
	$classic_templates = gfo_get_setting('classic_templates');
	$block_templates = gfo_get_setting('block_templates');

	// combine existing settings (to persist beyond current theme)
	$post_templates = array();
	if (is_array($classic_templates)) {
		foreach ($classic_templates as $file => $label) {$post_templates[$file] = $label;}
	}
	if (is_array($block_templates)) {
		foreach ($block_templates as $file => $label) {$post_templates[$file] = $label;}
	}
	// resort array for easier template type access
	foreach ($templates as $type => $template) {
		foreach ($template as $file => $label) {
			$file = str_replace('.php', '', $file);
			// if (!is_array($post_template_types[$file])) {$post_template_types[$file] = array();}
			// if (!in_array($type, $post_template_types[$file])) {$post_template_types[$file][] = $type;}
			if (!isset($post_templates[$file])) {$post_templates[$file] = $label;}
		}
	}
	// print_r($post_templates);

	// loop templates and output option display
	if (count($post_templates) > 0) {

		echo "<table><tr><td class='label-cell'><b>".__('Post Template','guten-free-options')."</b></td><td width='10'></td>";
		echo "<td align='center'><b>".__('Classic','guten-free-options')."</b></td><td width='5'></td>";
		echo "<td align='center'><b>".__('Inherit','guten-free-options')."</b></td><td width='5'></td>";
		echo "<td align='center'><b>".__('Block','guten-free-options')."</b></td></tr>";

		foreach ($post_templates as $key => $label) {
			echo "<tr><td class='label-cell'>".$label."</td><td width='10'></td>";
			echo "<td align='center'><input type='radio' name='gfo_template_".$key."' value='classic'";
				if (array_key_exists($key, $classic_templates)) {echo " checked";}
			echo "></td><td width='10'></td>";
			echo "<td align='center'><input type='radio' name='gfo_template_".$key."' value=''";
				if (!array_key_exists($key, $classic_templates) && !array_key_exists($key, $block_templates)) {echo " checked";}
			echo "></td><td width='10'></td>";
			echo "<td align='center'><input type='radio' name='gfo_template_".$key."' value='block'";
				if (array_key_exists($key, $block_templates)) {echo " checked";}
			echo "></td></tr>";
		}
		echo "</table>";
	} else {echo __('No Post Templates found in Theme or Settings.','guten-free-options')."<br><br>";}

	echo "<table><tr height='20'><td></td>";

		// --- Add Editor Metabox? ---
		$editor_metabox = gfo_get_setting('editor_metabox', false);
		echo "<tr><td class='label-cell'><b>".__('Editor Metabox Overrides?','guten-free-options')."</b></td><td width='10'></td>";
		echo "<td class='yes-no-cell'><input type='radio' name='gfo_editor_metabox' value='yes'";
			if ($editor_metabox == 'yes') {echo " checked";}
		echo "> ".__('Yes')."</td><td width='10'></td>";
		echo "<td class='yes-no-cell'><input type='radio' name='gfo_editor_metabox' value=''";
			if ($editor_metabox != 'yes') {echo " checked";}
		echo "> ".__('No')."</td><td width='10'></td>";
		echo "<td>".__('Adds metabox to writing screen allowing editor override.','guten-free-options')."</td></tr>";

		// --- No Override Metabox ---
		// $no_override = gfo_get_setting('no_override', false);
		// if (is_array($no_override)) {$no_override = implode(',', $no_override);}
		// echo "<tr><td class='label-cell' style='vertical-align:top;'><b>".__('No Metabox Post Types','guten-free-options')."</b></td><td width='10'></td>";
		// echo "<td colspan='5'><input class='post-input' type='text' name='gfo_no_override' value='".$no_override."'><br>";
		// echo __('(comma-separated list of Post Type slugs)','guten-free-options')."</td></tr>";

	echo "</table>";

	// --- Admin Post Overrides ---
	// ----------------------------
	$classic_ids = gfo_get_setting('classic_ids', false);
	if (is_array($classic_ids)) {$classic_ids = implode(', ', $classic_ids);}
	$block_ids = gfo_get_setting('block_ids', false);
	if (is_array($block_ids)) {$block_ids = implode(', ', $block_ids);}

	echo "<h3>".__('Admin Overrides','guten-free-options')."</h3>";
	echo __('Enter Post IDs or Slugs (comma-separated) and the Editor used will be <i>forced on</i> for those posts.','guten-free-options')."<br>";
	echo __('Note: Both querystring and metabox overrides will no longer be checked or used for these posts.','guten-free-options')."<br>";
	echo "<table>";
		echo "<tr><td class='label-cell'><b>".__('Use Classic Editor','guten-free-options')."</b></td><td width='10'></td>";
		echo "<td><input type='text' class='post-input' name='gfo_classic_ids' value='".$classic_ids."'></td></tr>";
		echo "<tr><td class='label-cell'><b>".__('Use Block Editor','guten-free-options')."</b></td><td width='10'></td>";
		echo "<td><input type='text' class='post-input' name='gfo_block_ids' value='".$block_ids."'></td></tr>";
	echo "</table>";

	// check and warn for overlapping overrides
	if (is_array($classic_ids) && is_array($block_ids)) {
		$common = array_intersect($classic_ids, $block_ids);
		if (is_array($common) && (count($common) > 0)) {
			echo __('Warning! There is an override overlap. Overrides are cancelled for these settings:','guten-free-options');
			echo "<br>".implode(', ', $common)."<br>";
		}
	}

	// debug switch and submit buttons
	echo "<table><tr height='30'><td> </td></tr>";

		// debug mode switch
		$debug = gfo_get_setting('debug', false);
		echo "<tr><td class='label-cell'><b>".__('Debug Mode?','guten-free-options')."</b></td><td width='10'></td>";
		echo "<td class='yes-no-cell'><input type='radio' name='gfo_debug' value='yes'";
			if ($debug == 'yes') {echo " checked";}
		echo "> ".__('Yes')."</td><td width='10'></td>";
		echo "<td class='yes-no-cell'><input type='radio' name='gfo_debug' value=''";
			if ($debug != 'yes') {echo " checked";}
		echo "> ".__('No')."</td><td width='10'></td>";
		echo "<td>".__('Logs editor condition changes to debug.log','guten-free-options')."</td></tr>";

		echo "<tr height='20'><td> </td></tr>";

		// reset and submit buttons
		echo "<tr><td align='center'><input type='button' class='button-secondary' onclick='return gfo_reset_settings();' value='".__('Reset Settings','guten-free-options')."'></td>";
		echo "<td width='10'></td>";
		echo "<td align='center' colspan='5'><input type='submit' class='button-primary' value='".__('Update Settings','guten-free-options')."'></td></tr>";

	echo "</table>";

	// close settings form
	echo "</form><br><br><br>";

	// reset settings form script
	$confirm_message = __('Are you sure you want to reset the plugin settings?','guten-free-options');
	echo "<script>function gfo_reset_settings() {
		var agree = confirm('".$confirm_message."');
		if (!agree) {return false;}
		document.getElementById('gfo-update-action').value = 'reset';
		document.getElementById('gfo-settings-form').submit();
	}</script>";

}

// -------------------------------
// Multisite Network Settings Page
// -------------------------------
function gfo_network_settings_page() {

	// plugin page network header
	gfo_plugin_page_header(true);

	// network default selection form
	echo "<form id='gfo-network-settings-form' method='post'>";
	echo "<input type='hidden' name='page' value='guten-free-network-options'>";
	echo "<input type='hidden' name='gfo_update_settings' value='network'>";
	wp_nonce_field('guten-free-options');

	// table heading
	echo "<table><tr>";
	echo "<td><b>".__('Network Default Editor','guten-free-options')."</b></td><td width='10'></td>";

	// get current default editor for network
	$default_editor = get_site_option('network_default_editor', '');

	// classic editor
	$classic_image = plugins_url('images/classicberger.png', __FILE__);
	echo "<td align='center'><!-- <img src='".$classic_image."'> --><br>";
	echo "<input type='radio' name='gfo_network_default_editor' value='classic'";
		if ($default_editor == 'classic') {echo " checked";}
	echo "> ".__('Classic Editor','guten-free-options')."<br>(".__('TinyMCE','guten-free-options').")";
	echo "</td><td width='10'></td>";

	// site specific
	$default_image = plugins_url('images/wordpress.png', __FILE__);
	echo "<td align='center'><!-- <img src='".$default_image."'> --><br>";
	echo "<input type='radio' name='gfo_network_default_editor' value=''";
		if ($default_editor == '') {echo " checked";}
	echo "> ".__('No Network Default','guten-free-options')."<br>(".__('Per Site Only','guten-free-options').")";
	echo "</td><td width='10'></td>";

	// gutenberg block editor
	$guten_image = plugins_url('images/gutenberger.png', __FILE__);
	echo "<td align='center'><!-- <img src='".$guten_image."'> --><br>";
	echo "<input type='radio' name='gfo_network_default_editor' value='block'";
		if ($default_editor == 'block') {echo " checked";}
	echo "> ".__('Block Editor','guten-free-options')."<br>(".__('Gutenberg','guten-free-options').")";
	echo "</td></tr>";

	// network settings note
	echo "<tr height='20'><td></td></tr>";
	echo "<tr><td colspan='7' align='center'>".__('Further editor defaults can be set from any individual site settings page.','guten-free-options');
	echo "</td></tr>";

	// submit button
	echo "<tr height='20'><td></td></tr>";
	echo "<tr><td colspan='2'></td><td colspan='3' align='center'>";
	echo "<input type='submit' class='button-primary' value='".__('Update Network Settings','guten-free-options')."'>";
	echo "</td></tr></table></form><br><br>";
}


// ======
// LOADER
// ======

// -----------------
// Guten Free Cooker
// -----------------
add_action('plugins_loaded', 'gfo_cooker', 9);
function gfo_cooker() {

	global $gutenfree;

	// 0.9.1: add post type filter after plugins_loaded to allow priority filtering
	$filter_priority = apply_filters('gfo_post_type_filter_priority', 11);
	add_filter('gutenberg_can_edit_post_type', 'gfo_can_edit_post_type', $filter_priority, 2);
	add_filter('use_block_editor_for_post_type', 'gfo_can_edit_post_type', $filter_priority, 2);

	// 0.9.1: add single post filter after plugins_loaded to allow priority filtering
	$filter_priority = apply_filters('gfo_single_post_filter_priority', 11);
	add_filter('use_block_editor_for_post', 'gfo_can_edit_post', $filter_priority, 2);
	add_filter('gutenberg_can_edit_post', 'gfo_can_edit_post', $filter_priority, 2);

	// remove the Classic Editor plugin init action (override it to prevent conflicts!)
	// ...and also remove its Settings link to the settings from the plugins screen
	if (function_exists('classic_editor_init_actions')) {
		remove_action('plugins_loaded', 'classic_editor_init_actions');
		remove_filter('plugin_action_links', 'classic_editor_add_settings_link');
		remove_action('admin_init', 'classic_editor_admin_init');
	}
	// TODO: check for other Gutenberg disabler plugins and print admin notice if found ?

	// maybe add user default editor selection field
	if (gfo_get_setting('user_default') == 'yes') {
		add_action('show_user_profile', 'gfo_user_default_editor_field');
		add_action('edit_user_profile', 'gfo_user_default_editor_field');
		add_action('personal_options_update', 'gfo_user_default_editor_save');
		add_action('edit_user_profile_update', 'gfo_user_default_editor_save');
	}

	// maybe add editor override metabox
	if (gfo_get_setting('editor_metabox') == 'yes') {
		add_action('admin_init', 'gfo_editor_override_metabox_add');
		add_action('save_post', 'gfo_editor_override_metabox_save');
	}

	// maybe remove the "Try Gutenberg" dashboard widget
	// TODO: maybe replace "try Guenberg" panel with plugin-relevant information ?
	if (gfo_get_setting('disable_nag') == 'yes') {
		if (has_filter('try_gutenberg_panel', 'wp_try_gutenberg_panel')) {
			remove_filter('try_gutenberg_panel', 'wp_try_gutenberg_panel');
		}
	}

	// always maintain querystrings through redirections
	add_filter('redirect_post_location', 'gfo_redirect_location');

	// always maintain classic editor if in querystring
	add_filter('get_edit_post_link', 'gfo_get_edit_post_link');

	// redirect correctly on saving in the classic editor
	add_action('edit_form_top', 'gfo_remember_when_saving_posts');

	// check if Gutenberg will load
	$will_load = gfo_will_gutenberg_load();

	// maybe remove gutenberg menu (plugin only)
	if ($will_load == 'plugin') {
		$remove_menu = gfo_get_setting('remove_menu');
		// 0.9.1: check for exact value of remove_menu
		if ( ($remove_menu == 'yes') && has_action('admin_menu', 'gutenberg_menu') ) {
			remove_action('admin_menu', 'gutenberg_menu');
		}
	}

	// check if Gutenberg should load
	$check_load = gfo_check_gutenberg_load();

	// to debug loading states
	$debugload = false;
	if ($debugload) {
		if ($check_load) {echo "Gutenberg Should Load";} else {echo "Gutenberg Should NOT Load";}
		if ($will_load) {echo "Gutenberg Will Load";} else {echo "Gutenberg Will NOT Load";}
	}

	// maybe load or unload
	if (!$check_load && $will_load) {

		// unload gutenberg (if possible!)
		gfo_force_gutenberg_unload($will_load);
		$load = false;

	} elseif ($check_load && !$will_load) {

		// attempt force load of Gutenberg plugin
		$load = gfo_force_gutenberg_load();

	}

	// gutenberg or block editor is loaded
	if (!isset($load) || (isset($load) && $load) ) {

		// add Classic Editor Admin menu links
		add_action( 'admin_menu', 'gfo_classic_editor_submenus' );

		// Admin Bar menu links (removed in favour of onscreen buttons)
		// add_action( 'admin_bar_menu', 'gfo_admin_bar_menu', 120 );

		// Classic Editor Row actions (edit.php)
		// removed as calculating for each post listed could slow pageload,
		// and clicking Edit will now go to default editor for post anyway
		// note: 'page' here actually means any hierarchical post type
		// add_filter('page_row_actions', 'gfo_add_edit_links', 15, 2);
		// add_filter('post_row_actions', 'gfo_add_edit_links', 15, 2);

	} else {

		// 0.9.1: add Block Editor Admin menu links
		add_action( 'admin_menu', 'gfo_block_editor_submenus' );

	}

}

// -------------------
// Will Gutenberg Load
// -------------------
function gfo_will_gutenberg_load() {

	// Gutenberg will load...

	// ...for WordPress version 5.0-beta1 and up
	global $wp_version;
	if (version_compare($wp_version, '5.0-beta', '>=')) {return 'inbuilt';}

	// ...if the Gutenberg plugin is active
	if (gfo_is_gutenberg_plugin_active()) {return 'plugin';}

	// ...if one these filters is present
	if ( has_filter('replace_editor', 'gutenberg_init')
	  || has_filter('load-post.php', 'gutenberg_intercept_edit_post')
	  || has_filter('load-post-new.php', 'gutenberg_intercept_post_new') ) {return 'plugin';}

	return false;
}

// --------------------
// Check Gutenberg Load
// --------------------
function gfo_check_gutenberg_load($post_id=null) {

	// check for editor querystrings (user overrides)
	if (isset($_GET['editor'])) {
		if ($_GET['editor'] == 'block') {return true;}
		elseif ($_GET['editor'] == 'classic') {return false;}
	} elseif (isset($_GET['classic-editor'])) {return false;}

	// load Gutenberg on the front-end to allow blocks to render correctly
	if (!is_admin()) {

		// return now as no post ID is available here
		if (is_null($post_id)) {return true;}

		// TODO: maybe check if current post has Gutenberg blocks ?
		// but without post_id, we may need some further way to run post query early ?
		// $post = get_post($post_id);
		// $hasblocks = gfo_has_blocks($post->content);
		// return apply_filters('gfo_load_gutenberg', $hasblocks, false);

		return true; // TEMP
	}

	// set null load to start with
	$load = null;

	// maybe get base multisite network setting for default editor
	$load = gfo_check_network_default($load);

	// get base site setting for default editor
	$load = gfo_check_site_default($load);

    // only load on admin edit screens anyway
	if (!gfo_is_editor_admin_url()) {$load = false;}
	else {
		// set default load if not already set
		if (is_null($load)) {$load = true;}

		// if possible, check post type and post ID (plugin settings)
		if (is_null($post_id) && isset($_GET['post'])) {$post_id = absint($_GET['post']);}
		if (!is_null($post_id) && is_numeric($post_id) && ($post_id > 0)) {

			// note: these will only work on admin editor pages
			// where there is a post ID available to be checked
			// ...otherwise need to rely on later filtering anyway
			$post_type = gfo_get_post_type($post_id);
			$load = gfo_can_edit_post_type($load, post_type);
			$load = gfo_can_edit_post($load, $post_id);

		} else {
			// check user default editor (roles / user selection)
			global $current_user; $current_user = wp_get_current_user();
			$load = gfo_check_user_role($load, null, $current_user);
			$load = gfo_check_user_selection($load, null, $current_user);
		}
	}

	// allow for further filtering for this post combo
	$load = apply_filters('gfo_load_gutenberg', $load, $post_id);
	return $load;
}

// -------------------------
// Get Post Type for Post ID
// -------------------------
// (with additional checks for admin edit screen and querystring)
function gfo_get_post_type($post_id) {
	$post_type = false;
	if (0 === (int)$post_id) {
		// use querystring or set to post for post-new.php if not specified and on that page
		if (isset($_GET['post_type'])) {$post_type = sanitize_title($_GET['post_type']);}
		elseif (gfo_is_gutenberg_admin_url(array('post-new.php'))) {$post_type = 'post';}
	} else {$post_type = get_post_type($post_id);}
	return $post_type;
}


// =================
// POST TYPE FILTERS
// =================

// ----------------------------
// Check Can Edit For Post Type
// ----------------------------
function gfo_can_edit_post_type($can_edit, $post_type) {

	global $gutenfree; $edit = $can_edit;

	// check for editor querystrings (user overrides)
	if (isset($_GET['editor'])) {
		if ($_GET['editor'] == 'block') {return true;}
		elseif ($_GET['editor'] == 'classic') {return false;}
	} elseif (isset($_GET['classic-editor'])) {return false;}

	// ? maybe force use of block editor for wp_block post types ?
	// if ($post_type == 'wp_block') {return true;}

	// maybe prevent duplicate (re)filtering
	if (isset($gutenfree[$post_type.'_filtered'])) {return $gutenfree[$post_type.'_filtered'];}

	// maybe get base network setting for default editor
	add_filter('gfo_post_type_filters', 'gfo_check_network_default', 10, 3);

	// get base site setting for default editor
	add_filter('gfo_post_type_filters', 'gfo_check_site_default', 20, 3);

	// check user role (plugin setting)
	add_filter('gfo_post_type_filters', 'gfo_check_user_role', 30, 3);

	// check user selection (user meta)
	add_filter('gfo_post_type_filters', 'gfo_check_user_selection', 40, 3);

	// check post type default (plugin setting)
	add_filter('gfo_post_type_filters', 'gfo_check_post_type', 50, 3);

	// apply all post type editor default filtering
	global $current_user; $current_user = wp_get_current_user();
	$can_edit = apply_filters('gfo_post_type_filters', $can_edit, $post_type, $current_user);

	if ($can_edit != $edit) {
		if ($can_edit) {gfo_debug_log('Overall Post Type Filtering -> Block');}
		else {gfo_debug_log('Overall Post Type Filtering -> Classic');}
	}

	$gutenfree[$post_type.'_filtered'] = $can_edit;
	return $can_edit;
}

// ---------------------
// Check Network Default
// ---------------------
// maybe get base network setting for default editor
function gfo_check_network_default($can_edit, $post_type=null, $user=null) {
	if (!is_multisite()) {return $can_edit;}
	$editor = get_site_option('network_default_editor');
	if ($editor == 'classic') {$can_edit = false; /* gfo_debug_log('Multisite Network Default -> Classic'); */}
	elseif ($editor == 'block') {$can_edit = true; /* gfo_debug_log('Multisite Network Default -> Block'); */}
	return $can_edit;
}

// ------------------
// Check Site Default
// ------------------
// get base site setting for default editor
function gfo_check_site_default($can_edit, $post_type=null, $user=null) {
	$editor = gfo_get_setting('default_editor');
	if ($can_edit && ($editor == 'classic')) {$can_edit = false; /* gfo_debug_log('Site Default -> Classic'); */}
	elseif (!$can_edit && ($editor == 'block')) {$can_edit = true; /* gfo_debug_log('Site Default -> Block'); */}
	return $can_edit;
}

// ---------------
// Check User Role
// ---------------
// check user role for default editor (plugin settings)
function gfo_check_user_role($can_edit, $post_type=null, $user) {

	// if (!is_user_logged_in()) {return $can_edit;}
	if ($user->ID === 0) {return $can_edit;}

	$roles = $user->roles;
	$classic_role = $block_role = false;
	if ($roles && is_array($roles)) {
		$classic_roles = gfo_get_setting('classic_roles');
		if ($classic_roles && is_array($classic_roles)) {
			$common = array_intersect($roles, array_keys($classic_roles));
			if (is_array($common) && (count($common) > 0)) {
				$classic_role = true; $classic_common = implode(',', $common);
			}
		}
		$block_roles = gfo_get_setting('block_roles');
		if ($block_roles && is_array($block_roles)) {
			$common = array_intersect($roles, array_keys($block_roles));
			if (is_array($common) && (count($common) > 0)) {
				$block_role = true; $block_common = implode(',', $common);
			}
		}
	}

	// only change if no conflicting roles
	if ($classic_role && !$block_role) {
		$can_edit = false; gfo_debug_log('User Role ('.$classic_common.') -> Classic');
	} elseif ($block_role && !$classic_role) {
		$can_edit = true; gfo_debug_log('User Role ('.$block_common.') -> Block');
	}
	return $can_edit;
}

// ------------------
// Check User Default
// ------------------
// check possible user default editor selection (user meta)
function gfo_check_user_selection($can_edit, $post_type=null, $user) {
	if (gfo_get_setting('user_default') == 'yes') {
		$editor = get_user_meta($user->ID, '_default_editor', true);
		if ($editor && ($editor != '')) {
			if (!$can_edit && ($editor == 'block')) {
				$can_edit = true; gfo_debug_log('User Profile Setting -> Block');
			} elseif ($can_edit) {
				// note: probably 'classic' but could be another editor value
				$can_edit = false; gfo_debug_log('User Profile Setting -> Classic');
			}
		}
	}
	return $can_edit;
}

// -----------------------
// Check Post Type Default
// -----------------------
function gfo_check_post_type($can_edit, $post_type, $user=null) {
	if ($can_edit) {
		$classic_types = gfo_get_setting('classic_types');
		if (is_array($classic_types) && in_array($post_type, $classic_types)) {
			$can_edit = false; gfo_debug_log('Post Type '.$post_type.' -> Classic');
		}
	} else {
		$block_types = gfo_get_setting('block_types');
		if (is_array($block_types) && in_array($post_type, $block_types)) {
			$can_edit = true; gfo_debug_log('Post Type '.$post_type.' -> Block');
		}
	}
	return $can_edit;
}


// ===================
// SINGLE POST FILTERS
// ===================

// --------------------------
// Check Can Edit for Post ID
// --------------------------
function gfo_can_edit_post($can_edit, $post) {

	global $gutenfree;
	if (!is_object($post)) {$post = get_post($post);}
	$post_id = $post->ID;

	// check admin-specified override (plugin setting)
	$override = gfo_check_admin_override($post_id);
	if (!is_null($override)) {return $override;}

	// check for editor querystrings (user override)
	if (isset($_GET['editor'])) {
		if ($_GET['editor'] == 'block') {return true;}
		elseif ($_GET['editor'] == 'classic') {return false;}
	} elseif (isset($_GET['classic-editor'])) {return false;}

	// maybe prevent duplicate (re)filtering
	if (isset($gutenfree[$post_id.'_filtered'])) {return $gutenfree[$post_id.'_filtered'];}

	// maybe check for Gutenberg blocks (in post content)
	add_filter('gfo_single_post_filters', 'gfo_check_for_blocks', 20, 2);

	// check page template (plugin settings)
	add_filter('gfo_single_post_filters', 'gfo_check_template_override', 30, 2);

	// check single post ID overrides (post meta via metabox)
	add_filter('gfo_single_post_filters', 'gfo_check_post_override', 40, 2);

	// apply all the single post filters now
	$can_edit = apply_filters('gfo_single_post_filters', $can_edit, $post_id);

	$gutenfree[$post_id.'_filtered'] = $can_edit;
	return $can_edit;
}

// --------------------------
// Check for Gutenberg Blocks
// --------------------------
function gfo_check_for_blocks($can_edit, $post_id) {
	if ($can_edit) {return $can_edit;}
	global $gutenfree;
	$check_blocks = gfo_get_setting('check_blocks');
	$content = gfo_get_post_content_only($post_id);
	// 0.9.2: bug out if could not get content
	if (!$content) {return $can_edit;}
	$hasblocks = gfo_has_blocks($post->content);
	if ($hasblocks) {
		// also set global flag to indicate current post has blocks
		$gutenfree['hasblocks'] = $can_edit = true;
		gfo_debug_log('Post '.$post_id.' Content Blocks Found -> Block');
	} elseif (isset($gutenfree['hasblocks'])) {unset($gutenfree['hasblocks']);}
	return $can_edit;
}

// -----------------------------
// Check Post Template Overrides
// -----------------------------
// note: since WP 4.7 any post type can have a page template
function gfo_check_template_override($can_edit, $post_id) {
	$template = get_page_template_slug($post_id);
	if (!$template) {return $can_edit;}
	$template = str_replace('.php', '', $template);
	if ($can_edit) {
		$classic_templates = gfo_get_setting('classic_templates');
		if (is_array($classic_templates) && array_key_exists($template, $classic_templates)) {
			$can_edit = false; gfo_debug_log('Post '.$post_id.' Template '.$template.' -> Classic');
		}
	} else {
		$block_templates = gfo_get_setting('block_templates');
		if (is_array($block_templates) && array_key_exists($template, $block_templates)) {
			$can_edit = true; gfo_debug_log('Post '.$post_id.' Template '.$template.' -> Block');
		}
	}
	return $can_edit;
}

// -----------------------------------
// Check Single Post Metabox Overrides
// -----------------------------------
function gfo_check_post_override($can_edit, $post_id) {
	$override = get_post_meta($post_id, '_editor_override', true);
	if (!$override) {return $can_edit;}
	if ($override == 'classic') {
		$can_edit = false; gfo_debug_log('Post '.$post_id.' Meta Override -> Classic');
	} elseif ($override == 'block') {
		$can_edit = true; gfo_debug_log('Post '.$post_id.' Meta Override -> Block');
	} elseif ($can_edit) {
		// assume another editor is using this override
		$can_edit = false; gfo_debug_log('Post '.$post_id.' Meta Other -> Classic');
	}
	return $can_edit;
}

// ---------------------------------
// Check Single Post Admin Overrides
// ---------------------------------
// check post ID admin override (plugin settings)
function gfo_check_admin_override($post_id) {

	$override = null;
	$classic_ids = gfo_get_setting('classic_ids');
	$block_ids = gfo_get_setting('block_ids');

	// note: always making sure override is not in both override settings (cancels override)
	if (is_array($classic_ids)) {
		if (in_array($post_id, $classic_ids)) {
			if (!is_array($block_ids) || !in_array($post_id, $block_ids)) {
				$override = false; gfo_debug_log('Post ID Admin Override '.$post_id.' -> Classic');
			}
		} else {
			$post_name = gfo_get_post_name_only($post_id);
			if (in_array($post_name, $classic_ids)) {
				if (!is_array($block_ids) || !in_array($post_name, $block_ids)) {
					$override = false; gfo_debug_log('Post Slug Admin Override '.$post_name.' ('.$post_id.') -> Classic');
				}
			}
		}
	} elseif (is_array($block_ids)) {
		if (in_array($post_id, $block_ids)) {
			if (!is_array($classic_ids) || !in_array($post_id, $classic_ids)) {
				$override = true; gfo_debug_log('Post Admin Override '.$post_id.' -> Block');
			}
		} else {
			$post_name = gfo_get_post_name_only($post_id);
			if (in_array($post_name, $block_ids)) {
				if (!is_array($classic_ids) || !in_array($post_name, $classic_ids)) {
					$override = true; gfo_debug_log('Post Slug Admin Override '.$post_name.' ('.$post_id.') -> Block');
				}
			}
		}
	}
	return $override;
}


// ----------------
// Gutenberg Loader
// ----------------
function gfo_force_gutenberg_load() {

	global $gutenfree;
	do_action('gfo_before_gutenberg_load');

	$gutenberg_filepath = WP_PLUGIN_DIR.'/gutenberg/gutenberg.php';
	$gutenberg_filepath = apply_filters('gutenberg_plugin_load_path', $gutenberg_filepath);
	if (validate_file($gutenberg_filepath) !== 0) {return false;}

	if (file_exists($gutenberg_filepath)) {
		// 0.9.1: set gutenberg plugin loaded flag
		$gutenfree['gutenberg_plugin'] = true;
		include_once(gutenberg_filepath);
		do_action('gfo_after_gutenberg_load');
		return true;
	}
	return false;
}

// ------------------
// Gutenberg Unloader
// ------------------
function gfo_force_gutenberg_unload($load_type) {

	// set global flag
	global $gutenfree; $guten_free['gutenberg'] = 'unload';

	// remove the Classic Editor row action links
	remove_action('admin_init', 'gutenberg_add_edit_link_filters');

	if ($load_type == 'plugin') {

		// main plugin filter
		remove_filter( 'replace_editor', 'gutenberg_init' );

		// gutenberg.php
		// remove_action( 'admin_menu', 'gutenberg_menu' );
		remove_action( 'admin_notices', 'gutenberg_build_files_notice' );
		remove_action( 'admin_notices', 'gutenberg_wordpress_version_notice' );
		remove_action( 'admin_init', 'gutenberg_redirect_demo' );
		remove_action( 'admin_init', 'gutenberg_add_edit_link_filters' );
		remove_action( 'admin_print_scripts-edit.php', 'gutenberg_replace_default_add_new_button' );

		remove_filter( 'body_class', 'gutenberg_add_responsive_body_class' );
		remove_filter( 'admin_url', 'gutenberg_modify_add_new_button_url' );

		// Keep
		// remove_filter( 'wp_kses_allowed_html', 'gutenberg_kses_allowedtags', 10, 2 ); // not needed in 5.0
		// remove_filter( 'bulk_actions-edit-wp_block', 'gutenberg_block_bulk_actions' );

		// lib/client-assets.php
		remove_action( 'wp_enqueue_scripts', 'gutenberg_register_scripts_and_styles', 5 );
		remove_action( 'admin_enqueue_scripts', 'gutenberg_register_scripts_and_styles', 5 );
		remove_action( 'wp_enqueue_scripts', 'gutenberg_common_scripts_and_styles' );
		remove_action( 'admin_enqueue_scripts', 'gutenberg_common_scripts_and_styles' );

		// lib/compat.php
		remove_filter( 'wp_refresh_nonces', 'gutenberg_add_rest_nonce_to_heartbeat_response_headers' );
		remove_action( 'admin_enqueue_scripts', 'gutenberg_check_if_classic_needs_warning_about_blocks' );

		// lib/rest-api.php
		remove_action( 'rest_api_init', 'gutenberg_register_rest_routes' );
		remove_action( 'rest_api_init', 'gutenberg_add_taxonomy_visibility_field' );
		remove_filter( 'rest_request_after_callbacks', 'gutenberg_filter_oembed_result' );
		remove_filter( 'registered_post_type', 'gutenberg_register_post_prepare_functions' );
		remove_filter( 'register_post_type_args', 'gutenberg_filter_post_type_labels' );

		// lib/meta-box-partial-page.php
		remove_action( 'do_meta_boxes', 'gutenberg_meta_box_save', 1000 );
		remove_action( 'submitpost_box', 'gutenberg_intercept_meta_box_render' );
		remove_action( 'submitpage_box', 'gutenberg_intercept_meta_box_render' );
		remove_action( 'edit_page_form', 'gutenberg_intercept_meta_box_render' );
		remove_action( 'edit_form_advanced', 'gutenberg_intercept_meta_box_render' );

		remove_filter( 'redirect_post_location', 'gutenberg_meta_box_save_redirect' );
		remove_filter( 'filter_gutenberg_meta_boxes', 'gutenberg_filter_meta_boxes' );

		// lib/register.php
		remove_action( 'edit_form_top', 'gutenberg_remember_classic_editor_when_saving_posts' );

		remove_filter( 'redirect_post_location', 'gutenberg_redirect_to_classic_editor_when_saving_posts' );
		remove_filter( 'get_edit_post_link', 'gutenberg_revisions_link_to_editor' );
		remove_filter( 'wp_prepare_revision_for_js', 'gutenberg_revisions_restore' );
		remove_filter( 'display_post_states', 'gutenberg_add_gutenberg_post_state' );

		// lib/plugin-compat.php
		remove_filter( 'rest_pre_insert_post', 'gutenberg_remove_wpcom_markdown_support' );

		// Keep content filter so blocks are always rendered.
		// remove_filter( 'the_content', 'do_blocks', 9 );

		// Continue to disable wpautop inside TinyMCE for posts that were started in Gutenberg.
		// remove_filter( 'wp_editor_settings', 'gutenberg_disable_editor_settings_wpautop' );

		// Keep the tweaks to the PHP wpautop.
		// add_filter( 'the_content', 'wpautop' );
		// remove_filter( 'the_content', 'gutenberg_wpautop', 8 );

		// Keep registration of Gutenberg / block post types.
		// remove_action( 'init', 'gutenberg_register_post_types' );

		// ? adding this filter this will force Classic Editor to load early ?
		// (before use_block_editor filter checks and thus bypassing them)
		// add_filter('replace_editor', 'gfo_classic_editor_replace');

	} elseif ($load_type == 'inbuilt') {

		// remove block scripts and style resources
		remove_action( 'wp_enqueue_scripts', 'wp_common_block_scripts_and_styles' );
		remove_action( 'admin_enqueue_scripts', 'wp_common_block_scripts_and_styles' );

		// TODO: anything else to do here for WP 5.0-beta+ ?!?

	}
}

// ---------------
// The GutenButton
// ---------------
// "Edit with Block Editor" button for Classic Editor
add_action('edit_form_after_title', 'gfo_gutenberg_button');
function gfo_gutenberg_button() {
	global $gutenfree, $post;
	$switch_button = gfo_get_setting('switch_buttons');
	$switch_button = apply_filters('gfo_add_gutenberg_button', $switch_button);
	if (!$switch_button) {return;}

	// if (isset($gutenfree['hasblocks']) && $gutenfree['hasblocks']) {
	// 	TODO: maybe alert that current post has existing blocks?
	// }

	// set edit URL and GutenButton
	$edit_url = get_edit_post_link($post->ID);
	$edit_url = add_query_arg('editor', 'block', $edit_url);
	$button = array(
		'class'			=> 'button button-primary button-large',
		'styles'		=> '',
		'id'			=> 'gutenberg-editor-button',
		'anchor'		=> __('Edit with Block Editor','guten-free-options'),
		'title'			=> __('Edit this Post with the Block Editor (Gutenberg)','guten-free-options'),
		'url'			=> $edit_url,
		'icon_class'	=> 'dashicons dashicons-welcome-write-blog',
		'icon_style'	=> 'margin-top:4px;',
	);
	$button = apply_filters('gfo_gutenberg_button', $button);

	// output the switch editor button the editor page
	if (!empty($button['style'])) {$style = ' style="'.$button['style'].'"';}
	echo '<a href="'.$button['url'].'"><div id="'.$button['id'].'" class="'.$button['class'].'" title="'.$button['title'].'"'.$style.'>';
	if (!empty($button['icon_style'])) {$style = ' style="'.$button['icon_style'].'"';}
	echo '<span class="'.$button['icon_class'].'"'.$style.'></span> '.$button['anchor'].'</div></a>';
}

// ------------------
// The ClassicButton
// ------------------
// "Classic Editor" button for Block Editor (Gutenberg)
add_action('admin_footer', 'gfo_classic_button');
function gfo_classic_button() {
	global $gutenfree, $post, $current_screen;

	// 0.9.1: use block editor checking function to prevent Gutenberg plugin crashing
	if (!gfo_is_block_editor_page()) {return;}

	$switch_button = gfo_get_setting('switch_buttons');
	$switch_button = apply_filters('gfo_add_classic_button', $switch_button);
	if (!$switch_button) {return;}

	// javascript confirmation onclick if content has existing blocks
	if (!isset($gutenfree['hasblocks']) || !$gutenfree['hasblocks']) {$onclick = 'onclick="gfo_switch_editor();"';}
	else {$onclick = ' onclick="return gfo_switch_confirm();"';}
	$confirm = __('Switching back to Classic Editor\\nfor this post is not recommended\\nbecause you have existing blocks. Continue?', 'guten-free-options');

	// create edit URL and Classic Button
	$edit_url = get_edit_post_link($post->ID, 'url');
	// 0.9.1: fix to updated editor querystring arg
	$edit_url = add_query_arg('editor', 'classic', $edit_url);
	$button = array(
		'class'		=> 'components-button editor-post-switch-to-classic is-button is-default is-large',
		'id'		=> 'classic-editor-button',
		'title'		=> __('Edit with Classic Editor','guten-free-options'),
		'anchor'	=> __('Classic','guten-free-options'),
		'url'		=> $edit_url,
		'onclick'	=> $onclick,
		'confirm'	=> $confirm,
	);
	$button = apply_filters('gfo_classic_button', $button);
	$button_html = '<button type="button" id="'.$button['id'].'" class="'.$button['class'].'" title="'.$button['title'].'"'.$onclick.'>'.$button['anchor'].'</div>';

	// add the switch editor button to the editor page
	// note: using setInterval cycling above to ensure DOM is ready for appending!
	// 0.9.1: add extra check for .edit-post-header__settings element just in case
	echo "<script>var editor_switch_confirm_message = '".$button['confirm']."';
	function gfo_switch_confirm() {var agree = confirm(editor_switch_confirm_message); if (!agree) {return false;} gfo_switch_editor();}
	function gfo_switch_editor() {window.location.href = '".$edit_url."';}
	var add_classic_button; var classic_button = jQuery('".$button_html."');
	jQuery(document).ready(function($) {
		if (jQuery('.edit-post-header__settings')) {
			add_classic_button = setInterval(function() {
				jQuery('.edit-post-header__settings').append(classic_button);
				if (jQuery('#".$button['id']."')) {clearInterval(add_classic_button);}
			}, 500);
		}
	});</script>";
}

// ----------------------
// Replace Editor Scripts
// ----------------------
// JS and CSS enqueue code from Classic Editor plugin...
// ...presumably for if classic editor scripts are deprecated (hopefully not!)
function gfo_classic_editor_replace($return) {

	// Bail if the editor has been replaced already.
	if (true === $return ) {return $return;}

	$suffix = SCRIPT_DEBUG ? '' : '.min';
	$js_url = plugin_dir_url( __FILE__ ) . 'js/';
	$css_url = plugin_dir_url( __FILE__ ) . 'css/';

	// Enqueued conditionally from legacy-edit-form-advanced.php
	wp_register_script( 'editor-expand', $js_url . "editor-expand$suffix.js", array( 'jquery', 'underscore' ), false, 1 );

	// The dependency 'tags-suggest' is also needed for 'inline-edit-post', not included.
	wp_register_script( 'tags-box', $js_url . "tags-box$suffix.js", array( 'jquery', 'tags-suggest' ), false, 1 );
	wp_register_script( 'word-count', $js_url . "word-count$suffix.js", array(), false, 1 );

	// The dependency 'heartbeat' is also loaded on most wp-admin screens, not included.
	wp_register_script( 'autosave', $js_url . "autosave$suffix.js", array( 'heartbeat' ), false, 1 );
	wp_localize_script( 'autosave', 'autosaveL10n', array(
		'autosaveInterval' => AUTOSAVE_INTERVAL,
		'blog_id' => get_current_blog_id(),
	) );

	wp_enqueue_script( 'post', $js_url . "post$suffix.js", array(
	//	'suggest', // deprecated
		'tags-box', // included
		'word-count', // included
		'autosave', // included
		'wp-lists', // not included, also dependency for 'admin-comments', 'link', and 'nav-menu'.
		'postbox', // not included, also dependency for 'link', 'comment', 'dashboard', and 'nav-menu'.
		'underscore', // not included, library
		'wp-a11y', // not included, library
	), false, 1 );

	wp_localize_script( 'post', 'postL10n', array(
		'ok' => __( 'OK', 'classic-editor' ),
		'cancel' => __( 'Cancel', 'classic-editor' ),
		'publishOn' => __( 'Publish on:', 'classic-editor' ),
		'publishOnFuture' =>  __( 'Schedule for:', 'classic-editor' ),
		'publishOnPast' => __( 'Published on:', 'classic-editor' ),
		/* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
		'dateFormat' => __( '%1$s %2$s, %3$s @ %4$s:%5$s', 'classic-editor' ),
		'showcomm' => __( 'Show more comments', 'classic-editor' ),
		'endcomm' => __( 'No more comments found.', 'classic-editor' ),
		'publish' => __( 'Publish', 'classic-editor' ),
		'schedule' => __( 'Schedule', 'classic-editor' ),
		'update' => __( 'Update', 'classic-editor' ),
		'savePending' => __( 'Save as Pending', 'classic-editor' ),
		'saveDraft' => __( 'Save Draft', 'classic-editor' ),
		'private' => __( 'Private', 'classic-editor' ),
		'public' => __( 'Public', 'classic-editor' ),
		'publicSticky' => __( 'Public, Sticky', 'classic-editor' ),
		'password' => __( 'Password Protected', 'classic-editor' ),
		'privatelyPublished' => __('Privately Published', 'classic-editor' ),
		'published' => __( 'Published', 'classic-editor' ),
		'saveAlert' => __( 'The changes you made will be lost if you navigate away from this page.', 'classic-editor' ),
		'savingText' => __( 'Saving Draft&#8230;', 'classic-editor' ),
		'permalinkSaved' => __( 'Permalink saved', 'classic-editor' ),
	) );

	wp_enqueue_style( 'classic-edit', plugin_dir_url( __FILE__ ) . "css/edit$suffix.css" );

	// Other scripts and stylesheets:
	// wp_enqueue_script( 'admin-comments' ) is a dependency for 'dashboard', also used in edit-comments.php.
	// wp_enqueue_script( 'image-edit' ) and wp_enqueue_style( 'imgareaselect' ) are also used in media.php and media-upload.php.

	include_once( plugin_dir_path( __FILE__ ) . 'edit-form-advanced.php' );

	return true;
}


// =====
// ADMIN
// =====

// ----------------------
// Editor Submenu Actions
// ----------------------
function gfo_classic_editor_submenus() {gfo_add_editor_submenus('classic');}
function gfo_block_editor_submenus() {gfo_add_editor_submenus('block');}

// --------------------------
// Add Submenu Links Abstract
// --------------------------
// (via Classic Editor plugin)
function gfo_add_editor_submenus($editor) {

	foreach ( get_post_types( array( 'show_ui' => true ) ) as $type ) {

		$type_obj = get_post_type_object( $type );

		if ( ! $type_obj->show_in_menu || ! post_type_supports( $type, 'editor' ) ) {
			continue;
		}

		if ( $type_obj->show_in_menu === true ) {
			if ( 'post' === $type ) {
				$parent_slug = 'edit.php';
			} elseif ( 'page' === $type ) {
				$parent_slug = 'edit.php?post_type=page';
			} else {
				// Not for a submenu.
				continue;
			}
		} else {
			$parent_slug = $type_obj->show_in_menu;
		}

		$item_name = $type_obj->labels->add_new . ' ';

		if ($editor == 'classic') {$item_name .= __( '(Classic)', 'guten-free-options' );}
		if ($editor == 'block') {$item_name .= __( '(Block)', 'guten-free-options' );}

		$add_new_url = "post-new.php?post_type=" . $type . "&editor=" .$editor;
		add_submenu_page( $parent_slug, $type_obj->labels->add_new, $item_name, $type_obj->cap->edit_posts, $add_new_url );
	}
}

// ---------------
// Admin Bar Links
// ---------------
// (via Classic Editor plugin)
function gfo_admin_bar_menu($wp_admin_bar) {

	global $post_id, $wp_the_query;

	if (is_admin()) {$post = get_post($post_id);} else {$post = $wp_the_query->get_queried_object();}
	if (empty($post) || empty($post->ID)) {return;}

	// Capability check is in get_edit_post_link().
	$edit_url = get_edit_post_link($post->ID, 'url');

	if ( $edit_url &&
		( ( is_admin() && 'post' === get_current_screen()->base ) || ( ! is_admin() && ! empty( $post->post_type ) ) ) &&
		post_type_supports( $post->post_type, 'editor' ) ) {

		// filter to find default editor for this post
		$can_edit = gfo_can_edit_post_type(false, $post->post_type);
		$can_edit = gfo_can_edit_post($can_edit, $post->ID);

		if ( $can_edit ) {
			$href = remove_query_arg( 'classic-editor', $edit_url );
			$href = remove_query_arg( 'editor', $href );
			$href = add_query_arg( 'editor', 'classic', $href );
			$wp_admin_bar->add_menu( array(
				'id' => 'classic-editor-link',
				'title' => __( 'Edit (Classic)', 'guten-free-options' ),
				'href' => $href,
			) );
		} else {
			$href = remove_query_arg( 'classic-editor', $edit_url );
			$href = remove_query_arg( 'editor', $href );
			$href = add_query_arg( 'editor', 'block', $href );
			$wp_admin_bar->add_menu( array(
				'id' => 'block-editor-link',
				'title' => __( 'Edit (Block)', 'guten-free-options' ),
				'href' => $href,
			) );
		}
	}
}

// ----------------------
// Post/Page Action Links
// ----------------------
// (via Classic Editor plugin)
function gfo_add_edit_links($actions, $post) {

	// This is in Gutenberg now.
	if (array_key_exists('classic', $actions)) {return $actions;}

	if ( ('trash' === $post->post_status) || !post_type_supports($post->post_type, 'editor') ) {
		return $actions;
	}

	$edit_url = get_edit_post_link( $post->ID, 'raw' );
	if (!$edit_url) {return $actions;}
	$edit_url = add_query_arg('editor', 'classic', $edit_url);

	// Build the classic edit action. See also: WP_Posts_List_Table::handle_row_actions().
	$title       = _draft_or_post_title( $post->ID );
	$edit_action = array(
		'classic' => sprintf(
			'<a href="%s" aria-label="%s">%s</a>',
			esc_url( $edit_url ),
			esc_attr( sprintf(
				/* translators: %s: post title */
				__( 'Edit &#8220;%s&#8221; in the classic editor', 'guten-free-options' ),
				$title
			) ),
			__( 'Edit (Classic)', 'classic-editor' )
		),
	);

	// Insert the Classic Edit action after the Edit action.
	$edit_offset = array_search( 'edit', array_keys( $actions ), true );
	array_splice( $actions, $edit_offset + 1, 0, $edit_action );

	return $actions;
}


// ============
// USER OPTIONS
// ============

// ------------------------------
// Add User Default Editor Option
// ------------------------------
function gfo_user_default_editor_field($user) {

	$value = get_user_meta($user->ID, '_default_editor', true);
	$options = array(
		'' 			=> __('Use Site/Role Default'),
		'classic'	=> __('Classic Editor (TinyMCE)'),
		'block'		=> __('Block Editor (Gutenberg)'),
	);
	$options = apply_filters('gfo_default_editor_options', $options);
	if (!$value || !array_key_exists($value, $options)) {$value = '';}

	// echo '<h3>'.__('Default Editor').'</h3>';
    echo '<table class="form-table">';
	echo '<tr>';
		echo '<th><label for="default_editor">'.__('Default Editor').'</label></th>';
			echo '<td>';
				foreach ($options as $option => $label) {
					echo '<input type="radio" name="default_editor" class="default_editor" value="'.$option.'"';
						if ($value == $option) {echo ' checked';}
					echo '> '.$label.'<br>';
				}
				echo '<span class="description">'.__('Sets your default editor for Writing.').'</span>';
			echo '</td>';
    	echo '</tr>';
    echo '</table>';
}

// ------------------------
// Save User Default Editor
// ------------------------
function gfo_user_default_editor_save($user_id) {

    if (!current_user_can('edit_user', $user_id)) {return;}
	$options = array(
		'' 			=> __('Use Site/Role Default'),
		'classic'	=> __('Classic Editor (TinyMCE)'),
		'block'		=> __('Block Editor (Gutenberg)'),
	);
	$options = apply_filters('gfo_default_editor_options', $options);
	$value = $_POST['default_editor'];
	if (!array_key_exists($value, $options)) {$value = '';}

    if ($value == '') {delete_user_meta($user_id, '_default_editor');}
    elseif (array_key_exists($value, $options)) {update_user_meta($user_id, '_default_editor', $value);}
    else {
    	// error invalid option (you can custom filter options array to fix)
    }
}

// ---------------------------
// Add Editor Override Metabox
// ---------------------------
function gfo_editor_override_metabox_add() {

	// do not add metabox on non-editor screens
	if (!gfo_is_editor_admin_url()) {return;}

	// do not add metabox for posts with an existing admin override (plugin setting)
	if (isset($_GET['post'])) {
		$post_id = absint($_GET['post']);
		if (is_numeric($post_id) && ($post_id > 0)) {
			$classic_ids = gfo_get_setting('classic_ids');
			$block_ids = gfo_get_setting('block_ids');
			if (!is_array($classic_ids)) {$classic_ids = array();}
			if (!is_array($block_ids)) {$block_ids = array();}
			if (in_array($post_id, $classic_ids) || in_array($post_id, $block_ids)) {
				// ...also make sure the post ID is not in *both* arrays (cancelling override)
				if (!in_array($post_id, $classic_ids) || !in_array($post_id, $block_ids)) {return;}
			}
			$post_type = get_post_type($post_id);
		}
	}

	if (!isset($post_type) && isset($_REQUEST['post_type'])) {$post_type = $_REQUEST['post_type'];}

	// filter post type for metabox screens (plugin setting)
	// $no_override = gfo_get_setting('no_override');
	$no_override = apply_filters('gfo_no_override_post_types', false, $post_type);
	if ($no_override && is_array($no_override)) {
	 	if (isset($post_type) && in_array($post_type, $no_override)) {return;}
	}

	// finally, add the editor override meta box
	add_meta_box('editor-override', __('Editor Override'), 'gfo_editor_override_metabox', $post_type, 'side', 'high');
}

// -----------------------
// Editor Override Metabox
// -----------------------
function gfo_editor_override_metabox() {
	global $post;
	$override = get_post_meta($post->ID, '_editor_override', true);
	// if ($override == 'classic') {$debug = 'Metabox Override -> Classic';}
	// elseif ($override == 'block') {$debug = 'Metabox Override -> Block';}
	// gfo_debug_log($debug);

	$options = array(
		'' 			=> __('Do Not Override'),
		'classic'	=> __('Classic Editor (TinyMCE)'),
		'block'		=> __('Block Editor (Gutenberg)'),
	);
	$options = apply_filters('gfo_editor_override_options', $options);
	if (!$override || !array_key_exists($override, $options)) {$override = '';}

	echo '<table id="editor-override-table"><tr>';

		echo '<td><span class="editor-override-label">'.__('Editor','guten-free-options').'</span></td>';
		echo '<td><select name="editor_override" id="editor_override"'.$onchange.'>';
		foreach ($options as $option => $label) {
			echo '<option value="'.$option.'"';
				if ($override == $option) {echo ' selected="selected"';}
			echo '>'.$label.'</option>';
		}
		echo '</select></td>';

		// set a help icon with title for the setting
		$posttypeobject = get_post_type_object(get_post_type($post->ID));
		$title = __('Set the Editor for this','guten-free-options')." ".$posttypeobject->labels->singular_name;
		echo '<td><span id="editor-override-help" class="dashicons dashicons-editor-help" title="'.$title.'"></span></td>';

	echo '</tr></table>';

	// editor override metabox styles
	$styles = "#editor_override {font-size:12px;} .editor-override-label {font-size:10px; font-weight:bold;}
	#editor-override-help {color:#008ec2;} #editor-override-help:hover {color:##0085ba;}";
	$styles = apply_filters('gfo_editor_override_metabox_styles', $styles);
	if ($styles != '') {echo "<style>".$styles."</style>";}

	// the next part is for block editor pages only
	if (!gfo_is_block_editor_page()) {return;}

	// metabox save refresh trigger for block editor to classic editor (the super super crazy fix.)
	$confirm_message = __('Editor Override Saved. Refresh the page to Classic Editor now?','guten-free-options');
	$norefresh_message = __('Okay. It is recommended you refresh the page or remove the editor override.','guten-free-options');
	$script = "var classic_reload_check = false; var publish_button_click = false;
	jQuery(document).ready(function($) {
		add_publish_button_click = setInterval(function() {
			\$publish_button = jQuery('.edit-post-header__settings .editor-post-publish-button');
			if (\$publish_button && !publish_button_click) {
				publish_button_click = true;
				\$publish_button.on('click', function() {
					var classic_reloader = setInterval(function() {
						if (classic_reload_check) {return;} else {classic_reload_check = true;}
						postsaving = wp.data.select('core/editor').isSavingPost();
						autosaving = wp.data.select('core/editor').isAutosavingPost();
						success = wp.data.select('core/editor').didPostSaveRequestSucceed();
						/* console.log('Saving: '+postsaving+' - Autosaving: '+autosaving+' - Success: '+success); */
						if (postsaving || autosaving || !success) {classic_reload_check = false; return;}
						clearInterval(classic_reloader);

						select = document.getElementById('editor_override');
						value = select.options[select.selectedIndex].value;
						if (value == 'classic') {
							if (confirm('".$confirm_message."')) {
								currenthref = window.location.href.replace('&classic-editor', '');
								if (currenthref.indexOf('&editor=block') > -1) {
									window.location.href = currenthref.replace('&editor=block', '&editor=classic');
								} else {window.location.href = currenthref+'&editor=classic';}
							} else {alert('".$norefresh_message."');}
						}
					}, 1000);
				});
			}
		}, 500);
	});";
	$script = apply_filters('gfo_editor_override_metabox_script', $script);
	if ($script != '') {echo "<script>".$script."</script>";}
}

// -----------------------------
// Save Override Metabox Options
// -----------------------------
function gfo_editor_override_metabox_save($post_id) {
	if (!current_user_can('edit_post', $post_id)) {return;}
	if (isset($_POST['editor_override'])) {
		$override = $_POST['editor_override'];
		$options = array(
			'' 			=> __('Do Not Override'),
			'classic'	=> __('Classic Editor (TinyMCE)'),
			'block'		=> __('Block Editor (Gutenberg)'),
		);
		$options = apply_filters('gfo_editor_override_options', $options);

		if ($override == '') {delete_post_meta($post_id, '_editor_override');}
		elseif (array_key_exists($override, $options)) {
			update_post_meta($post_id, '_editor_override', $override);

			// maybe remove querystring override upon saving
			if ( ( ($override == 'block') && isset($_REQUEST['classic-editor']) )
			  || ( ($override == 'block') && isset($_REQUEST['editor']) && ($_REQUEST['editor'] == 'classic') )
			  || ( ($override == 'classic') && isset($_REQUEST['editor']) && ($_REQUEST['editor'] == 'block') ) ) {
			  	add_action('save_post', 'gfo_editor_redirect', 999);
			}
		} else {
			// error invalid option (you can custom filter options array to fix)
		}
	}
}

// -----------------------------------
// Redirect Editor without Querystring
// -----------------------------------
function gfo_editor_redirect() {
	add_filter('redirect_post_location', 'gfo_editor_redirect_location');
	function gfo_editor_redirect_location($location) {
		$location = remove_query_arg('editor', $location);
		$location = remove_query_arg('classic-editor', $location);
		echo $location; exit;
		return $location;
	}
	redirect_post($_REQUEST['post_ID']); exit;
}


// ============
// REDIRECTIONS
// ============

// --------------------------------------
// Remember when Saving in Classic Editor
// --------------------------------------
function gfo_remember_when_saving_posts() {
	// echo '<input type="hidden" name="classic-editor" value="">';
	echo '<input type="hidden" name="editor" value="classic">';
}

// --------------------------------
// Classic Editor Arg for Revisions
// --------------------------------
function gfo_get_edit_post_link($url) {
	if (isset($_REQUEST['classic-editor'])) {
		$url = add_query_arg('classic-editor', $_REQUEST['classic-editor'], $url);
	} elseif (isset($_REQUEST['editor'])) {
		$url = add_query_arg('editor', $_REQUEST['editor'], $url);
	}
	return $url;
}

// --------------------------------------
// Maintain Querystring through Redirects
// --------------------------------------
function gfo_redirect_location($location) {
	if (isset($_POST['_wp_http_referer']) && strpos($_POST['_wp_http_referer'], '&classic-editor') !== false) {
		$location = add_query_arg('classic-editor', '', $location);
	}
	if (isset($_POST['_wp_http_referer']) && strpos($_POST['_wp_http_referer'], '&editor=classic') !== false) {
		$location = add_query_arg('editor', 'classic', $location);
	}
	// not sure if will ever really be needed... but let us add the other way around just in case!
	if (isset($_POST['_wp_http_referer']) && strpos($_POST['_wp_http_referer'], '&editor=block') !== false) {
		$location = add_query_arg('editor', 'block', $location);
	}
	return $location;
}


// =======
// HELPERS
// =======

// ---------------------------
// Check for Blocks in Content
// ---------------------------
function gfo_has_blocks($content) {
	return strstr($content, "<!-- wp:"); // -->
}

// --------------------------
// Get Post ID from Post Slug
// --------------------------
function gfo_get_post_id_only($slug) {
	global $wpdb;
	$query = "SELECT ID FROM ".$wpdb->prefix."posts WHERE post_name = '%s'";
	$query = $wpdb->prepare($query, $slug);
	$result = $wpdb->get_var($query);
	return $result;
}

// --------------------------
// Get Post Slug from Post ID
// --------------------------
function gfo_get_post_name_only($post_id) {
	global $wpdb;
	$query = "SELECT post_name FROM ".$wpdb->prefix."posts WHERE ID = '%d'";
	$query = $wpdb->prepare($query, $post_id);
	$result = $wpdb->get_var($query);
	return $result;
}

// ---------------------
// Get Post Content Only
// ---------------------
function gfo_get_post_content_only($post_id=false) {
	// 0.9.2: bug out if not a valid integer
	if (!$post_id || !is_integer($post_id)) {return false;}
	global $wpdb;
	$query = "SELECT post_content FROM ".$wpdb->prefix."posts WHERE ID = '%d'";
	// 0.9.2: fix to use post_id not id
	$query = $wpdb->prepare($query, $post_id);
	$result = $wpdb->get_var($query);
	return $result;
}

// -----------------------------------
// Check if Gutenberg Plugin is Active
// -----------------------------------
function gfo_is_gutenberg_plugin_active() {
	// 0.9.1: store active state for faster rechecking
	global $gutenfree; $active = false;
	if (isset($gutenfree['gutenberg_plugin']) && $gutenfree['gutenberg_plugin']) {return true;}
	$active_plugins = (array)get_option('active_plugins');
	if (in_array('gutenberg/gutenberg.php', $active_plugins)) {$active = true;}
	if (is_multisite()) {
		$sitewide_plugins = (array)get_site_option('active_sitewide_plugins');
		if (array_key_exists('gutenberg/gutenberg.php', $sitewide_plugins)) {$active = true;}
	}
	$gutenfree['gutenberg_plugin'] = true;
	return $active;
}

// -----------------------------
// Check for Editor Admin Screen
// -----------------------------
function gfo_is_editor_admin_url($supported_filenames = array('post.php', 'post-new.php')) {

	$path = trim(sanitize_text_field(wp_parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
	$wp_admin_slug = trim(wp_parse_url(get_admin_url(), PHP_URL_PATH), '/');

	foreach ($supported_filenames as $filename) {
		// require $filename not to be empty to avoid accidents like matching against a plain `/wp-admin/`
		if (!empty($filename) && ("/".$wp_admin_slug."/".$filename === $path)) {return true;}
	}
	return false;
}

// ---------------------------
// Check for Block Editor Page
// ---------------------------
// 0.9.1: added check to help prevent Gutenberg plugin crashing without is_block_editor method
function gfo_is_block_editor_page() {
	global $current_screen;
	$current_screen = get_current_screen();
	if (method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor()) {return true;}
	elseif (gfo_is_gutenberg_plugin_active()) {
		// do similar to is_gutenberg_page function here
		if (!is_admin()) {return false;}
		if ($current_screen->base !== 'post') {return false;}
		if (!gfo_is_editor_admin_url()) {return false;}
		global $post;
		$can_edit = apply_filters('gutenberg_can_edit_post_type', true, $post->post_type);
		$can_edit = apply_filters('gutenberg_can_edit_post', $can_edit, $post);
		return $can_edit;
	}
	return false;
}

// --------------
// Get User Roles
// --------------
function gfo_get_user_roles() {
	$roles = get_editable_roles();
	$role_types = array();
	foreach ($roles as $key => $value) {
		if (isset($value['name'])) {$label = $value['name'];} else {$label = "Unknown (".$key.")";}
		$role_types[$key] = $label;
	}
	$role_types = apply_filters('gfo_get_user_roles', $role_types);
	return $role_types;
}

// --------------
// Get Post Types
// --------------
function gfo_get_post_types() {
	$post_types = get_post_types(array(), 'objects');
	$inbuilt_post_types = array(
		'attachment', 'revision', 'nav_menu_item', 'custom_css',
		'customize_changeset', 'oembed_cache', 'user_request', 'wp_block'
	);
	$inbuilt = apply_filters('gfo_inbuilt_post_types', $inbuilt_post_types);
	// 0.9.2: if not an array revert to default inbuilt post types
	if (!is_array($inbuilt)) {$inbuild = $inbuild_post_types;}
	$types = array();
	foreach($post_types as $key => $post_type) {
		if (!in_array($key, $inbuilt)) {$types[$key] = $post_type->label;}
		// TODO: check if/why next line is necessary? (I think it is not)
		// if (!post_type_supports($post_type->name, 'custom-fields')) {unset($types[$key]);}
	}
	// print_r($types); // debug point
	$types = apply_filters('gfo_get_post_types', $types);
	return $types;
}

// ------------------
// Get Post Templates
// ------------------
function gfo_get_post_templates() {
	$theme = wp_get_theme();
	// note: use get_post_templates not get_page_templates
	$templates = $theme->get_post_templates();
	$templates = apply_filters('gfo_get_post_templates', $templates);
	return $templates;
}

// -------------
// Debug Logging
// -------------
function gfo_debug_log($logline) {
	global $gutenfree;
	if (isset($gutenfree['debug']) && $gutenfree['debug']) {
		if (!isset($gutenfree['debugpath'])) {
			if (defined('GUTEN_FREE_DEBUG_PATH')) {$gutenfree['debugpath'] = GUTEN_FREE_DEBUG_PATH;}
			else {$gutenfree['debugpath'] = apply_filters('gfo_debug_log_path', dirname(__FILE__).'/debug.log');}
		}
		error_log($logline.PHP_EOL, 3, $gutenfree['debugpath']);
	}
}

// Bonus Video: https://www.youtube.com/watch?v=SAUN4HPjtLY

