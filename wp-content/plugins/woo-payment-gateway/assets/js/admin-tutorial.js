var $ = jQuery;
$(document).ready(function(){
	var $ = jQuery;
	$(document.body).on('click', '.div--tutorialHeader ul li a', function(e){
		e.preventDefault();
		var id = $(this).attr('tutorial-container');
		$('.braintree-explanation-container').each(function(index){
			$(this).slideUp(400);
		});
		$('#' + id).slideDown(400);
	})
});