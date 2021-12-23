jQuery('#import-video').click(function ($) {
    jQuery('#import-video').css('opacity', '0.4');
    jQuery('.load-check').css('opacity', '1');
    jQuery.ajax({
        type: "POST",
        url: ajax_object.ajax_url,
        dataType: 'html',
        data: { action: 'inert_video' },
        success: function (response) {
            // alert(response);
            location.reload(true);
            console.log(response);
            jQuery('#import-video').css('opacity', '1');
            jQuery('.load-check').css('opacity', '0');
        }
    });
});