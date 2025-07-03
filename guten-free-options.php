<?php
/*
Plugin Name: Guten Free Options
Plugin URI: https://wpmedic.tech/guten-free-options/
Author: Tony Hayes
Description: Gutenberg Free Options for your WordPressed Burger err I mean Editor
Version: 0.9.7
Author URI: https://wpmedic.tech
GitHub Plugin URI: majick777/guten-free-options
*/

if ( !defined( 'ABSPATH' ) ) {
	exit;
}


// === Plugin Setup ===
// - Define Constants
// - Do Includes File Checks
// - Set Plugin Values
// - maybe Load Include Files
// === Admin ===
// - Add Admin Page
// - Add Network Admin Page
// - Add Plugin Page Settings Link
// - Add Network Plugin Page Settings Link
// - Admin Message Box Output
// - Add Submenu Links Abstract
// x Admin Bar Links
// - Post/Page Action Links
// - The GutenButton
// - The ClassicButton
// x Replace Editor Scripts
// - Maybe Disable Widgets Block Editor
// === Plugin Settings ===
// - Set Named Global Plugin Settings
// - Get Plugin Settings
// - Prevent Disabling of Gutenberg plugin on Update
// - Check Debug Switch Overrides
// - Get Default Plugin Settings
// - Get Plugin Setting
// - Add Plugin Settings
// - Update Plugin Settings
// - Reset Plugin Settings
// - Update Network Plugin Settings
// - Plugin Page Header
// - Plugin Settings Page
// - Multisite Network Settings Page
// - Add Thickbox for Readme Link
// - Readme Viewer
// === Plugin Loader ===
// - Guten Free Cooker
// - Will Gutenberg Load?
// - Get Post Type for Post ID
// === Post Type Filters ===
// - Check Can Edit For Post Type
// - Check Network Default
// - Check Site Default
// - Check User Role
// - Check User Default
// - Check Post Type Default
// === Single Post Filters ===
// - Check Can Edit for Post ID
// - Check for Gutenberg Blocks
// - Check Post Template Overrides
// - Check Single Post Metabox Overrides
// - Check Single Post Admin Overrides
// - Gutenberg Loader
// - Gutenberg Unloader
// === User Options ===
// - Add User Default Editor Option
// - Save User Default Editor
// - Add Editor Override Metabox
// - Editor Override Metabox
// - Save Override Metabox Options
// - Redirect Editor without Querystring
// - Set Editor Redirect Location
// === Redirections === 
// - Remember when Saving in Classic Editor
// - Classic Editor Arg for Revisions
// - Maintain Querystring through Redirects
// === Helpers ===
// - Check for Blocks in Content
// - Get Post ID from Post Slug
// - Get Post Slug from Post ID
// - Get Post Content Only
// - Check if Gutenberg Plugin is Active
// - Check for Block Editor Page
// - Check if Widgets have Blocks
// - Get User Roles
// - Get Post Types
// - Get Post Templates
// - Debug Loggging


// ====================
// --- Plugin Setup ---
// ====================

// ----------------
// Define Constants
// ----------------
global $gutenfree;
$slug = 'guten-free-options';
define( 'GUTEN_FREE_FILE', __FILE__ );
define( 'GUTEN_FREE_DIR', dirname( __FILE__ ) );

// -----------------------
// Do Includes File Checks
// -----------------------

// --- Plugin Update Checker ---
// note: lack of updatechecker.php file indicates WordPress.Org SVN repo version
// presence of updatechecker.php indicates direct site download or GitHub version
$updatechecker = GUTEN_FREE_DIR . '/updatechecker.php';
$wporg = file_exists( $updatechecker ) ? false : true;
// --- WordQuest Helper ---
$wordquest = GUTEN_FREE_DIR . '/wordquest.php';
$loadwordquest = file_exists( $wordquest ) ? true : false;
// --- Freemius ---
$freemiusloader = GUTEN_FREE_DIR . '/freemius.php';
$loadfreemius = file_exists( $freemiusloader ) ? true : false;

// -----------------
// Set Plugin Values
// -----------------
// 0.9.4: used simplified plugin settings array
$settings = array(
	'slug'			=> $slug,
	'proslug'		=> false,
	'networkslug'	=> 'guten-free-network-options',
	'version'		=> '0.9.6',
	'title'			=> 'Guten Free Options',
	'menutitle'		=> __( 'Guten Free','guten-free-options' ),
	'pagetitle'		=> __( 'Guten Free Options','guten-free-options' ),
	'networktitle'	=> __(  'Guten Free Network Options','guten-free-options' ),
	'parentmenu'	=> 'wordquest',
	'home'			=> 'http://wpmedic.tech/guten-free-options/',
	'support'		=> 'http://wordquest.org/quest/quest-category/plugin-support/' . $slug . '/',
	'namespace'		=> 'gutenfree',
	'settings'		=> 'gfo',
	'option'		=> 'guten_free_options',
	'textdomain'	=> 'guten-free-options',
	'wporgslug'		=> 'guten-free-options',
	'wporg'			=> $wporg,
);
$settings['infokeys'] = array_keys( $settings );

// ------------------------
// maybe Load Include Files
// ------------------------
// --- Update Checker ---
if ( !$wporg ) {
	include $updatechecker ;
}
// --- Freemius ---
if ( $loadfreemius && ( version_compare( PHP_VERSION, '5.4.0') >= 0 ) ) {
	include $freemiusloader;
}
// --- WordQuest Helper ---
if ( $loadwordquest && ( version_compare( PHP_VERSION, '5.3.0') >= 0 ) ) {
	global $wordquestplugins;
	foreach ( $settings as $key => $value ) {
		$wordquestplugins[$slug][$key] = $value;
	}
	if ( is_admin() ) {
		include $wordquest;
	}
}


// -------------
// === Admin ===
// -------------

// --------------
// Add Admin Page
// --------------
add_action( 'admin_menu', 'gfo_add_admin_page' );
function gfo_add_admin_page() {
	global $gutenfree;
	// --- allow programmatic locking of plugin settings page ---
	// 0.9.4: also check for options locking constant (unfiltered)
	if ( defined( 'GUTEN_FREE_OPTIONS_LOCK' ) && GUTEN_FREE_OPTIONS_LOCK)  {
		return;
	}
	$lock_settings = apply_filters( 'gfo_lock_settings', get_option( 'gfo_lock_settings' ) );
	if ( $lock_settings ) {
		return;
	}

	// --- add plugin options page ---
	// 0.9.4: get menu defaults from plugin info array
	$page_title = apply_filters( 'gfo_settings_page_title', $gutenfree['pagetitle'] );
	$menu_title = apply_filters( 'gfo_settings_menu_title', $gutenfree['menutitle'] );
	$capability = apply_filters( 'gfo_manage_options_capability', 'manage_options' );
	add_options_page( $page_title, $menu_title, $capability, $gutenfree['slug'], 'gfo_settings_page' );
}

// ----------------------
// Add Network Admin Page
// ----------------------
add_action( 'network_admin_menu', 'gfo_add_network_page' );
function gfo_add_network_page() {
	global $gutenfree;
	// --- allow programmatic locking of network settings page ---
	// 0.9.4: also check for network options locking constant (unfiltered)
	if ( defined( 'GUTEN_FREE_NETWORK_LOCK') && GUTEN_FREE_NETWORK_LOCK ) {
		return;
	}
	$lock_settings = apply_filters('gfo_lock_network_settings', get_option('gfo_lock_network_settings'));
	if ( $lock_settings ) {
		return;
	}

	// --- only add network menu if plugin is network activated ---
	$plugins = get_site_option( 'active_sitewide_plugins' );
	$plugin = plugin_basename( GUTEN_FREE_FILE );
	if ( array_key_exists( $plugin, $plugins ) ) {
		// 0.9.4: get menu defaults from plugin info array
		$page_title = apply_filters( 'gfo_network_page_title', __( 'Guten Free Network Options', 'guten-free-options' ) );
		$menu_title = apply_filters( 'gfo_network_menu_title', $gutenfree['menutitle'] );
		$capability = apply_filters( 'gfo_manage_network_options_capability', 'manage_network_options' );
		add_submenu_page( 'settings.php', $page_title, $menu_title, $capability, $gutenfree['networkslug'], 'gfo_network_settings_page' );
	}
}

// -----------------------------
// Add Plugin Page Settings Link
// -----------------------------
// 0.9.1: added plugin page settings link
add_filter( 'plugin_action_links', 'gfo_plugin_action_links', 10, 2 );
function gfo_plugin_action_links( $links, $file ) {
	global $gutenfree;
	if ( plugin_basename( GUTEN_FREE_FILE ) == $file ) {
		// 0.9.6: escape settings link output
		// 0.9.6: link to settings at options-general.php not admin.php
		$settings_url = add_query_arg( 'page', $gutenfree['slug'], admin_url( 'options-general.php' ) );
		$settings_link = '<a href="' . esc_url( $settings_url ) . '">' . esc_html( __( 'Settings', 'guten-free-options' ) ) . '</a>';
		$newlink['settings'] = $settings_link;
		$links = array_merge( $newlink, $links );
	}
	return $links;
}

// -------------------------------------
// Add Network Plugin Page Settings Link
// -------------------------------------
// 0.9.1: added network plugin page settings link
add_filter( 'network_admin_plugin_action_links', 'gfo_network_plugin_action_links', 10, 4 );
function gfo_network_plugin_action_links( $links, $file, $plugin_data, $context ) {
	global $gutenfree;
	if ( plugin_basename( GUTEN_FREE_FILE ) == $file ) {
		// 0.9.6: escape settings link output
	 	$settings_url = add_query_arg( 'page', $gutenfree['networkslug'], network_admin_url( 'settings.php' ) );
	 	$settings_link = '<a href="' . esc_url( $settings_url ) . '">' . esc_html( __( 'Network Settings', 'guten-free-options' ) ) . '</a>';
	 	$newlink['settings'] = $settings_link;
	 	$links = array_merge( $newlink, $links );
	}
	return $links;
}

// ------------------------
// Admin Message Box Output
// ------------------------
function gfo_message_box( $message, $echo = true ) {
	$box = '<table style="background-color: lightYellow; border-style:solid; border-width:1px; border-color: #E6DB55; text-align:center;">';
		$box .= '<tr><td>';
			$box .= '<div class="message" style="margin:0.25em;"><font style="font-weight:bold;">';
				// 0.9.6: added wp_kses_post to message output
				$box .= wp_kses_post( $message );
			$box .= '</font></div>';
		$box .= '</td></tr>';
	$box .= '</table>';

	if ( $echo ) {
		echo $box;
	} else {
		return $box;
	}
}

// --------------------------
// Add Submenu Links Abstract
// --------------------------
// (modified from Classic Editor plugin)
// 0.9.3: use single loading action and refilter for each post type
function gfo_add_editor_submenus() {

	foreach ( get_post_types( array( 'show_ui' => true ) ) as $post_type ) {

		$type_obj = get_post_type_object( $post_type );

		if ( ! $type_obj->show_in_menu || ! post_type_supports( $post_type, 'editor' ) ) {
			continue;
		}

		if ( $type_obj->show_in_menu === true ) {
			if ( 'post' === $post_type ) {
				$parent_slug = 'edit.php';
			} elseif ( 'page' === $post_type ) {
				$parent_slug = 'edit.php?post_type=page';
			} else {
				// Not for a submenu.
				continue;
			}
		} else {
			$parent_slug = $type_obj->show_in_menu;
		}

		$item_name = $type_obj->labels->add_new . ' ';

		// 0.9.3: check/filter submenu item for each post type default
		global $current_user;
		$current_user = wp_get_current_user();
		$block_editor = apply_filters( 'gfo_post_type_filters', false, $post_type, $current_user );

		// 0.9.6: move parenthesis to outside translation strings
		if ( $block_editor ) {
			$editor = 'classic';
			$item_name .= '(' . __( 'Classic', 'guten-free-options' ) . ')';
		} else {
			$editor = 'block';
			$item_name .= '(' . __( 'Block', 'guten-free-options' ) . ')';
		}

		$add_new_url = "post-new.php?post_type=" . $post_type . "&editor=" . $editor;
		add_submenu_page( $parent_slug, $type_obj->labels->add_new, $item_name, $type_obj->cap->edit_posts, $add_new_url );
	}
}

// ---------------
// Admin Bar Links
// ---------------
// [unused] - via Classic Editor plugin
function gfo_admin_bar_menu( $wp_admin_bar ) {

	global $post_id, $wp_the_query;

	if ( is_admin() ) {
		$post = get_post( $post_id);
	} else {
		$post = $wp_the_query->get_queried_object();
	}
	if ( empty( $post ) || empty( $post->ID ) ) {
		return;
	}

	// Capability check is in get_edit_post_link().
	$edit_url = get_edit_post_link( $post->ID, 'url' );

	if ( $edit_url &&
		( ( is_admin() && 'post' === get_current_screen()->base ) || ( ! is_admin() && ! empty( $post->post_type ) ) ) &&
		post_type_supports( $post->post_type, 'editor' ) ) {

		// filter to find default editor for this post
		$can_edit = gfo_can_edit_post_type( false, $post->post_type );
		$can_edit = gfo_can_edit_post( $can_edit, $post->ID );

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
// [unused] - via Classic Editor plugin
function gfo_add_edit_links( $actions, $post ) {

	// This is in Gutenberg now.
	if ( array_key_exists( 'classic', $actions ) ) {
		return $actions;
	}

	if ( ( 'trash' === $post->post_status ) || !post_type_supports( $post->post_type, 'editor' ) ) {
		return $actions;
	}

	$edit_url = get_edit_post_link( $post->ID, 'raw' );
	if ( !$edit_url ) {
		return $actions;
	}
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

// ---------------
// The GutenButton
// ---------------
// "Edit with Block Editor" button for Classic Editor
// 0.9.4: move switch editor button to top of page
// add_action('edit_form_after_title', 'gfo_gutenberg_button');
add_action( 'edit_form_top', 'gfo_gutenberg_button' );
function gfo_gutenberg_button() {

	global $gutenfree, $post;
	$switch_button = gfo_get_setting( 'switch_buttons' );
	$switch_button = apply_filters( 'gfo_add_gutenberg_button', $switch_button );
	if ( !$switch_button ) {
		return;
	}

	// 0.9.6: bug out if gutenberg is completely unavailable
	if ( !function_exists( 'has_blocks' ) && !function_exists( 'gutenberg_content_has_blocks' ) ) {
		return;
	}

	// --- set edit URL and GutenButton ---
	// 0.9.4: added margin styles for new button position
	$edit_url = get_edit_post_link( $post->ID );
	$edit_url = add_query_arg( 'editor', 'block', $edit_url );
	$button = array(
		'class'			=> 'button button-primary button-medium',
		'styles'		=> 'margin-top:-35px; margin-left:200px;',
		'id'			=> 'gutenberg-editor-button',
		'anchor'		=> __( 'Edit with Block Editor','guten-free-options' ),
		'title'			=> __( 'Edit this Post with the Block Editor (Gutenberg)','guten-free-options' ),
		'url'			=> $edit_url,
		'icon_class'	=> 'dashicons dashicons-welcome-write-blog',
		'icon_style'	=> 'margin-top:4px;',
	);
	$button = apply_filters( 'gfo_gutenberg_button', $button );

	// --- output the switch editor button on the editor page ---
	// 0.9.7: add missing escaping to style/class outputs
	echo '<div id="gutenberg-editor-button-wrapper">' . "\n";
		echo '<a href="' . esc_url( $button['url'] ) . '">' . "\n";
			echo '<div id="' . esc_attr( $button['id'] ) . '" class="' . esc_attr( $button['class'] ) . '" title="' . esc_attr( $button['title'] ) . '"';
			if ( !empty( $button['styles'] ) ) {
				echo ' style="' . esc_attr( $button['styles'] ) . '"';
			}
			echo '>' . "\n";
				echo '<span class="' . esc_attr( $button['icon_class'] ) . '"';
				if ( !empty( $button['icon_style'] ) ) {
					echo ' style="' . esc_attr( $button['icon_style'] ) . '"';
				}
				echo '></span>' . "\n";
			echo ' ' . esc_html( $button['anchor'] );
			echo '</div>' . "\n";
		echo '</a>' . "\n";
	echo '</div>' . "\n";

	// 0.9.6: move the button to better positions on document load
	echo "<script>jQuery(document).ready(function() {
		styles = {'display':'inline-block', 'vertical-align':'top', 'margin-top':'10px', 'margin-left':'10px'};
		jQuery('#gutenberg-editor-button-wrapper').insertBefore('.wp-header-end').css(styles);
		jQuery('#gutenberg-editor-button').css('margin-top', '').css('margin-left', '');
	});</script>";

	// if ( isset( $gutenfree['hasblocks'] ) && $gutenfree['hasblocks'] ) {
	// 	TODO: maybe add an alert that current post has existing blocks ?
	// }
}

// -----------------
// The ClassicButton
// -----------------
// "Classic Editor" button for Block Editor (Gutenberg)
add_action( 'admin_footer', 'gfo_classic_button' );
function gfo_classic_button() {
	global $gutenfree, $post, $current_screen;

	// 0.9.1: use block editor checking function to prevent Gutenberg plugin crashing
	if ( !gfo_is_block_editor_page() ) {
		return;
	}

	$switch_button = gfo_get_setting( 'switch_buttons' );
	$switch_button = apply_filters( 'gfo_add_classic_button', $switch_button );
	if ( !$switch_button ) {
		return;
	}

	// --- javascript confirmation onclick if content has existing blocks ---
	if ( !isset( $gutenfree['hasblocks'] ) || !$gutenfree['hasblocks'] ) {
		$onclick = 'gfo_switch_editor();';
	} else {
		$onclick = 'return gfo_switch_confirm();';
	}
	// TODO: how to escape this properly for translation with line breaks ?
	$confirm = __( 'Switching back to Classic Editor\\nfor this post is not recommended\\nbecause you have existing blocks. Continue?', 'guten-free-options' );

	// create edit URL and Classic Button
	$edit_url = get_edit_post_link( $post->ID, 'url' );
	// 0.9.1: fix to updated editor querystring arg
	$edit_url = add_query_arg( 'editor', 'classic', $edit_url );
	$button = array(
		'class'		=> 'components-button editor-post-switch-to-classic is-button is-default is-large',
		'id'		=> 'classic-editor-button',
		'title'		=> __( 'Edit with Classic Editor','guten-free-options' ),
		'anchor'	=> __( 'Classic','guten-free-options' ),
		'url'		=> $edit_url,
		'onclick'	=> $onclick,
		'confirm'	=> $confirm,
	);
	$button = apply_filters( 'gfo_classic_button', $button );

	// --- add the switch editor button to the editor page ---
	// note: using setInterval cycling above to ensure DOM is ready for appending!
	// 0.9.1: add extra check for .edit-post-header__settings element just in case
	echo "<script>var editor_switch_confirm_message = '" . esc_js( $button['confirm'] ) . "';
	function gfo_switch_confirm() {
		var agree = confirm(editor_switch_confirm_message);
		if (!agree) {return false;} 
		gfo_switch_editor();
	}
	function gfo_switch_editor() {window.location.href = '" . esc_url( $edit_url ) . "';}
	var classic_button = jQuery('";
		// 0.9.6: move button HTML output inline
		echo '<button type="button" id="' . esc_js( $button['id'] ) . '" class="' . esc_js( $button['class'] ) . '" title="' . esc_js( $button['title'] ) . '"';
		if ( !isset( $gutenfree['hasblocks'] ) || !$gutenfree['hasblocks'] ) {
			echo ' onclick="gfo_switch_editor();"';
		} else {
			echo ' onclick="return gfo_switch_confirm();"';
		}
		echo esc_js( $button['anchor'] ) . '</button>';
	echo "');
	jQuery(document).ready(function() {
		var add_classic_button;
		if (jQuery('.edit-post-header__settings')) {
			add_classic_button = setInterval(function() {
				jQuery('.edit-post-header__settings').append(classic_button);
				if (jQuery('#" . esc_js( $button['id'] ) . "')) {clearInterval(add_classic_button);}
			}, 500);
		}
	});</script>";
}

// ----------------------
// Replace Editor Scripts
// ----------------------
// this is (unused) JS and CSS enqueue code from Classic Editor plugin...
// ...presumably for when Classic Editor scripts are deprecated  - hopefully never!
function gfo_classic_editor_replace( $return ) {

	// --- bail if the editor has been replaced already ---
	if ( true === $return ) {
		return $return;
	}

	// 0.9.6: added check if SCRIPT_DEBUG is defined
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	$js_url = plugin_dir_url( __FILE__ ) . 'js/';
	$css_url = plugin_dir_url( __FILE__ ) . 'css/';

	// Enqueued conditionally from legacy-edit-form-advanced.php
	wp_register_script( 'editor-expand', $js_url . "editor-expand" . $suffix . ".js", array( 'jquery', 'underscore' ), false, 1 );

	// The dependency 'tags-suggest' is also needed for 'inline-edit-post', not included.
	wp_register_script( 'tags-box', $js_url . "tags-box" . $suffix . ".js", array( 'jquery', 'tags-suggest' ), false, 1 );
	wp_register_script( 'word-count', $js_url . "word-count" . $suffix . ".js", array(), false, 1 );

	// The dependency 'heartbeat' is also loaded on most wp-admin screens, not included.
	wp_register_script( 'autosave', $js_url . "autosave" . $suffix . ".js", array( 'heartbeat' ), false, 1 );
	wp_localize_script( 'autosave', 'autosaveL10n', array(
		'autosaveInterval' => AUTOSAVE_INTERVAL,
		'blog_id' => get_current_blog_id(),
	) );

	wp_enqueue_script( 'post', $js_url . "post" . $suffix . ".js", array(
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

	wp_enqueue_style( 'classic-edit', plugin_dir_url( __FILE__ ) . 'css/edit' . $suffix . '.css' );

	// Other scripts and stylesheets:
	// wp_enqueue_script( 'admin-comments' ) is a dependency for 'dashboard', also used in edit-comments.php.
	// wp_enqueue_script( 'image-edit' ) and wp_enqueue_style( 'imgareaselect' ) are also used in media.php and media-upload.php.

	include_once plugin_dir_path( __FILE__ ) . 'edit-form-advanced.php';

	return true;
}

// ----------------------------------
// Maybe Disable Widgets Block Editor
// ----------------------------------
// 0.9.6: added for disabling block editor for widgets
// note: uses init rather than admin_init hook for Customizer widget support
add_action( 'init', 'gfo_maybe_disable_widgets_block_editor' );
function gfo_maybe_disable_widgets_block_editor() {
	
	$disable_widget_blocks = false;
	$widget_blocks = gfo_get_setting( 'widget_blocks', false );
	if ( 'off' == $widget_blocks ) {
		$disable_widget_blocks = true;
	} elseif ( ( '' == $widget_blocks ) || ( 'auto' == $widget_blocks ) ) {
		// --- auto: check if widget_sidebars contain any widget blocks ---
		$disable_widget_blocks = true;
		if ( gto_sidebars_have_widget_blocks() ) {
			$disable_widget_blocks = false;
		}
	}

	if ( $disable_widget_blocks ) {
		add_filter( 'gutenberg_use_widgets_block_editor', '__return_false', 100 );
		add_filter( 'use_widgets_block_editor', '__return_false', 100 );
	}
}


// ------------------------------------------
// Display Widgets Block Editor Admin Message
// ------------------------------------------
// 0.9.6: added admin message for widget page
add_action( 'widgets_admin_page', 'gfo_widgets_page_message' );
function gfo_widgets_page_message() {
	
	global $gutenfree;
	$disable_widget_blocks = false;
	$widget_blocks = gfo_get_setting( 'widget_blocks', false );

	// 0.9.7: fix mismatched text domains
	echo '<div class="message">' . "\n";
		echo '<b>' . esc_html( __( 'Guten Free Options', 'guten-free-options' ) ) . '</b>: ';
		if ( 'off' == $widget_blocks ) {
			echo esc_html( __( 'The use of the Block Editor has been disabled for Widgets.', 'guten-free-options' ) );
		} elseif ( ( '' == $widget_blocks ) || ( 'auto' == $widget_blocks ) ) {
			echo esc_html( __( 'No Widgets contain blocks. Auto mode has disabled the Block Editor for Widgets.', 'guten-free-options' ) );
		}
		// 0.9.6: link to settings at options-general.php not admin.php
		$settings_url = add_query_arg( 'page', $gutenfree['slug'], admin_url( 'options-general.php' ) );
		echo ' <a href="' . esc_url( $settings_url ) . '">' . esc_html( __( 'Settings', 'guten-free-options' ) ) . '</a>' . "\n";	
	echo '</div>' . "\n";
	
}


// =======================
// --- Plugin Settings ---
// =======================

// --------------------------------
// Set Named Global Plugin Settings
// --------------------------------
$gutenfree = $settings;
unset( $settings );

// -------------------
// Get Plugin Settings
// -------------------
$gutenfree_settings = get_option( 'guten_free_options', false );
// 0.9.4: loop array instead of merging
if ( $gutenfree_settings && is_array( $gutenfree_settings ) ) {
	foreach ( $gutenfree_settings as $key => $value ) {
		$gutenfree[$key] = $value;
	}
}

// -----------------------------------------------
// Prevent Disabling of Gutenberg plugin on Update
// -----------------------------------------------
// 0.9.3: prevent update disabling gutenberg plugin
// 0.9.4: add check for if constant is already defined
$prevent_disable = gfo_get_setting( 'prevent_disable' );
if ( $prevent_disable && !defined( 'GUTENBERG_USE_PLUGIN' ) ) {
	define( 'GUTENBERG_USE_PLUGIN', true );
}

// ----------------------------
// Check Debug Switch Overrides
// ----------------------------
// 0.9.4: simplified and added missing debug off switches
// 0.9.6: add to action to allow for capabaility check
add_action( 'init', 'gfo_debug_switch', 9 );
function gfo_debug_switch() {
	global $gutenfree;
	if ( defined( 'GUTEN_FREE_DEBUG' ) ) {
		$gutenfree['debug'] = GUTEN_FREE_DEBUG ? true : false;
	} elseif ( isset( $_REQUEST['gfo_debug'] ) ) {
		$switch = sanitize_text_field( wp_unslash( $_REQUEST['gfo_debug'] ) );
		// 0.9.6: 
		if ( current_user_can( 'edit_posts' ) ) {
			if ( ( '1' == $switch ) || ( 'on' == $switch ) ) {
				$gutenfree['debug'] = true;
			} elseif ( ( '0' == $switch ) || ( 'off' == $switch ) ) {
				$gutenfree['debug'] = false;
			}
		}
	}
}

// ---------------------------
// Get Default Plugin Settings
// ---------------------------
function gfo_default_settings() {

	// set site default for a multisite site or single site
	// 0.9.3: fix to default value of inherit
	// 0.9.6: simplified logic to one line
	$default = is_multisite() ? 'inherit' : 'classic';

	// 0.9.3: added prevent_disable switch
	// 0.9.6: added widget_blocks option
	$defaults = array(

		/* Switches */
		'default_editor'		=> $default,
		'widget_blocks'    		=> 'auto',
		'switch_buttons'		=> 'yes',
		'disable_nag'			=> 'yes',
		'remove_menu'			=> 'yes',
		'prevent_disable'		=> 'yes',
		'check_blocks'			=> 'yes',
		'user_default'			=> '',
		'editor_metabox'		=> 'yes',
		// 'no_override'		=> '',

		/* Post Types */
		'classic_types'			=> array(),
		'block_types'			=> array(),
		// 0.9.3: added post type locks
		'lock_types'			=> array(),

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
	$defaults = apply_filters( 'gfo_default_settings', $defaults );
	return $defaults;
}

// ------------------
// Get Plugin Setting
// ------------------
// note: any setting can be easily filtered using gfo_ prefix to the setting key eg.
// add_filter('gfo_default_editor', 'my_custom_filter');
function gfo_get_setting( $key, $filter = true ) {
	global $gutenfree;
	if ( isset( $gutenfree[$key] ) ) {
		$value = $gutenfree[$key];
	} else {
		if ( !isset( $gutenfree['defaults'] ) ) {
			$gutenfree['defaults'] = gfo_default_settings();
		}
		if ( isset( $gutenfree['defaults'][$key] ) ) {
			$value = $gutenfree['defaults'][$key];
		} else {
			$value = null;
		}
	}
	if ( $filter ) {
		$value = apply_filters( 'gfo_' . $key, $value );
	}
	return $value;
}

// -------------------
// Add Plugin Settings
// -------------------
// register_activation_hook(__FILE__, 'gfo_add_settings');
// note: in effect this does the same as register_activation hook
$plugin_file = plugin_basename( GUTEN_FREE_FILE );
add_action( 'activate_' . $plugin_file, 'gfo_add_settings', 10, 1 );
function gfo_add_settings( $network_wide ) {

	global $gutenfree;

	// --- add default settings ---
	$defaults = gfo_default_settings();
	$added = add_option( 'guten_free_options', $defaults );

	// 0.9.4: override settings global only if added
	if ( $added ) {
		foreach ( $defaults as $key => $value ) {
			$gutenfree[$key] = $value;
		}
	}

	// note: no need to set any network defaults (yet)
	// if (is_multisite() && $network_wide) {
	//	$add = add_site_option('network_default_editor', '');
	// }

	// --- add sidebar options ---
	$adsboxoff = file_exists( GUTEN_FREE_DIR . '/updatechecker.php' ) ? '' : 'checked';
	$sidebar_options = array(
		'adsboxoff'      => $adsboxoff,
		'donationboxoff' => '',
		'reportboxoff'   => '',
		'installdate'    => gmdate( 'Y-m-d' ),
	);
	add_option( $gutenfree['settings'] . '_sidebar_options', $sidebar_options );
}

// ----------------------
// Update Plugin Settings
// ----------------------
add_action('admin_init', 'gfo_update_settings');
function gfo_update_settings() {

	global $gutenfree;
	$settings = $gutenfree;
	if ( !isset( $_POST['gfo_update_settings'] ) || ( 'yes' != sanitize_text_field( wp_unslash( $_POST['gfo_update_settings'] ) ) ) ) {
		return;
	}
	$capability = apply_filters( 'gfo_manage_options_capability', 'manage_options' );
	if ( !current_user_can( $capability ) ) {
		return;
	}
	check_admin_referer( 'guten-free-options' );

	// 0.9.4: get default settings here
	$defaults = gfo_default_settings();

	// 0.9.3: added prevent_disable switch
	// 0.9.6: added widget_blocks option
	$options = array(

		/* Switches */
		'default_editor'		=> 'classic/inherit/block',
		'widget_blocks'			=> 'on/off/auto',
		'switch_buttons'		=> 'checkbox',
		'disable_nag'			=> 'checkbox',
		'remove_menu'			=> 'checkbox',
		'prevent_disable'		=> 'checkbox',
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

	// --- loop options to get settings ---
	foreach ( $options as $key => $type ) {
		if ( !isset( $_POST['gfo_' . $key] ) ) {
			$posted = '';
		} elseif ( 'textarea' == $type ) {
			$posted = sanitize_textarea_field( wp_unslash( $_POST['gfo_' . $key] ) );
		} else {
			$posted = sanitize_text_field( wp_unslash( $_POST['gfo_' . $key] ) );
		}

		if ( strstr( $type, '/' ) ) {
			$valid = explode( '/', $type );
			if ( in_array( $posted, $valid ) ) {
				$settings[$key] = $posted;
			}
		} elseif ( 'checkbox' == $type ) {
			if ( ( '' == $posted ) || ( 'yes' == $posted ) ) {
				$settings[$key] = $posted;
			}
		} elseif ( 'numeric' == $type ) {
			// 0.9.6: fix to numeric check on absint
			$posted = absint( $posted );
			if ( $posted > -1 ) {
				$settings[$key] = $posted;
			} else {
				$settings[$key] = '';
			}
		} elseif ( 'alphanumeric' == $type ) {
			$checkposted = preg_match( '/^[a-zA-Z0-9_]+$/', $posted );
			if ( $checkposted ) {
				$settings[$key] = $posted;
			}
		} elseif ( 'text' == $type ) {
			$posted = sanitize_text_field( $posted );
			$settings[$key] = $posted;
		} elseif ( 'textarea' == $type ) {
			$posted = stripslashes( wp_kses_post( $posted ) );
			$settings[$key] = $posted;
		} elseif ( 'csv' == $type ) {
			$cleaned = array();
			if ( strstr( $posted, ',' ) ) {
				$values = explode( ',', $posted );
			} else {
				$values = array( $posted );
			}
			foreach ( $values as $i => $value ) {
				$value = sanitize_text_field( trim( $value ) );
				if ( ( '' != $value ) && !in_array( $value, $cleaned ) ) {
					$cleaned[] = $value;
				}
			}
			$settings[$key] = $cleaned;
		}
	}

	// --- get post type settings ---
	$classic_types = $settings['classic_types'];
	if ( !is_array( $classic_types ) ) {
		$classic_types = array();
	}
	$block_types = $settings['block_types'];
	if ( !is_array( $block_types ) ) {
		$block_types = array();
	}
	$post_types = gfo_get_post_types();

	// 0.9.3: get/set post type lock array
	$lock_types = $checked_lock_types = array();
	if ( isset( $settings['lock_types'] ) && is_array( $settings['lock_types'] ) ) {
		$lock_types = $settings['lock_types'];
	}

	// -- get user role settings ---
	$classic_roles = $settings['classic_roles'];
	if ( !is_array( $classic_roles ) ) {
		$classic_roles = array();
	}
	$block_roles = $settings['block_roles'];
	if ( !is_array( $block_roles ) ) {
		$block_roles = array();
	}
	$role_types = gfo_get_user_roles();

	// --- get post template settings ---
	$classic_templates = $settings['classic_templates'];
	if ( !is_array( $classic_templates ) ) {
		$classic_templates = array();
	}
	$block_templates = $settings['block_templates'];
	if ( !is_array( $block_templates ) ) {
		$block_templates = array();
	}
	$templates = gfo_get_post_templates();

	// --- loop all post values to check keys ---
	// 0.9.6: get keys to loop first
	$keys = array_keys( $_POST );
	foreach ( $keys as $key ) {

		$value = sanitize_text_field( wp_unslash( $_POST[$key] ) );

		if ( 0 === strpos( $key, 'gfo_post_type_' ) ) {

			// === Post Types ===
			// ------------------
			$key = substr( $key, strlen( 'gfo_post_type_' ), strlen( $key ) );
			foreach ( $post_types as $post_type_key => $post_type_label ) {
				if ( $key == $post_type_key ) {
					$label = $post_type_label;
					break;
				}
			}

			if ( '' == $value ) {
				if ( array_key_exists( $key, $block_types ) ) {
					unset( $block_types[$key] );
				}
				if ( array_key_exists( $key, $classic_types ) ) {
					unset( $classic_types[$key] );
				}
			} elseif ( 'classic' == $value ) {
				if ( !array_key_exists( $key, $classic_types ) ) {
					$classic_types[$key] = $label;
				}
				if ( array_key_exists( $key, $block_types ) ) {
					unset( $block_types[$key] );
				}
			} elseif ( 'block' == $value ) {
				if ( !array_key_exists( $key, $block_types ) ) {
					$block_types[$key] = $label;
				}
				if ( array_key_exists( $key, $classic_types ) ) {
					unset( $classic_types[$key] );
				}
			}

		} elseif ( 0 === strpos( $key, 'gfo_user_role_' ) ) {

			// === User Roles ===
			// ------------------
			$key = substr( $key, strlen( 'gfo_user_role_' ), strlen( $key ) );
			foreach ( $role_types as $role_type_key => $role_type_label ) {
				if ( $key == $role_type_key ) {
					$label = $role_type_label;
					break;
				}
			}

			if ( '' == $value ) {
				if ( array_key_exists( $key, $block_roles ) ) {
					unset( $block_roles[$key] );
				}
				if ( array_key_exists( $key, $classic_roles ) ) {
					unset( $classic_roles[$key] );
				}
			} elseif ( 'classic' == $value ) {
				if ( !array_key_exists( $key, $classic_roles ) ) {
					$classic_roles[$key] = $label;
				}
				if ( array_key_exists( $key, $block_roles ) ) {
					unset( $block_roles[$key] );
				}
			} elseif ( 'block' == $value ) {
				if ( !array_key_exists( $key, $block_roles ) ) {
					$block_roles[$key] = $label;
				}
				if ( array_key_exists( $key, $classic_roles ) ) {
					unset( $classic_roles[$key] );
				}
			}

		} elseif ( 0 === strpos( $key, 'gfo_template_' ) ) {

			// === Post Templates ===
			// ----------------------
			$key = substr( $key, strlen( 'gfo_template_' ), strlen( $key ) );
			foreach ( $templates as $type => $template ) {
				foreach ( $template as $file => $label ) {
					if ( !isset( $post_templates[$file] ) ) {
						$post_templates[$file] = $label;
					}
				}
			}

			// 0.9.3: check post template count before looping
			if ( count( $post_templates ) > 0 ) {
				foreach ( $post_templates as $file => $label ) {
					if ( $key . '.php' == $file ) {
						$label = $post_templates[$file];
						if ( '' == $value ) {
							if ( array_key_exists( $key, $block_templates ) ) {
								unset( $block_templates[$key] );
							}
							if ( array_key_exists( $key, $classic_templates)) {
								unset( $classic_templates[$key] );
							}
						} elseif ( 'classic' == $value ) {
							if ( !array_key_exists( $key, $classic_templates ) ) {
								$classic_templates[$key] = $label;
							}
							if ( array_key_exists( $key, $block_templates ) ) {
								unset( $block_templates[$key] );
							}
						} elseif ( 'block' == $value ) {
							if ( !array_key_exists( $key, $block_templates ) ) {
								$block_templates[$key] = $label;
							}
							if ( array_key_exists( $key, $classic_templates ) ) {
								unset( $classic_templates[$key] );
							}
						}
					}
				}
			}

		} elseif ( 0 === strpos( $key, 'gfo_lock_type_' ) ) {

			// === Post Type Locks ===
			// -----------------------
			// 0.9.3: check post type locking checkboxes

			$key = substr( $key, strlen( 'gfo_lock_type_' ), strlen( $key ) );
			foreach ( $post_types as $post_type_key => $post_type_label ) {
				if ( ( $key == $post_type_key ) && ( 'yes' == $value ) && !in_array( $key, $lock_types ) ) {
					// --- add lock types for post types with checkbox checked ---
					$lock_types[] = $key;
					$checked_lock_types[] = $key;
				}
			}
		}
	}

	// --- update post type, role and template settings ---
	$settings['classic_types'] = $classic_types;
	$settings['block_types'] = $block_types;
	$settings['classic_roles'] = $classic_roles;
	$settings['block_roles'] = $block_roles;
	$settings['classic_templates'] = $classic_templates;
	$settings['block_templates'] = $block_templates;

	// --- remove locks from active post types with unchecked checkboxes ---
	foreach ( $post_types as $post_type_key => $post_type_label ) {
		if ( !in_array( $post_type_key, $checked_lock_types ) && in_array( $post_type_key, $lock_types ) ) {
			$i = array_search( $post_type_key, $lock_types );
			unset( $lock_types[$i] );
		}
	}

	// 0.9.3: recheck post type lock settings
	// ? maybe only apply lock if post type is set to classic or block (not inherit) ?
	// if (count($lock_types) > 0) {
	//	foreach ($lock_types as $i => $lock_type) {
	//		if (!array_key_exists($lock_type, $classic_types) && !array_key_exists($lock_type, $block_types)) {
	//			unset($lock_types[$i];
	//		}
	//	}
	// }
	$settings['lock_types'] = $lock_types;

	// 0.9.4: remove non-default keys and merge with existing settings
	$settings_keys = array_keys( $defaults );
	foreach ( $settings as $key => $value ) {
		if ( !in_array( $key, $settings_keys ) ) {
			unset( $settings[$key] );
		}
	}

	// --- update plugin settings ---
	// 0.9.6: added filter to updated plugin settings
	$settings['savetime'] = time();
	$settings = apply_filters( 'gfo_updated_settings', $settings );
	update_option( 'guten_free_options', $settings );

	// 0.9.4: merge with existing settings for pageload
	foreach ( $settings as $key => $value ) {
		$gutenfree[$key] = $value;
	}

	// 0.9.4: setting update message flag
	$_GET['updated'] = 'yes';
}

// ---------------------
// Reset Plugin Settings
// ---------------------
add_action( 'admin_init', 'gfo_reset_settings' );
function gfo_reset_settings() {

	if ( !isset( $_POST['gfo_update_settings'] ) || ( 'reset' != sanitize_text_field( $_POST['gfo_update_settings'] ) ) ) {
		return;
	}
	$capability = apply_filters( 'gfo_manage_options_capability', 'manage_options' );
	if ( !current_user_can( $capability ) ) {
		return;
	}
	check_admin_referer( 'guten-free-options' );

	// reset plugin settings to defaults
	// 0.9.3: fix to not override plugin info values
	$defaults = gfo_default_settings();
	update_option( 'guten_free_options', $defaults );

	// 0.9.4: fix to only save settings not plugin info values
	global $gutenfree;
	foreach ( $defaults as $key => $value ) {
		$gutenfree[$key] = $value;
	}

	// 0.9.4: settings reset message flag
	$_GET['updated'] = 'reset';
}

// ------------------------------
// Update Network Plugin Settings
// ------------------------------
add_action( 'admin_init', 'gfo_update_network_settings' );
function gfo_update_network_settings() {
	if ( !is_multisite() || !is_network_admin() ) {
		return;
	}
	if ( !isset( $_POST['gfo_update_settings'] ) || ( 'network' != sanitize_text_field( $_POST['gfo_update_settings'] ) ) ) {
		return;
	}
	$capability = apply_filters( 'gfo_manage_network_options_capability', 'manage_network_options' );
	if ( !current_user_can( $capability ) ) {
		return;
	}
	check_admin_referer( 'guten-free-options' );

	// --- update network default editor setting ---
	if ( isset( $_POST['gfo_network_default_editor'] ) ) {
		$default_editor = sanitize_text_field( wp_unslash( $_POST['gfo_network_default_editor'] ) );
		$valid = array( 'classic', 'block' );
		if ( in_array( $default_editor, $valid ) ) {
			update_site_option( 'network_default_editor', $default_editor );
		}
	}
}

// ------------------
// Plugin Page Header
// ------------------
function gfo_plugin_page_header( $network = false ) {

	global $gutenfree;
	$settings = $gutenfree;

	$icon_url = plugins_url( 'images/' . $settings['slug'] . '.png', GUTEN_FREE_FILE );
	$wpmedic_icon_url = plugins_url( 'images/wpmedic.png', GUTEN_FREE_FILE );
	// $wordquest_icon_url = plugins_url( 'images/wordquest.png', GUTEN_FREE_FILE );
	echo '<style>.pluginlink {text-decoration:none;} .pluginlink:hover {text-decoration:underline;}</style>';

	echo '<br><table><tr><td><img src="' . esc_url( $icon_url ) . '"></td>' . "\n";
	echo '<td width="20"></td><td>' . "\n";
		echo '<table><tr><td>' . "\n";
			echo '<h3 style="font-size:20px;margin:10px;">' . "\n";
				echo '<a href="http://wpmedic.tech/guten-free-options/" target="_blank" style="text-decoration:none;">' . esc_html( $settings['title'] ) . '</a></h3>' . "\n";
		echo '</td><td width="20"></td><td>' . "\n";
			echo '<h3 style="margin:10px;">v' . esc_html( $settings['version'] ) . '</h3>' . "\n";
		echo '</td></tr><tr><td colspan="3" align="center">' . "\n";
			echo '<table><tr><td align="center">' . "\n";
				echo '<span style="font-size:16px;">' . esc_html( __( 'by', 'guten-free-options' ) ) . '</span> ' . "\n";
				echo '<a href="http://wpmedic.tech/" target="_blank" style="text-decoration:none;font-size:16px;">';
					echo '<b>' . esc_html( __( 'WP Medic', 'guten-free-options' ) ) . '</b></a><br><br>' . "\n";
				// 0.9.4: add readme thickbox link
				$readme_url = add_query_arg( 'action', 'gfo_readme_viewer', admin_url( 'admin-ajax.php' ) );
				echo '<a href="' . esc_url( $readme_url ) . '" class="pluginlink thickbox">' . "\n";
					echo '<b>' . esc_html( __( 'Readme', 'guten-free-options' ) ) . '</b></a>' . "\n";
			echo '</td><td width="30"></td><td>' . "\n";
				echo '<a href="https://wpmedic.tech/" target="_blank"><img src="' . esc_url( $wpmedic_icon_url ) . '" width="64" height="64" border="0">' . "\n";
			echo '</td></tr></table>' . "\n";
		echo '</td></tr></table>' . "\n";

	echo '</td><td width="50"></td><td style="vertical-align:top;">' . "\n";
		// 0.9.4: add star rating review link
		// 0.9.6: fix variable args to settings
		echo '<br>' . "\n";
		if ( isset( $settings['wporgslug'] ) ) {
			$rate_url = 'https://wordpress.org/plugins/' . $settings['wporgslug'] . '/reviews/#newpost';
			echo '<span style="font-size:24px; color:#FC5; margin-right:10px;" class="dashicons dashicons-star-filled"></span> ' . "\n";
			echo '<a href="' . esc_url( $rate_url ) . '" class="pluginlink" target="_blank">';
				echo esc_html( __( 'Rate on WordPress.Org', 'guten-free-options') ) . '</a><br><br>' . "\n";
		}
	echo '</td></tr>' . "\n";
	// 0.9.4: add update and reset message flags
	if ( isset( $_GET['updated'] ) ) {
		echo '<tr><td></td><td></td><td>' . "\n";
		if ( 'yes' == sanitize_text_field( wp_unslash( $_GET['updated'] ) ) ) {
			$message = esc_html( $settings['title'] ) . ' ';
			if ( $network ) {
				$message .= esc_html( __( 'Network', 'guten-free-options' ) ) . ' ';
			}
			$message .= esc_html( __( 'Settings Updated.', 'guten-free-options' ) );
		} elseif ( 'reset' == $_GET['updated'] ) {
			$message = esc_html( $settings['title'] ) . ' ';
			$message .= esc_html( __( 'Settings Reset!', 'guten-free-options' ) );
		}
		gfo_message_box( $message, true );
		echo '</td></tr>' . "\n";
	}
	echo '</table><br>' . "\n";
}

// --------------------
// Plugin Settings Page
// --------------------
function gfo_settings_page() {

	$capability = apply_filters( 'gfo_manage_options_capability', 'manage_options' );
	if ( !current_user_can( $capability ) ) {
		return;
	}

	global $gutenfree;

	// --- output filtered plugin settings page styles ---
	$styles = ".label-cell {min-width:200px;} .post-input {width:400px;}
	.yes-no-cell {width:65px; text-align:center;}";
	$styles = apply_filters( 'gfo_settings_page_styles', $styles );
	if ( '' != $styles ) {
		// 0.9.6: use wp_kses_post on filtered styles
		echo '<style>' . wp_kses_post( $styles ) . '</style>';
	}

	// --- plugin settings page header ---
	gfo_plugin_page_header();

	// --- cascading settings usage note ---
	echo esc_html( __( 'This plugin sets the Default Editor to be used by checking these conditions in order:', 'guten-free-options' ) ) . '<br>' . "\n";
	if ( is_multisite() ) {
		$network_options_url = add_query_arg( 'page', 'guten-free-network-options', network_admin_url() );
		echo '<a href="' . esc_url( $network_options_url ) . '">' . "\n";
			echo '<i>' . esc_html( __( 'Network Default', 'guten-free-option' ) ) . '</i></a> -> ' . "\n";
	} else {
		echo '(<i>' . esc_html( __( 'Network Default', 'guten-free-options' ) ) . '</i> ' . "\n";
		echo esc_html( __( 'which optionally sets the default editor on Multisite installations.', 'guten-free-options' ) );
		echo ')<br>' . "\n";
	}
	echo '<i>' . esc_html( __( 'Site Default', 'guten-free-options' ) ) . '</i> -> ';
	echo '<i>' . esc_html( __( 'User Filtering', 'guten-free-options') ) . '</i> -> ';
	echo '<i>' . esc_html( __( 'Post Type Filtering', 'guten-free-options' ) ) . '</i> -> ';
	echo '<i>' . esc_html( __( 'Post Filtering', 'guten-free-options' ) ) . '</i><br>';
	echo '-> <i>' . esc_html( __( 'Querystring Overrides','guten-free-options' ) ) . '</i> ';
	echo esc_html( __( 'set via the browser URL address will override the above settings.', 'guten-free-options' ) ) . '<br>' . "\n";
	echo "-> <i>" . esc_html( __( 'Admin Post Overrides', 'guten-free-options' ) ) . '</i> ';
	echo esc_html( __( 'set here will override all the above settings for the specified posts.', 'guten-free-options' ) ) . '<br><br>';

	echo esc_html( __( 'If any of the conditions set match for the pageload, they will override the previous level(s).', 'guten-free-options' ) ) . '<br>' . "\n";
	echo esc_html( __( 'This makes for the most flexible options, while still keeping all settings completely optional!', 'guten-free-options' ) ) . '<br>' . "\n";
	echo '(' . esc_html( __( 'This hierarchy of settings is similar to how cascading stylesheet rules work for page elements.', 'guten-free-options' ) ) . ')<br><br>' . "\n";

	// --- open settings form ---
	echo '<form id="gfo-settings-form" method="post">' . "\n";
	echo '<input type="hidden" name="page" value="guten-free-options">' . "\n";
	echo '<input type="hidden" id="gfo-update-action" name="gfo_update_settings" value="yes">' . "\n";
	wp_nonce_field( 'guten-free-options' );

	// === Default Editor Settings ===
	// -------------------------------
	echo '<h3>' . esc_html( __( 'Default Editor Settings', 'guten-free-options' ) ) . '</h3>' . "\n";
	echo '<table>' . "\n";

		// --- Site Default Editor ---
		$default_editor = gfo_get_setting( 'default_editor', false );
		echo '<tr><td class="label-cell" style="vertical-align:top;">' . "\n";
			echo '<b>' . esc_html( __( 'Site Default Editor', 'guten-free-options' ) ) . '</b>' . "\n";
		echo '</td><td width="10"></td><td style="vertical-align:top;">' . "\n";
			echo '<input type="radio" name="gfo_default_editor" value="classic"';
			if ( 'classic' == $default_editor ) {
				echo ' checked="checked"';
			}
			echo '> ' . esc_html( __( 'Classic', 'guten-free-options' ) );
		echo '</td><td width="10"></td>' . "\n";

		echo '<td style="vertical-align:top;">' . "\n";
			echo '<input type="radio" name="gfo_default_editor" value="block"';
			if ( 'block' == $default_editor ) {
				echo ' checked="checked"';
			}
			echo '> ' . esc_html( __( 'Block', 'guten-free-options' ) );
		echo '</td><td width="10"></td>' . "\n";

		echo '<td>' . "\n";
			// --- optional inherit from multisite setting ---
			if ( is_multisite() ) {
				echo '<table><tr><td>' . "\n";
					echo '<input type="radio" name="gfo_default_editor" value="inherit"';
					// 0.9.3: fix to checked value match
					if ( ( '' == $default_editor ) || ( 'inherit' == $default_editor ) ) {
						echo ' checked="checked"';
					}
					echo '> ' . esc_html( __( 'Inherit', 'guten-free-options' ) )  . "\n";
				echo '</td><td width="20"></td><td>' . "\n";
					echo esc_html( __( 'Sets the Site Default Editor.', 'guten-free-options' ) );
				echo '</td></tr></table>' . "\n";
			} else {
				echo esc_html( __( 'Sets the Site Default Editor.', 'guten-free-options' ) ) . '<br>' . "\n";
			}

			// --- shorthand settings note ---
			echo '<i>' . esc_html( __( 'Classic', 'guten-free-options' ) ) . '</i> = <i>' . esc_html( __( 'Classic TinyMCE Editor', 'guten-free-options' ) ) . '</i><br>' . "\n";
			echo '<i>' . esc_html( __( 'Block', 'guten-free-options' ) ) . '</i> = <i>' . esc_html( __( 'Gutenberg Block Editor', 'guten-free-options' ) ) . '</i>' . "\n";
		echo '</td></tr>' . "\n";

		// --- Widget Blocks Editor ---
		// 0.9.6: added widgets block editor disabler option
		$widget_blocks = gfo_get_setting( 'widget_blocks', false );
		echo '<tr><td class="label-cell" style="vertical-align:top;">' . "\n";
			echo '<b>' . esc_html( __( 'Widget Blocks Editor', 'guten-free-options' ) ) . '</b>' . "\n";
		echo '</td><td width="10"></td><td colspan="3" style="vertical-align:top; text-align: center;">' . "\n";

			echo '<table><tr><td>' . "\n";
				echo '<input type="radio" name="gfo_widget_blocks" value="auto"';
				if ( ( '' == $widget_blocks ) || ( 'auto' == $widget_blocks ) ) {
					echo ' checked="checked"';
				}
				echo '> ' . esc_html( __( 'Auto', 'guten-free-options' ) )  . "\n";

			echo '</td><td width="5"></td><td>' . "\n";
				echo '<input type="radio" name="gfo_widget_blocks" value="off"';
				if ( 'off' == $widget_blocks ) {
					echo ' checked="checked"';
				}
				echo '> ' . esc_html( __( 'Off', 'guten-free-options' ) );
			echo '</td><td width="5"></td><td>' . "\n";
				echo '<input type="radio" name="gfo_widget_blocks" value="on"';
				if ( 'on' == $widget_blocks ) {
					echo ' checked="checked"';
				}
				echo '> ' . esc_html( __( 'On', 'guten-free-options' ) );
			echo '</td></tr></table>' . "\n";

		echo '</td><td width="10"></td><td>' . "\n";

			echo esc_html( __( 'Disables the Block Editor on widgets page.', 'guten-free-options' ) ) . '<br>' . "\n";
			echo '(' . esc_html( __( 'Auto disables unless a block widget is found.', 'guten-free-options' ) ) . ')<br>' . "\n";
		
		echo '</td></tr>' . "\n";
	
		// --- Switch Editor Buttons? ---
		$switch_buttons = gfo_get_setting( 'switch_buttons', false );
		echo '<tr><td class="label-cell" style="vertical-align:top;">' . "\n";
			echo '<b>' . esc_html( __( 'Switch Editor Buttons?', 'guten-free-options' ) ) . '</b>' . "\n";
		echo '</td><td width="10"></td><td class="yes-no-cell">' . "\n";
			echo '<input type="radio" name="gfo_switch_buttons" value="yes"';
			if ( 'yes' == $switch_buttons ) {
				echo ' checked="checked"';
			}
			echo '> ' . esc_html( __( 'Yes', 'guten-free-options' ) );
		echo '</td><td width="10"></td><td class="yes-no-cell">' . "\n";
			echo '<input type="radio" name="gfo_switch_buttons" value=""';
			if ( 'yes' != $switch_buttons ) {
				echo ' checked="checked"';
			}
			echo '> ' . esc_html( __( 'No', 'guten-free-options' ) );
		echo '</td><td width="10"></td><td>' . "\n";
			echo esc_html( __( 'Add a Switch Editor Buttons to both Editors.', 'guten-free-options' ) );
		echo '</td></tr>' . "\n";

		echo '<tr height="10"><td> </td></tr>' . "\n";

		// --- Prevent Gutenberg Plugin Disable on Update ---
		$prevent_disable = gfo_get_setting( 'prevent_disable', false );
		echo '<tr><td class="label-cell">' . "\n";
			echo '<b>' . esc_html( __( 'Prevent Gutenberg Disable?', 'guten-free-options' ) ) . '</b>' . "\n";
		echo '</td><td width="10"></td><td class="yes-no-cell">' . "\n";
			echo '<input type="radio" name="gfo_prevent_disable" value="yes"';
			if ( 'yes' == $prevent_disable ) {
				echo ' checked="checked"';
			}
			echo '> ' . esc_html( __( 'Yes', 'guten-free-options' ) );
		echo '</td><td width="10"></td><td class="yes-no-cell">' . "\n";
			echo '<input type="radio" name="gfo_prevent_disable" value=""';
			if ( 'yes' != $prevent_disable ) {
				echo ' checked="checked"';
			}
			echo '> ' . esc_html( __( 'No', 'guten-free-options' ) );
		echo '</td><td width="10"></td><td>' . "\n";
			echo esc_html( __( 'Continue using Gutenberg plugin version.', 'guten-free-options' ) );
			echo '<br>' . esc_html( __( '(active plugin on update to WordPress 5+ only)', 'guten-free-options' ) );
		echo '</td></tr>' . "\n";

		// --- Remove Gutenberg Admin Menu? ---
		$remove_menu = gfo_get_setting( 'remove_menu', false );
		echo '<tr><td class="label-cell">' . "\n";
			echo '<b>' . esc_html( __( 'Remove Gutenberg Menu?', 'guten-free-options' ) ) . '</b>' . "\n";
		echo '</td><td width="10"></td><td class="yes-no-cell">' . "\n";
			echo '<input type="radio" name="gfo_remove_menu" value="yes"';
			if ( 'yes' == $remove_menu ) {
				echo ' checked="checked"';
			}
			echo '> ' . esc_html( __( 'Yes', 'guten-free-options') );
		echo '</td><td width="10"></td><td class="yes-no-cell">' . "\n";
			echo '<input type="radio" name="gfo_remove_menu" value=""';
			if ( 'yes' != $remove_menu ) {
				echo ' checked="checked"';
			}
			echo '> ' . esc_html( __( 'No', 'guten-free-options') );
		echo '</td><td width="10"></td><td>' . "\n";
			echo esc_html( __( 'Affects active Gutenberg plugin only.', 'guten-free-options' ) );
		echo '</td></tr>' . "\n";

		// --- Disable Try Gutenberg Nag? ---
		$disable_nag = gfo_get_setting( 'disable_nag', false );
		echo '<tr><td class="label-cell">' . "\n";
			echo '<b>' . esc_html( __( 'Disable Try Gutenberg Panel?', 'guten-free-options' ) ) . '</b>' . "\n";
		echo '</td><td width="10"></td><td class="yes-no-cell">' . "\n";
			echo '<input type="radio" name="gfo_disable_nag" value="yes"';
			if ( 'yes' == $disable_nag ) {
				echo ' checked="checked"';
			}
			echo '> ' . esc_html( __( 'Yes', 'guten-free-options' ) );
		echo '</td><td width="10"></td><td class="yes-no-cell">' . "\n";
			echo '<input type="radio" name="gfo_disable_nag" value=""';
			if ( 'yes' != $disable_nag ) {
				echo ' checked="checked"';
			}
			echo '> ' . esc_html( __( 'No', 'guten-free-options' ) );
			echo '</td><td width="10"></td><td>' . "\n";
				echo esc_html( __( 'Affects pre-5.0 WordPress only.', 'guten-free-options' ) );
			echo '</td></tr>' . "\n";

	echo '</table>' . "\n";

	// ----------------------
	// === USER FILTERING ===
	// ----------------------
	echo '<h3>' . esc_html( __( 'User Filtering', 'guten-free-options' ) ) . '</h3>' . "\n";

	// --- User Role Defaults ---
	// --------------------------
	echo '<h4>' . esc_html( __( 'User Role Defaults', 'guten-free-options' ) ) . '</h4>' . "\n";

	// --- get all user role settings ---
	$role_types = gfo_get_user_roles();
	$classic_roles = gfo_get_setting( 'classic_roles' );
	$block_roles = gfo_get_setting( 'block_roles' );

	// --- merge roles to preserve existing settings ---
	$all_role_types = array();
	if ( is_array( $classic_roles ) ) {
		foreach ( $classic_roles as $key => $label ) {
			$all_role_types[$key] = $label;
		}
	}
	if ( is_array( $block_roles ) ) {
		foreach ( $block_roles as $key => $label ) {
			$all_role_types[$key] = $label;
		}
	}
	foreach ( $role_types as $key => $label ) {
		if ( !array_key_exists( $key, $all_role_types ) ) {
			$all_role_types[$key] = $label;
		}
	}
	// print_r( $all_role_types ); // debug point

	// --- role type table headers ---
	echo '<table><tr><td class="label-cell">' . "\n";
		echo '<b>' . esc_html( __( 'User Role', 'guten-free-options' ) ) . '</b>' . "\n";
	echo '</td><td width="10"></td><td align="center">' . "\n";
		echo '<b>' . esc_html( __( 'Classic', 'guten-free-options' ) ) . '</b>' . "\n";
	echo '</td><td width="5"></td><td align="center">' . "\n";
		echo '<b>' . esc_html( __( 'Inherit', 'guten-free-options' ) ) . '</b>' . "\n";
	echo '</td><td width="5"></td><td align="center">' . "\n";
		echo '<b>' . esc_html( __( 'Block', 'guten-free-options' ) ) . '</b>' . "\n";
	echo '</td></tr>' . "\n";

		// --- loop role types to display options ---
		foreach ( $role_types as $key => $label ) {
			echo '<tr><td class="label-cell">' . "\n";
				echo esc_html( $label );
			echo '</td><td width="10"></td><td align="center">' . "\n";
				echo '<input type="radio" name="gfo_user_role_' . esc_attr( $key ) . '" value="classic"';
				if ( array_key_exists( $key, $classic_roles ) ) {
					echo ' checked="checked"';
				}
				echo '>' . "\n";
			echo '</td><td width="5"></td><td align="center">' . "\n";
				echo '<input type="radio" name="gfo_user_role_' . esc_attr( $key ) . '" value=""';
				if ( !array_key_exists( $key, $classic_roles ) && !array_key_exists( $key, $block_roles ) ) {
					echo ' checked="checked"';
				}
				echo '>' . "\n";
			echo '</td><td width="5"></td><td align="center">' . "\n";
				echo '<input type="radio" name="gfo_user_role_' . esc_attr( $key ) . '" value="block"';
				if ( array_key_exists( $key, $block_roles ) ) {
					echo ' checked="checked"';
				}
				echo '>' . "\n";
			echo '</td></tr>' . "\n";
		}
	echo '</table>' . "\n";

	// --- User Default Editor? ---
	echo '<table>';
		$user_default = gfo_get_setting( 'user_default', false );
		echo '<tr><td class="label-cell" style="vertical-align:top;">' . "\n";
			echo '<b>' . esc_html( __( 'User Editor Selection?', 'guten-free-options' ) ) . '</b>' . "\n";
		echo '</td><td width="10"></td><td class="yes-no-cell" style="vertical-align:top;">' . "\n";
			echo '<input type="radio" name="gfo_user_default" value="yes"';
			if ( 'yes' == $user_default ) {
				echo ' checked="checked"';
			}
			echo '> ' . esc_html( __( 'Yes', 'guten-free-options' ) ) . "\n";
		echo '</td><td width="10"></td><td class="yes-no-cell" style="vertical-align:top;">' . "\n";
			echo '<input type="radio" name="gfo_user_default" value=""';
			if ( 'yes' != $user_default ) {
				echo ' checked="checked"';
			}
			echo '> ' . esc_html( __( 'No', 'guten-free-options' ) ) . "\n";
		echo '</td><td width="10"></td><td>' . "\n";
			echo esc_html( __( 'Adds default editor selection to user profile.', 'guten-free-options' ) ) . '<br>' . "\n";
			echo esc_html( __( '(if disabled, existing user selections are ignored.)', 'guten-free-option' ) );
		echo '</td></tr>' . "\n";
	echo '</table>' . "\n";

	// ---------------------------
	// === POST TYPE FILTERING ===
	// ---------------------------
	echo '<h3>' . esc_html( __( 'Post Type Filtering', 'guten-free-options' ) ) . '</h3>' . "\n";

	// --- Post Type Defaults ---
	// --------------------------
	echo '<h4>' . esc_html( __( 'Post Type Defaults', 'guten-free-options' ) ) . '</h4>' . "\n";

	// 0.9.3: add post type locking explanation
	echo esc_html( __( 'Note: the Lock option allows you to lock the editor to be used for technical reasons.', 'guten-free-options' ) ) . '<br>' . "\n";
	echo esc_html( __( 'If it is ticked for a Post Type, further filtering for that Post Type will be ignored.', 'guten-free-options' ) ) . '<br>' . "\n";
	echo esc_html( __( 'Querystring and metabox overrides will no longer work or display for that post type.', 'guten-free-options' ) ) . '<br>' . "\n";
	echo '(' . esc_html( __( 'Only a manual Admin Override for a specific post of that post type will be respected.', 'guten-free-options' ) ) . ')<br>' . "\n";

	// --- get all post type settings ---
	$post_types = gfo_get_post_types();
	$classic_types = gfo_get_setting( 'classic_types', false );
	$block_types = gfo_get_setting( 'block_types', false );
	// 0.9.3: get post type locking options
	$lock_types = gfo_get_setting( 'lock_types', false );

	// --- merge all post types to preserve existing settings ---
	$all_post_types = array();
	if ( is_array( $classic_types ) ) {
		foreach ( $classic_types as $key => $label ) {
			$all_post_types[$key] = $label;
		}
	}
	if ( is_array( $block_types ) ) {
		foreach ( $block_types as $key => $label ) {
			$all_post_types[$key] = $label;
		}
	}
	foreach ( $post_types as $key => $label ) {
		if ( !array_key_exists( $key, $all_post_types ) ) {
			$all_post_types[$key] = $label;
		}
	}
	// print_r( $all_post_types ); // debug point

	// --- post types table headings ---
	echo '<table><tr><td class="label-cell">' . "\n";
		echo '<b>' . esc_html( __( 'Post Type', 'guten-free-options' ) ) . '</b>' . "\n";
	echo '</td><td width="10"></td><td align="center">' . "\n";
		echo '<b>' . esc_html( __( 'Classic', 'guten-free-options' ) ) . '</b>' . "\n";
	echo '</td><td width="5"></td><td align="center">' . "\n";
		echo '<b>' . esc_html( __( 'Inherit', 'guten-free-options' ) ) . '</b>' . "\n";
	echo '</td><td width="5"></td><td align="center">' . "\n";
		echo '<b>' . esc_html( __( 'Block', 'guten-free-options' ) ) . '</b>' . "\n";
	echo '</td><td width="10"></td><td align="center">' . "\n";
	// 0.9.3: add post type locking column header
		echo '<b>' . esc_html( __( 'Lock?', 'guten-free-options' ) ) . '</b>' . "\n";
	echo '</td></tr>' . "\n";

		// --- loop all post types to display options ---
		foreach ( $all_post_types as $key => $label)  {
			echo '<tr><td>' . "\n";
				echo esc_html( $label );
			echo '</td><td width="10"></td><td align="center">' . "\n";
				echo '<input type="radio" name="gfo_post_type_' . esc_attr( $key ) . '" value="classic"';
				if ( array_key_exists( $key, $classic_types ) ) {
					echo ' checked="checked"';
				}
				echo '>' . "\n";
			echo '</td><td width="5"></td><td align="center">' . "\n";
				echo '<input type="radio" name="gfo_post_type_' . esc_attr( $key ) . '" value=""';
				if ( !array_key_exists( $key, $classic_types ) && !array_key_exists( $key, $block_types ) ) {
					echo ' checked="checked"';
				}
				echo '>' . "\n";
			echo '</td><td width="5"></td><td align="center">' . "\n";
				echo '<input type="radio" name="gfo_post_type_' . esc_attr( $key ) . '" value="block"';
				if ( array_key_exists( $key, $block_types ) ) {
					echo ' checked="checked"';
				}
				echo '>' . "\n";
			echo '</td><td width="10"></td><td align="center">' . "\n";
				// 0.9.3: add post type locking checkboxes
				echo '<input type="checkbox" name="gfo_lock_type_' . esc_attr( $key ) . '" value="yes"';
				if ( is_array( $lock_types ) && in_array( $key, $lock_types ) ) {
					echo ' checked="checked"';
				}
				echo '>' . "\n";
			echo '</td></tr>' . "\n";
		}

	echo '</table>' . "\n";

	// ----------------------------
	// === POST LEVEL FILTERING ===
	// ----------------------------
	echo '<h3>' . esc_html( __( 'Post Level Filtering', 'guten-free-options' ) ) . '</h3>' . "\n";

	echo '<table>';
		// --- Check for Blocks? ---
		$check_blocks = gfo_get_setting( 'check_blocks', false );
		echo '<tr><td class="label-cell">' . "\n";
			echo '<b>' . esc_html( __( 'Check Content for Blocks?', 'guten-free-options' ) ) . '</b>' . "\n";
		echo '</td><td width="10"></td><td class="yes-no-cell">' . "\n";
			echo '<input type="radio" name="gfo_check_blocks" value="yes"';
			if ( 'yes' == $check_blocks ) {
				echo ' checked="checked"';
			}
			echo '> ' . esc_html( __( 'Yes', 'guten-free-options' ) ) . "\n";
		echo '</td><td width="10"></td><td class="yes-no-cell">' . "\n";
			echo '<input type="radio" name="gfo_check_blocks" value=""';
			if ( 'yes' != $check_blocks ) {
				echo ' checked="checked"';
			}
			echo '> ' . esc_html( __( 'No', 'guten-free-options') ) . "\n";
		echo '</td><td width="10"></td><td>' . "\n";
			echo esc_html( __( 'Defaults to Block Editor (Gutenberg) if blocks found.', 'guten-free-options' ) );
		echo '</td></tr>' . "\n";
	echo '</table>' . "\n";

	// --- Post Templates ---
	// ----------------------
	echo '<h4>' . esc_html( __( 'Post Template Defaults', 'guten-free-options' ) ) . '</h4>' . "\n";

	$templates = gfo_get_post_templates();
	$classic_templates = gfo_get_setting( 'classic_templates' );
	$block_templates = gfo_get_setting( 'block_templates' );

	// combine existing settings (to persist beyond current theme)
	$post_templates = array();
	if ( is_array( $classic_templates ) ) {
		foreach ( $classic_templates as $file => $label ) {
			$post_templates[$file] = $label;
		}
	}
	if ( is_array( $block_templates ) ) {
		foreach ( $block_templates as $file => $label ) {
			$post_templates[$file] = $label;
		}
	}
	// --- resort array for easier template type access ---
	foreach ( $templates as $type => $template ) {
		foreach ( $template as $file => $label ) {
			$file = str_replace( '.php', '', $file );
			// if (!is_array($post_template_types[$file])) {$post_template_types[$file] = array();}
			// if (!in_array($type, $post_template_types[$file])) {$post_template_types[$file][] = $type;}
			if ( !isset( $post_templates[$file] ) ) {
				$post_templates[$file] = $label;
			}
		}
	}
	// print_r( $post_templates );

	// --- loop templates and output option display ---
	if ( count( $post_templates ) > 0 ) {

		echo '<table><tr><td class="label-cell">' . "\n";
			echo '<b>' . esc_html( __( 'Post Template', 'guten-free-options' ) ) . '</b>' . "\n";
		echo '</td><td width="10"></td><td align="center">' . "\n";
			echo '<b>' . esc_html( __( 'Classic', 'guten-free-options' ) ) . '</b>' . "\n";
		echo '</td><td width="5"></td><td align="center">' . "\n";
			echo '<b>' . esc_html( __( 'Inherit', 'guten-free-options' ) ) . '</b>' . "\n";
		echo '</td><td width="5"></td><td align="center">' . "\n";
			echo '<b>' . esc_html( __( 'Block', 'guten-free-options' ) ) . '</b>' . "\n";
		echo '</td></tr>' . "\n";

		foreach ( $post_templates as $key => $label ) {
			echo '<tr><td class="label-cell">' . "\n";
				echo esc_html( $label );
			echo '</td><td width="10"></td><td align="center">' . "\n";
				echo '<input type="radio" name="gfo_template_' . esc_attr( $key ) . '" value="classic"';
				if ( array_key_exists( $key, $classic_templates ) ) {
					echo ' checked="checked"';
				}
				echo '>' . "\n";
			echo '</td><td width="10"></td><td align="center">' . "\n";
				echo '<input type="radio" name="gfo_template_' . esc_attr( $key ) . '" value=""';
				if ( !array_key_exists( $key, $classic_templates ) && !array_key_exists( $key, $block_templates ) ) {
					echo ' checked="checked"';
				}
				echo '>' . "\n";
			echo '</td><td width="10"></td><td align="center">' . "\n";
				echo '<input type="radio" name="gfo_template_' . esc_attr( $key ) . '" value="block"';
				if ( array_key_exists( $key, $block_templates ) ) {
					echo ' checked="checked"';
				}
				echo '>';
			echo '</td></tr>' . "\n";
		}
		echo '</table>' . "\n";
	} else {
		echo esc_html( __( 'No Post Templates found in Theme or Settings.', 'guten-free-options' ) ) . '<br><br>' . "\n";
	}

	echo '<table><tr height="20"><td></td>' . "\n";

		// --- Add Editor Metabox? ---
		$editor_metabox = gfo_get_setting( 'editor_metabox', false );
		echo '<tr><td class="label-cell">' . "\n";
			echo '<b>' . esc_html( __( 'Editor Metabox Overrides?', 'guten-free-options' ) ) . '</b>' . "\n";
		echo '</td><td width="10"></td><td class="yes-no-cell">' . "\n";
			echo '<input type="radio" name="gfo_editor_metabox" value="yes"';
			if ( 'yes' == $editor_metabox ) {
				echo ' checked="checked"';
			}
			echo '> ' . esc_html( __( 'Yes', 'guten-free-options' ) ) . "\n";
		echo '</td><td width="10"></td><td class="yes-no-cell">' . "\n";
			echo '<input type="radio" name="gfo_editor_metabox" value=""';
			if ( 'yes' != $editor_metabox ) {
				echo ' checked="checked"';
			}
			echo '> ' . esc_html( __( 'No', 'guten-free-options' ) ) . "\n";
		echo '</td><td width="10"></td><td>' . "\n";
			echo esc_html( __( 'Adds metabox to writing screen for editor overriding.', 'guten-free-options' ) ) . "\n";
		echo '</td></tr>' . "\n";

		// --- No Override Metabox ---
		// $no_override = gfo_get_setting( 'no_override', false );
		// if ( is_array($no_override ) ) {
		//	$no_override = implode( ',', $no_override );
		// }
		// echo '<tr><td class="label-cell" style="vertical-align:top;">';
		//	 	echo '<b>' . esc_html( __( 'No Metabox Post Types', 'guten-free-options' ) ) . '</b>';
		//	echo '</td><td width="10"></td><td colspan="5">';
		//		echo '<input class="post-input" type="text" name="gfo_no_override" value="' . $no_override . '"><br>';
		// 		echo esc_html( __( '(comma-separated list of Post Type slugs)', 'guten-free-options' ) );
		// echo '</td></tr>';

	echo '</table>' . "\n";

	// --- Querystring Overrides ---
	// -----------------------------
	// 0.9.3: added missing querystring level explanation
	echo '<h3>' . esc_html( __( 'Querystrings', 'guten-free-options' ) ) . '</h3>' . "\n";
	echo esc_html( __( 'You can use querystrings to manually override the editor loaded via the browser URL.', 'guten-free-options' ) ) . '<br>' . "\n";
	echo '<table><tr><td>' . "\n";
		echo '/wp-admin/post.php?post=<font color="#E00;">x</font>&action=edit<i>&editor=classic</i>' . "\n";
	echo '</td><td width="20"></td><td>' . "\n";
		echo esc_html( __( 'Override to use Classic Editor', 'guten-free-options' ) ) . "\n";
	echo '</td></tr>' . "\n";
	echo '<tr><td>' . "\n";
		echo '/wp-admin/post.php?post=<font color="#E00;">x</font>&action=edit<i>&editor=block</i>' . "\n";
	echo '</td><td width="20"></td><td>' . "\n";
		echo esc_html( __( 'Override to use Block Editor', 'guten-free-options' ) ) . "\n";
	echo '</td></table><br>' . "\n";

	// --- Admin Post Overrides ---
	// ----------------------------
	$classic_ids = gfo_get_setting( 'classic_ids', false );
	if ( is_array( $classic_ids ) ) {
		$classic_ids = implode( ', ', $classic_ids );
	}
	$block_ids = gfo_get_setting( 'block_ids', false );
	if ( is_array( $block_ids ) ) {
		$block_ids = implode( ', ', $block_ids );
	}

	echo '<h3>' . esc_html( __( 'Admin Overrides', 'guten-free-options' ) ) . '</h3>' . "\n";
	echo esc_html( __( 'Enter Post IDs or Slugs (comma-separated) and the Editor used will be forced on for those posts.', 'guten-free-options' ) ) . '<br>' . "\n";
	echo esc_html( __( 'Note: Both querystring and metabox overrides will no longer be checked or used for these posts.', 'guten-free-options' ) ) . '<br>' . "\n";
	echo '<table>' . "\n";
		echo '<tr><td class="label-cell">' . "\n";
			echo '<b>' . esc_html( __( 'Use Classic Editor', 'guten-free-options' ) ) . '</b>' . "\n";
		echo '</td><td width="10"></td><td>' . "\n";
			echo '<input type="text" class="post-input" name="gfo_classic_ids" value="' . esc_attr( $classic_ids ) . '">' . "\n";
		echo '</td></tr>' . "\n";
		echo '<tr><td class="label-cell">' . "\n";
			echo '<b>' . esc_html( __( 'Use Block Editor', 'guten-free-options' ) ) . '</b>' . "\n";
		echo '</td><td width="10"></td><td>' . "\n";
			echo '<input type="text" class="post-input" name="gfo_block_ids" value="' . esc_attr( $block_ids ) . '">' . "\n";
		echo '</td></tr>' . "\n";
	echo '</table>' . "\n";

	// --- check and warn for overlapping overrides ---
	if ( is_array( $classic_ids ) && is_array( $block_ids ) ) {
		$common = array_intersect( $classic_ids, $block_ids );
		if ( is_array( $common ) && ( count( $common ) > 0 ) ) {
			echo esc_html( __( 'Warning! There is an override overlap. Overrides are cancelled for these settings:', 'guten-free-options' ) ) . "\n";
			echo '<br>' . implode( ', ', $common ) . '<br>' . "\n";
		}
	}

	// --- debug switch and submit buttons ---
	echo '<table><tr height="30"><td> </td></tr>' . "\n";

		// --- debug mode switch ---
		$debug = gfo_get_setting( 'debug', false );
		echo '<tr><td class="label-cell">' . "\n";
			echo '<b>' . esc_html( __( 'Debug Mode?', 'guten-free-options' ) ) . '</b>' . "\n";
		echo '</td><td width="10"></td><td class="yes-no-cell">' . "\n";
			echo '<input type="radio" name="gfo_debug" value="yes"';
			if ( 'yes' == $debug ) {
				echo ' checked="checked"';
			}
			echo '> ' . esc_html( __( 'Yes', 'guten-free-options' ) ) . "\n";
		echo '</td><td width="10"></td><td class="yes-no-cell">' . "\n";
			echo '<input type="radio" name="gfo_debug" value=""';
			if ( 'yes' != $debug ) {
				echo ' checked="checked"';
			}
			echo '> ' . esc_html( __( 'No', 'guten-free-options' ) ) . "\n";
		echo '</td><td width="10"></td><td>' . "\n";
			esc_html( __( 'Logs editor condition changes to debug.log', 'guten-free-options' ) ) . "\n";
		echo '</td></tr>' . "\n";

		echo '<tr height="20"><td> </td></tr>' . "\n";

		// --- reset and submit buttons ---
		echo '<tr><td align="center">' . "\n";
			echo '<input type="button" class="button-secondary" onclick="return gfo_reset_settings();" value="' . esc_attr( __( 'Reset Settings', 'guten-free-options' ) ) . '">' . "\n";
		echo '</td><td width="10"></td><td align="center" colspan="5">' . "\n";
			echo '<input type="submit" class="button-primary" value="' . esc_attr( __( 'Update Settings', 'guten-free-options' ) ) . '">' . "\n";
		echo '</td></tr>' . "\n";

	echo '</table>' . "\n";

	// --- close settings form ---
	echo '</form><br><br><br>' . "\n";

	// --- reset settings form script ---
	// 0.9.4: added reset settings confirmation message
	$confirm_message = __( 'Are you sure you want to reset the plugin settings?', 'guten-free-options' );
	echo "<script>function gfo_reset_settings() {
		var agree = confirm('" . esc_js( $confirm_message ) . "');
		if (!agree) {return false;}
		document.getElementById('gfo-update-action').value = 'reset';
		document.getElementById('gfo-settings-form').submit();
	}</script>";

}

// -------------------------------
// Multisite Network Settings Page
// -------------------------------
function gfo_network_settings_page() {

	// --- plugin page network header ---
	gfo_plugin_page_header( true );

	// network default selection form
	echo '<form id="gfo-network-settings-form" method="post">' . "\n";
	echo '<input type="hidden" name="page" value="guten-free-network-options">' . "\n";
	echo '<input type="hidden" name="gfo_update_settings" value="network">' . "\n";
	wp_nonce_field( 'guten-free-options' );

	// --- table heading ---
	echo '<table><tr><td>';
		echo '<b>' . esc_html( __( 'Network Default Editor', 'guten-free-options' ) ) . '</b>' . "\n";
	echo '</td><td width="10"></td>' . "\n";

	// --- get current default editor for network ---
	$default_editor = get_site_option( 'network_default_editor', '' );

	// --- classic editor ---
	$classic_image = plugins_url( 'images/classicberger.png', GUTEN_FREE_FILE );
	echo '<td align="center"><!-- <img src="' . esc_url( $classic_image ) . '"> --><br>';
	echo "<input type='radio' name='gfo_network_default_editor' value='classic'";
	if ( 'classic' == $default_editor ) {
		echo ' checked="checked"';
	}
	echo '> ' . esc_html( __( 'Classic Editor', 'guten-free-options' ) ) . '<br>' . "\n";
	echo '(' . esc_html( __( 'TinyMCE', 'guten-free-options' ) ) . ')' . "\n";
	echo '</td><td width="10"></td>' . "\n";

	// --- site specific ---
	$default_image = plugins_url( 'images/wordpress.png', GUTEN_FREE_FILE );
	echo '<td align="center"><!-- <img src="' . esc_url( $default_image ) . '"> --><br>';
	echo '<input type="radio" name="gfo_network_default_editor" value=""';
	if ( '' == $default_editor ) {
		echo ' checked="checked"';
	}
	echo '> ' . esc_html( __( 'No Network Default', 'guten-free-options' ) ) . '<br>' . "\n";
	echo '(' . esc_html( __( 'Per Site Only', 'guten-free-options' ) ) . ')' . "\n";
	echo '</td><td width="10"></td>' . "\n";

	// --- gutenberg block editor ---
	$guten_image = plugins_url( 'images/gutenberger.png', __FILE__ );
	echo '<td align="center"><!-- <img src="' . esc_url( $guten_image ) . '"> --><br>' . "\n";
	echo '<input type="radio" name="gfo_network_default_editor" value="block"';
	if ( 'block' == $default_editor ) {
		echo ' checked="checked"';
	}
	echo '> ' . esc_html( __( 'Block Editor', 'guten-free-options' ) ) . '<br>' . "\n";
	echo '(' . esc_html( __( 'Gutenberg', 'guten-free-options' ) ) . ')' . "\n";
	echo '</td></tr>' . "\n";

	// --- network settings note ---
	echo '<tr height="20"><td></td></tr>' . "\n";
	echo '<tr><td colspan="7" align="center">' . "\n";
		echo esc_html( __( 'Further editor defaults can be set from any individual site settings page.', 'guten-free-options' ) ) . "\n";
	echo '</td></tr>' . "\n";

	// --- settings update button ---
	echo '<tr height="20"><td></td></tr>' . "\n";
	echo '<tr><td colspan="2"></td><td colspan="3" align="center">' . "\n";
		echo '<input type="submit" class="button-primary" value="' . esc_attr( __( 'Update Network Settings', 'guten-free-options' ) ) . '">' . "\n";
	echo '</td></tr></table></form><br><br>' . "\n";
}

// ----------------------------
// Add Thickbox for Readme Link
// ----------------------------
add_action( 'admin_enqueue_scripts', 'gfo_add_thickbox' );
function gfo_add_thickbox() {
	if ( isset( $_REQUEST['page'] ) && ( 'guten-free-options' == sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) ) ) {
		add_thickbox();
	}
}

// -------------
// Readme Viewer
// -------------
add_action( 'wp_ajax_gfo_readme_viewer', 'gfo_readme_viewer' );
function gfo_readme_viewer() {
	$readme = GUTEN_FREE_DIR . '/readme.txt';
	$contents = str_replace( "\n", "<br>", file_get_contents( $readme ) );
	echo $contents;
	exit;
}


// =====================
// --- Plugin Loader ---
// =====================

// -----------------
// Guten Free Cooker
// -----------------
// 0.9.5: change init action to after_setup_theme (to access query and rewrite globals)
// add_action('plugins_loaded', 'gfo_cooker', 9);
add_action( 'after_setup_theme', 'gfo_cooker', 9 );
function gfo_cooker() {

	global $gutenfree;

	// 0.9.1: add post type filter after plugins_loaded to allow priority filtering
	$filter_priority = apply_filters( 'gfo_post_type_filter_priority', 11 );
	add_filter( 'gutenberg_can_edit_post_type', 'gfo_can_edit_post_type', $filter_priority, 2 );
	add_filter( 'use_block_editor_for_post_type', 'gfo_can_edit_post_type', $filter_priority, 2 );

	// 0.9.1: add single post filter after plugins_loaded to allow priority filtering
	$filter_priority = apply_filters( 'gfo_single_post_filter_priority', 11 );
	add_filter( 'use_block_editor_for_post', 'gfo_can_edit_post', $filter_priority, 2 );
	add_filter( 'gutenberg_can_edit_post', 'gfo_can_edit_post', $filter_priority, 2 );

	// remove the Classic Editor plugin init action (override it to prevent conflicts!)
	// ...and also remove its Settings link to the settings from the plugins screen
	if ( function_exists('classic_editor_init_actions' ) ) {
		remove_action( 'plugins_loaded', 'classic_editor_init_actions' );
		remove_filter( 'plugin_action_links', 'classic_editor_add_settings_link' );
		remove_action( 'admin_init', 'classic_editor_admin_init' );
	}
	// TODO: check for other Gutenberg disabler plugins
	// as these could conflict, and print admin notice if found ?

	// --- maybe add user default editor selection field ---
	if ( 'yes' == gfo_get_setting( 'user_default' ) ) {
		add_action( 'show_user_profile', 'gfo_user_default_editor_field' );
		add_action( 'edit_user_profile', 'gfo_user_default_editor_field' );
		add_action( 'personal_options_update', 'gfo_user_default_editor_save' );
		add_action( 'edit_user_profile_update', 'gfo_user_default_editor_save' );
	}

	// --- maybe add editor override metabox ---
	if ( 'yes' == gfo_get_setting('editor_metabox') ) {
		add_action( 'admin_init', 'gfo_editor_override_metabox_add' );
		add_action( 'save_post', 'gfo_editor_override_metabox_save' );
	}

	// --- maybe remove the "Try Gutenberg" dashboard widget ---
	// TODO: maybe replace "try Guenberg" panel with plugin-relevant information ?
	if ( 'yes' == gfo_get_setting( 'disable_nag' ) ) {
		if ( has_filter( 'try_gutenberg_panel', 'wp_try_gutenberg_panel' ) ) {
			remove_filter( 'try_gutenberg_panel', 'wp_try_gutenberg_panel' );
		}
	}

	// --- maintain querystrings through redirections ---
	add_filter( 'redirect_post_location', 'gfo_redirect_location' );

	// --- redirect correctly on saving in the classic editor ---
	add_action( 'edit_form_top', 'gfo_remember_when_saving_posts' );

	// --- always maintain classic editor if in querystring ---
	add_filter( 'get_edit_post_link', 'gfo_get_edit_post_link' );

	// --- check if Gutenberg will load ---
	$will_load = gfo_will_gutenberg_load();

	// --- maybe remove gutenberg menu (plugin only) ---
	if ( 'plugin' == $will_load ) {
		$remove_menu = gfo_get_setting( 'remove_menu' );
		// 0.9.1: check for exact value of remove_menu
		if ( ( 'yes' == $remove_menu ) && has_action( 'admin_menu', 'gutenberg_menu' ) ) {
			remove_action( 'admin_menu', 'gutenberg_menu' );
		}
	}

	// 0.9.3: use single action for adding extra post type submenus
	add_action( 'admin_menu', 'gfo_add_editor_submenus' );


	// --- Add Post Type Filters ---
	// -----------------------------

	// 0.9.3: add all gfo_post_type filters on init

	// --- maybe get base network setting for default editor ---
	add_filter( 'gfo_post_type_filters', 'gfo_check_network_default', 10, 3 );

	// --- get base site setting for default editor ---
	add_filter( 'gfo_post_type_filters', 'gfo_check_site_default', 20, 3 );

	// --- check user role (plugin setting) ---
	add_filter( 'gfo_post_type_filters', 'gfo_check_user_role', 30, 3 );

	// --- check user selection (user meta) ---
	add_filter( 'gfo_post_type_filters', 'gfo_check_user_selection', 40, 3 );

	// --- check post type default (plugin setting) ---
	add_filter( 'gfo_post_type_filters', 'gfo_check_post_type', 50, 3 );

	// 0.9.3: add all gfo_single_post_filters on init

	// --- maybe check for Gutenberg blocks (in post content) ---
	add_filter( 'gfo_single_post_filters', 'gfo_check_for_blocks', 20, 3 );

	// --- check page template (plugin settings) ---
	add_filter( 'gfo_single_post_filters', 'gfo_check_template_override', 30, 3 );

	// --- check single post ID overrides (post meta via metabox) ---
	add_filter( 'gfo_single_post_filters', 'gfo_check_post_override', 40, 3 );


	// --- Check Loading ---
	// ---------------------

	// --- check if Gutenberg should load ---
	$check_load = gfo_check_gutenberg_load();

	// --- debug loading states ---
	$debugload = true;
	if ( $debugload ) {
		if ( $check_load ) {
			$debug = "Gutenberg Should Load";
		} else {
			$debug = "Gutenberg Should NOT Load";
		}
		if ( $will_load ) {
			$debug .= " - Gutenberg Will Load";
		} else {
			$debug .= " - Gutenberg Will NOT Load";
		}
		gfo_debug_log( $debug );
	}

	// --- maybe load or unload ---
	if ( !$check_load && $will_load ) {

		// --- unload gutenberg (if possible!) ---
		gfo_force_gutenberg_unload( $will_load );
		$load = false;

	} elseif ( $check_load && !$will_load ) {

		// --- attempt force load of Gutenberg plugin ---
		$load = gfo_force_gutenberg_load();

	}

	// --- gutenberg or block editor is loaded ---
	if ( !isset( $load ) || ( isset( $load ) && $load ) ) {

		// Admin Bar menu links (removed in favour of onscreen buttons)
		// add_action( 'admin_bar_menu', 'gfo_admin_bar_menu', 120 );

		// Classic Editor Row actions (edit.php)
		// removed as calculating for each post listed could slow pageload,
		// and clicking Edit will now go to default editor for post anyway
		// note: 'page' here actually means any hierarchical post type
		// add_filter( 'page_row_actions', 'gfo_add_edit_links', 15, 2 );
		// add_filter( 'post_row_actions', 'gfo_add_edit_links', 15, 2 );

	}

}

// --------------------
// Will Gutenberg Load?
// --------------------
function gfo_will_gutenberg_load() {

	// Gutenberg will load...

	// ...for WordPress version 5.0-beta1 and up
	global $wp_version;
	if ( version_compare( $wp_version, '5.0-beta', '>=' ) ) {
		return 'inbuilt';
	}

	// ...if the Gutenberg plugin is active
	if ( gfo_is_gutenberg_plugin_active() ) {
		return 'plugin';
	}

	// ...if one these filters is present
	if ( has_filter( 'replace_editor', 'gutenberg_init' )
	  || has_filter( 'load-post.php', 'gutenberg_intercept_edit_post' )
	  || has_filter( 'load-post-new.php', 'gutenberg_intercept_post_new' ) ) {
	  	return 'plugin';
	}

	return false;
}

// --------------------
// Check Gutenberg Load
// --------------------
function gfo_check_gutenberg_load( $post_id = null ) {

	// 0.9.4: remove querystring override checks from here (too early)

	// --- load Gutenberg on the front-end to allow blocks to render correctly ---
	if ( !is_admin() ) {

		// by default, load Gutes as non-admin pages may contain blocks
		// note: this will force an inactive Gutenberg plugin to load!
		$load = true;

		// 0.9.4: maybe check if current post has Gutenberg blocks
		// ...using url_to_postid as we are checking very early
		if ( 'yes' == gfo_get_setting( 'check_blocks' ) ) {
			$protocol = 'http';
			if ( isset( $_SERVER['HTTPS'] ) && ( 'off' !== $_SERVER['HTTPS'] ) ) {
				$protocol = 'https';
			}
			if ( 443 == $_SERVER['SERVER_PORT'] ) {
				$protocol = 'https';
			}
			$url = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$post_id = url_to_postid( $url );
			if ( $post_id ) {
				$post = get_post( $post_id );
				$load = gfo_has_blocks( $post->content );
				if ( $load ) {
					gfo_debug_log( 'Preload detected Post ID ' . $post_id . ' has Blocks.' );
				}
			}
		}

		// 0.9.4: standardized filter args to match below
		$load = apply_filters( 'gfo_load_gutenberg', $load, $post_id );
		return $load;
	}

	// --- set null load to start with ---
	$load = null;

	// --- maybe get base multisite network setting for default editor ---
	$load = gfo_check_network_default( $load );

	// --- get base site setting for default editor ---
	$load = gfo_check_site_default( $load );

    // --- only load on admin edit screens anyway ---
	if ( !gfo_is_editor_admin_url() ) {
		$load = false;
	} else {

		// --- set default load if not already set ---
		if ( is_null( $load ) ) {
			$load = true;
		}

		// --- check user default editor (roles / user selection) ---
		// 0.9.3: moved up to match settings hierarchy
		global $current_user;
		$current_user = wp_get_current_user();
		$load = gfo_check_user_role( $load, null, $current_user );
		$load = gfo_check_user_selection( $load, null, $current_user );

		// --- if possible, check post type and post ID (plugin settings) ---
		if ( is_null( $post_id ) && isset( $_GET['post'] ) ) {
			$post_id = absint( $_GET['post'] );
		}
		if ( !is_null( $post_id ) && is_numeric( $post_id ) && ( $post_id > 0 ) ) {

			// note: these will only work on admin editor pages
			// where there is a post ID available to be checked
			// ...otherwise need to rely on later filtering anyway
			$post_type = gfo_get_post_type( $post_id );

			// 0.9.3: fix to missing variable sign
			$load = gfo_can_edit_post_type( $load, $post_type );
			$load = gfo_can_edit_post( $load, $post_id );

		} else {
			// 0.9.3: check using post type default (for post-new.php)
			$load = gfo_can_edit_post_type( $load, 'post' );
		}
	}

	// ---allow for further filtering for this post combo ---
	$load = apply_filters( 'gfo_load_gutenberg', $load, $post_id );

	return $load;
}

// -------------------------
// Get Post Type for Post ID
// -------------------------
// (with additional checks for admin edit screen and querystring)
function gfo_get_post_type( $post_id ) {
	$post_type = false;
	if ( 0 === (int)$post_id ) {
		// use querystring or set to post for post-new.php if not specified and on that page
		if ( isset( $_GET['post_type'] ) ) {
			$post_type = sanitize_text_field( wp_unslash( $_GET['post_type'] ) );
		} elseif ( gfo_is_gutenberg_admin_url( array( 'post-new.php' ) ) ) {
			$post_type = 'post';
		}
	} else {
		$post_type = get_post_type( $post_id );
	}

	return $post_type;
}


// =========================
// --- Post Type Filters ---
// =========================

// ----------------------------
// Check Can Edit For Post Type
// ----------------------------
function gfo_can_edit_post_type( $can_edit, $post_type ) {

	global $gutenfree;
	$edit = $can_edit;

	// 0.9.3: also check locked post types to ignore querystrings for
	$locked = false;
	$lock_types = gfo_get_setting( 'lock_types' );
	if ( is_array( $lock_types ) && in_array( $post_type, $lock_types ) ) {
		$locked = true;
	}
	$gutenfree['locked'] = $locked;

	// --- check for editor querystrings (manual user override) ---
	// 0.9.3: ignore querystring overrides for locked post types
	if ( !$locked ) {
		if ( isset( $_GET['editor'] ) ) {
			if ( 'block' == sanitize_text_field( wp_unslash( $_GET['editor'] ) ) ) {
				$gutenfree['last'] = 'querystring';
				return true;
			} elseif ( 'classic' == sanitize_text_field( wp_unslash( $_GET['editor'] ) ) ) {
				$gutenfree['last'] = 'querystring';
				return false;
			}
		} elseif ( isset( $_GET['classic-editor'] ) ) {
			$gutenfree['last'] = 'querystring';
			return false;
		}
	}

	// --- maybe prevent duplicate (re)filtering ---
	if ( isset( $gutenfree[$post_type . '_filtered'] ) ) {
		return $gutenfree[$post_type . '_filtered'];
	}

	// 0.9.6: maybe force use of block editor for wp_block post types ?
	if ( $post_type == 'wp_block') {
		$gutenfree['wpblock'] = true;
		return true;
	}

	// 0.9.3: add post type filters on init (to prevent adding duplicate filters)

	// --- apply all above default editor default filters ---
	global $current_user;
	$current_user = wp_get_current_user();
	$can_edit = apply_filters( 'gfo_post_type_filters', $can_edit, $post_type, $current_user );

	if ( $can_edit != $edit ) {
		if ( $can_edit ) {
			gfo_debug_log( 'Overall Post Type Filtering -> Block' );
		} else {
			gfo_debug_log( 'Overall Post Type Filtering -> Classic' );
		}
	}

	// --- store post type value ---
	$gutenfree[$post_type . '_filtered'] = $can_edit;

	return $can_edit;
}

// ---------------------
// Check Network Default
// ---------------------
// maybe get base network setting for default editor
function gfo_check_network_default( $can_edit, $post_type = null, $user = null ) {
	if ( !is_multisite() ) {
		return $can_edit;
	}

	global $gutenfree;
	// 0.9.3: add missing check for if plugin is still multisite activated
	$plugins = get_site_option( 'active_sitewide_plugins' );
	$plugin = plugin_basename( GUTEN_FREE_FILE );
	if ( array_key_exists( $plugin, $plugins ) ) {
		$editor = get_site_option( 'network_default_editor' );
		if ( $can_edit && ( 'classic' == $editor ) ) {
			$can_edit = false;
			$gutenfree['last'] = 'network';
			// gfo_debug_log( 'Multisite Network Default -> Classic' );
		} elseif ( !$can_edit && ( 'block' == $editor ) ) {
			$can_edit = true;
			$gutenfree['last'] = 'network';
			// gfo_debug_log( 'Multisite Network Default -> Block' );
		}
	}

	return $can_edit;
}

// ------------------
// Check Site Default
// ------------------
// get base site setting for default editor
function gfo_check_site_default( $can_edit, $post_type = null, $user = null ) {
	$editor = gfo_get_setting( 'default_editor' );
	if ( $can_edit && ( 'classic' == $editor ) ) {
		$can_edit = false;
		$gutenfree['last'] = 'site';
		if ( is_multisite() ) {
			gfo_debug_log( 'Site Default -> Classic' );
		}
	} elseif ( !$can_edit && ( 'block' == $editor ) ) {
		$can_edit = true;
		$gutenfree['last'] = 'site';
		if ( is_multisite() ) {
			gfo_debug_log( 'Site Default -> Block' );
		}
	}

	return $can_edit; // (inherit)
}

// ---------------
// Check User Role
// ---------------
// check user role for default editor (plugin settings)
function gfo_check_user_role( $can_edit, $post_type = null, $user ) {
	global $gutenfree;
	// 0.9.4: improved user object checking
	if ( is_null( $user ) || !is_object( $user ) || ( 0 === $user->ID ) ) {
		return $can_edit;
	}
	if ( !property_exists( $user, 'roles' ) ) {
		return $can_edit;
	}

	$roles = $user->roles;
	$classic_role = $block_role = false;
	if ( $roles && is_array( $roles ) ) {
		$classic_roles = gfo_get_setting( 'classic_roles' );
		if ( $classic_roles && is_array( $classic_roles ) ) {
			$common = array_intersect( $roles, array_keys( $classic_roles ) );
			if ( is_array( $common ) && ( count( $common ) > 0 ) ) {
				$classic_role = true;
				$classic_common = implode( ',', $common );
			}
		}
		$block_roles = gfo_get_setting( 'block_roles' );
		if ( $block_roles && is_array( $block_roles ) ) {
			$common = array_intersect( $roles, array_keys( $block_roles ) );
			if ( is_array( $common ) && ( count( $common) > 0 ) ) {
				$block_role = true;
				$block_common = implode( ',', $common );
			}
		}
	}

	// --- only change if no conflicting role settings ---
	if ( $classic_role && !$block_role ) {
		$can_edit = false;
		$gutenfree['last'] = 'role';
		gfo_debug_log( 'User Role (' . $classic_common . ') -> Classic' );
	} elseif ($block_role && !$classic_role) {
		$can_edit = true;
		$gutenfree['last'] = 'role';
		gfo_debug_log( 'User Role (' . $block_common . ') -> Block' );
	}

	return $can_edit;
}

// ------------------
// Check User Default
// ------------------
// check possible user default editor selection (user meta)
function gfo_check_user_selection( $can_edit, $post_type = null, $user ) {
	global $gutenfree;
	// 0.9.4: added improved user object checking
	// 0.9.6: merge separate property_exists check
	if ( is_null( $user ) || !is_object( $user ) || !property_exists( $user, 'ID' ) || ( 0 === $user->ID ) ) {
		return $can_edit;
	}

	if ( 'yes' == gfo_get_setting( 'user_default' ) ) {
		$editor = get_user_meta( $user->ID, '_default_editor', true );
		if ( $editor && ( '' != $editor ) ) {
			if ( !$can_edit && ( 'block' == $editor ) ) {
				$can_edit = true;
				$gutenfree['last'] = 'user';
				gfo_debug_log( 'User Profile Setting -> Block' );
			} elseif ( $can_edit ) {
				// note: probably 'classic' but could be another editor value
				$can_edit = false;
				$gutenfree['last'] = 'user';
				gfo_debug_log( 'User Profile Setting -> Classic' );
			}
		}
	}

	return $can_edit;
}

// -----------------------
// Check Post Type Default
// -----------------------
function gfo_check_post_type( $can_edit, $post_type, $user = null ) {
	global $gutenfree;
	if ( $can_edit ) {
		$classic_types = gfo_get_setting( 'classic_types' );
		if ( is_array( $classic_types ) && array_key_exists( $post_type, $classic_types ) ) {
			$can_edit = false;
			$gutenfree['last'] = 'type';
			gfo_debug_log( 'Post Type ' . $post_type . ' -> Classic' );
		}
	} else {
		$block_types = gfo_get_setting( 'block_types' );
		if ( is_array( $block_types ) && array_key_exists( $post_type, $block_types ) ) {
			$can_edit = true;
			$gutenfree['last'] = 'type';
			gfo_debug_log( 'Post Type ' . $post_type . ' -> Block' );
		}
	}

	return $can_edit;
}


// ===========================
// --- Single Post Filters ---
// ===========================

// --------------------------
// Check Can Edit for Post ID
// --------------------------
function gfo_can_edit_post( $can_edit, $post ) {

	global $gutenfree;

	// --- get post as object ---
	if ( !is_object( $post ) ) {
		$post = get_post( $post );
	}
	if ( !is_object( $post ) ) {
		return $can_edit;
	}
	$post_id = $post->ID;

	// 0.9.6: force bug out for reuseable wp_block post types
	if ( 'wp_block' == $post->post_type ) {
		return true;
	}

	// --- check full admin-specified override (plugin setting) ---
	$override = gfo_check_admin_override( $post_id );
	if ( !is_null( $override ) ) {
		return $override;
	}

	// 0.9.3: also check locked post types to ignore querystrings for
	$locked = false;
	$lock_types = gfo_get_setting( 'lock_types' ); 
	$post_type = get_post_type( $post );
	if ( is_array( $lock_types ) && in_array( $post_type, $lock_types ) ) {
		$locked = true;
	}
	// if locked already, bug out here as no need to process further filters
	if ( $locked ) {
		return $can_edit;
	}

	// --- check for editor querystrings (manual user override) ---
	if ( isset( $_GET['editor'] ) ) {
		if ( 'block' == sanitize_text_field( wp_unslash( $_GET['editor'] ) ) ) {
			$gutenfree['last'] = 'querystring';
			return true;
		} elseif ( 'classic' == sanitize_text_field( wp_unslash( $_GET['editor'] ) ) ) {
			$gutenfree['last'] = 'querystring';
			return false;
		}
	} elseif ( isset( $_GET['classic-editor'] ) ) {
		$gutenfree['last'] = 'querystring';
		return false;
	}

	// --- maybe prevent duplicate (re)filtering ---
	if ( isset( $gutenfree[$post_id . '_filtered'] ) ) {
		return $gutenfree[$post_id . '_filtered'];
	}

	// 0.9.3: add all single post filters on init (to prevent duplicate filters)

	// apply all the single post filters now
	global $current_user;
	$current_user = wp_get_current_user();
	$can_edit = apply_filters( 'gfo_single_post_filters', $can_edit, $post_id, $current_user );

	// --- store filtered value for post ID ---
	$gutenfree[$post_id . '_filtered'] = $can_edit;

	return $can_edit;
}

// --------------------------
// Check for Gutenberg Blocks
// --------------------------
function gfo_check_for_blocks( $can_edit, $post_id, $user ) {
	global $gutenfree;
	if ( $can_edit ) {
		return $can_edit;
	}
	$check_blocks = gfo_get_setting( 'check_blocks' );
	$content = gfo_get_post_content_only( $post_id );
	// 0.9.2: bug out if could not get content
	if ( !$content ) {
		return $can_edit;
	}
	// 0.9.6: fix to incorrect variable ($post->content)
	$hasblocks = gfo_has_blocks( $content );
	if ( $hasblocks ) {
		// also set global flag to indicate current post has blocks
		$gutenfree['hasblocks'] = $can_edit = true;
		$gutenfree['last'] = 'blocks';
		gfo_debug_log( 'Post ' . $post_id . ' Content Blocks Found -> Block' );
	} elseif ( isset( $gutenfree['hasblocks'] ) ) {
		unset( $gutenfree['hasblocks'] );
	}

	return $can_edit;
}

// -----------------------------
// Check Post Template Overrides
// -----------------------------
// note: since WP 4.7 any post type can have a page template
function gfo_check_template_override( $can_edit, $post_id, $user ) {
	global $gutenfree;
	
	// 0.9.6: force bug out for reuseable wp_block post types
	if ( isset( $gutenfree['wpblock'] ) ) {
		return $can_edit;
	}
	
	$template = get_page_template_slug( $post_id );
	if ( !$template ) {
		return $can_edit;
	}
	$template = str_replace( '.php', '', $template );
	if ( $can_edit ) {
		$classic_templates = gfo_get_setting( 'classic_templates' );
		if ( is_array( $classic_templates ) && array_key_exists( $template, $classic_templates ) ) {
			$can_edit = false;
			$gutenfree['last'] = 'template';
			gfo_debug_log( 'Post ' . $post_id . ' Template ' . $template . ' -> Classic' );
		}
	} else {
		$block_templates = gfo_get_setting( 'block_templates' );
		if ( is_array( $block_templates ) && array_key_exists( $template, $block_templates ) ) {
			$can_edit = true;
			$gutenfree['last'] = 'template';
			gfo_debug_log( 'Post ' . $post_id . ' Template ' . $template . ' -> Block' );
		}
	}

	return $can_edit;
}

// -----------------------------------
// Check Single Post Metabox Overrides
// -----------------------------------
function gfo_check_post_override( $can_edit, $post_id, $user ) {
	global $gutenfree;

	// 0.9.6: force bug out for reuseable wp_block post types
	if ( isset( $gutenfree['wpblock'] ) ) {
		return $can_edit;
	}

	$override = get_post_meta( $post_id, '_editor_override', true );
	if ( !$override ) {
		return $can_edit;
	}
	if ( 'classic' == $override ) {
		$can_edit = false;
		$gutenfree['last'] = 'meta';
		gfo_debug_log( 'Post ' . $post_id . ' Meta Override -> Classic' );
	} elseif ( 'block' == $override ) {
		$can_edit = true;
		$gutenfree['last'] = 'meta';
		gfo_debug_log( 'Post ' . $post_id . ' Meta Override -> Block' );
	} elseif ( $can_edit ) {
		// assume another editor is using this override
		$can_edit = false;
		$gutenfree['last'] = 'meta';
		gfo_debug_log( 'Post ' . $post_id . ' Meta Other -> Classic' );
	}

	return $can_edit;
}

// ---------------------------------
// Check Single Post Admin Overrides
// ---------------------------------
// check post ID admin override (plugin settings)
function gfo_check_admin_override( $post_id ) {

	global $gutenfree;

	$override = null;
	$classic_ids = gfo_get_setting( 'classic_ids' );
	$block_ids = gfo_get_setting( 'block_ids' );

	// note: always making sure override is not in both override settings (cancels override)
	if ( is_array( $classic_ids ) ) {
		if ( in_array( $post_id, $classic_ids ) ) {
			if ( !is_array( $block_ids ) || !in_array( $post_id, $block_ids ) ) {
				$override = false;
				$gutenfree['last'] = 'override';
				gfo_debug_log( 'Post ID Admin Override ' . $post_id . ' -> Classic' );
			}
		} else {
			$post_name = gfo_get_post_name_only( $post_id );
			if ( in_array( $post_name, $classic_ids ) ) {
				if ( !is_array( $block_ids ) || !in_array( $post_name, $block_ids ) ) {
					$override = false;
					gfo_debug_log( 'Post Slug Admin Override ' . $post_name . ' (' . $post_id . ') -> Classic' );
				}
			}
		}
	} elseif ( is_array( $block_ids ) ) {
		if ( in_array( $post_id, $block_ids ) ) {
			if ( !is_array( $classic_ids ) || !in_array( $post_id, $classic_ids ) ) {
				$override = true; 
				$gutenfree['last'] = 'override';
				gfo_debug_log( 'Post Admin Override ' . $post_id . ' -> Block' );
			}
		} else {
			$post_name = gfo_get_post_name_only( $post_id );
			if ( in_array( $post_name, $block_ids ) ) {
				if ( !is_array( $classic_ids ) || !in_array( $post_name, $classic_ids ) ) {
					$override = true;
					$gutenfree['last'] = 'override';
					gfo_debug_log( 'Post Slug Admin Override ' . $post_name . ' (' . $post_id . ') -> Block' );
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
	do_action( 'gfo_before_gutenberg_load' );

	$gutenberg_filepath = WP_PLUGIN_DIR . '/gutenberg/gutenberg.php';
	$gutenberg_filepath = apply_filters( 'gutenberg_plugin_load_path', $gutenberg_filepath );
	
	// 0.9.6: removed extraneous validate_file call
	// if ( 0 !== validate_file( $gutenberg_filepath ) ) {return false;}

	if ( file_exists( $gutenberg_filepath ) ) {
		// 0.9.1: set gutenberg plugin loaded flag
		$gutenfree['gutenberg_plugin'] = true;
		// 0.9.4: fix to missing $ on variable
		include_once $gutenberg_filepath;
		do_action( 'gfo_after_gutenberg_load' );
		return true;
	}

	return false;
}

// ------------------
// Gutenberg Unloader
// ------------------
function gfo_force_gutenberg_unload( $load_type ) {

	// --- set global flag ---
	global $gutenfree;
	$guten_free['gutenberg'] = 'unload';


	if ( 'plugin' == $load_type ) {

		// --- remove the Classic Editor row action links ---
		remove_action( 'admin_init', 'gutenberg_add_edit_link_filters' );

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

	} elseif ( 'inbuilt' == $load_type ) {

		// --- remove block scripts and style resources ---
		remove_action( 'wp_enqueue_scripts', 'wp_common_block_scripts_and_styles' );
		remove_action( 'admin_enqueue_scripts', 'wp_common_block_scripts_and_styles' );
		// remove_action( 'enqueue_block_assets', 'wp_enqueue_registered_block_scripts_and_styles' );

		add_action( 'wp_enqueue_scripts', 'gfo_dequeue_block_styles', 11 );

	}
}

// --------------------
// Dequeue Block styles
// --------------------
// 0.9.6: added separate function to dequeue block styles
function gfo_dequeue_block_styles() {

	// 0.9.6: dequeue block library styles
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	
	// 0.9.6: remove WooCommerce block styles and scripts
	if ( class_exists( 'woocommerce' ) ) {
		wp_dequeue_style( 'wc-block-editor' );
		wp_dequeue_style( 'wc-block-style' );
		wp_dequeue_style( 'wc-blocks-style' );
		wp_dequeue_style( 'wc-all-blocks-style' );
		wp_dequeue_style( 'wc-blocks-vendors-style' );
	}
}

// ====================
// --- User Options ---
// ====================

// ------------------------------
// Add User Default Editor Option
// ------------------------------
// 0.9.3: added missing translation wrappers
function gfo_user_default_editor_field( $user ) {

	$value = get_user_meta( $user->ID, '_default_editor', true );
	$options = array(
		'' 			=> __( 'Use Site/Role Default', 'guten-free-options' ),
		'classic'	=> __( 'Classic Editor (TinyMCE)', 'guten-free-options' ),
		'block'		=> __( 'Block Editor (Gutenberg)', 'guten-free-options' ),
	);
	$options = apply_filters( 'gfo_default_editor_options', $options );
	if ( !$value || !array_key_exists( $value, $options ) ) {
		$value = '';
	}

	// echo '<h3>'.__('Default Editor').'</h3>';
    echo '<table class="form-table">' . "\n";
	echo '<tr>' . "\n";
		echo '<th><label for="default_editor">' . esc_html( __( 'Default Editor', 'guten-free-options' ) ) . '</label></th>' . "\n";
			echo '<td>' . "\n";
				foreach ( $options as $option => $label ) {
					echo '<input type="radio" name="default_editor" class="default_editor" value="' . esc_attr( $option ) . '"';
						if ( $value == $option ) {
							echo ' checked="checked"';
						}
					echo '> ' . esc_html( $label ) . '<br>' . "\n";
				}
				echo '<span class="description">' . "\n";
					echo esc_html( __( 'Sets your default editor for Writing.', 'guten-free-options' ) ) . "\n";
				echo '</span>' . "\n";
			echo '</td>' . "\n";
    	echo '</tr>' . "\n";
    echo '</table>' . "\n";
}

// ------------------------
// Save User Default Editor
// ------------------------
function gfo_user_default_editor_save( $user_id ) {

    if ( !current_user_can( 'edit_user', $user_id ) ) {
    	return;
    }
	$options = array(
		'' 			=> __( 'Use Site/Role Default', 'guten-free-options' ),
		'classic'	=> __( 'Classic Editor (TinyMCE)', 'guten-free-options' ),
		'block'		=> __( 'Block Editor (Gutenberg)', 'guten-free-options' ),
	);
	$options = apply_filters( 'gfo_default_editor_options', $options );
	$value = $_POST['default_editor'];
	if ( !array_key_exists( $value, $options ) ) {
		$value = '';
	}

    if ( '' == $value ) {
    	delete_user_meta( $user_id, '_default_editor' );
    } elseif ( array_key_exists( $value, $options ) ) {
    	update_user_meta($user_id, '_default_editor', $value);
    }
}

// ---------------------------
// Add Editor Override Metabox
// ---------------------------
function gfo_editor_override_metabox_add() {

	// --- do not add metabox on non-editor screens ---
	if ( !gfo_is_editor_admin_url() ) {
		return;
	}

	// --- do not add metabox for posts with an existing admin override (plugin setting) ---
	if ( isset( $_GET['post'] ) ) {
		$post_id = absint( $_GET['post'] );
		if ( $post_id > 0 ) {
			$classic_ids = gfo_get_setting( 'classic_ids' );
			$block_ids = gfo_get_setting( 'block_ids' );
			if ( !is_array( $classic_ids ) ) {
				$classic_ids = array();
			}
			if ( !is_array( $block_ids ) ) {
				$block_ids = array();
			}
			if ( in_array( $post_id, $classic_ids ) || in_array( $post_id, $block_ids ) ) {
				// ...also make sure the post ID is not in *both* arrays (cancelling override)
				if ( !in_array( $post_id, $classic_ids ) || !in_array( $post_id, $block_ids ) ) {
					return;
				}
			}
			$post_type = get_post_type( $post_id );
		}
	}

	if ( !isset( $post_type ) && isset( $_REQUEST['post_type'] ) ) {
		// 0.9.6: add sanitize_text_field to request variable
		$post_type = sanitize_text_field( wp_unslash( $_REQUEST['post_type'] ) );
	}
	// 0.9.3: set default for post type to post (for post-new.php)
	if ( !isset( $post_type ) ) {
		$post_type = 'post';
	}

	// 0.9.3: check post type locks
	$locked = false;
	$lock_types = gfo_get_setting( 'lock_types' );
	if ( is_array( $lock_types ) && in_array( $post_type, $lock_types ) ) {
		$locked = true;
	}

	// --- filter post type for metabox screens ---
	// 0.9.3: simplify to boolean true or false filter
	$no_override = apply_filters( 'gfo_no_override_post_types', $locked, $post_type );
	if ( $no_override ) {
		return;
	}

	// --- finally, add the editor override meta box ---
	// 0.9.6: add missing translation wrapper
	add_meta_box( 'editor-override', __( 'Editor Override', 'guten-free-options' ), 'gfo_editor_override_metabox', $post_type, 'side', 'high');
}

// -----------------------
// Editor Override Metabox
// -----------------------
// 0.9.3: added missing translation wrappers
function gfo_editor_override_metabox() {

	global $post;
	$override = get_post_meta( $post->ID, '_editor_override', true );
	// if ( 'classic' == $override {
	//	$debug = 'Metabox Override -> Classic';
	// } elseif ( 'block' == $override ) {
	//	$debug = 'Metabox Override -> Block';
	// }
	// gfo_debug_log($debug);

	$options = array(
		'' 			=> __( 'Do Not Override','guten-free-options' ),
		'classic'	=> __( 'Classic Editor (TinyMCE)','guten-free-options' ),
		'block'		=> __( 'Block Editor (Gutenberg)','guten-free-options' ),
	);
	$options = apply_filters( 'gfo_editor_override_options', $options );
	if ( !$override || !is_array( $options ) || !array_key_exists( $override, $options ) ) {
		$override = '';
	}

	echo '<table id="editor-override-table"><tr>';

		// 0.9.6: removed unused and undefined onchange variable
		// 0.9.7: adding missing escaping on editor label
		echo '<td><span class="editor-override-label">' . esc_html( __( 'Editor','guten-free-options' ) ) . '</span></td>';
		echo '<td><select name="editor_override" id="editor_override">';
		foreach ( $options as $option => $label ) {
			echo '<option value="' . esc_attr( $option ) . '"';
				if ( $override == $option ) {
					echo ' selected="selected"';
				}
			echo '>' . esc_html( $label ) . '</option>';
		}
		echo '</select></td>';

		// --- set a help icon with title for the setting ---
		$posttypeobject = get_post_type_object( get_post_type($post->ID ) );
		$title = __( 'Set the Editor for this', 'guten-free-options' ) . ' ' . $posttypeobject->labels->singular_name;
		echo '<td><span id="editor-override-help" class="dashicons dashicons-editor-help" title="' . esc_attr( $title ) . '"></span></td>';

	echo '</tr></table>';

	// --- editor override metabox styles ---
	$styles = "#editor_override {font-size:12px;} .editor-override-label {font-size:10px; font-weight:bold;}
	#editor-override-help {color:#008ec2;} #editor-override-help:hover {color:##0085ba;}";
	$styles = apply_filters( 'gfo_editor_override_metabox_styles', $styles );
	if ( '' != $styles ) {
		// 0.9.6: use wp_kses_post on filtered styles
		echo '<style>' . wp_kses_post( $styles ) . '</style>';
	}

	// --- the next part is for block editor pages only ---
	if ( !gfo_is_block_editor_page() ) {
		return;
	}

	// --- metabox save refresh trigger for block editor to classic editor ---
	// (the super super crazy fix.)
	$confirm_message = __( 'Editor Override Saved. Refresh the page to Classic Editor now?', 'guten-free-options' );
	$norefresh_message = __( 'Okay. It is recommended you refresh the page or remove the editor override.', 'guten-free-options' );

	// 0.9.6: removed script filter for direct output
	// $script = apply_filters( 'gfo_editor_override_metabox_script', $script );
	echo "<script>var classic_reload_check = false; var publish_button_click = false;
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
							if (confirm('" . esc_js( $confirm_message ) . "')) {
								currenthref = window.location.href.replace('&classic-editor', '');
								if (currenthref.indexOf('&editor=block') > -1) {
									window.location.href = currenthref.replace('&editor=block', '&editor=classic');
								} else {window.location.href = currenthref+'&editor=classic';}
							} else {alert('" . esc_js( $norefresh_message ) . "');}
						}
					}, 1000);
				});
			}
		}, 500);
	});</script>" . "\n";

}

// -----------------------------
// Save Override Metabox Options
// -----------------------------
function gfo_editor_override_metabox_save( $post_id ) {

	if ( !current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['editor_override'] ) ) {
		$override = sanitize_text_field( wp_unslash( $_POST['editor_override'] ) );
		$options = array(
			'' 			=> __( 'Do Not Override', 'guten-free-options' ),
			'classic'	=> __( 'Classic Editor (TinyMCE)', 'guten-free-options' ),
			'block'		=> __( 'Block Editor (Gutenberg)', 'guten-free-options' ),
		);
		$options = apply_filters( 'gfo_editor_override_options', $options );

		if ( '' == $override ) {
			delete_post_meta( $post_id, '_editor_override' );
		} elseif ( array_key_exists( $override, $options ) ) {
			update_post_meta( $post_id, '_editor_override', $override );

			// --- maybe remove querystring override upon saving ---
			// 0.9.6: add sanitize_text_field to request variables
			if ( ( ( 'block' == $override ) && isset( $_REQUEST['classic-editor'] ) )
			  || ( ( 'block' == $override ) && isset( $_REQUEST['editor'] ) && ( 'classic' == sanitize_text_field( wp_unslash( $_REQUEST['editor'] ) ) ) )
			  || ( ( 'classic' == $override ) && isset( $_REQUEST['editor']) && ( 'block' == sanitize_text_field( wp_unslash( $_REQUEST['editor'] ) ) ) ) ) {
			  	add_action( 'save_post', 'gfo_editor_redirect', 999 );
			}
		}
	}
}

// -----------------------------------
// Redirect Editor without Querystring
// -----------------------------------
function gfo_editor_redirect() {
	add_filter( 'redirect_post_location', 'gfo_editor_redirect_location' );
	// 0.9.6: add sanitize_text_field to request variable
	redirect_post( sanitize_text_field( wp_unslash( $_REQUEST['post_ID'] ) ) );
	exit;
}

// ----------------------------
// Set Editor Redirect Location
// ----------------------------
function gfo_editor_redirect_location( $location ) {
	$location = remove_query_arg( 'editor', $location );
	$location = remove_query_arg( 'classic-editor', $location );
	// 0.9.3: debug log location value and return redirect
	gfo_debug_log( 'Redirect Post Location: ' . $location );
	return $location;
}


// ====================
// --- Redirections ---
// ====================

// --------------------------------------
// Remember when Saving in Classic Editor
// --------------------------------------
function gfo_remember_when_saving_posts() {
	// 0.9.4: only add this when querystring override is actually present
	// 0.9.6: add sanitize_text_field to request variable
	if ( isset( $_REQUEST['classic-editor'] )
	  || ( isset( $_REQUEST['editor'] ) && ( 'classic' == sanitize_text_field( wp_unslash( $_REQUEST['editor'] ) ) ) ) ) {
		echo '<input type="hidden" name="editor" value="classic">';
	}
}

// --------------------------------
// Classic Editor Arg for Revisions
// --------------------------------
function gfo_get_edit_post_link( $url ) {
	if ( isset( $_REQUEST['classic-editor'] ) ) {
		$url = add_query_arg( 'classic-editor', '', $url );
	} elseif ( isset( $_REQUEST['editor'] ) ) {
		// 0.9.6: add sanitize_text_field to request variable
		$url = add_query_arg( 'editor', sanitize_text_field( wp_unslash( $_REQUEST['editor'] ) ), $url );
	}
	return $url;
}

// --------------------------------------
// Maintain Querystring through Redirects
// --------------------------------------
function gfo_redirect_location( $location ) {
	if ( isset( $_POST['_wp_http_referer'] ) ) {
		// 0.9.6: add sanitize_text_field to posted referer
		$referer = sanitize_text_field( wp_unslash( $_POST['_wp_http_referer'] ) );
		if ( false !== strpos( $referer, '&classic-editor' ) ) {
			$location = add_query_arg( 'classic-editor', '', $location );
		}
		if ( false !== strpos( $referer, '&editor=classic' ) ) {
			$location = add_query_arg( 'editor', 'classic', $location );
		}
		// not sure if will ever really be needed... but let us add the other way around just in case!
		if ( false !== strpos( $referer, '&editor=block' ) ) {
			$location = add_query_arg( 'editor', 'block', $location );
		}
	}
	return $location;
}


// ===============
// --- Helpers ---
// ===============

// ---------------------------
// Check for Blocks in Content
// ---------------------------
function gfo_has_blocks( $content ) {
	// 0.9.6: use internally maintained block checks
	if ( function_exists( 'has_blocks' ) ) {
		return has_blocks( $content );
	} elseif ( function_exists( 'gutenberg_content_has_blocks' ) ) {
		return gutenberg_content_has_blocks( $content );
	} else {
		return strstr( $content, '<!-- wp:' );
	}
}

// --------------------------
// Get Post ID from Post Slug
// --------------------------
function gfo_get_post_id_only( $slug ) {
	// 0.9.6: remove quotes from %s (added by prepare method)
	global $wpdb;
	$query = "SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_name = %s";
	$query = $wpdb->prepare( $query, $slug );
	$result = $wpdb->get_var( $query );
	return $result;
}

// --------------------------
// Get Post Slug from Post ID
// --------------------------
function gfo_get_post_name_only( $post_id ) {
	global $wpdb;
	$query = "SELECT post_name FROM " . $wpdb->prefix . "posts WHERE ID = '%d'";
	$query = $wpdb->prepare( $query, $post_id );
	$result = $wpdb->get_var( $query );
	return $result;
}

// ---------------------
// Get Post Content Only
// ---------------------
function gfo_get_post_content_only( $post_id = false ) {
	// 0.9.2: bug out if not a valid integer
	if ( !$post_id || !is_integer( $post_id ) ) {
		return false;
	}
	global $wpdb;
	// 0.9.6: remove unnecessary quotes around integer value
	$query = "SELECT post_content FROM " . $wpdb->prefix . "posts WHERE ID = %d";
	// 0.9.2: fix to use post_id not id
	$query = $wpdb->prepare( $query, $post_id );
	$result = $wpdb->get_var( $query );
	return $result;
}

// -----------------------------------
// Check if Gutenberg Plugin is Active
// -----------------------------------
function gfo_is_gutenberg_plugin_active() {
	// 0.9.1: store active state for faster rechecking
	global $gutenfree;
	$active = false;
	if ( isset( $gutenfree['gutenberg_plugin'] ) && $gutenfree['gutenberg_plugin'] ) {
		return true;
	}
	$active_plugins = (array) get_option( 'active_plugins' );
	if ( in_array( 'gutenberg/gutenberg.php', $active_plugins ) ) {
		$active = true;
	}
	if ( is_multisite() ) {
		$sitewide_plugins = (array)get_site_option( 'active_sitewide_plugins' );
		if ( array_key_exists( 'gutenberg/gutenberg.php', $sitewide_plugins ) ) {
			$active = true;
		}
	}
	// 0.9.3: fix to set active value
	$gutenfree['gutenberg_plugin'] = $active;
	return $active;
}

// -----------------------------
// Check for Editor Admin Screen
// -----------------------------
function gfo_is_editor_admin_url( $supported_filenames = array( 'post.php', 'post-new.php' ) ) {

	$path = trim( sanitize_text_field( wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) ) );
	$wp_admin_slug = trim( wp_parse_url( get_admin_url(), PHP_URL_PATH ), '/' );

	foreach ( $supported_filenames as $filename ) {
		// require $filename not to be empty to avoid accidents like matching against a plain `/wp-admin/`
		if ( !empty( $filename ) && ( '/' . $wp_admin_slug . '/' . $filename === $path ) ) {
			return true;
		}
	}
	return false;
}

// ---------------------------
// Check for Block Editor Page
// ---------------------------
function gfo_is_block_editor_page() {
	global $current_screen;
	$current_screen = get_current_screen();
	// 0.9.1: added check to help prevent Gutenberg plugin crashing without is_block_editor method
	if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
		return true;
	} elseif ( gfo_is_gutenberg_plugin_active() ) {
		// --- do similar to is_gutenberg_page function here ---
		if ( !is_admin() ) {
			return false;
		}
		if ( 'post' !== $current_screen->base ) {
			return false;
		}
		if ( !gfo_is_editor_admin_url() ) {
			return false;
		}

		global $post;
		$can_edit = apply_filters( 'gutenberg_can_edit_post_type', true, $post->post_type );
		$can_edit = apply_filters( 'gutenberg_can_edit_post', $can_edit, $post );
		return $can_edit;
	}
	return false;
}

// ----------------------------
// Check if Widgets have Blocks
// ----------------------------
function gto_sidebars_have_widget_blocks() {
	global $wp_registered_widgets;
	$sidebars_widgets = get_option( 'sidebars_widgets' );
	if ( $sidebars_widgets && is_array( $sidebars_widgets ) && ( count( $sidebars_widgets ) > 0 ) ) {
		foreach ( $sidebars_widgets as $sidebar ) {
			if ( is_array( $sidebar ) && ( count( $sidebar ) > 0 ) ) {
				foreach ( $sidebar as $widget ) {
					if ( isset( $wp_registered_widgets[$widget] ) ) {
						$object = $wp_registered_widgets[$widget]['callback'][0];
						if ( is_object( $object) && is_a( $object, 'WP_Widget_Block' ) ) {
							return true;
						}
					}
				}
			}
		}
	}
	return false;
}

// --------------
// Get User Roles
// --------------
function gfo_get_user_roles() {
	$roles = get_editable_roles();
	$role_types = array();
	foreach ( $roles as $key => $value ) {
		if ( isset( $value['name'] ) ) {
			$label = $value['name'];
		} else {
			$label = 'Unknown (' . $key . ')';
		}
		$role_types[$key] = $label;
	}
	$role_types = apply_filters( 'gfo_get_user_roles', $role_types );
	return $role_types;
}

// --------------
// Get Post Types
// --------------
function gfo_get_post_types() {
	$post_types = get_post_types(array(), 'objects' );
	$inbuilt_post_types = array(
		'attachment', 'revision', 'nav_menu_item', 'custom_css',
		'customize_changeset', 'oembed_cache', 'user_request', 'wp_block'
	);
	$inbuilt = apply_filters( 'gfo_inbuilt_post_types', $inbuilt_post_types );
	// 0.9.2: if not an array revert to default inbuilt post types
	// 0.9.6: fix to incorrect variable name (inbuild)
	if ( !is_array( $inbuilt ) ) {
		$inbuilt = $inbuild_post_types;
	}
	$types = array();
	foreach( $post_types as $key => $post_type ) {
		if ( !in_array( $key, $inbuilt ) ) {
			$types[$key] = $post_type->label;
		}
		// TODO: check if/why next line is necessary? (I think it is not)
		// if ( !post_type_supports( $post_type->name, 'custom-fields' ) ) {unset( $types[$key] );}
	}
	$types = apply_filters( 'gfo_get_post_types', $types );
	return $types;
}

// ------------------
// Get Post Templates
// ------------------
function gfo_get_post_templates() {
	$theme = wp_get_theme();
	// note: use get_post_templates not get_page_templates
	$templates = $theme->get_post_templates();
	$templates = apply_filters( 'gfo_get_post_templates', $templates );
	return $templates;
}

// -------------
// Debug Logging
// -------------
function gfo_debug_log( $logline ) {
	global $gutenfree;
	if ( isset( $gutenfree['debug'] ) && $gutenfree['debug'] ) {
		if ( !isset( $gutenfree['debugpath'] ) ) {
			if ( defined( 'GUTEN_FREE_DEBUG_PATH' ) ) {
				$gutenfree['debugpath'] = GUTEN_FREE_DEBUG_PATH;
			} else {
				$debugpath = dirname( __FILE__ ) . '/debug.log';
				$gutenfree['debugpath'] = apply_filters( 'gfo_debug_log_path', $debugpath );
			}
		}
		error_log( $logline . PHP_EOL, 3, $gutenfree['debugpath'] );
	}
}

// Secret Bonus Video :-) https://www.youtube.com/watch?v=SAUN4HPjtLY

