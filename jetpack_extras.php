<?php

/*
 * Plugin Name: Jetpack Extras by BarryCarlyon
 * Plugin URI: http://barrycarlyon.co.uk/wordpress/category/wordpress/jetpack/
 * Description: Extends WordPress.com's JetPack to include Additional Features
 * Author: Barry Carlyon
 * Version: 1.6.1.1
 * Author URI: http://barrycarlyon.co.uk/wordpress/
 * License: GPL2+
 * Text Domain: jetpack
 */

$plugin_name = plugin_basename(__FILE__);
$plugin_dir_path = plugin_dir_path( __FILE__ );
$plugin_dir_url = plugin_dir_url( __FILE__ );

define( 'JETPACK_META_BASENAME', $plugin_name );
define( 'JETPACK_EXTRAS_PLUGIN_DIR_URL', $plugin_dir_url );

add_action( 'init', 'jetpack_extras_init', 20 );
//add_action( 'plugins_loaded', 'jetpack_extras_plugins_loaded' );

/**
Load extra sharing sources
*/
function jetpack_extras_init() {
	if (class_exists('Sharing_Source')) {
		add_filter('plugin_action_links' , 'jetpack_extras_action_link', 10, 2);

		include( $plugin_dir_path . 'modules/sharedaddy/sharing-sources.php' );

		require_once( $plugin_dir_path . 'modules/sharedaddy/sharing-display.php' );

		remove_filter( 'the_content', 'sharing_display', 19 );
		remove_filter( 'the_excerpt', 'sharing_display', 19 );

		add_filter( 'the_content', 'sharing_display_extra', 19 );
		add_filter( 'the_excerpt', 'sharing_display_extra', 19 );
	} else {
		add_action('after_plugin_row_' . JETPACK_META_BASENAME, 'jetpack_extras_after_plugin_row', 10, 3);
	}
}

/**
Admin
*/
function jetpack_extras_admin_init() {
	if ( class_exists( 'Sharing_Admin' ) ) {
		add_action( 'sharing_global_options', 'jetpack_extras_sharing_global_options' );
		add_action( 'sharing_admin_update', 'jetpack_extras_sharing_admin_update' );
	}
}
add_action( 'admin_init', 'jetpack_extras_admin_init');

/**
Nag
*/
function jetpack_extras_after_plugin_row($plugin_file, $plugin_data, $plugin_status) {
	if ( $plugin_file != JETPACK_META_BASENAME )
		return;
	echo '<tr class="plugin-update-tr"><td colspan="3" class="plugin-update colspanchange"><div class="update-message">' . __('JetPack Extras Requires, <a href="http://wordpress.org/extend/jetpack/">JetPack</a> to be installed and the Sharing Service Module to be Enabled', 'jetpack') . '</div></td></tr>';
	return;
}
function jetpack_extras_action_link($links, $file){
	if ($file == JETPACK_META_BASENAME)
		array_unshift($links, '<a href="' . admin_url('options-general.php?page=sharing') . '" title="Settings">' . __('Settings', 'jetpack') . '</a>');
	return $links;
}

/**
Functions
*/

function jetpack_extras_plugins_loaded() {
	add_filter( 'sharing_services', 'jetpack_extras_sharing_services' );
}

function jetpack_extras_sharing_services($services) {
	$services['twitter_extra'] = 'Share_Twitter_JetPack_Extras';
	return $services;
}

function jetpack_extras_sharing_global_options() {
	$global  = get_option( 'jetpack_extras-options' );
	$shows = array_values( get_post_types( array( 'public' => true ) ) );
	array_unshift( $shows, 'index' );

	foreach ( $shows as $show ) :
		if ( 'index' == $show ) {
			$label = __( 'Front Page, Archive Pages, and Search Results', 'jetpack' );
		} else {
			$post_type_object = get_post_type_object( $show );
			$label = $post_type_object->labels->name;
		}
		?>
		<tr valign="top">
			<th scope="row"><label><?php echo sprintf(__( 'Button Placement (on %s)', 'jetpack' ), $label); ?></label></th>
			<td>
				<select name="placement[<?php echo $show; ?>]">
					<option value="below"<?php if ( $global['placement'][$show] == 'below' ) echo ' selected="selected"';?>><?php _e( 'Below Content', 'jetpack' ); ?></option>
					<option value="above"<?php if ( $global['placement'][$show] == 'above' ) echo ' selected="selected"';?>><?php _e( 'Above Content', 'jetpack' ); ?></option>
					<option value="both"<?php if ( $global['placement'][$show] == 'both' ) echo ' selected="selected"';?>><?php _e( 'Above and Below Content', 'jetpack' ); ?></option>
				</select>
			</td>
		</tr>
	<?php	endforeach;

	return;
}

function jetpack_extras_sharing_admin_update() {
	$options = get_option( 'jetpack_extras-options' );

	$shows = array_values( get_post_types( array( 'public' => true ) ) );
	array_unshift( $shows, 'index' );

	// Placement optoons
	$options['placement'] = array();
	foreach ( $shows as $show ) {
		if ( isset( $_POST['placement'][$show] ) && in_array( $_POST['placement'][$show], array( 'below', 'above', 'both' ) ) )
			$options['placement'][$show] = $_POST['placement'][$show];
		else
			$options['placement'][$show] = 'below';
	}

	update_option( 'jetpack_extras-options', $options );

	return;
}
