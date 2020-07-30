var ls = ls || {};

/**
 * Всплывающие поп-апы
 */
ls.infobox = (function ($) {

    this.showInfoBlog = function(oLink,iBlogId) {
        if (!$(oLink).hasClass('tooltipstered')) {
            $(oLink).tooltipster({
                trigger: 'click',
                theme: 'tooltipster-shadow',
                contentAsHTML: true,
                side: 'right',
                interactive: true,
                content: 'Loading...'
            });
            var url = aRouter['ajax']+'infobox/info/blog/';
            var params = {iBlogId: iBlogId};
            ls.ajax(url, params, function(result) {
                if (result.bStateError) {
                    ls.msg.error(null, result.sMsg);
                    this.hide(oLink);
                } else {
                    $(oLink).tooltipster('content', result.sText).tooltipster('open');
                    ls.hook.run('ls_infobox_show_info_blog_after',[oLink, iBlogId, result]);
                }
            }.bind(this));
        } else {
            $(oLink).tooltipster('open');
        }
        return false;
    };

	return this;
}).call(ls.infobox || {},jQuery);