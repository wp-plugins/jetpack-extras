<?php

add_filter('jetpack_sharing_twitter_via', 'jetpack_extras_sharing_twitter_via', 10, 2);
add_filter('jetpack_sharing_twitter_related', 'jetpack_extras_sharing_twitter_related', 10, 2);

function jetpack_extras_sharing_twitter_via($via, $post_id) {
	$global  = get_option( 'jetpack_extras-options', array() );
	$via = $global['twitter_via'];
	return $via;
}
function jetpack_extras_sharing_twitter_related($related, $post_id) {
	$global  = get_option( 'jetpack_extras-options', array() );
	foreach ($global['twitter_related'] as $item) {
		$related[$global['twitter_related']] = '';
	}
	return $related;
}
