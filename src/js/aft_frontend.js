jQuery(document).ready(function ($) {
	var trackedLinks = [];

	function isLinkVisible($link) {
		var fold = $(window).height();
		var linkTop = $link.offset().top;

		return linkTop <= fold; // Only links within the visible viewport on load
	}

	var visibleLinks = [];

	$('a').each(function () {
		var $link = $(this);
		var href = $link.attr('href');

		if (!href || trackedLinks.includes(href)) {
			return; // Skip if no href or already tracked
		}

		if (isLinkVisible($link)) {
			trackedLinks.push(href);
			visibleLinks.push(href);
		}
	});

	if (visibleLinks.length > 0) {
		$.ajax({
			url: aft_frontend_data.ajax_url,
			method: 'POST',
			data: {
				action: 'aft_track_links',
				nonce: aft_frontend_data.nonce,
				links: visibleLinks,
				screen_size: {
					width: $(window).width(),
					height: $(window).height()
				},
			},
			success: function (response) {
				console.log('Above-the-fold links tracked:', visibleLinks);
			},
			error: function (xhr, status, error) {
				console.error('Tracking failed:', error);
			}
		});
	}
});
