jQuery(document).ready(function($){
	$('#gaSiteMetaData').DataTable();
	$('#startDate').datepicker({
		dateFormat: 'mm/dd/yy',
		changeMonth: true,
		changeYear: true,
		onSelect: function(date){
			var selectedDate = new Date(date);
			$('#endDate').datepicker({
				dateFormat: 'mm/dd/yy',
				changeMonth: true,
				changeYear: true,
				minDate: date
			});
		}
	});
	if(getUrlParameter('endDate')){
		var selectedDate = new Date(getUrlParameter('startDate'));
		$('#endDate').datepicker({
			dateFormat: 'mm/dd/yy',
			changeMonth: true,
			changeYear: true,
			minDate: selectedDate
		});
	}
	$('.filters span').on('click', function(){
		$('.filterBlock').toggle();
	});
});
function getUrlParameter(sParam) {
	var sPageURL = decodeURIComponent(window.location.search.substring(1)),
		sURLVariables = sPageURL.split('&'),
		sParameterName,
		i;
	for (i = 0; i < sURLVariables.length; i++) {
		sParameterName = sURLVariables[i].split('=');

		if (sParameterName[0] === sParam) {
			return sParameterName[1] === undefined ? true : sParameterName[1];
		}
	}
}
