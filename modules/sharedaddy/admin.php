<?php

/**
Admin
*/
function jetpack_extras_admin_init() {
	if ( class_exists( 'Sharing_Admin' ) ) {
		add_action( 'sharing_global_options', 'jetpack_extras_sharing_global_options' );
		add_action( 'sharing_admin_update', 'jetpack_extras_sharing_admin_update' );
		add_action( 'admin_enqueue_scripts', 'jetpack_extras_sharing_admin_scripts' );
	}
}
add_action( 'admin_init', 'jetpack_extras_admin_init');

function jetpack_extras_sharing_admin_scripts() {
	wp_enqueue_script( 'jetpack_extras_sharing_admin', JETPACK_EXTRAS_PLUGIN_DIR_URL . 'modules/sharedaddy/admin.js', array('jquery') );
}

/**
Admin Functions
*/

function jetpack_extras_sharing_global_options() {
	// display options
	$global  = get_option( 'jetpack_extras-options', array() );
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
				<select name="jetpack_extras_placement[<?php echo $show; ?>]">
					<option value="below"<?php if ( $global['placement'][$show] == 'below' ) echo ' selected="selected"';?>><?php _e( 'Below Content', 'jetpack' ); ?></option>
					<option value="above"<?php if ( $global['placement'][$show] == 'above' ) echo ' selected="selected"';?>><?php _e( 'Above Content', 'jetpack' ); ?></option>
					<option value="both"<?php if ( $global['placement'][$show] == 'both' ) echo ' selected="selected"';?>><?php _e( 'Above and Below Content', 'jetpack' ); ?></option>
				</select>
			</td>
		</tr>
	<?php	endforeach;

	// twitter options
	?>

	<tr valign="top">
		<td></td>
		<th scope="row"><label><?php _e('Twitter Options'); ?></label></th>
	</tr>
	<tr valign="top">
		<th scope="row"><label>Via Account</label></th>
		<td><input type="text" name="jetpack_extras_twitter_via" value="<?php echo $global['twitter_via']; ?>" /></td>
	</tr>
	<tr valign="top">
		<th scope="row"><label>Related Account(s)</label></th>
		<td><div id="jetpack_extras_twitter_related">
			<?php
				if (count($global['twitter_related'])) {
					foreach ($global['twitter_related'] as $related) {
						echo '<input type="text" class="jetpack_extras_twitter_related_input" name="jetpack_extras_twitter_related[]" value="' . $related . '" style="clear: left;" />';
					}
				} else {
					echo '<input type="text" class="jetpack_extras_twitter_related_input" name="jetpack_extras_twitter_related[]" value="" style="clear: left;" />';
				}
			?>
			</div>
			<a href="#" id="jetpack_extras_add_related">Add Another Related Account</a>
		</td>
	</tr>

	<?php

	return;
}

function jetpack_extras_sharing_admin_update() {
	$options = get_option( 'jetpack_extras-options', array() );

	$shows = array_values( get_post_types( array( 'public' => true ) ) );
	array_unshift( $shows, 'index' );

	// Placement optoons
	$options['placement'] = array();
	foreach ( $shows as $show ) {
		if ( isset( $_POST['jetpack_extras_placement'][$show] ) && in_array( $_POST['jetpack_extras_placement'][$show], array( 'below', 'above', 'both' ) ) )
			$options['placement'][$show] = $_POST['jetpack_extras_placement'][$show];
		else
			$options['placement'][$show] = 'below';
	}
	// twitter
	$options['twitter_via'] = $_POST['jetpack_extras_twitter_via'];
	$options['twitter_related'] = $_POST['jetpack_extras_twitter_related'];

	update_option( 'jetpack_extras-options', $options );

	return;
}
