jQuery(document).ready(function ($) {
    $('.kitalabel-convert-pdf-all').on('click', function(e) {
        e.preventDefault();
        var items = $('.kitalabel-order-item');
        var listKey = [];
        items.each( function() {
          var item_key = $( this ).data('item-key');
          listKey.push(item_key);
        });
        jQuery.ajax({
          type: "post",
          dataType: "json",
          url: kitalabel_frontend.url,
          data: {
            action: "kitalbel_convert_pdf_all",
            items_key: listKey,
          },
          context: this,
          beforeSend: function () {
            // printcart_trigger_button_design(true);
          },
          success: function (response) {
            if (response.success && response.data) {
              jQuery("#printcart-design-tool-sdk-wrap").append(response.data);
              // printcart_trigger_button_design();
            } else {
              jQuery("#printcart-design-tool-sdk").remove();
              jQuery("body .printcart-button-design-loading").remove();
            }
          },
        });
    })
    $('.kitalabel-download-pdf-all').on('click', function(e) {
        e.preventDefault();
        console.log(123);
    })
});