function showLoading() {
    // remove existing loaders
    jQuery('.wpmf-loading-container').remove();
    jQuery('<div id="orrsLoader" class="wpmf-loading-container"><div><div class="mdl-spinner mdl-js-spinner is-active"></div></div></div>').appendTo("body");

    componentHandler.upgradeElements(jQuery('.mdl-spinner').get());
    setTimeout(function () {
        jQuery('#orrsLoader').css({opacity: 1});
    }, 1);
}

function hideLoading() {
    jQuery('#orrsLoader').css({opacity: 0});
    setTimeout(function () {
        jQuery('#orrsLoader').remove();
    }, 400);
}

function showDialog(options) {
    options = jQuery.extend({
        id: 'orrsDiag',
        title: null,
        text: null,
        neutral: false,
        negative: false,
        positive: false,
        cancelable: true,
        contentStyle: null,
        onLoaded: false,
        hideOther: true,
        closeicon:false,
        question: false,
        question_text: ''
    }, options);

    if (options.hideOther) {
        // remove existing dialogs
        jQuery('.wpmf-dialog-container').remove();
        jQuery(document).unbind("keyup.dialog");
    }

    jQuery('<div id="' + options.id + '" class="wpmf-dialog-container"><div class="mdl-card mdl-shadow--16dp" id="' + options.id + '_content"></div></div>').appendTo("body");

    var dialog = jQuery('#' + options.id);
    var content = dialog.find('.mdl-card');
    if(options.closeicon){
        jQuery('<i class="material-icons wpmfclosedlg">clear</i>').appendTo(content);
    }
    if (options.contentStyle != null) content.css(options.contentStyle);
    var header = '<div class="mdl-header">';
    if (options.title != null) {
        header += '<h5>' + options.title + '</h5>';
    }

    if (options.help_icon != null) {
        header += options.help_icon;
    }
    header += '</div>';
    if (options.title != null || options.help_icon != null) {
        jQuery(header).appendTo(content);
    }
    if (options.text != null) {
        jQuery('<div class="wpmf-dialog-text">' + options.text + '</div>').appendTo(content);
    }
    if (options.neutral || options.negative || options.positive) {
        var buttonBar = jQuery('<div class="mdl-card__actions dialog-button-bar"></div>');
        if (options.neutral) {
            options.neutral = jQuery.extend({
                id: 'neutral',
                title: 'Neutral',
                onClick: null
            }, options.neutral);
            var neuButton = jQuery('<button class="mdl-button mdl-js-button mdl-js-ripple-effect" id="' + options.neutral.id + '">' + options.neutral.title + '</button>');
            neuButton.click(function (e) {
                e.preventDefault();
                if (options.neutral.onClick == null || !options.neutral.onClick(e))
                    hideDialog(dialog)
            });
            neuButton.appendTo(buttonBar);
        }
        if (options.negative) {
            options.negative = jQuery.extend({
                id: 'negative',
                title: 'Cancel',
                onClick: null
            }, options.negative);
            var negButton = jQuery('<button class="mdl-button mdl-js-button mdl-js-ripple-effect" id="' + options.negative.id + '">' + options.negative.title + '</button>');
            negButton.click(function (e) {
                e.preventDefault();
                if (options.negative.onClick == null || !options.negative.onClick(e))
                    hideDialog(dialog)
            });
            negButton.appendTo(buttonBar);
        }
        if (options.positive) {
            options.positive = jQuery.extend({
                id: 'positive',
                title: 'OK',
                onClick: null
            }, options.positive);
            var posButton = jQuery('<button class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" id="' + options.positive.id + '">' + options.positive.title + '</button>');
            posButton.click(function (e) {
                e.preventDefault();
                if (options.positive.onClick == null || !options.positive.onClick(e))
                    hideDialog(dialog)
            });
            posButton.appendTo(buttonBar);
        }
        buttonBar.appendTo(content);
    }
    componentHandler.upgradeDom();
    if (options.cancelable) {
        dialog.click(function () {
            hideDialog(dialog, options.question, options.question_text);
        });
        jQuery(document).bind("keyup.dialog", function (e) {
            if (e.which == 27)
                hideDialog(dialog);
        });

        content.click(function (e) {
            e.stopPropagation();
        });
    }

    jQuery('.wpmfclosedlg').click(function () {
        hideDialog(dialog);
    });
    setTimeout(function () {
        dialog.css({opacity: 1});
        if (options.onLoaded)
            options.onLoaded();
    }, 1);

    var this_url = new URL(location.href);
    var get_post = this_url.searchParams.get("post");
    if (get_post) {
        var modal_dialog_remote_video = document.getElementById("wpmf-add-video-dialog");
        if (modal_dialog_remote_video) {
            modal_dialog_remote_video.setAttribute('style', 'z-index:999999999 !important');
        }
    }
}

function hideDialog(dialog, question = false, text = '') {
    jQuery(document).unbind("keyup.dialog");
    if (!question) {
        dialog.css({opacity: 0});
        setTimeout(function () {
            dialog.remove();
        }, 400);
    } else {
        showDialog({
            id: 'question-dialog',
            text: text,
            hideOther: false,
            negative: {
                title: 'No'
            },
            positive: {
                title: 'Yes',
                onClick: function () {
                    dialog.css({opacity: 0});
                    setTimeout(function () {
                        dialog.remove();
                    }, 400);
                }
            }
        });
    }
}