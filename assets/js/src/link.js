//Link related functions
Kanboard.Link = (function() {
	
	function on_change() {
		if ($('.behaviour').prop('checked')) {
			$('.link-inverse-label').hide();
		}
		else {
			$('.link-inverse-label').show();
		}
	}
	
	return {
	
		Init: function() {
			on_change();
			$(".behaviour").click(on_change);
		}
	};

})();