<?php

add_filter('jetpack_sharing_twitter_via', 'jetpack_extras_sharing_twitter_via', 10, 2);
function jetpack_extras_sharing_twitter_via($via, $post_id) {
	$global  = get_option( 'jetpack_extras-options', array() );
	$via = $global['twitter_via'];
	// dnt?
//	if ($global['enable_dnt']) {
//		$via .= '&dnt=true';
//	}
	return $via;
}

add_filter('jetpack_sharing_twitter_related', 'jetpack_extras_sharing_twitter_related', 10, 2);
function jetpack_extras_sharing_twitter_related($related, $post_id) {
	$global  = get_option( 'jetpack_extras-options', array() );
	foreach ($global['twitter_related'] as $user => $desc) {
		$related[$user] = $desc;
	}
	return $related;
}

add_filter('sharing_permalink', 'jetpack_extras_sharing_permalink', 10, 3);
function jetpack_extras_sharing_permalink($url, $post_id, $button) {
	if ($button != 'twitter') {
		return $url;
	}
	if (function_exists('wpme_get_shortlink')) {
		$global  = get_option( 'jetpack_extras-options', array() );
		if ($global['use_wpme']) {
			$url = wpme_get_shortlink($post_id);
		}
	}
	return $url;
}

class jetpack_extras_Share_Facebook extends Sharing_Source {
	var $shortname = 'facebook';
	private $share_type = 'default';
	
	public function __construct( $id, array $settings ) {
		parent::__construct( $id, $settings );

		if ( isset( $settings['share_type'] ) )
			$this->share_type = $settings['share_type'];
		
		if ( 'official' == $this->button_style )
			$this->smart = true;
		else
			$this->smart = false;
	}

	public function get_name() {
		return __( 'Facebook', 'jetpack' );
	}
	
	public function display_header() {
	}
	
	function guess_locale_from_lang( $lang ) {
		if ( 'en' == $lang || 'en_US' == $lang || !$lang ) {
			return 'en_US';
		}

		if ( !class_exists( 'GP_Locales' ) ) {
			if ( !defined( 'JETPACK__GLOTPRESS_LOCALES_PATH' ) || !file_exists( JETPACK__GLOTPRESS_LOCALES_PATH ) ) {
				return false;
			}

			require JETPACK__GLOTPRESS_LOCALES_PATH;
		}

		if ( defined( 'IS_WPCOM' ) && IS_WPCOM ) {
			// WP.com: get_locale() returns 'it'
			$locale = GP_Locales::by_slug( $lang );
		} else {
			// Jetpack: get_locale() returns 'it_IT';
			$locale = GP_Locales::by_field( 'wp_locale', $lang );
		}

		if ( !$locale || empty( $locale->facebook_locale ) ) {
			return false;
		}

		return $locale->facebook_locale;
	}

	public function get_display( $post ) {
		$share_url = $this->get_share_url( $post->ID );
		if ( $this->smart ) {
			$url = 'http://www.facebook.com/plugins/like.php?href=' . rawurlencode( $share_url ) . '&amp;layout=button_count&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;height=21';
			
			// Default widths to suit English
			$inner_w = 90;
			
			// Locale-specific widths/overrides
			$widths = array(
				'bg_BG' => 120,
				'de_DE' => 100,
				'da_DK' => 120,
				'es_ES' => 110,
				'es_LA' => 110,
				'fi_FI' => 100,
				'it_IT' => 100,
				'ja_JP' => 100,
				'ru_RU' => 128,
			);

			$widths = apply_filters( 'sharing_facebook_like_widths', $widths );

			$locale = $this->guess_locale_from_lang( get_locale() );
			if ( $locale ) {
				if ( 'en_US' != $locale ) {
					$url .= '&amp;locale=' . $locale;
				}

				if ( isset( $widths[$locale] ) ) {
					$inner_w = $widths[$locale];
				}
			}

			$url .= '&amp;width='.$inner_w;
			return '<div class="like_button"><iframe src="'.$url.'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'.( $inner_w + 6 ).'px; height:21px;" allowTransparency="true"></iframe></div>';
		}
		
		if ( 'icon-text' == $this->button_style || 'text' == $this->button_style )
			sharing_register_post_for_share_counts( $post->ID );
		return $this->get_link( get_permalink( $post->ID ), _x( 'Facebook', 'share to', 'jetpack' ), __( 'Share on Facebook', 'jetpack' ), 'share=facebook', 'sharing-facebook-' . $post->ID );
	}
	
	public function process_request( $post, array $post_data ) {
		$fb_url = 'http://www.facebook.com/sharer.php?u=' . rawurlencode( $this->get_share_url( $post->ID ) ) . '&t=' . rawurlencode( $post->post_title );
		
		// Record stats
		parent::process_request( $post, $post_data );
		
		// Redirect to Facebook
		wp_redirect( $fb_url );
		die();
	}
	
	public function display_footer() {
		$this->js_dialog( $this->shortname );
	}
}
