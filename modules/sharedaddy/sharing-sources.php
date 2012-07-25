<?php


class Share_Pinterest_JetPack_Extras extends Sharing_Source {
	public function get_name() {
		return __( 'Pinterest', 'jetpack' );
	}

	public function get_display( $post ) {
		return '<div class="pinterest_button"><a href="http://pinterest.com/pin/create/button/?url=' . rawurlencode( apply_filters( 'sharing_permalink', get_permalink( $post->ID ), $post->ID, $this->id ) ) . '&media=' . rawurlencode( wp_get_attachment_url( get_post_thumbnail_id($post->ID) ) ) . '&description=' . rawurlencode( get_the_title( $post->ID ) ) . '" class="pin-it-button" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a></div>';
	}
	
	public function display_preview() {
?>
	<div class="option option-smart-on"></div>
<?php
	}
		
	public function display_footer() {
?>
	<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
<?php
	}
}

class Share_Twitter_JetPack_Extras extends Sharing_Advanced_Source {
	public $smart = true;
	private $dnt = false;
	private $via = '';
	private $related = '';
	private $use_wpme = false;
	
	public function __construct( $id, array $settings ) {
		parent::__construct( $id, $settings );

		if ( isset( $settings['smart'] ) )
			$this->smart = $settings['smart'];
		if ( isset( $settings['dnt'] ) )
			$this->dnt = $settings['dnt'];
		if ( isset( $settings['via'] ) )
			$this->via = $settings['via'];
		if ( isset( $settings['related'] ) )
			$this->related = $settings['related'];
		if ( isset( $settings['use_wpme'] ) )
			$this->use_wpme = $settings['use_wpme'];
	}
	
	public function get_name() {
		return __( 'Twitter', 'jetpack' );
	}

	public function get_display( $post ) {
		$share_url = get_permalink( $post->ID );
		if ($this->use_wpme && function_exists( 'wpme_get_shortlink' ) )
			$share_url = wpme_get_shortlink( $post->ID );

		if ( $this->smart == 'smart' ) {
			$twitter_url = '';
			if ( $this->dnt )
				$twitter_url .= 'dnt=true&amp;';
			if ( $this->via )
				$twitter_url .= 'via=' . $this->via . '&amp;';
			if ( $this->related )
				$twitter_url .= 'related=' . $this->related . '&amp;';

			return '<div class="twitter_button"><iframe allowtransparency="true" frameborder="0" scrolling="no" src="https://platform.twitter.com/widgets/tweet_button.html?url=' . rawurlencode( apply_filters( 'sharing_permalink', $share_url, $post->ID, $this->id ) ) . '&amp;counturl=' . rawurlencode( str_replace( 'https://', 'http://', get_permalink( $post->ID ) ) ) . '&amp;count=horizontal&amp;' . $twitter_url . 'text=' . rawurlencode( apply_filters( 'sharing_post_title', $post->post_title, $post->ID, $this->id ) ) . ': " style="width:97px; height:20px;"></iframe></div>';
		} else {
			return $this->get_link( $share_url, _x( 'Twitter', 'share to', 'jetpack' ), __( 'Click to share on Twitter', 'jetpack' ), 'share=twitter' );
		}
	}	
	
	public function process_request( $post, array $post_data ) {
		$post_title = apply_filters( 'sharing_post_title', $post->post_title, $post->ID, $this->id );
		
		$share_url = get_permalink( $post->ID );
		if ($this->use_wpme && function_exists( 'wpme_get_shortlink' ) )
			$share_url = wpme_get_shortlink( $post->ID );

		$post_link = apply_filters( 'sharing_permalink', $share_url, $post->ID, $this->id );

		$twitter_url = '';	
		if ( function_exists( 'mb_stripos' ) )
			$mb = true;
		else
			$mb = false;

		$twitter_url = 'https://twitter.com/share?';
		if ( $this->dnt )
			$twitter_url .= 'dnt=true&';
		if ( $this->via )
			$twitter_url .= 'via=' . $this->via . '&';
		if ( $this->related )
			$twitter_url .= 'related=' . $this->related . '&';
		
		if ( ( $mb && ( mb_strlen( $post_title ) + 1 + mb_strlen( $post_link ) ) > 140 ) || ( !$mb && ( strlen( $post_title ) + 1 + strlen( $post_link ) ) > 140 ) ) {
			if ( $mb )
				$twitter_url .= 'text=' . rawurlencode( ( mb_substr( $post_title, 0, (140 - mb_strlen ( $post_link ) - 4 ) ) ) . '... ' . $post_link );		
			else
				$twitter_url .= 'text=' . rawurlencode( ( substr( $post_title, 0, (140 - strlen ( $post_link ) - 4 ) ) ) . '... ' . $post_link );		
		} else {
			$twitter_url .= 'text=' . rawurlencode( $post_title . ' ' . $post_link );
		}

		// Record stats
		parent::process_request( $post, $post_data );
		
		// Redirect to Twitter
		wp_redirect( $twitter_url );
		die();
	}
	
	public function has_custom_button_style() {
		return $this->smart;
	}

	public function display_preview() {
?>
	<div class="option option-smart-<?php echo $this->smart ? 'on' : 'off'; ?>">
		<?php
			if ( !$this->smart ) {
				if ( $this->button_style == 'text' || $this->button_style == 'icon-text' )
					echo $this->get_name();
				else
					echo '&nbsp;';
			}
		?>
	</div>
<?php
	}
	
	public function update_options( array $data ) {
		$this->smart = false;
		$this->dnt = false;
		$this->via = '';
		$this->related = '';
		$this->use_wpme = false;

		if ( isset( $data['smart'] ) )
			$this->smart = true;
		if ( isset( $data['dnt'] ) )
			$this->dnt = true;
		if ( isset( $data['via'] ) )
			$this->via = $data['via'];
		if ( isset( $data['related'] ) )
			$this->related = $data['related'];
		if ( isset( $data['use_wpme'] ) )
			$this->use_wpme = true;
	}

	public function get_options() {
		return array(
			'smart' 	=> $this->smart,
			'dnt'		=> $this->dnt,
			'via'		=> $this->via,
			'related'	=> $this->related,
			'use_wpme'	=> $this->use_wpme,
		);
	}

	public function display_options() {
?>
	<div class="input">
		<label>
			<input name="smart" type="checkbox"<?php if ( $this->smart ) echo ' checked="checked"'; ?>/>
			
			<?php _e( 'Use smart button', 'jetpack' ); ?>
		</label>
	</div>
	<div class="input">
		<label>
			<input name="dnt" type="checkbox"<?php if ( $this->dnt ) echo ' checked="checked"'; ?>/>
			
			<?php _e( 'Enable DNT', 'jetpack' ); ?>
		</label>
	</div>

	<?php
		if ( function_exists( 'wpme_get_shortlink' ) ) :
	?>
	<div class="input">
		<label>
			<input name="use_wpme" type="checkbox"<?php if ( $this->use_wpme ) echo ' checked="checked"'; ?>/>
			
			<?php _e( 'Share WP.me URL Instead', 'jetpack' ); ?>
		</label>
	</div>
	<?php
		endif;
	?>

	<div class="input">
		<label>
			<input name="via" type="type" value="<?php echo $this->via; ?>"/>
			
			<?php _e( 'Via User', 'jetpack' ); ?>
		</label>
	</div>
	<div class="input">
		<label>
			<input name="related" type="type" value="<?php echo $this->related; ?>"/>
			
			<?php _e( 'Related User', 'jetpack' ); ?>
		</label>
	</div>
	<input type="submit" value="Save" />

<?php
	}
}
