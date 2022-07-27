jQuery(document).ready(function ($) {
    function kitalabel_check_can_download_all() {
        var items = $('.kitalabel-order-item');
        var can_download_all = true;
        items.each(function() {
            var has_pdf = $( this ).data('has-pdf');
            if(has_pdf == 0) {
                can_download_all = false;
            }
        });
        $('.kitalabel-download-pdf-all').prop('disabled', !can_download_all);
    }
    $('.kitalabel-convert-pdf-all').on('click', function(e) {
        e.preventDefault();
        var items = $('.kitalabel-order-item');
        items.each(async function() {
            var item_key = $( this ).data('item-key');
            var has_pdf = $( this ).data('has-pdf');
            if(!has_pdf) {
                await jQuery.ajax({
                    type: "post",
                    dataType: "json",
                    url: kitalabel_frontend.url,
                    data: {
                        action: "kitalbel_convert_pdf_item",
                        item_key: item_key,
                    },
                    context: this,
                    beforeSend: function () {
                        $(this).find('.kitalabel-create').toggleClass('active');
                        $(this).find('.kitalabel-load').toggleClass('active');
                    },
                    success: function (response) {
                        $(this).find('.kitalabel-create').toggleClass('active');
                        $(this).find('.kitalabel-load').toggleClass('active');
                        if (response.data && response.data.created) {
                            $(this).parent().parent().find('.kitalabel-has-pdf').toggleClass('active');
                            $(this).parent().parent().find('.kitalabel-no-pdf').toggleClass('active');
                            $( this ).data('has-pdf', 1);
                            $(this).prop('disabled', true);
                        }
                        kitalabel_check_can_download_all();
                    },
                });
            }

        });
    })

    $('.kitalabel-order-item').on('click', function(e) {
        e.preventDefault();
        var item_key = $( this ).data('item-key');
        var has_pdf = $( this ).data('has-pdf');
        if(!has_pdf) {
            jQuery.ajax({
                type: "post",
                dataType: "json",
                url: kitalabel_frontend.url,
                data: {
                    action: "kitalbel_convert_pdf_item",
                    item_key: item_key,
                },
                context: this,
                beforeSend: function () {
                    $(this).find('.kitalabel-create').toggleClass('active');
                    $(this).find('.kitalabel-load').toggleClass('active');
                },
                success: function (response) {
                    $(this).find('.kitalabel-create').toggleClass('active');
                    $(this).find('.kitalabel-load').toggleClass('active');
                    if (response.data && response.data.created) {
                        $(this).parent().parent().find('.kitalabel-has-pdf').toggleClass('active');
                        $(this).parent().parent().find('.kitalabel-no-pdf').toggleClass('active');
                        $( this ).data('has-pdf', 1);
                        $(this).prop('disabled', true);
                    }
                    kitalabel_check_can_download_all();
                },
            });
        }
    })

    $('.kitalabel-download-pdf-all').on('click', function(e) {
        // e.preventDefault();
        // jQuery.ajax({
        //     type: "post",
        //     dataType: "json",
        //     url: kitalabel_frontend.url,
        //     data: {
        //         action: "kitalabel_download_all",
        //     },
        //     context: this,
        //     beforeSend: function () {
        //         $(this).find('.kitalabel-create').toggleClass('active');
        //         $(this).find('.kitalabel-load').toggleClass('active');
        //     },
        //     success: function (response) {
        //         $(this).find('.kitalabel-create').toggleClass('active');
        //         $(this).find('.kitalabel-load').toggleClass('active');
        //         if (response.data && response.data.created) {
        //             $(this).parent().parent().find('.kitalabel-has-pdf').toggleClass('active');
        //             $(this).parent().parent().find('.kitalabel-no-pdf').toggleClass('active');
        //             $( this ).data('has-pdf', 1);
        //             $(this).prop('disabled', true);
        //         }
        //         kitalabel_check_can_download_all();
        //     },
        // });
    })
});