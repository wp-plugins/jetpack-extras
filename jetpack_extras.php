<?php

/*
 * Plugin Name: Jetpack Extras by BarryCarlyon
 * Plugin URI: http://barrycarlyon.co.uk/wordpress/category/wordpress/jetpack/
 * Description: Bring the power of the WordPress.com cloud to your self-hosted WordPress. Jetpack enables you to connect your blog to a WordPress.com account to use the powerful features normally only available to WordPress.com users.
 * Author: Barry Carlyon
 * Version: 1.5
 * Author URI: http://barrycarlyon.co.uk/wordpress/
 * License: GPL2+
 * Text Domain: jetpack
 */

$plugin_dir_path = plugin_dir_path( __FILE__ );
$plugin_dir_url = plugin_dir_url( __FILE__ );

define( 'JETPACK_EXTRAS_PLUGIN_DIR_URL', $plugin_dir_url );

function jetpack_extras_plugins_loaded() {
	add_filter( 'sharing_services', 'jetpack_extras_sharing_services' );
}
add_action( 'plugins_loaded', 'jetpack_extras_plugins_loaded' );

function jetpack_extras_sharing_services($services) {
	$services['twitter'] = 'Share_Twitter_Extended';
	$services['pinterest'] = 'Share_Pinterest';
	return $services;
}

/**
CSS/JS
*/
function jetpack_extras_wp_enqueue_scripts() {
	wp_enqueue_style( 'jetpack_extras_sharing', JETPACK_EXTRAS_PLUGIN_DIR_URL . 'modules/sharedaddy/sharing.css');
}
add_action( 'wp_enqueue_scripts', 'jetpack_extras_wp_enqueue_scripts' );

function jetpack_extras_admin_enqueue_scripts() {
	wp_enqueue_style( 'jetpack_extras_sharing', JETPACK_EXTRAS_PLUGIN_DIR_URL . 'modules/sharedaddy/admin-sharing.css');
}
add_action( 'admin_enqueue_scripts', 'jetpack_extras_admin_enqueue_scripts' );

/**
Load extra sharing sources
*/
function jetpack_extras_init() {
	include( $plugin_dir_path . 'modules/sharedaddy/sharing-sources.php' );

	require_once( $plugin_dir_path . 'modules/sharedaddy/sharing-display.php' );

	remove_filter( 'the_content', 'sharing_display', 19 );
	remove_filter( 'the_excerpt', 'sharing_display', 19 );

	add_filter( 'the_content', 'sharing_display_extra', 19 );
	add_filter( 'the_excerpt', 'sharing_display_extra', 19 );
}
add_action( 'init', 'jetpack_extras_init', 20 );

/**
Admin
*/
function jetpack_extras_admin_init() {
	if ( class_exists( 'Sharing_Admin') ) {
		add_action( 'sharing_global_options', 'jetpack_extras_sharing_global_options' );
	}
}
add_action( 'admin_init', 'jetpack_extras_admin_init', 20 );

function jetpack_extras_sharing_global_options() {
	$sharer  = new Sharing_Service();

	$global  = $sharer->get_global_options();
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

add_action( 'sharing_admin_update', 'jetpack_extras_sharing_admin_update' );
function jetpack_extras_sharing_admin_update() {
	$options = get_option( 'sharing-options' );

	$shows = array_values( get_post_types( array( 'public' => true ) ) );
	array_unshift( $shows, 'index' );

	// Placement optoons
	$options['global']['placement'] = array();
	foreach ( $shows as $show ) {
		if ( isset( $_POST['placement'][$show] ) && in_array( $_POST['placement'][$show], array( 'below', 'above', 'both' ) ) )
			$options['global']['placement'][$show] = $_POST['placement'][$show];
		else
			$options['global']['placement'][$show] = 'below';
	}

	update_option( 'sharing-options', $options );

	return;
}
