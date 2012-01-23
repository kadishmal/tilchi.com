function enablePermissionManagement()
{
    var permissionName = $('.name,.description,.bizrule'),
        removePermission = $('.link-button.remove'),
        expandRole = $('.link-button.expand');

    removePermission.click(function()
    {
        $(this).parent().parent().remove();
        return false;
    });

    permissionName.blur(function(event)
    {
        event.preventDefault();
        updatePermissions(this);
    })
    .each(function()
    {
        $(this).data('old', this.value);
    });

    expandRole.click(function()
    {
        loadPermission($(this));
    });
}

function loadPermission($this)
{
    var parent = $this.parent().parent(),
        roleName = parent.find('input.name').val(),
        output = $('<div class="tabContainer" id="' + roleName + '"></div>'),
        tabContents, users, $input,
        spinner = $('#spinner').clone(), msgBox;

    output.append(spinner);

    jQuery.ajax({
        'type':'POST',
        'url': '/user/manage/getRoleData',
        'cache': false,
        'dataType':'json',
        'data': 'Role[name]=' + roleName,
        'success': function(data, textStatus, jqXHR)
        {
            if (data.status == '1')
            {
                tabContents = $('<div class="tabContents"></div>');

                output.append('<div class="tabs">' +
                    '<span class="active" id="descendants">' + data.messages.descendants +
                    '<span class="count">' + data.descendantCount + '</span>' +
                    '</span><span id="users">' + data.messages.users +
                    '<span class="count"></span>' +
                    '</span></div>')
                    .append(tabContents);

                users = $('<div id="users"><input class="filterInput" type="text" placeholder="' +
                    data.messages.filterByEmail + '" /></div>');

                tabContents.append('<div class="active" id="descendants"><input class="filterInput" type="text" placeholder="' +
                    data.messages.filterByName + '" /></div>')
                    .append(users);

                enableTabs(roleName);

                // filter by permission name handler
                $permissionInput = tabContents.find('#descendants > .filterInput');

                $permissionInput.typing({
                    stop: function (event, $elem)
                    {
                        filterPermissions(roleName, $elem);
                    },
                    delay: 900,
                    onBlur: false
                });

                filterPermissions(roleName, $permissionInput);

                // filter by email handler
                $userInput = users.children('input[type="text"]');

                $userInput.typing({
                    stop: function (event, $elem)
                    {
                        filterUsers(roleName, $elem);
                    },
                    delay: 900,
                    onBlur: false
                });

                filterUsers(roleName, $userInput);
            }
            else{
                output.append(data.message);
            }

            msgBox = $('#msgBox');
            msgBox.find('.buttons .ok a').text(data.ok);

            msgBox.animate({top: ( $(window).height() - msgBox.height() ) / 2 - msgBox.outerHeight() / 2});
        },
        'complete': function()
        {
            spinner.remove();
        }
    });

    showMessage(roleName, output, 'Ok');
}

function filterUsers(authItemName, $input)
{
    jQuery.ajax({
        'type':'POST',
        'url': '/user/manage/getAuthItemUsers',
        'cache': false,
        'dataType':'json',
        'data': 'Auth[name]=' + authItemName + '&Auth[email]=' + $input.val(),
        'success': function(data, textStatus, jqXHR)
        {
            if (data.status == 1)
            {
                $input.siblings().remove();

                $('#' + authItemName + ' .tabs #users .count').text(data.userCount);

                $.each(data.users, function(index, val)
                {
                    $('<div class="item"><span class="email">' + val + '</span><span class="link-button">' + data.messages.revoke + '</span></div>').insertAfter($input);
                });

                if (data.userCount < 1 && data.userExists)
                {
                    $('<div class="flash-notice"><div>' + data.messages.noPermission
                        + '</div><div class="center"><span class="button green"><input type="button" value="' + data.messages.assign + '" /></span></div></div>').insertAfter($input);

                    $input.siblings().find('input[type="button"]').click(function(){
                        assignUserToPermission(authItemName, $input);
                    });
                }
                else{
                    $input.siblings().find('.link-button').click(function(){
                        revokeUserFromPermission(authItemName, $(this).siblings('.email'));
                    });
                }
            }
            else{
                alert(data.message);
            }
        }
    });
}

function assignUserToPermission(authItemName, $input)
{
    jQuery.ajax({
        'type':'POST',
        'url': '/user/manage/assignUser',
        'cache': false,
        'dataType':'json',
        'data': 'Auth[name]=' + authItemName + '&Auth[email]=' + $input.val(),
        'success': function(data, textStatus, jqXHR)
        {
            if (data.status == 1)
            {
                filterUsers(authItemName, $input);
            }
            else{
                alert(data.message);
            }
        }
    });
}

function revokeUserFromPermission(authItemName, $email)
{
    jQuery.ajax({
        'type':'POST',
        'url': '/user/manage/revokeUser',
        'cache': false,
        'dataType':'json',
        'data': 'Auth[name]=' + authItemName + '&Auth[email]=' + $email.text(),
        'success': function(data, textStatus, jqXHR)
        {
            if (data.status == 1)
            {
                filterUsers(authItemName, $email.parent().siblings('.filterInput'));
            }
            else{
                alert(data.message);
            }
        }
    });
}

function filterPermissions(authItemName, $input)
{
    jQuery.ajax({
        'type':'POST',
        'url': '/user/manage/getDescendants',
        'cache': false,
        'dataType':'json',
        'data': 'Auth[name]=' + authItemName + '&Auth[desName]=' + $input.val(),
        'success': function(data, textStatus, jqXHR)
        {
            if (data.status == 1)
            {
                $input.siblings().remove();

                $('#' + authItemName + ' .tabs #descendants .count').text(data.descendantCount);

                $.each(data.descendants, function(index, val)
                {
                    $('<div class="item"><span class="name">' + val + '</span><span class="link-button">' + data.messages.remove + '</span></div>').insertAfter($input);
                });

                if (data.descendantCount < 1 && data.desExists)
                {
                    $('<div class="flash-notice"><div>' + data.messages.noPermission
                        + '</div><div class="center"><span class="button green"><input type="button" value="' + data.messages.addChild + '" /></span></div></div>').insertAfter($input);

                    $input.siblings().find('input[type="button"]').click(function(){
                        addChild(authItemName, $input);
                    });
                }
                else{
                    $input.siblings().find('.link-button').click(function(){
                        removeChild(authItemName, $(this).siblings('.name'));
                    });
                }
            }
            else{
                alert(data.message);
            }
        }
    });
}

function addChild(authItemName, $input)
{
    jQuery.ajax({
        'type':'POST',
        'url': '/user/manage/addChild',
        'cache': false,
        'dataType':'json',
        'data': 'Auth[name]=' + authItemName + '&Auth[desName]=' + $input.val(),
        'success': function(data, textStatus, jqXHR)
        {
            if (data.status == 1)
            {
                filterPermissions(authItemName, $input);
            }
            else{
                alert(data.message);
            }
        }
    });
}

function removeChild(authItemName, $desName)
{
    jQuery.ajax({
        'type':'POST',
        'url': '/user/manage/removeChild',
        'cache': false,
        'dataType':'json',
        'data': 'Auth[name]=' + authItemName + '&Auth[desName]=' + $desName.text(),
        'success': function(data, textStatus, jqXHR)
        {
            if (data.status == 1)
            {
                filterPermissions(authItemName, $desName.parent().siblings('.filterInput'));
            }
            else{
                alert(data.message);
            }
        }
    });
}

function updatePermissions(input)
{
    var $input = $(input),
        oldValue = $input.data('old'), newPermission,
        parentRow = $input.parent().parent(), name,
        field = $input.attr('class');

    if (input.value != oldValue)
    {
        name = 'User[' + input.id + '][';

        if (oldValue == '' && field == 'name')
        {
            newPermission = parentRow.clone(true);
            name += input.value;
        }
        else{
            name += input.name;
        }

        name +=  '][' + $input.attr('class') + ']';

        jQuery.ajax({
            'type':'POST',
            'url': '/user/manage/permissions',
            'cache': false,
            'dataType':'json',
            'data': 'ajax=permission-form&' + name + '=' + input.value,
            'beforeSend': function()
            {
                parentRow.find('input').prop('disabled', true);
            },
            'success': function(data, textStatus, jqXHR)
            {
                if (data.status == '1')
                {
                    $input.data('old', input.value);

                    if (field == 'name')
                    {
                        input.name = input.value;
                        parentRow.find('input.description').attr('name', input.value);
                    }

                    if (newPermission)
                    {
                        newPermission.insertAfter(parentRow.removeClass('last'))
                            .find('input').val('');
                    }
                }
                else{
                    alert(data.message);

                    if (data.status == -2)
                    {
                        parentRow.find('input.permission-name').focus();
                    }
                    else if (data.status == -3)
                    {
                        input.value = oldValue;
                        $input.focus();
                    }
                }
            },
            'complete': function()
            {
                parentRow.find('input').prop('disabled', false);
                $input.focus();
            }
        });
    }
}

function editSettings($this)
{
    var form = $('#settings').clone(),
        cells = $this.parent().siblings(),
        spinner = $('#spinner').clone(),
        name = cells.eq(0).text(),
        module = cells.eq(1).text(),
        msgBox = $('#msgBox');

    showMessage(name, spinner, UIMessages['save'], addSiteSettings, UIMessages['cancel']);

    $.ajax({
        'type':'POST',
        'url': '/user/manage/getSiteSettingInfo',
        'cache': false,
        'dataType':'json',
        'data': 'SiteSettings[name]=' + name + '&SiteSettings[module]=' + module,
        'beforeSend': function()
        {
            spinner.show();
        },
        'success': function(data, textStatus, jqXHR)
        {
            if (data.status == '1')
            {
                form.find('#SiteSettings_id').val(data.settingInfo.id);
                form.find('#SiteSettings_name').val(data.settingInfo.name);
                form.find('#SiteSettings_module').val(data.settingInfo.module);
                form.find('#SiteSettings_data_type').val(data.settingInfo.data_type);
                form.find('#SiteSettings_default_value').val(data.settingInfo.default_value);
                form.find('#SiteSettings_auth_item').val(data.settingInfo.auth_item);
                form.find('#SiteSettings_en_label').val(data.settingInfo.en_label);
                form.find('#SiteSettings_en_hint').val(data.settingInfo.en_hint);
                form.find('#SiteSettings_on_login').val(data.settingInfo.on_login);

                if (data.settingInfo.on_login == 1)
                {
                    form.find('#SiteSettings_on_login').attr('checked', 'checked');
                }

                msgBox.find('.msg').append(form);
                form.show();
                form.find('#SiteSettings_name').focus();
                msgBox.trigger('centralize');
            }
            else{
                msgBox.find('.msg').append(data.message);
            }
        },
        'complete': function()
        {
            spinner.remove();
        }
    });
}

function addSiteSettings()
{
    var msgBox = $('#msgBox'),
        submitButton = msgBox.find('.buttons .ok'),
        form = msgBox.find('#site-settings-form');

    submitButton.hide();

    form.submit();
    return false;
}

function deleteSiteSettings($this)
{
    showMessage(UIMessages["confirm"], UIMessages["confirmMessage"], UIMessages["yes"], function(){ doDeleteSiteSettings($this) }, UIMessages["cancel"]);
}

function doDeleteSiteSettings($this)
{
    var cells = $this.parent().siblings(),
        name = cells.eq(1).text(),
        module = cells.eq(2).text();

    $.ajax({
        'type':'POST',
        'url': '/user/manage/deleteSiteSetting',
        'cache': false,
        'dataType':'json',
        'data': 'SiteSettings[name]=' + name + '&SiteSettings[module]=' + module,
        'success': function(data, textStatus, jqXHR)
        {
            if (data.status == '1')
            {
                cells.eq(0).parent().remove();
            }
            else{
                showMessage(UIMessages['confirm'], data.message, UIMessages['close']);
            }
        }
    });
}