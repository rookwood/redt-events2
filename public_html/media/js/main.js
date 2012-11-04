if (typeof jQuery != "undefined"){

	// On DOM Ready
	(function($){$(document).ready(function(){

		/**
		 * Notices
		 */
		
		// Show closing "X"s
		$("div.notice-close").show();
		//$('div.notice').width($('div.notice').width());

		// Close a notice if the "X" is clicked
		$('div.notice-close a').live("click", function(){
			var notice = $(this).closest("div.notice");
			var persistent = notice.hasClass('notice-persistent');
			notice.hide("fast");

			if (persistent){
				var ajax_url = $(this).attr("href");
				$.ajax({
					url: ajax_url,
					cache: false,
					dataType: 'json',
					success: $.noop(),
					error: $.noop()
				});
			}

			return false;
		});

	/**
	 * Event details loading via ajax
	 */
	
	// Ajax loading of event details
	// can't figure out why ul.header won't work for event binding
	// so using pointless class for the moment but this is problematic
	$('.clickable').click(function() {

		// Find container element for our event data
		var event_data = $(this).find('section.event_data');

		// See if we've already loaded data for this event
		if ( ! $(this).hasClass('loaded'))
		{
			// save reference to clicked element as a starting point for all traversal
			var that = this;

			// ajax call to fetch event data
			$.get($(this).data('url'), function(data) {
				event_data.html(data);

				// setup for jquery ui tabs
				event_data.find('#tabs').tabs();

				// mark event has having all data loaded
				$(that).addClass('loaded');

				$(that).toggleClass('event_collapsed event_expanded');

				event_data.slideDown('slow');
			});
		}
		else
		{
			// Toggle details display
			if ($(this).hasClass('event_collapsed'))
			{
				$(this).toggleClass('event_collapsed event_expanded');

				event_data.slideDown('slow');
			}
			else
			{
				var that = this;

				event_data.slideUp('slow', function() {
					$(that).toggleClass('event_collapsed event_expanded');
				});
			}
		}
	});

	$('.clickable > section').click(function(e) {
		e.stopPropagation();
	});		

	})})(jQuery); // Prevent conflicts with other js libraries
}