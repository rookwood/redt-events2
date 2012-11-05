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
		$('.event_title, .event_numbers, .event_time').click(function() {

			// Find container element for our event data
			var event_data = $(this).parent().siblings('section.event_data');
			var parent_li = $(this).parent().parent();

			// See if we've already loaded data for this event
			if ( ! parent_li.hasClass('loaded'))
			{
				// ajax call to fetch event data
				$.get(parent_li.data('url'), function(data) {
					event_data.html(data);

					// setup for jquery ui tabs
					event_data.find('#tabs').tabs();

					// mark event has having all data loaded
					parent_li.addClass('loaded');

					parent_li.toggleClass('event_collapsed event_expanded');

					event_data.slideDown('slow');
				});
			}
			else
			{
				// Toggle details display
				if (parent_li.hasClass('event_collapsed'))
				{
					parent_li.toggleClass('event_collapsed event_expanded');

					event_data.slideDown('slow');
				}
				else
				{
					event_data.slideUp('slow', function() {
						$(parent_li).toggleClass('event_collapsed event_expanded');
					});
				}
			}
		});

		// Modal window for event enrollment
		$(document).on('click', 'a', (function(e) {
			// Prevent click on anchor from loading new page
			e.preventDefault();
			
			url = $(this).attr('href');
			
			// Create modal from anchor's href
			jQuery.facebox(function($) {
				jQuery.get(url, function(data) { 
					jQuery.facebox(data) 
				});
			});
			return false;
		}));
		
	})})(jQuery); // Prevent conflicts with other js libraries
}