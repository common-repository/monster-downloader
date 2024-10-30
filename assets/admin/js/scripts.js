/**
 * Front Script
 */

(function ($, window, document, pluginObject) {
    "use strict";

    let getUrlParameter = function getUrlParameter(sParam) {
            let sPageURL = window.location.search.substring(1),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                }
            }
            return false;
        },
        getCurrentPage = function getCurrentpage() {
            return window.location.pathname.split('/').at(-1);
        };


    $(document).on('focus', '.theme-overlay', function (e) {

        if ('themes.php' !== getCurrentPage()) {
            return;
        }

        let themeActions = $(e.target).find('.theme-actions'),
            themeActionsActive = themeActions.find('.active-theme'),
            themeActionsInActive = themeActions.find('.inactive-theme'),
            activeTheme = getUrlParameter('theme'),
            themeDownloadLink = pluginObject.themeDownloadLink.replace('object_name', activeTheme),
            themeDownloadBtn = '<a class="button" href="' + themeDownloadLink + '">' + pluginObject.themeDownloadText + '</a>';

        if (activeTheme.length > 0) {
            themeActionsActive.append(themeDownloadBtn);
            themeActionsInActive.append(themeDownloadBtn);
        }
    });


    $(document).on('ready', function () {

        if ('themes.php' !== getCurrentPage()) {
            return;
        }
        setTimeout(function () {

            let themesAll = $('.themes'),
                themeDownloadBtnSample = '<a class="button" href="' + pluginObject.themeDownloadLink + '">' + pluginObject.themeDownloadText + '</a>';

            themesAll.find('> .theme').each(function () {
                let themeSlug = $(this).data('slug');

                if (typeof themeSlug !== 'undefined') {
                    $(this).find('.theme-id-container .theme-actions').append(themeDownloadBtnSample.replace('object_name', themeSlug));
                }
            });
        }, 50);
    });

})(jQuery, window, document, monsterDownload);