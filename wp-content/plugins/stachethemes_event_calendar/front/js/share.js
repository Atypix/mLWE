;
(function ($) {

    'use strict';

    $(function () {

        window.stecSharer = function ($instance, glob, helper, url, id, repeatOffset) {

            this.$instance = $instance;
            this.glob = glob;
            this.$share = null;
            this.url = url;
            this.id = id;
            this.repeatOffset = repeatOffset;

            this.init = function () {
                var parent = this;
                $('.stec-share').not('.stec-share-template').remove();
                this.$share = $(glob.template.share).appendTo('body');
                this.$share.removeClass('stec-share-template');
                this.$share.find('input[name=stec_permalink]').val(url);
                this.getEmbed(id, this.$share.find('input[name=stec_embed_width]').val(), function (embedCode) {
                    parent.$share.find('textarea[name=stec_embed]').text(embedCode);
                });

                this.open();
            };

            this.open = function () {

            };

            this.close = function () {
                this.$share.remove();
            };

            this.getEmbed = function (eventid, width, callback) {
                var frameHeight = 0;
                var frameWidth = width ? width : 400;
                var embedHtml = '';

                var embed = '<iframe src="' + window.resturl + 'stec/v2/get/embed/' + eventid + '/' + repeatOffset + '" width="' + frameWidth + '" frameborder=0></iframe>';

                var $iframe = $(embed).css({
                    position: 'fixed',
                    left: -9999,
                    visibility: 'hidden'
                }).appendTo('body');

                $iframe.on('load', function () {
                    frameHeight = $(this).contents().outerHeight(true);
                    $iframe.remove();
                    embedHtml = ('<iframe src="' + window.resturl + 'stec/v2/get/embed/' + eventid + '/' + repeatOffset + '" height="' + frameHeight + '" width="' + frameWidth + '" frameborder=0></iframe>');
                    callback(embedHtml);
                });
            };

            this.Controller = function () {

                var parent = this;

                this.$share.on(helper.clickHandle(), function () {
                    parent.close();
                });

                this.$share.find('.stec-share-close').on(helper.clickHandle(), function () {
                    parent.close();
                });

                this.$share.find('.stec-share-block').on(helper.clickHandle(), function (e) {
                    e.stopPropagation();
                });

                this.$share.find('input[name=stec_embed_width]').on('change', function () {
                    parent.getEmbed(id, $(this).val(), function (embedCode) {
                        parent.$share.find('textarea[name=stec_embed]').text(embedCode);
                    });
                });

                this.$share.find('button[data-copy-permalink]').on(helper.clickHandle(), function () {
                    var linkURL = parent.$share.find('input[name=stec_permalink]').val();
                    var $temp = $("<input>");
                    $("body").append($temp);
                    $temp.val(linkURL).select();
                    document.execCommand("copy");
                    $temp.remove();
                    alert(window.stecLang.copiedToClipboard);
                });

                this.$share.find('button[data-copy-embed]').on(helper.clickHandle(), function () {
                    var embedCode = parent.$share.find('textarea[name=stec_embed]').text();
                    var $temp = $("<input>");
                    $("body").append($temp);
                    $temp.val(embedCode).select();
                    document.execCommand("copy");
                    $temp.remove();
                    alert(window.stecLang.copiedToClipboard);
                });

            };

            this.init();
            this.Controller();
        };


    });

})(jQuery);