(function ($) {
	$(document).on( 'pumBeforeOpen', function( ) {
		var $popup = PUM.getPopup('#mc_embed_signup');
		if (window.location.href.match(/utm_source=Rivard\+Report/i)) {
			$popup.addClass('preventOpen');
		}
	});
}(jQuery));
