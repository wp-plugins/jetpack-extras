jQuery(document).ready(function() {
	jQuery('#jetpack_extras_add_related').click(function(event) {
		event.preventDefault();
		jQuery('.jetpack_extras_twitter_related_input').first().clone().find('input').val('').appendTo('#jetpack_extras_twitter_related');
	});
});
