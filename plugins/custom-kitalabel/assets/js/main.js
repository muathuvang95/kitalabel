(function ($) {
    $(document).ready(function() {        
        hoverTemplateTag();
        loadMoreTemplate();
        doUploadTab();
        customInputFile();
        addMaxLengthOrderNotesField();
    });

    function hoverTemplateTag() {
        let templateTagItem     = $('.template-tag-item');
        
        templateTagItem.each(function(i , value) {
            let index = i + 1;
            $(this).hover(function (e) {
                let tagDescriptionWrapper = $(this).find('.tag-description-wrapper');
                if(!$(this).hasClass('show-description')) {
                    let elHeight    = $(this).outerHeight();
                    let desHeight   = tagDescriptionWrapper.outerHeight();
                    // $(this).stop().animate({height : elHeight + desHeight + 'px'}, 250);
                    $(this).css('height', elHeight + desHeight + 'px');
                }
                $(this).addClass('show-description');
            }, function () {
                $(this).css('height', 'auto');
                // $(this).stop().animate({height : elHeight - desHeight + 'px'}, 250);
                $(this).removeClass('show-description');
            });
        });
    }

    function loadMoreTemplate() {
        $('.kita-load-more-btn').on('click', function (e) {
            e.preventDefault();
            let $this                   = $(this);
            let templateTagsWrapper     = $('.template-tags-wrapper');
            let page                    = $this.attr('data-current-page');
            let perPage                 = $this.attr('data-per-page');
            let totalPage               = $this.attr('data-total-page');
            let nextPage                = parseInt(page) + 1;
            $.ajax({
                type: "get",
                url: nbds_frontend.url,
                data: {
                    action  : 'load_more_usecase',
                    page    : nextPage,
                    perPage : perPage,
                },
                dataType: "json",
                beforeSend: function() {
                    $this.addClass('loading');
                    $this.prop('disabled', true);
                }
            }).done(function(response) {
                $this.attr('data-current-page', nextPage);
                templateTagsWrapper.append(response.data);
                hoverTemplateTag();
                if(totalPage == nextPage) {
                    $this.hide();
                }
                $this.removeClass('loading');
                $this.prop('disabled', false);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log( 'Could not get posts, server response: ' + textStatus + ': ' + errorThrown );
            });
        });
    }

    function doUploadTab() {

        let UFListProduct = $('.kita-uf-list-product');
        let UFProductTitle = $('.kita-uf-product-title');
        UFProductTitle.each(function(i , value) {
            let $this         = $(this);
            $this.on('click', function (e) {
                e.preventDefault();
                let productId           = $this.attr('data-product-id');
                let optionContainer     = $('.kita-uf-nbo-option-container');
                let optionWrapper       = $('.kita-uf-nbo-option-container div');
                let currentWrapper      = $('.kita-uf-nbo-option-wrapper-' + productId);
                UFProductTitle.removeClass('activate');
                $this.addClass('activate');

                if(currentWrapper.hasClass('loaded')) {
                    optionWrapper.removeClass('activate');
                    currentWrapper.addClass('activate');
                }
                else {
                    $.ajax({
                        type: "get",
                        url: nbds_frontend.url,
                        data: {
                            action      : 'upload_file_tab',
                            productId   : productId,
                        },
                        dataType: "json",
                        beforeSend: function() {
                            optionContainer.addClass('disabled');
                            UFListProduct.addClass('disabled');
                        }
                    }).done(function(response) {
                        currentWrapper.html(response.data);
                        setTimeout(function() {
                            optionContainer.removeClass('disabled');
                            UFListProduct.removeClass('disabled');
                            optionWrapper.removeClass('activate');
                            currentWrapper.addClass('activate loaded');
                        }, 500);
    
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        console.log( 'Could not get posts, server response: ' + textStatus + ': ' + errorThrown );
                    });
                }
            });
        });
    }

    function customInputFile() {
        let fileInput = $('.kita-uf-nbo-option-container .nbd-input-u');
        fileInput.after('<div id="fileLabel"></div>');
        
        $(document).on('change', '.kita-uf-nbo-option-container .nbd-input-u', function(e) {
            fileInputValue = $(this).val();
            if(fileInputValue != "")
            {
                let theSplit = fileInputValue.split('\\');
                $('#fileLabel').html(theSplit[theSplit.length-1]);
            }
        } )
    }

    function addMaxLengthOrderNotesField() {
        $('#order_comments').attr('maxlength', 5000);
    }

})(jQuery);