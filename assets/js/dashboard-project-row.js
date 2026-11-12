document.querySelectorAll('.js-dashboard-project-row').forEach(function (row) {
	row.addEventListener('click', function (event) {
		if (event.target.closest('a, button, input, select, textarea, [role="button"], .dropdown')) {
			return;
		}

		window.location.href = row.dataset.href;
	}, true);

	row.addEventListener('keydown', function (event) {
		if (event.target === row && (event.key === 'Enter' || event.key === ' ')) {
			event.preventDefault();
			window.location.href = row.dataset.href;
		}
	});
});
