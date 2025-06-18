jQuery(document).ready(function ($) {
	var trackedLinks = [];

	function getCookie(name) {
		var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
		if (match) return match[2];
		return null;
	}

	function setCookie(name, value, minutes) {
		var expires = '';
		if (minutes) {
			var date = new Date();
			date.setTime(date.getTime() + (minutes * 60 * 1000));
			expires = '; expires=' + date.toUTCString();
		}
		document.cookie = name + '=' + value + expires + '; path=/';
	}

	var visitId = getCookie('aft_visit_id');
	if (!visitId) {
		visitId = 'visit_' + Math.random().toString(36).substr(2, 9);
		setCookie('aft_visit_id', visitId, 30); // 30-minute session
	}

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
				visit_id: visitId,
				page_url: window.location.href
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
