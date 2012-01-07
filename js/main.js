function isNumber(n){
	return !isNaN(parseFloat(n)) && isFinite(n);
}
function showMessage(title, message, okButtonText, okCallback, alternativeButton, altButtonText, altCallback)
{
	$('#floodPanel').show();
	var msgBox = $('#msgBox');
	msgBox.append($('<div class="title">' + title + '</div>'))
		.append($('<div class="msg"></div>').append(message))
		.css("top", ( $(window).height() - msgBox.height() ) / 2 + "px")
    	.css("left", ( $(window).width() - msgBox.width() ) / 2 + "px");

	var buttons = $('<div class="buttons"></div>');
	msgBox.append(buttons).show();

	okButtonEvent = function(){
						msgBox.empty().hide();
						$('#floodPanel').hide();
						if (okCallback) okCallback();
					};

	$('<span class="button blue ok" tabindex="0"><a>' + okButtonText + '</a></span>')
		.appendTo(buttons)
		.click(function(){okButtonEvent();})
		.focus()
		.keydown(function(event){
			switch(event.keyCode){
				case 13:okButtonEvent();
			}
		});

	if (alternativeButton){
		cancelButton = $('<span class="button alt">' + altButtonText + '</span>');
		cancelButton.click(function(){
			msgBox.empty().hide();
			$('#floodPanel').hide();
			if (altCallback) altCallback();
		});
		buttons.append(cancelButton);
	}

}
function activateMainMenu(){
	var subMenus = $('#mainmenu li ul');

	if (subMenus.length > 0)
	{
		var page = $('#page'),
			pageRightBorderX = page.offset().left + page.outerWidth();

		subMenus.each(function(index){
			var menu = $(this),
				parentMenu = menu.prev(),
				parentMenuPosition = parentMenu.offset(),
				floodPanel = $('#floodPanel');

			parentMenu.mouseenter(function()
			{
				if (floodPanel.is(':visible')){
					floodPanel.mouseenter();
				}

				parentMenu.addClass('menuHover');
				menu.css('top', parentMenuPosition.top + parentMenu.outerHeight(true))
					.css('left', parentMenuPosition.left)
					.addClass('submenu');

				floodPanel.show()
						.mouseenter(function(){
							parentMenu.removeClass('menuHover');
							menu.removeClass('submenu');
							$(this).hide().unbind('mouseenter');
						});

				if (parentMenuPosition.left + menu.outerWidth(true) > pageRightBorderX){
					menu.css('left', parentMenuPosition.left - menu.outerWidth(true) + parentMenu.outerWidth(true));
				}
			});
		});
	}
}