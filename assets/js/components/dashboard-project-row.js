document.addEventListener('click', function (event) {
	var target = event.target instanceof Element ? event.target : null;
	if (!target) {
		return;
	}

	var row = target.closest('.js-dashboard-project-row');
	if (!row || target.closest('a, button, input, select, textarea, [role="button"], .dropdown')) {
		return;
	}

	window.location.href = row.dataset.href;
});

document.addEventListener('keydown', function (event) {
	if ((event.key === 'Enter' || event.key === ' ') && event.target.matches('.js-dashboard-project-row')) {
		event.preventDefault();
		window.location.href = event.target.dataset.href;
	}
});
