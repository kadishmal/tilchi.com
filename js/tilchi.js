function doSearch(url, dataToSend, searchHandler, completeHandler)
{
	jQuery.ajax({
		'type':'POST',
		'url': url,
		'cache': true,
		'dataType':'json',
		'data': dataToSend,
		'success': function(data, textStatus, jqXHR)
		{
			searchHandler(data);
		},
		'complete':function()
		{
			if (completeHandler)
			{
				completeHandler();
			}
		}
	});
}

function enableLanguageSwitch()
{
    $('#fromLang,#toLang').change(function()
    {
        $('[id=Tilchi_' + $(this).attr('id') + ']').val($(this).val());
        $.cookie('Tilchi_' + $(this).attr('id'), $(this).val());
        $('#Tilchi_phrase').focus();
    })
    .change();

    $('.switch').click(function()
    {
        var t = $('#fromLang').val();
        $('#fromLang').val($('#toLang').val()).change();
        $('#toLang').val(t).change();
    });

    $('.meta .links a').click(function()
    {
        showMessage(UIMessages['shareTitle'], '<input type="text" value="' + $(this).attr('href') + '" class="share-link" />', UIMessages['close']);
        $('#msgBox .msg .share-link').focus().select();
        return false;
    });
}

function listenToLetters()
{
    var phrase = $('#Tilchi_phrase');

    phrase.keypress(function(event)
    {
        if (event.shiftKey)
        {
            var keyCode = 0;

            switch (event.keyCode)
            {
                // Cyrillic capitalized "Н" was pressed
                case 1053: keyCode = 1187; break;
                // Cyrillic capitalized "О" was pressed
                case 1054: keyCode = 1257; break;
                // Cyrillic capitalized "У" was pressed
                case 1059: keyCode = 1199; break;
            }

            if (keyCode > 0)
            {
                event.preventDefault();
                var len = phrase.val().length,
                    start = phrase[0].selectionStart,
                    end = phrase[0].selectionEnd,
                    sel = phrase[0].value.substring(start, end),
                    char = String.fromCharCode(keyCode);

                phrase.val(phrase.val().substring(0, start) + char + phrase.val().substr(end));
                phrase[0].selectionStart = phrase[0].selectionEnd = start + char.length;
            }
        }
    });
}

function activateTilchiSearch(formName)
{
    var form = $('#' + formName),
		spinner = $('#spinner').clone().addClass('top-10'),
        results = $('#results'),
		phrase = $('#Tilchi_phrase'), selectedPhrase,
		searchHandler = function(data)
		{
			if (data.count == 0)
			{
				results.html('<p class="top-10">' + data.status + '</p>');
				$('#add-phrase').show();
			}
			else
			{
				$('#add-phrase').hide();
				jQuery.each(data.phrases, function(index, phrase)
				{
					var item = $('<a class="item" href="/site/' + phrase.fromLang + '/' + phrase.toLang + '/' + phrase.phrase + '"></a>');
					results.append(item);

					item.append(phrase.phrase)
					.click(function(event)
					{
						event.preventDefault();
						retrieveTranslation($(this));
					});
				});
			}
		},
		completeHandler = function()
		{
			spinner.hide();
		};

	enableLanguageSwitch();

	phrase.keydown(function(event)
	{
		switch (event.keyCode)
		{
			case 40: // DOWN arrow
				event.preventDefault();

				if (results.length > 0)
				{
					selectedPhrase = results.children('.active');

					if (selectedPhrase.length > 0)
					{
						selectedPhrase.removeClass('active');
						selectedPhrase = selectedPhrase.next('.item');

						if (selectedPhrase.length == 0)
						{
							selectedPhrase = results.children('.item:first');
						}
					}
					else{
						selectedPhrase = results.children('.item:first');
					}

					selectedPhrase.addClass('active');
					phrase.val(selectedPhrase.text());
				}

				break;
			case 38: // UP arrow
				event.preventDefault();

				if (results.length > 0)
				{
					selectedPhrase = results.children('.active');

					if (selectedPhrase.length > 0)
					{
						selectedPhrase.removeClass('active');
						selectedPhrase = selectedPhrase.prev('.item');

						if (selectedPhrase.length == 0)
						{
							selectedPhrase = results.children('.item:last');
						}
					}
					else{
						selectedPhrase = results.children('.item:last');
					}

					selectedPhrase.addClass('active');
					phrase.val(selectedPhrase.text());
				}

				break;
			case 13: // Enter
				event.preventDefault();
				if (selectedPhrase && selectedPhrase.length > 0 && selectedPhrase.text() == phrase.val())
				{
					selectedPhrase.click();
				}
				break;
		}
	})
	.typing({
		stop: function (event, $elem)
		{
			form.submit();
		},
		delay: 900,
		onBlur: false
	});

    form.submit(function()
    {
		if (phrase.val() != '')
		{
			$('#search-container').hide();
			results.empty().prepend(spinner);
			spinner.show();
			doSearch(form.attr('action'), form.serialize() + '&ajax=' + formName, searchHandler, completeHandler);
		}

        return false;
    });
}

function retrieveTranslation(item)
{
	var translationsBox = $('#translation'),
		spinner = $('#spinner').clone(),
		translationForm = $('#add-translation-form');

	translationForm.hide()
		.children('#Tilchi_phrase').val(item.text());

	translationsBox.empty().prepend(spinner);

	$('#search-container').show();
	spinner.show();

	jQuery.ajax({
		'type': 'GET',
		'url': encodeURI(item.attr('href')),
		'cache': true,
		'dataType': 'json',
		'data': 'ajax=ajax',
		'success': function(data, textStatus, jqXHR)
		{
			translationsBox.append('<h3>' + item.html() + '</h3>');

            if (data.translationsCount == undefined)
            {
                translationsBox.append('<div id="translation-body"><p class="msg">' + data.messages.noPhrase + '</p></div>');
            }
            else{
                if (data.translationsCount == 0)
                {
                    translationsBox.append('<div id="translation-body"><p class="msg">' + data.messages.noTranslation + '</p></div>');
                }
                else{
                    jQuery.each(data.translations, function(index, translation)
                    {
                        translationsBox.append('<div id="translation-body"><p>' + translation.phrase + '</p><div>'
                            + translation.explanation + '</div></div>');
                    });
                }
            }

			$('#add-text').show();
		},
		'error':function(){
			alert('error');
		},
		'complete':function(){
			spinner.remove();
		}
	});
}

function activateTranslation()
{
	var addText = $('#add-text'),
		translation = $('#Tilchi_translation'),
		translationForm = $('#add-translation-form'),
		translationResults = $('#translationResults'),
		selectedPhrase,
		translationSearchHandler = function(data)
		{
			translationResults.empty();

			if (data.count > 0)
			{
				jQuery.each(data.phrases, function(index, phrase)
				{
					var item = $('<div class="item">' + phrase.phrase + '</div>');
					translationResults.append(item);

					item.click(function()
					{
						translation.val(phrase.phrase);
						translationResults.hide();
					});
				});
			}

			translationResults.show();
		};

	addText.on('click', function()
	{
		$(this).hide();

		translationForm.children('.textField').val('');

		translationForm.show()
			.children('#Tilchi_translation').focus();

		return false;
	});

	translation.keydown(function(event)
	{
		switch (event.keyCode)
		{
			// DOWN key
			case 40:
				event.preventDefault();

				if (translationResults.length > 0)
				{
					selectedPhrase = translationResults.children('.active');

					if (selectedPhrase.length > 0)
					{
						selectedPhrase.removeClass('active');
						selectedPhrase = selectedPhrase.next();

						if (selectedPhrase.length == 0)
						{
							selectedPhrase = translationResults.children(':first');
						}
					}
					else{
						selectedPhrase = translationResults.children(':first');
					}

					selectedPhrase.addClass('active');
					translation.val(selectedPhrase.text());
				}

				break;
			// UP key
			case 38:
				event.preventDefault();

				if (translationResults.length > 0)
				{
					selectedPhrase = translationResults.children('.active');

					if (selectedPhrase.length > 0)
					{
						selectedPhrase.removeClass('active');
						selectedPhrase = selectedPhrase.prev();

						if (selectedPhrase.length == 0)
						{
							selectedPhrase = translationResults.children(':last');
						}
					}
					else{
						selectedPhrase = translationResults.children(':last');
					}

					selectedPhrase.addClass('active');
					translation.val(selectedPhrase.text());
				}

				break;
			// ENTER key
			case 13:
				event.preventDefault();
				translationResults.hide();
				break;
		}
	})
	.typing({
		stop: function (event, $elem)
		{
			if (translation.val() != '')
			{
				doSearch('/site/search',
					'Tilchi[fromLang]=' + $('#Tilchi_toLang').val() +
					'&Tilchi[toLang]=' + $('#Tilchi_fromLang').val() +
					'&Tilchi[phrase]=' + translation.val() +
					'&ajax=tilchi-search-form', translationSearchHandler);
			}
			else{
				translationResults.hide();
			}
		},
		delay: 900,
		onBlur: false
	})
	.blur(function(){
		translationResults.hide();
	});

	$('#add-explanation').click(function()
	{
		$('#Tilchi_explanation_block').show();
		$(this).removeClass('block-button');
	});

	translationForm.find('.link-button').click(function(){
		translationForm.hide();
		$('#Tilchi_explanation_block').hide();
		$('#add-explanation').addClass('block-button');
		addText.show();
		return false;
	});

	translationForm.submit(function()
	{
		var translationBody = $('#translation-body'),
			toLang = translationForm.children('#Tilchi_toLang :selected');

		if (toLang.length == 0)
		{
			toLang = $('#Tilchi_toLang :selected');
		}

		jQuery.ajax({
			'type': 'POST',
			'url': translationForm.attr('action'),
			'cache': false,
			'dataType': 'json',
			'data': translationForm.serialize() + '&ajax=' + translationForm.attr('id'),
			'success': function(data, textStatus, jqXHR)
			{
				// succeeded
				if (data.status == 2)
				{
					translationForm.hide();

					translationBody.children('.msg').remove();

					translationBody.append('<h4 class="language">' + toLang.text() + '</h4><p>' + translation.val() + '</p>');
					addText.show();
				}
				else if (data.status == 23000){
					showMessage(data.messages.title, data.messages.message, data.messages.ok);
				}
			},
			'error':function(){
				alert('error');
			}
		});

		return false;
	});
}