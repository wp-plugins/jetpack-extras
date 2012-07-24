<?php

function sharing_display_extra( $text = '' ) {
	global $post, $wp_current_filter;

	if ( is_preview() ) {
		return $text;
	}

	if ( in_array( 'get_the_excerpt', (array) $wp_current_filter ) ) {
		return $text;
	}
	
	$sharer = new Sharing_Service();
	$global = $sharer->get_global_options();

	$show = false;
	if ( !is_feed() ) {
		if ( is_singular() && in_array( get_post_type(), $global['show'] ) ) {
			$show = true;
		} elseif ( in_array( 'index', $global['show'] ) && ( is_home() || is_archive() || is_search() ) ) {
			$show = true;
		}
	}

	// Pass through a filter for final say so
	$show = apply_filters( 'sharing_show', $show, $post );
	
	// Disabled for this post?
	$switched_status = get_post_meta( $post->ID, 'sharing_disabled', false );

	if ( !empty( $switched_status ) )
		$show = false;

	$sharing_content = '';
	
	if ( $show ) {
		$enabled = $sharer->get_blog_services();

		if ( count( $enabled['all'] ) > 0 ) {
			global $post;
			
			$dir = get_option( 'text_direction' );

			// Wrapper
			$sharing_content .= '<div class="snap_nopreview sharing robots-nocontent">';
			$sharing_content .= '<ul>';
			
			// Visible items
			$visible = '';
			foreach ( $enabled['visible'] AS $id => $service ) {
				// Individual HTML for sharing service
				$visible .= '<li class="share-'.$service->get_class().' share-regular">';
				$visible .= $service->get_display( $post );
				$visible .= '</li>';
			}

			$parts = array();
			
			if ( FALSE === $global['sharing_label'] ) {
				$parts[] = '<li class="sharing_label">' . __( 'Share this:', 'jetpack' ) . '</li>';
			} elseif ( '' != $global['sharing_label'] ) {
				$parts[] = '<li class="sharing_label">' . esc_html( $global['sharing_label'] ) . '</li>';
			}

			$parts[] = $visible;
			if ( count( $enabled['hidden'] ) > 0 )
				$parts[] = '<li class="share-custom"><a href="#" class="sharing-anchor">'._x( 'Share', 'dropdown button', 'jetpack' ).'</a></li>';

			if ( $dir == 'rtl' )
				$parts = array_reverse( $parts );

			$sharing_content .= implode( '', $parts );			
			$sharing_content .= '<li class="share-end"></li></ul>';
			
			if ( count( $enabled['hidden'] ) > 0 ) {
				$sharing_content .= '<div class="sharing-hidden"><div class="inner" style="display: none;';

				if ( count( $enabled['hidden'] ) == 1 )
					$sharing_content .= 'width:150px;';
								
				$sharing_content .= '">';
				
				if ( count( $enabled['hidden'] ) == 1 )
					$sharing_content .= '<ul style="background-image:none;">';
				else
					$sharing_content .= '<ul>';
	
				$count = 1;
				foreach ( $enabled['hidden'] AS $id => $service ) {
					// Individual HTML for sharing service
					$sharing_content .= '<li class="share-'.$service->get_class().'">';
					$sharing_content .= $service->get_display( $post );
					$sharing_content .= '</li>';
					
					if ( ( $count % 2 ) == 0 )
						$sharing_content .= '<li class="share-end"></li>';

					$count ++;
				}
				
				// End of wrapper
				$sharing_content .= '<li class="share-end"></li></ul></div></div>';
			}

			$sharing_content .= '<div class="sharing-clear"></div></div>';
			
			// Register our JS
			wp_register_script( 'sharing-js', WP_SHARING_PLUGIN_URL .'sharing.js', array( 'jquery' ), '0.1' );
			add_action( 'wp_footer', 'sharing_add_footer' );
		}
	}
	
	$option = '';
	if ( is_single() ) {
		$option = isset($global['placement'][get_post_type()]) ? $global['placement'][get_post_type()] : 'below';
	} else {
		$option = $global['placement']['index'];
	}

	switch($option) {
		case 'above':
			return $sharing_content.$text;
		case 'both':
			return $sharing_content.$text.$sharing_content;
		case 'below':
		default:
			return $text.$sharing_content;
	}
}
