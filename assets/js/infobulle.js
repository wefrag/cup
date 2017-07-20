if(jQuery) {
	( function($) {
	$.cursorMessageData = {};

	$(window).ready(function(e) {
		if ($('#cursorMessageDiv').length==0) {
			  $('body').append('<div id="infobulle_detail">&nbsp;</div>');
			  $('#infobulle_detail').hide();
		}

		$('body').mousemove(function(e) {
			$.cursorMessageData.mouseX = e.pageX + 8;
			$.cursorMessageData.mouseY = e.pageY + 5;
			if ($.cursorMessageData.options != undefined) $._showCursorMessage();
		});
	});
	$.extend({
		cursorMessage: function(message, options) {
			if( options == undefined ) options = {};
			if( options.offsetX == undefined ) options.offsetX = 5;
			if( options.offsetY == undefined ) options.offsetY = 5;
			if( options.hideTimeout == undefined ) options.hideTimeout = 200;

			$('#infobulle_detail').html(message).fadeIn('fast');
			if (jQuery.cursorMessageData.hideTimeoutId != undefined)  clearTimeout(jQuery.cursorMessageData.hideTimeoutId);
			if (options.hideTimeout>0) jQuery.cursorMessageData.hideTimeoutId = setTimeout($.hideCursorMessage, options.hideTimeout);
			jQuery.cursorMessageData.options = options;
			$._showCursorMessage();
		},
		hideCursorMessage: function() {
			$('#infobulle_detail').fadeOut('fast');
		},
		_showCursorMessage: function() {
			$('#infobulle_detail').css({ top: ($.cursorMessageData.mouseY + $.cursorMessageData.options.offsetY)+'px', left: ($.cursorMessageData.mouseX + $.cursorMessageData.options.offsetX) });
		}
	});
})(jQuery);
}