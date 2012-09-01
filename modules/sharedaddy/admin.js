jQuery(document).ready(function() {
	jQuery('#jetpack_extras_add_related').click(function(event) {
		event.preventDefault();
		jQuery(this).parent('.jetpack_extras_twitter_related_input').first().clone().val('').appendTo('.jetpack_extras_twitter_related');
	});
});
