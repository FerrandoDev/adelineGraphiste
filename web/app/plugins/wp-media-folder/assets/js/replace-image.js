var wpmfReplaceModule;
(function ($) {
    if (typeof ajaxurl === "undefined") {
        ajaxurl = wpmfParams.vars.ajaxurl;
    }

    wpmfReplaceModule = {
        /**
         * Init event
         */
        doEvent: function () {
            /* When change input file value */
            $('#wpmf_upload_input_version').off('change').on('change', function (event) {
                /* submit form upload */
                $('#wpmf_form_upload').submit();
            });

            /* submit form upload */
            $('.wpmf_submit_upload').off('click').on('click', function (event) {
                $('#wpmf_upload_input_version').click();
            });
        },
        /**
         * Create form replace
         * @param id id of attachment
         * @returns {string}
         */
        genFormReplace: function (id) {
            $('.replace_wrap').remove();
            var form_replace = '<div class="replace_wrap">';
            form_replace += '<form id="wpmf_form_upload" method="post" action="' + wpmfParams.vars.ajaxurl + '" enctype="multipart/form-data">';
            form_replace += '<input type="hidden" name="wpmf_nonce" value="' + wpmfParams.vars.wpmf_nonce + '">';
            form_replace += '<input class="hide" type="file" name="wpmf_replace_file" id="wpmf_upload_input_version" style="display: none"><input type="button" value="' + wpmfParams.l18n.replace + '" class="button-primary wpmf_submit_upload" id="submit-upload"/>';
            form_replace += '<input type="hidden" name="action" value="wpmf_replace_file">';
            form_replace += '<input type="hidden" name="post_selected" value="' + id + '">';
            form_replace += '</form>';
            form_replace += '</div>';
            form_replace += '<style>#post ~ .replace_wrap {display: none}</style>';
            return form_replace;
        },

        /**
         * Replace attachment
         * @param attachmentID
         */
        replace_attachment: function (attachmentID) {
            var $snack = '';
            $('#wpmf_form_upload').ajaxForm({
                uploadProgress: function (event, position, total, wpmf_percentComplete) {
                    if (!$('.wpmf_replace_process').length) {
                        $snack = wpmfSnackbarModule.show({
                            id : 'wpmf_replace_process',
                            content : wpmfParams.l18n.file_uploading,
                            auto_close : false,
                            is_progress : true
                        });
                    }
                },
                success: function () {
                    wpmfSnackbarModule.close('wpmf_replace_process');
                },
                complete: function (xhr) {
                    $('#wpmf_progress').hide();
                    var ob = JSON.parse(xhr.responseText);
                    if (typeof xhr.responseText !== "undefined") {
                        if (ob.status) {
                            $('.file-size').html('<strong>' + wpmfParams.l18n.filesize_label + ' ' + ob.size + '</strong>');
                            if (typeof ob.dimensions !== "undefined") {
                                $('.dimensions').html('<strong>' + wpmfParams.l18n.dimensions_label + ' ' + ob.dimensions + '</strong>');
                            }
                            var d = new Date();
                            var n = d.getTime();
                            if (wpmfParams.vars.wpmf_pagenow !== 'post.php') {
                                if (typeof wpmfFoldersModule.hover_images[attachmentID] !== "undefined") {
                                    var url = wpmfFoldersModule.hover_images[attachmentID].wpmfurl;
                                    wpmfFoldersModule.hover_images[attachmentID].wpmfurl = url + '?ver=' + n;
                                }
                            }

                            // Show snackbar
                            wpmfSnackbarModule.show({
                                id: 'replace_file',
                                content: wpmfParams.l18n.wpmf_file_replace
                            });

                            var $thumb;
                            if (wpmfParams.vars.wpmf_pagenow !== 'post.php') {
                                $thumb = $('.attachment[data-id="' + attachmentID + '"] .thumbnail').find('img');
                            } else {
                                $thumb = $('#thumbnail-head-' + attachmentID + ' img');
                            }



                            if (wpmfParams.vars.wpmf_pagenow === 'post.php') {
                                var old_url = $thumb.attr('src');
                                $('#thumbnail-head-' + attachmentID + ' img').attr('src', old_url + '?ver=' + n);
                            } else {
                                var src_thumbnail = $thumb.attr('src');
                                var $img = $('.attachment-details').find('.thumbnail img');
                                var src_detail = $img.attr('src');
                                if (ob.cloud_file_type === 'google_drive' || ob.cloud_file_type === 'onedrive_business' || ob.cloud_file_type === 'nextcloud') {
                                    $thumb.attr('src', src_thumbnail + '&ver=' + n);
                                    $img.attr('src', src_detail + '&ver=' + n);
                                } else {
                                    $thumb.attr('src', src_thumbnail + '?ver=' + n);
                                    $img.attr('src', src_detail + '?ver=' + n);
                                }
                                /* clear cache img */
                                wpmfReplaceModule.forceImgReload(src_thumbnail, false, null, false);
                                wpmfReplaceModule.forceImgReload(src_detail, false, null, false);
                            }
                        } else {
                            alert(ob.msg);
                        }
                    }
                }
            });
        },

        /**
         * read http://stackoverflow.com/questions/1077041/refresh-image-with-a-new-one-at-the-same-url#answer-22429796
         * @param src
         * @returns {Array}
         */
        imgReloadBlank: function (src) {
            // ##### Everything here is provisional on the way the pages are designed, and what images they contain; what follows is for example purposes only!
            // ##### For really simple pages containing just a single image that's always the one being refreshed, this function could be as simple as just the one line:
            // ##### document.getElementById("myImage").src = "/img/1x1blank.gif";

            var blankList = [],
                fullSrc = src /* Fully qualified (absolute) src - i.e. prepend protocol, server/domain, and path if not present in src */,
                imgs, img, i;

            //foreach (/* window accessible from this one, i.e. this window, and child frames/iframes, the parent window, anything opened via window.open(), and anything recursively reachable from there */)
            //{
            // get list of matching images:
            imgs = window.document.body.getElementsByTagName("img");
            for (i = imgs.length; i--;)   // could instead use body.querySelectorAll(), to check both tag name and src attribute, which would probably be more efficient, where supported
            {
                if ((img = imgs[i]).src === fullSrc) {
                    img.src = "/img/1x1blank.gif";  // blank them
                    blankList.push(img);            // optionally, save list of blanked images to make restoring easy later on
                }

            }
            //}


            // ##### If necessary, do something here that tells all accessible windows not to create any *new* images with src===fullSrc, until further notice,
            // ##### (or perhaps to create them initially blank instead and add them to blankList).
            // ##### For example, you might have (say) a global object window.top.blankedSrces as a propery of your topmost window, initially set = {}.  Then you could do:
            // #####
            // #####     var bs = window.top.blankedSrces;
            // #####     if (bs.hasOwnProperty(src)) bs[src]++; else bs[src] = 1;
            // #####
            // ##### And before creating a new image using javascript, you'd first ensure that (blankedSrces.hasOwnProperty(src)) was false...
            // ##### Note that incrementing a counter here rather than just setting a flag allows for the possibility that multiple forced-reloads of the same image are underway at once, or are overlapping.

            return blankList;   // optional - only if using blankList for restoring back the blanked images!  This just gets passed in to imgReloadRestore(), it isn't used otherwise.
        },

        /**
         * This function restores all blanked images, that were blanked out by imgReloadBlank(src) for the matching src argument.
         * You should code the actual contents of this function according to your page design, and what images there are on them, as well as how/if images are dimensioned, etc!!! #####
         * @param src
         * @param blankList
         * @param imgDim
         * @param loadError
         */
        imgReloadRestore: function (src, blankList, imgDim, loadError) {
            // ##### Everything here is provisional on the way the pages are designed, and what images they contain; what follows is for example purposes only!
            // ##### For really simple pages containing just a single image that's always the one being refreshed, this function could be as simple as just the one line:
            // ##### document.getElementById("myImage").src = src;

            // ##### if in imgReloadBlank() you did something to tell all accessible windows not to create any *new* images with src===fullSrc until further notice, retract that setting now!
            // ##### For example, if you used the global object window.top.blankedSrces as described there, then you could do:
            // #####
            // #####     var bs = window.top.blankedSrces;
            // #####     if (bs.hasOwnProperty(src)&&--bs[src]) return; else delete bs[src];  // return here means don't restore until ALL forced reloads complete.

            var i, img, width = imgDim && imgDim[0], height = imgDim && imgDim[1];
            if (width) width += "px";
            if (height) height += "px";

            if (loadError) {/* If you want, do something about an image that couldn't load, e.g: src = "/img/brokenImg.jpg"; or alert("Couldn't refresh image from server!"); */
            }

            // If you saved & returned blankList in imgReloadBlank(), you can just use this to restore:

            for (i = blankList.length; i--;) {
                (img = blankList[i]).src = src;
                if (width) img.style.width = width;
                if (height) img.style.height = height;
            }
        },

        /**
         * Force an image to be reloaded from the server, bypassing/refreshing the cache.
         * due to limitations of the browser API, this actually requires TWO load attempts - an initial load into a hidden iframe, and then a call to iframe.contentWindow.location.reload(true);
         * If image is from a different domain (i.e. cross-domain restrictions are in effect, you must set isCrossDomain = true, or the script will crash!
         * imgDim is a 2-element array containing the image x and y dimensions, or it may be omitted or null; it can be used to set a new image size at the same time the image is updated, if applicable.
         * if "twostage" is true, the first load will occur immediately, and the return value will be a function
         * that takes a boolean parameter (true to proceed with the 2nd load (including the blank-and-reload procedure), false to cancel) and an optional updated imgDim.
         * This allows you to do the first load early... for example during an upload (to the server) of the image you want to (then) refresh.
         * @param src
         * @param isCrossDomain
         * @param imgDim
         * @param twostage
         * @returns {*}
         */
        forceImgReload: function (src, isCrossDomain, imgDim, twostage) {
            var blankList, step = 0,                                // step: 0 - started initial load, 1 - wait before proceeding (twostage mode only), 2 - started forced reload, 3 - cancelled
                iframe = window.document.createElement("iframe"),   // Hidden iframe, in which to perform the load+reload.
                loadCallback = function (e)                          // Callback function, called after iframe load+reload completes (or fails).
                {                                                   // Will be called TWICE unless twostage-mode process is cancelled. (Once after load, once after reload).
                    if (!step)  // initial load just completed.  Note that it doesn't actually matter if this load succeeded or not!
                    {
                        if (twostage) step = 1;  // wait for twostage-mode proceed or cancel; don't do anything else just yet
                        else {
                            step = 2;
                            blankList = wpmfReplaceModule.imgReloadBlank(src);
                            iframe.contentWindow.location.reload(true);
                        }  // initiate forced-reload
                    }
                    else if (step === 2)   // forced re-load is done
                    {
                        wpmfReplaceModule.imgReloadRestore(src, blankList, imgDim, (e || window.event).type === "error");    // last parameter checks whether loadCallback was called from the "load" or the "error" event.
                        if (iframe.parentNode) iframe.parentNode.removeChild(iframe);
                    }
                };
            iframe.style.display = "none";
            window.parent.document.body.appendChild(iframe);    // NOTE: if this is done AFTER setting src, Firefox MAY fail to fire the load event!
            iframe.addEventListener("load", loadCallback, false);
            iframe.addEventListener("error", loadCallback, false);
            iframe.src = (isCrossDomain ? "/echoimg.php?src=" + encodeURIComponent(src) : src);  // If src is cross-domain, script will crash unless we embed the image in a same-domain html page (using server-side script)!!!
            return (twostage
                ? function (proceed, dim) {
                    if (!twostage) return;
                    twostage = false;
                    if (proceed) {
                        imgDim = (dim || imgDim);  // overwrite imgDim passed in to forceImgReload() - just in case you know the correct img dimensions now, but didn't when forceImgReload() was called.
                        if (step === 1) {
                            step = 2;
                            blankList = wpmfReplaceModule.imgReloadBlank(src);
                            iframe.contentWindow.location.reload(true);
                        }
                    }
                    else {
                        step = 3;
                        if (iframe.contentWindow.stop) iframe.contentWindow.stop();
                        if (iframe.parentNode) iframe.parentNode.removeChild(iframe);
                    }
                }
                : null);
        }

    };

    $(document).ready(function () {
        if (wpmfParams.vars.wpmf_pagenow === 'upload.php' && typeof wpmfParams.vars.override !== 'undefined' && parseInt(wpmfParams.vars.override) === 1) {
            // add Replace button on list mode
            $('.wpmf-action-attachment').each(function(i, obj) {
                const id = $(this).attr('data-id');
                const form_replace = document.createElement('div');
                var this_url = new URL(location.href);
                form_replace.innerHTML = 
                `<form id="wpmf_form_upload_` + id + `" method="post" class="wpmf_form_upload" data-id="` + id + `" action="` + wpmfParams.vars.ajaxurl + `" enctype="multipart/form-data">
                    <input type="hidden" name="wpmf_nonce" value="` + wpmfParams.vars.wpmf_nonce + `">
                    <input class="hide" type="file" name="wpmf_replace_file" id="wpmf_upload_input_version_` + id +`" style="display: none"><input type="button" value="` + wpmfParams.l18n.replace + `" class="button-primary wpmf_submit_upload" data-id="` + id + `"/>
                    <input type="hidden" name="mode_list" value="1">
                    <input type="hidden" name="this_url" value="` + this_url + `">
                    <input type="hidden" name="action" value="wpmf_replace_file">
                    <input type="hidden" name="post_selected" value="` + id + `">
                </form>`;
                obj.appendChild(form_replace);
            });

            // select file replace
            $('.wpmf-action-attachment .wpmf_submit_upload').off('click').on('click', function (event) {
                var id = $(this).attr('data-id');
                if (id) {
                    var element_input_upload = '#wpmf_upload_input_version_' + id;
                    $(element_input_upload).click();
                    /* submit form upload */
                    $(element_input_upload).off('change').on('change', function (event) {
                        var form_submit = '#wpmf_form_upload_' + id;
                        $(form_submit).submit();
                    });
                }
            });

            // show notification when replace file
            var msgReplaceFile = wpmfFoldersModule.getCookie('msgReplaceFile_' + wpmf.vars.host);
            if (msgReplaceFile) {
                wpmfSnackbarModule.show({
                    id: 'replace_file',
                    content: decodeURIComponent(msgReplaceFile),
                });
                wpmfFoldersModule.setCookie('msgReplaceFile_' + wpmf.vars.host, '', 365);
            }
        }
        
        if (wpmfParams.vars.wpmf_pagenow === 'post.php') {
            if (!$('.wpmf_replace_btn').length) {
                $('.wp_attachment_image').append('<p><input type="button" value="'+ wpmfParams.l18n.replace +'" class="button-primary wpmf_replace_btn"></p>');
                $('.wpmf_replace_btn').on('click', function () {
                    $('.wpmf_submit_upload').click();
                });
                var attachmentID = $('#post_ID').val();
                var form_replace = wpmfReplaceModule.genFormReplace(attachmentID);
                $('#post').after(form_replace);
                wpmfReplaceModule.doEvent();
                wpmfReplaceModule.replace_attachment(attachmentID);
            }
            return;
        }

        if (typeof wp === "undefined") {
            return
        }

        if ((wpmfParams.vars.wpmf_pagenow === 'upload.php' && !wpmfFoldersModule.page_type) || typeof wp.media === "undefined") {
            return;
        }

        if (wpmfFoldersModule.page_type !== 'upload-list') {
            var myreplaceForm = wp.media.view.AttachmentsBrowser;
            if (typeof myreplaceForm !== "undefined") {
                wp.media.view.AttachmentsBrowser = wp.media.view.AttachmentsBrowser.extend({
                    createSingle: function () {
                        myreplaceForm.prototype.createSingle.apply(this, arguments);
                        var sidebar = this.sidebar;
                        var attachmentID = $('.wpmf_attachment_id').val();
                        var form_replace = wpmfReplaceModule.genFormReplace(attachmentID);
                        if (wpmfParams.vars.wpmf_pagenow !== 'upload.php') {
                            if (typeof wpmfParams.vars.override !== 'undefined' && parseInt(wpmfParams.vars.override) === 1) {
                                $('.replace_wrap').remove();
                                $(sidebar.$el).find('.attachment-info .details').append(form_replace);
                            }
                        }
                        wpmfReplaceModule.doEvent();
                        wpmfReplaceModule.replace_attachment(attachmentID);
                    }
                });
            }

            /* Create replace button when wp smush plugin active */
            if (wpmfParams.vars.get_plugin_active.indexOf('wp-smush.php') !== -1) {
                if( 'undefined' !== typeof wp.media.view &&
                    'undefined' !== typeof wp.media.view.Attachment.Details.TwoColumn ) {
                    // Local instance of the Attachment Details TwoColumn used in the edit attachment modal view
                    var wpmfAssignMediaTwoColumn = wp.media.view.Attachment.Details.TwoColumn;

                    /**
                     * Add Smush details to attachment.
                     */
                    if (typeof wpmfAssignMediaTwoColumn !== "undefined") {
                        wp.media.view.Attachment.Details.TwoColumn = wp.media.view.Attachment.Details.TwoColumn.extend({
                            render: function () {
                                // Get Smush status for the image
                                wpmfAssignMediaTwoColumn.prototype.render.apply(this);
                                $( document ).ajaxComplete(function( event, xhr, settings ) {
                                    var data = settings.data;
                                    if (typeof data === 'string') {
                                        if (data.indexOf('smush_get_attachment_details') !== -1) {
                                            var attachmentID = $('.wpmf_attachment_id').val();
                                            var form_replace = wpmfReplaceModule.genFormReplace(attachmentID);
                                            $('.replace_wrap').remove();
                                            $('.details').append(form_replace);
                                            wpmfReplaceModule.doEvent();
                                            wpmfReplaceModule.replace_attachment(attachmentID);
                                        }
                                    }
                                });
                            }
                        });
                    }
                }
            }

            var myReplace = wp.media.view.Modal;
            if (typeof myReplace !== "undefined") {
                wp.media.view.Modal = wp.media.view.Modal.extend({
                    open: function () {
                        myReplace.prototype.open.apply(this, arguments);
                        if (wpmfParams.vars.wpmf_pagenow === 'upload.php') {
                            if (typeof wpmfParams.vars.override !== 'undefined' && parseInt(wpmfParams.vars.override) === 1) {
                                setTimeout(function () {
                                    var attachmentID = $('.wpmf_attachment_id').val();
                                    var form_replace = wpmfReplaceModule.genFormReplace(attachmentID);
                                    $('.replace_wrap').remove();
                                    $('.attachment-details .details').append(form_replace);
                                    wpmfReplaceModule.doEvent();
                                    wpmfReplaceModule.replace_attachment(attachmentID);
                                }, 150);
                            }
                        }
                    }
                });
            }


            if (wpmfParams.vars.wpmf_pagenow === 'upload.php') {
                // create replace button when next and prev media items
                var myReplaceEditAttachments = wp.media.view.MediaFrame.EditAttachments;
                if (typeof myReplaceEditAttachments !== "undefined") {
                    wp.media.view.MediaFrame.EditAttachments = wp.media.view.MediaFrame.EditAttachments.extend({
                        previousMediaItem: function () {
                            /* Create duplicate button setting */
                            myReplaceEditAttachments.prototype.previousMediaItem.apply(this, arguments);
                            if (typeof wpmfParams.vars.override !== 'undefined' && parseInt(wpmfParams.vars.override) === 1) {
                                var attachmentID = $('.wpmf_attachment_id').val();
                                var form_replace = wpmfReplaceModule.genFormReplace(attachmentID);
                                $('.replace_wrap').remove();
                                $('.attachment-details .details').append(form_replace);
                                wpmfReplaceModule.doEvent();
                                wpmfReplaceModule.replace_attachment(attachmentID);
                            }
                        },

                        nextMediaItem: function () {
                            /* Create duplicate button setting */
                            myReplaceEditAttachments.prototype.nextMediaItem.apply(this, arguments);
                            if (typeof wpmfParams.vars.override !== 'undefined' && parseInt(wpmfParams.vars.override) === 1) {
                                var attachmentID = $('.wpmf_attachment_id').val();
                                var form_replace = wpmfReplaceModule.genFormReplace(attachmentID);
                                $('.replace_wrap').remove();
                                $('.attachment-details .details').append(form_replace);
                                wpmfReplaceModule.doEvent();
                                wpmfReplaceModule.replace_attachment(attachmentID);
                            }
                        }
                    });
                }
            }
        }
    });
}(jQuery));