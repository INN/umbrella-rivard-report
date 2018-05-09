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
		heightStyle: "content"
	});

	$('.rivard-donation-form').show();

	// for donation page
	if ($('body').hasClass('page-id-184947') || $('body').hasClass('page-id-1975775')){
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

	$('input:radio+label').keypress(function(e){
	    if(e.keyCode === 0 || e.keyCode === 32 || e.keyCode === 13 ){
	        $(this).prev('input').trigger('click');
	        selected = $(this).prev('input').val();
			$('.rivard-donation-form').hide();
			$('.rivard-donation-form.level-' + selected).show();
			$('.donation-frequency.selected input').attr('checked', 'checked');
	    }
	});

});
