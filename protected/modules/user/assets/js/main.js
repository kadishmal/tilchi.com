function enableProfileEdit()
{
    var userSettings = $('.settings :checkbox');

    if (userSettings.length)
    {
        userSettings.change(function()
        {
            jQuery.ajax({
                'type':'POST',
                'url': '/user/profile/setSettings',
                'cache': true,
                'dataType':'json',
                'data': 'User[t]=' + this.id + '&User[v]=' + this.checked,
                'success': function(data, textStatus, jqXHR)
                {
                    if (data.status != '1')
                    {
                        this.checked = !this.checked;
                    }
                }
            });
        });
    }
}

function enableTabs(tabContainerId)
{
    var tabContainer = $('#' + tabContainerId),
        tabs = tabContainer.find('.tabs > span'),
        tabContents = tabContainer.find('.tabContents div'),
        active = tabs.filter('.active');

    tabs.click(function()
    {
        var $this = $(this);

        if (active != $this)
        {
            if (active)
            {
                active.removeClass();
                tabContents.filter('#' + active.attr('id')).removeClass('active');
            }

            active = $this;
            active.addClass('active');
            tabContents.filter('#' + active.attr('id')).addClass('active');
        }
    });
}