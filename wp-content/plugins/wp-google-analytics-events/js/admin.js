if (document.location.href.indexOf("wp-google-analytics-events") > -1) {

    jQuery.widget.bridge('gaetooltip', jQuery.ui.tooltip);

    jQuery('.ga-tooltip').gaetooltip({
        position: {
            my: "left bottom-10", at: "right top", collision: "none"
        }
    });

    jQuery('.remove').click(function (event) {
        event.preventDefault();
        jQuery(this).closest('tr').remove();
    });

    jQuery('.btn_upload').on('click', function (e) {
        jQuery('.settings_content').slideDown();
        e.preventDefault();
    });

    jQuery('.btn_close').on('click', function (e) {
        jQuery('.settings_content').slideUp();
        e.preventDefault();
    });

    jQuery('.popup').on('click', function (e) {
        jQuery('.popup').slideUp();
        e.preventDefault();
    });

    if (jQuery('#snippet').is(":checked")) {
        jQuery('#anonymizeip')[0].checked = false;
        jQuery('#anonymizeip').attr("disabled", true);
    }

    jQuery('#snippet').change(function () {
        if (this.checked) {
            jQuery('#anonymizeip')[0].checked = false;
            jQuery('#anonymizeip').attr("disabled", true);
        } else {
            jQuery('#anonymizeip').removeAttr("disabled");
        }
    });

    jQuery('#advanced:checkbox').change(function () {
        var checked = jQuery(this).is(':checked');
        if (checked) {
            if (!confirm('Advanced mode allows you to use jQuery selectors for click and scroll events. Enabling this feature and creating advanced events could cause errors on your site if misconfigured. \n\nAre you sure? ')) {
                jQuery(this).removeAttr('checked');
            }
        }
    });

    if (jQuery('#gtm').is(":checked")) {
        jQuery('#anonymizeip')[0].checked = false;
        jQuery('#anonymizeip').attr("disabled", true);
        jQuery('#snippet')[0].checked = true;
        jQuery('#snippet').attr("disabled", true);
        jQuery('#gst')[0].checked = false;
        jQuery('#gst').attr("disabled", true);
    }

    if (jQuery('#gst').is(":checked")) {
        jQuery('#anonymizeip')[0].checked = false;
        jQuery('#anonymizeip').attr("disabled", true);
        jQuery('#snippet')[0].checked = true;
        jQuery('#snippet').attr("disabled", true);
        jQuery('#gtm')[0].checked = false;
        jQuery('#gtm').attr("disabled", true);
    }

    jQuery('#gtm').change(function () {
        if (this.checked) {
            jQuery('#anonymizeip')[0].checked = false;
            jQuery('#anonymizeip').attr("disabled", true);
            jQuery('#snippet')[0].checked = true;
            jQuery('#snippet').attr("disabled", true);
            jQuery('#gst')[0].checked = false;
            jQuery('#gst').attr("disabled", true);
        } else {
            jQuery('#anonymizeip').removeAttr("disabled");
            jQuery('#snippet').removeAttr("disabled");
            jQuery('#gst').attr("disabled", false);
        }
    });
    jQuery('#gst').change(function () {
        if (this.checked) {
            jQuery('#anonymizeip')[0].checked = false;
            jQuery('#anonymizeip').attr("disabled", true);
            jQuery('#snippet')[0].checked = true;
            jQuery('#snippet').attr("disabled", true);
            jQuery('#gtm')[0].checked = false;
            jQuery('#gtm').attr("disabled", true);
        } else {
            jQuery('#anonymizeip').removeAttr("disabled");
            jQuery('#snippet').removeAttr("disabled");
            jQuery('#gtm').attr("disabled", false);
        }
    });
}
