<?php

add_filter('jetpack_sharing_twitter_via', 'jetpack_extras_sharing_twitter_via', 10, 2);
function jetpack_extras_sharing_twitter_via($via, $post_id) {
	$global  = get_option( 'jetpack_extras-options', array() );
	$via = $global['twitter_via'];
	return $via;
}

add_filter('jetpack_sharing_twitter_related', 'jetpack_extras_sharing_twitter_related', 10, 2);
function jetpack_extras_sharing_twitter_related($related, $post_id) {
	$global  = get_option( 'jetpack_extras-options', array() );
	foreach ($global['twitter_related'] as $item => $desc) {
		$related[$item] = $desc;
	}
	return $related;
}

add_filter('sharing_permalink', 'jetpack_extras_sharing_permalink', 10, 3);
function jetpack_extras_sharing_permalink($url, $post_id, $button) {
	if (function_exists('wpme_get_shortlink')) {
		$global  = get_option( 'jetpack_extras-options', array() );
		if ($global['use_wpme']) {
			$url = wpme_get_shortlink($post_id);
		}
	}
	return $url;
}
