var localStrings = new Array();

function setLocalText(controller, englishText, localText)
{
	if (!localStrings[controller])
	{
		localStrings[controller] = new Array();
	}

	localStrings[controller][englishText] = localText;
}

function getLocalText(controller, englishText)
{
	if (localStrings[controller] && localStrings[controller][englishText]){
		return localStrings[controller][englishText];
	}
	else{
		return englishText;
	}
}

function activateEditPost()
{
	// Publish Date Edit
	var editTimestamp = $('#publish-box .edit-timestamp'),
		saveTimestamp = $('#publish-box .save-timestamp'),
		cancelTimestamp = $('#publish-box .cancel-timestamp'),
		timestampdiv = editTimestamp.siblings('#timestampdiv'),
		timestamp = $('#publish-box #timestamp');

	editTimestamp.click(function(){
		editTimestamp.hide();
		timestampdiv.toggle(300);
		return false;
	});

	saveTimestamp.click(function(){
		var mm = $('#publish-box #Post_mm'),
			jj = $('#publish-box #Post_jj'),
			aa = $('#publish-box #Post_aa'),
			hh = $('#publish-box #Post_hh'),
			mn = $('#publish-box #Post_mn');

		timestamp.text(jj.val() + ' ' + mm.children(':selected').text() + ' ' +aa.val() + ' ' + $('#at').text() + ' ' + hh.val() + ':' + mn.val());

		editTimestamp.show();
		timestampdiv.toggle(300);
		return false;
	});

	cancelTimestamp.click(function(){
		editTimestamp.show();
		timestampdiv.toggle(300);
		return false;
	});

	// Status Edit
	var editStatus = $('#publish-box .edit-status'),
		saveStatus = $('#publish-box .save-status'),
		cancelStatus = $('#publish-box .cancel-status'),
		statusdiv = editStatus.siblings('#statusdiv');

	editStatus.click(function(){
		editStatus.hide();
		statusdiv.toggle(300);
		return false;
	});

	saveStatus.click(function(){
		var statusSelect = $('#publish-box #Post_status'),
			status = $('#publish-box #post-status');

		status.text(statusSelect.children(':selected').text());

		editStatus.show();
		statusdiv.toggle(300);
		return false;
	});

	cancelStatus.click(function(){
		editStatus.show();
		statusdiv.toggle(300);
		return false;
	});

	//Preview Post
	var preview = $('#publish-box #preview-post');

	preview.click(function(){
		var content = tinymce.EditorManager.get('Post_content').getContent();

		$('#blog .post').html('<a>' + $('#blog #edit-post #Post_title').val() + '</a>')
            .append('<span class="meta">' + getLocalText('blog', 'Published on') + ' ' + timestamp.text() + '</span>')
            .append(content).slideDown('slow');

        $("body,html").animate({scrollTop:0},800,"swing");

		return false;
	});

    // Tags
    enableTagEditor();
}

function enableTagEditor()
{
    var tagEditor = $('#tag-editor input');

    if (tagEditor.length)
    {
        var tagResults = $('#tag-results'), selectedPhrase,
            hiddenEl = tagEditor.parentsUntil('div.form').find('input#Post_tags'),
            minWidth = parseInt(tagEditor.css('min-width').replace('px', '')),
            maxWidth = parseInt(tagEditor.css('max-width').replace('px', '')),
            parentLeft = tagEditor.parent().offset().left, v;

        tagEditor.width(0);
        v = maxWidth - (tagEditor.offset().left - parentLeft) + 1;

        if (v < minWidth)
        {
            v = maxWidth;
        }

        tagEditor.width(v).keydown(function(event)
        {
            switch(event.keyCode)
            {
                case 40:    // DOWN key
                    event.preventDefault();

                    if (tagResults.length)
                    {
                        selectedPhrase = tagResults.children('.active');

                        if (selectedPhrase.length)
                        {
                            selectedPhrase.removeClass('active');
                            selectedPhrase = selectedPhrase.next();

                            if (selectedPhrase.length == 0)
                            {
                                selectedPhrase = tagResults.children(':first');
                            }
                        }
                        else{
                            selectedPhrase = tagResults.children(':first');
                        }

                        selectedPhrase.addClass('active');
                        tagEditor.val(selectedPhrase.text());
                    }
                    break;
                case 38:    // UP key
                    event.preventDefault();

                    if (tagResults.length)
                    {
                        selectedPhrase = tagResults.children('.active');

                        if (selectedPhrase.length)
                        {
                            selectedPhrase.removeClass('active');
                            selectedPhrase = selectedPhrase.prev();

                            if (selectedPhrase.length == 0)
                            {
                                selectedPhrase = tagResults.children(':last');
                            }
                        }
                        else{
                            selectedPhrase = tagResults.children(':last');
                        }

                        selectedPhrase.addClass('active');
                        tagEditor.val(selectedPhrase.text());
                    }
                    break;
                case 8:     // Backspace key
                    var siblings = tagEditor.siblings();

                    if (tagEditor.val() == '' && siblings.length)
                    {
                        event.preventDefault();

                        var lastTagText = siblings.last().text(),
                            pos = lastTagText.indexOf(' (new)');

                        if (pos > 0)
                        {
                            lastTagText = lastTagText.substring(0, pos);
                        }

                        siblings.last().children('.delete-tag').click();

                        tagEditor.val(lastTagText)
                            .focus().val(tagEditor.val());
                    }
                    break;
                case 13:    // Enter key
                    event.preventDefault();

                    if (tagEditor.val() != '')
                    {
                        selectedPhrase = tagResults.children('.active');

                        if (selectedPhrase && selectedPhrase.length)
                        {
                            selectedPhrase.click();
                        }
                        else{
                            selectedPhrase = $('<div>' + tagEditor.val() + '</div>');

                            var tagText = tagEditor.val(),
                                tag = $('<span class="tag">' + tagText + ' (new)</span>'),
                                tagDelete = $('<span class="sprite delete-tag" title="remove this tag"></span>');

                            tag.append(tagDelete).insertBefore(tagEditor);
                            tagResults.empty().hide();

                            if (hiddenEl.val() != '')
                            {
                                hiddenEl.val(hiddenEl.val() + ',');
                            }

                            hiddenEl.val(hiddenEl.val() + tagText);

                            tagEditor.val('').width(tagEditor.width() - tag.outerWidth(true));
                        }
                    }
            }
        })
        .keyup(function(event)
        {
            switch(event.keyCode)
            {
                case 40: case 38: case 13: event.preventDefault(); break;
                default:
                    var tagTextLower = tagEditor.val().toLowerCase();

                    if (tagTextLower != '')
                    {
                        tagResults.empty().hide();

                        $.ajax({
                            'type':'GET',
                            'url': tagEditor.data('url'),
                            'cache': true,
                            'dataType':'json',
                            'data': 'tag=' + tagTextLower,
                            'success': function(data, textStatus, jqXHR)
                            {
                                $(data).each(function(index, t)
                                {
                                    if (hiddenEl.val().indexOf(t.name.toLowerCase()) < 0)
                                    {
                                        tagResults.append('<div' + (t.name.toLowerCase() == tagTextLower ? ' class="active"' : '') + '>' + t.name + '</div>');
                                    }
                                });

                                if (tagResults.children().length)
                                {
                                    tagResults.show();
                                }

                                tagResults.children().click(function()
                                {
                                    var tagText = $(this).text(),
                                        tag = $('<span class="tag">' + tagText + '</span>'),
                                        tagDelete = $('<span class="sprite delete-tag" title="remove this tag"></span>'), v;

                                    tag.append(tagDelete).insertBefore(tagEditor);
                                    tagResults.empty().hide();

                                    if (hiddenEl.val() != '')
                                    {
                                        hiddenEl.val(hiddenEl.val() + ',');
                                    }

                                    hiddenEl.val(hiddenEl.val() + tagText);

                                    tagEditor.val('').width(0);
                                    v = maxWidth - (tagEditor.offset().left - parentLeft) + 1;

                                    if (v < minWidth)
                                    {
                                        v = maxWidth;
                                    }

                                    tagEditor.width(v);
                                });
                            }
                        });
                    }
                    else{
                        tagResults.empty().hide();
                    }
            }
        })
        .blur(function(){
            tagResults.empty().hide();
        });

        $('span.delete-tag').live('click', function()
        {
            var tag = $(this).parent(),
                tagText = tag.text(),
                pos = tagText.indexOf(' (new)');

            if (pos > 0)
            {
                tagText = tagText.substring(0, pos);
            }

            if (hiddenEl.val().indexOf(tagText) > 0)
            {
                tagText = ',?\s*' + tagText;
            }
            else{
                tagText = tagText + ',?\s*';
            }

            hiddenEl.val(hiddenEl.val().replace(new RegExp(tagText, "g"), ''));
            tag.remove();

            tagEditor.width(0);
            v = maxWidth - (tagEditor.offset().left - parentLeft) + 1;

            if (v < minWidth)
            {
                v = maxWidth;
            }

            tagEditor.width(v).focus();
        });
    }
}

function activateCommentForm()
{
    var commentForm = $('#comment-form'),
        btnLeaveComment = $('#button-leave-comment');

    btnLeaveComment.click(function(){
        $(this).hide();
        commentForm.show();
    });

    commentForm.find('.button-cancel-comment').click(function(){
        commentForm.hide();
        btnLeaveComment.show();
    });

    commentForm.find('input[type="submit"]').click(function(){
        if ($.trim(tinymce.EditorManager.get('Comment_content').getContent()).length == 0)
        {
            commentForm.find('.mceToolbar').effect("highlight", {}, 3000);
            return false;
        }

        return true;
    });

    $('.reply-button').click(function(){
        var replyButton = $(this),
            thisParent = replyButton.parent(),
            commentId = thisParent.parent().attr('id'),
            // comment-1, '1' is located at index 8
            commentIdNum = commentId.substring(8),
            replyForm = $(thisParent.siblings('#' + commentId + '-form'));

        if (replyForm.length > 0)
        {
            if (replyForm.is(':visible')){
                replyButton.removeClass('active');
                replyForm.hide();
            }
            else{
                replyButton.addClass('active');
                replyForm.show();
                // Autofocus the text field.
                tinymce.EditorManager.execCommand('mceFocus', false, commentId + '-content');
            }
        }
        else if (commentForm.is('p'))
        {
            replyButton.addClass('active');
            replyForm = commentForm.clone();
            replyForm.insertAfter(thisParent)
                .addClass('doLogin')
                .show();
        }
        else{
            replyButton.addClass('active');
            replyForm = $('<form id="' + commentId + '-form" action="/content/comment/new" method="post" style="display: block; "></form>');

            replyForm.append(commentForm.children('#Comment_post_id, #Comment_content, .row-last').clone())
                .append('<input type=\"hidden\" value=\"' + commentIdNum + '\" name=\"Comment[parent_id]\" id=\"Comment_parent_id\">');

            replyForm.find('.button-cancel-comment').click(function(){
                replyButton.click();
            });

            replyForm.find('input[type="submit"]').click(function(){
                if ($.trim(tinymce.EditorManager.get(commentId + '-content').getContent()).length == 0)
                {
                    replyForm.find('.mceToolbar').effect("highlight", {}, 3000);
                    return false;
                }

                return true;
            });

            var textArea = replyForm.find('#Comment_content');
            textArea.attr('id', commentId + '-content').show();
            replyForm.insertAfter(thisParent).show();

            textArea.tinyMCE({'mode':'exact','elements': textArea.attr('id'),'language':'ru','readonly':false,'relative_urls':false,'remove_script_host':false,'convert_fonts_to_spans':true,'fullscreen_new_window':true,'media_use_script':true,'content_css':'/assets/aba8f778/tinyContent.css','theme':'advanced','plugins':'spellchecker','skin':'o2k7','theme_advanced_toolbar_location':'top','theme_advanced_toolbar_align':'left','theme_advanced_buttons1':'bold,italic,underline,strikethrough,|,bullist,numlist,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code','theme_advanced_buttons2':'','theme_advanced_buttons3':''}, 'html', true);
            // Autofocus the text field.
            tinymce.EditorManager.execCommand('mceFocus', false, commentId + '-content');
        }
    });
    /* Hihglight the newly created comment */
    var url = window.location.href,
        pos = url.indexOf('#');

    if (pos > -1){
        url = url.substring(pos + 1);

        var comment = $('#' + url);

        if (comment.length > 0){
            comment.children('.message').effect("highlight", {}, 5000);
        }
    }
    /* END Hihglight*/
}
function activateSearchForm(formName)
{
    var form = $('#' + formName),
        container = $('#search-container'), spinner,
        results = container.children('#results');

    form.submit(function()
    {
        spinner = $('#spinner').clone();
        results.empty().prepend(spinner);
        spinner.show();
        container.show();

        $.ajax({
            'type':'POST',
            'url': form.attr('action'),
            'cache': true,
            'dataType':'json',
            'data': form.serialize() + '&ajax=' + formName,
            'success': function(data, textStatus, jqXHR)
			{
                results.empty();

                if (data.count == 0)
                {
                    container.children('#results').html('<h3>' + data.status + '</h3>');
                }
                else
                {
                    $.each(data.posts, function(index, post){
                        var item = $('<div class="item ' + post.type + '"></div>'),
                            icon = $('<div class="icon"></div>'),
                            answer = $('<span class="answer" title="' + post.votesTitle + '"></span>'),
                            info = $('<div class="info"></div>'),
                            meta = $('<span class="meta"></span>')
                            ;
                        results.append(item);
                        item.append(icon);
                        icon.append('<i></i>')
                            .append(answer);
                        answer.append('<i></i>')
                            .append(post.votesCount);
                        item.append(info);
                        info.append($('<h3><a href="' + post.link + '">' + post.title + '</a></h3>'))
                            .append(meta);
                        meta.append('<div class="summary">' + post.summary + '</div>')
                            .append('<div class="category"><a href="' + post.categoryLink + '">' + post.categoryText + '</a></div>');
                    });
                }
                container.children('.options').show();
            },
            'error':function(){
                alert('error');
            },
            'complete':function(){
                spinner.remove();
            }
        });
        return false;
    });
}
function enableVoting()
{
    $('.vote-up').click(function(){
        var voteBtn = $(this),
            postId = voteBtn.attr('id');

        $.ajax({
            'type':'POST',
            'url': '/vote/up',
            'cache': false,
            'dataType':'json',
            'data': 'Vote[post_id]=' + postId,
            'success': function(data)
            {
                if (data.status == 0)
                {
                    voteBtn.siblings('.vote-amount').html(data.count);

                    if (voteBtn.hasClass('voted'))
                        voteBtn.removeClass('voted');
                    else
                        voteBtn.addClass('voted');
                }
				// user is not signed in
                else if (data.status == 1)
                {
                    showMessage(data.title, data.message, data.yes, function(){
                        window.location = '/site/login';
                    }, true, data.no);
                }
				// no more votes left
                else if (data.status == 3)
                {
                    showMessage(data.title, data.message, data.ok);
                }
            }
        });
    });

	$('#post-actions').mouseenter(function(){
		var div = $(this);
		div.animate({right:'+=' + (div.width() - 30)},350);
	}).
	mouseleave(function(){
		var div = $(this);
		div.animate({right:'-=' + (div.width() - 30)},350);
	});
}