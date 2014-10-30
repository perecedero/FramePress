jQuery(document).ready(function($){

	if(!$('#framepress-errors').length) {
		$('body').append('<div id="framepress-errors"></div>');
	}
	$('#framepress-errors').append($('.error-list .error-item').detach());

	$('.error-item').click(function(){
		$(this).find('.panel').toggle();
	});
});
