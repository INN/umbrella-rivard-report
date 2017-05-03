jQuery(document).ready(function($) {
	$('#tabs').tabs();

	// hover states on the static widgets
	$('#dialog_link, ul#icons li').hover(
	function() { $(this).addClass('ui-state-hover'); },
	function() { $(this).removeClass('ui-state-hover'); }
	);

	$('.accordion').accordion({
		collasible: true,
		active: false,
		collapsible: true,
		header: 'h4',
		heightStyle: "content",
	});

	// for donation page
	if ($('body').hasClass('page-id-184947')){
		$('.rivard-donation-form').hide();
		var selected = $('input[name="membership-type"]:checked').val();
		console.log(selected);

		$('.rivard-donation-form.level-' + selected).show();

		var $input = $('input[name="membership-type"]');
		$input.on('click', function(e) {
			selected = $('input[name="membership-type"]:checked').val();
			$('.rivard-donation-form').hide();
			$('.rivard-donation-form.level-' + selected).show();
		});
	}

});
